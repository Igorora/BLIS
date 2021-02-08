<?php

use Illuminate\Database\QueryException;

/**
 * Contains test resources
 *
 */
class TestController extends \BaseController {

    /**
     * Display a listing of Tests. Factors in filter parameters
     * The search string may match: patient_number, patient name, test type name, specimen ID or visit ID
     *
     * @return Response
     */
    public function index() {

        $fromRedirect = Session::pull('fromRedirect');

        if ($fromRedirect) {

            $input = Session::get('TESTS_FILTER_INPUT');
        } else {

            $input = Input::except('_token');
        }

        $searchString = isset($input['search']) ? $input['search'] : '';
        $testStatusId = isset($input['test_status']) ? $input['test_status'] : '';
        $dateFrom = isset($input['date_from']) ? $input['date_from'] : '';
        $dateTo = isset($input['date_to']) ? $input['date_to'] : '';

        // Search Conditions
        if ($searchString || $testStatusId || $dateFrom || $dateTo) {

            $tests = Test::search($searchString, $testStatusId, $dateFrom, $dateTo)->limit(200)->groupBy('id');

            if (count($tests) == 0) {
                Session::flash('message', trans('messages.empty-search'));
            }
        } else {
            // List all the active tests
            $tests = Test::orderBy('id', 'DESC')->groupBy('id');
        }

        // Create Test Statuses array. Include a first entry for ALL
        $statuses = array('all') + TestStatus::all()->lists('name', 'id');

        foreach ($statuses as $key => $value) {
            $statuses[$key] = trans("messages.$value");
        }

        // Pagination
        $tests = $tests->paginate(Config::get('kblis.page-items'))->appends($input);
		//$tests = $tests->get();

        //	Barcode
        $barcode = Barcode::first();

        // Load the view and pass it the tests
        return View::make('test.index')
                        ->with('testSet', $tests)
                        ->with('testStatus', $statuses)
                        ->with('barcode', $barcode)
                        ->withInput($input);
    }

    /**
     * Recieve a Test from an external system
     *
     * @param
     * @return Response
     */
    public function receive($id) {
        $test = Test::find($id);
        $test->test_status_id = Test::PENDING;
        $test->time_created = date('Y-m-d H:i:s');
        $test->created_by = Auth::user()->id;
        $test->save();

        return $id;
    }

    /**
     * Display a form for creating a new Test.
     *
     * @return Response
     */
    public function create($patientID = 0) {
        if ($patientID == 0) {
            $patientID = Input::get('patient_id');
        }

        $testTypes = TestType::where('orderable_test', 1)->orderBy('name', 'asc')->get();
        $clinicians=Clinicians::orderBy('name', 'asc')->lists('name','name');
        $patient = Patient::find($patientID);

        //Load Test Create View
        return View::make('test.create')
                        ->with('testtypes', $testTypes)
                        ->with('patient', $patient)
                        ->with('clinicians', $clinicians);
    }

    /**
     * Save a new Test.
     *
     * @return Response
     */
    public function saveNewTest() {
       $requestID = Input::get('request_id');
        $testTypes = Input::get('testtypes');
        $visitType=Input::get('visit_type');
        $submitedDepartment=Input::get('department');
        if($visitType==1){
            $rules = array(
            'visit_type' => 'required',
            'physician' => 'required',
            'testtypes' => 'required',
            'ward'=>'required',
            //'visit_amount' => 'required|numeric',
            'department' => 'required',
            'clinicinfo'=> 'required',
            'visit_urgency'=> 'required'
        );
        }else{
            $rules = array(
            'visit_type' => 'required',
            'physician' => 'required',
            'testtypes' => 'required',
            //'visit_amount' => 'required|numeric',
            'department' => 'required',
            'clinicinfo'=> 'required',
            'visit_urgency'=> 'required'
        );


        }

        if($submitedDepartment==13){
            $rules['hospital']='required';
        }

        $physician=Input::get('physician');
        if (Input::get('physician')=='Not listed') {
        	$physician=Input::get('nonListedClinician');
        	$rules['nonListedClinician']='required|regex:/^[a-zA-Z\s]+/';

        }

        $messages = array(
            'regex' => 'Name of requesting clinician only contain letters and spaces.',
        );

        $validator = Validator::make(Input::all(), $rules,$messages);

        // process the login
        if ($validator->fails()) {
            if ($requestID) {
            return Redirect::route('test.update', array($requestID))->withInput(Input::all())->withErrors($validator);
            }
            return Redirect::route('test.create', array(Input::get('patient_id')))->withInput(Input::all())->withErrors($validator);

        }

        $visitTypes = ['Out-patient', 'In-patient'];
        $department = ['IM', 'Obs gyn', 'Ped', 'ENT/ORL', 'Dermato', 'Stomato', 'Ophtalmo', 'Surg', 'Emergency','ICU', 'Dialysis' ,'ARV PED','ARV IM', 'Reffered'];
        $activeRequest = array();
        $transactions=[];

        /*
         * - Create a visit
         * - Fields required: visit_type, patient_id
         */
        if ($requestID) {
            $visit = Visit::find($requestID);
        }else{
            $visit = new Visit;
        }

        $visit->patient_id = Input::get('patient_id');
        $visit->visit_type = $visitTypes[$visitType];
        $visit->visit_urgency = Input::get('visit_urgency');
        //dd($visit->visit_type );
        $visit->ward =Input::get('ward');
        $visit->bed = Input::get('bed');
        //$visit->visit_amount = Input::get('visit_amount');
        $visit->clinicinfo = Input::get('clinicinfo');
        $visit->registered_by = Auth::user()->id;
        $visit->department = $submitedDepartment!=13 ? $department[$submitedDepartment] : Input::get('hospital') ;
        //dd($visit->department );
        DB::beginTransaction();
        $transactions[]= $visit->save();
        $activeRequest[]=$visit->id;
        $testsTransactions=$visit->setTests($testTypes,$requestID,$physician);


        /*
         * - Create tests requested
         * - Fields required: visit_id, test_type_id, specimen_id, test_status_id, created_by, requested_by
         */
        $transactions=array_merge($transactions,$testsTransactions);


        if(in_array(false,$transactions , true)){
            DB::rollback();
            // $url = Session::get('SOURCE_URL');

            // return Redirect::to($url)->withInput(Input::all()) ->withErrors('An error has occured and request was not submited');

            if ($requestID) {
            return Redirect::route('test.update', array($requestID))->withInput(Input::all())->withErrors('An error has occured and request was not successfuly updated');
            }
            return Redirect::route('test.create', array(Input::get('patient_id')))->withInput(Input::all())->withErrors('An error has occured and request was not successfuly submited');

            }
        DB::commit();
        return Redirect::route('labRequest.viewDetails', $activeRequest)->with('message','Request successfuly registered, please verify payment');

    }

    /**
     * Display Rejection page
     *
     * @param
     * @return
     */
    public function reject($specimenID) {
        $specimen = Specimen::find($specimenID);
        $rejectionReason = RejectionReason::all();
        return View::make('test.reject')->with('specimen', $specimen)
                        ->with('rejectionReason', $rejectionReason);
    }

    /**
     * Executes Rejection
     *
     * @param
     * @return
     */
    public function rejectAction() {
        //Reject justifying why.
        $rules = array(
            'rejectionReason' => 'required|non_zero_key',
            'reject_explained_to' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::route('test.reject', array(Input::get('specimen_id')))
                            ->withInput()
                            ->withErrors($validator);
        } else {
            $specimen = Specimen::find(Input::get('specimen_id'));
            $specimen->rejection_reason_id = Input::get('rejectionReason');
            $specimen->specimen_status_id = Specimen::REJECTED;
            $specimen->rejected_by = Auth::user()->id;
            $specimen->time_rejected = date('Y-m-d H:i:s');
            $specimen->reject_explained_to = Input::get('reject_explained_to');
            DB::beginTransaction();
												$transaction =$specimen->save();
												if(!$transaction){
													DB::rollback();
													return Redirect::route('test.reject', array(Input::get('specimen_id')))
                            ->withInput()
                            ->withErrors('Sorry!! An error occured, specimen was not rejected');

													}
												DB::commit();
            $url = Session::get('SOURCE_URL');

            return Redirect::to($url)->with('message', 'messages.success-rejecting-specimen')
                            ->with('activeTest', array($specimen->test->id));
        }
    }

    /**
     * Accept a Test's Specimen
     *
     * @param
     * @return
     */
    public function accept() {
        $specimen = Specimen::find(Input::get('id'));
		$specimen->specimen_status_id = Specimen::ACCEPTED;
        $specimen->accepted_by = Auth::user()->id;
        $specimen->time_accepted = date('Y-m-d H:i:s');
        //$specimen->save();
							DB::beginTransaction();
							$transaction = $specimen->save();
							//dd($transaction);
							if(!$transaction){
									DB::rollback();
									return 'false';
									//Redirect::route('labRequest.viewDetails', [$requestId])->withInput(Input::all())	->withErrors('An error has occured and request was not submited');
								}
									DB::commit();

        return $specimen->specimen_status_id;
    }

    /**
     * Display Change specimenType form fragment to be loaded in a modal via AJAX
     *
     * @param
     * @return
     */
    public function changeSpecimenType() {
        $test = Test::find(Input::get('id'));
        return View::make('test.changeSpecimenType')->with('test', $test);
    }

    /**
     * Update a Test's SpecimenType
     *
     * @param
     * @return
     */
    public function updateSpecimenType() {
        $specimen = Specimen::find(Input::get('specimen_id'));
        $specimen->specimen_type_id = Input::get('specimen_type');
        $specimen->save();

        return Redirect::route('test.viewDetails', array($specimen->test->id));
    }

    /**
     * Starts Test
     *
     * @param
     * @return
     */
    public function start() {
        $test = Test::find(Input::get('id'));
        $test->test_status_id = Test::STARTED;
        $test->started_by = Auth::user()->id;
        $test->time_started = date('Y-m-d H:i:s');



								DB::beginTransaction();
							$transaction = $test->save();
							//dd($transaction);
							if(!$transaction){
									DB::rollback();
									return 'false';
									//Redirect::route('labRequest.viewDetails', [$requestId])->withInput(Input::all())	->withErrors('An error has occured and request was not submited');
								}
									DB::commit();

        return $test->test_status_id;
    }

    /**
     * Display Result Entry page
     *
     * @param
     * @return
     */
    public function enterResults($testID) {
        $test = Test::find($testID);
        return View::make('test.enterResults')->with('test', $test);
    }

    /**
     * Returns test result intepretation
     * @param
     * @return
     */
    public function getResultInterpretation() {
        $result = array();
        //save if it is available

        if (Input::get('age')) {
            $result['birthdate'] = Input::get('age');
            $result['gender'] = Input::get('gender');
        }
        $result['measureid'] = Input::get('measureid');
        $result['measurevalue'] = Input::get('measurevalue');
        $result['testId'] = Input::get('testId');

        $measure = new Measure;
        return $measure->getResultInterpretation($result);
    }

    /**
     * Saves Test Results
     *
     * @param $testID to save
     * @return view
     */
    public function saveResults($testID) {
        $test = Test::find($testID);
		$transactions=[];
         DB::beginTransaction();
        foreach ($test->testType->measures as $measure) {
            $testResult = TestResult::firstOrCreate(array('test_id' => $testID, 'measure_id' => $measure->id));
            $initialMeasureVal=$testResult->result;
            $inputName = "m_" . $measure->id;
            $inputVal=is_array(Input::get($inputName)) ? implode(" ** " , Input::get($inputName)) : Input::get($inputName);
            $audit = false;
            //Log in Audit if the values have changed
            if ($testResult->result != $inputVal) {
                $testResultAudit = new AuditResult();
                if ($testResult->result == null) {
                    $testResultAudit->previous_results = "";
                } else {
                    $testResultAudit->previous_results = $testResult->result;
                }
                $testResultAudit->test_result_id = $testResult->id;
                $testResultAudit->user_id = Auth::user()->id;
                $audit = true;
            }

            $testResult->result = $inputVal;
            if (!$initialMeasureVal) {
             $testResult->entered_by=Auth::user()->id;
             $testResult->time_entered = date('Y-m-d H:i:s');
            }


            //$inputName = "m_" . $measure->id;
            if ($measure->measure_type_id==Measure::NUMERIC && $inputVal!='-') {
                  $rules = array("$inputName" => 'numeric');

            }else{
                     $rules = array("$inputName" => 'max:5000');
                }
            $messages = array(
                    'numeric' => 'The '. $measure->name .' field has to be a numeric value or a single dash (-).',
                );
            $validator = Validator::make(Input::all(), $rules,$messages);

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput(Input::all());
            } else {

				$transactions[]=$testResult->save();
                if ($audit == true) {
                    	$transactions[]=$testResultAudit->save();
                }
            }
        }


		$test->interpretation = Input::get('interpretation');
		$nonreportedtests = $test->testResults()->where('result','=','')->count() + $test->testResults()->whereNull('result')->count();
		if($nonreportedtests == 0){
			//dd($reportedtests);
			if (!($test->isVerified() || $test->isCompleted())){

                $test->test_status_id = Test::COMPLETED;
                $test->time_completed = date('Y-m-d H:i:s');
            	$test->tested_by = Auth::user()->id;
            }



		}
		 	$transactions[]=$test->save();


        if(in_array(false,$transactions , true)){
									DB::rollback();
									return Redirect::back()->withErrors('Sorry!! An error occured, result was not saved')->withInput(Input::all());
								}
									DB::commit();

								$info = new stdclass();
        //Fire of entry saved/edited event
        $verification = RequireVerification::get()->first();
        if ($verification->allowProbativeResults()) {
            Event::fire('test.saved', array($testID));
            $info->info = trans('messages.success-saving-results');
        } else {
            //Alert user of the fact that results will not be sent
            //until they are verified.
            $info->danger = trans('messages.verifification-warning');
        }
        $input = Session::get('TESTS_FILTER_INPUT');
        Session::put('fromRedirect', 'true');

        // Get page
        $url = Session::get('SOURCE_URL');
        $urlParts = explode('&', $url);
        if (isset($urlParts['page'])) {
            $pageParts = explode('=', $urlParts['page']);
            $input['page'] = $pageParts[1];
        }

        // redirect
        return Redirect::action('TestController@index')
                        ->with('message', $info)
                        ->with('activeTest', array($test->id))
                        ->withInput($input);
    }

    /**
     * Display Edit page
     *
     * @param
     * @return
     */
    public function edit($testID) {
        $test = Test::find($testID);

        return View::make('test.edit')->with('test', $test);
    }

    /**
     * Display Test Details
     *
     * @param
     * @return
     */
    public function viewDetails($testID) {
        return View::make('test.viewDetails')->with('test', Test::find($testID));
    }

    /**
     * Verify Test
     *
     * @param
     * @return
     */
    public function verify($testID) {

        $test = Test::find($testID);
         if ($test->verified_by==0) {
            $test->test_status_id = Test::VERIFIED;
            $test->time_verified = date('Y-m-d H:i:s');
            $test->verified_by = Auth::user()->id;

            DB::beginTransaction();
            $transaction = $test->save();
            //dd($transaction);
            if(!$transaction){
                DB::rollback();
                return Redirect::route('test.viewDetails', array($testID))
                    ->withErrors('Sorry!! An error occured, test was not verified')
                    ->with('test', $test);
                }
                    DB::commit();
            //Fire of entry verified event
            Event::fire('test.verified', array($testID));


            if (Input::get('ajaxVerify')) {
                    return 'success';

            }
            $url = Session::get('SOURCE_URL');


            // return Redirect::route('test.viewDetails', array($testID))
            //             ->with('message', trans('messages.success-verifying-results'))
            //             ->with('test', $test);
            return Redirect::action('TestController@index')
                        ->with('message', trans('messages.success-verifying-results'))
                        ->with('activeTest', array($testID));

        } else {
            if (Input::get('ajaxVerify')) {

            $verifier=array('<strong>Verified by: </strong>'. $test->verifiedBy->name.'<span class="label label-success">' . $test->verifiedBy->phone.'</span>',$test->time_verified,1);
            return $verifier;

            }

            return Redirect::route('test.viewDetails', array($testID))
                        ->with('message', trans('messages.success-verifying-results'))
                        ->with('test', $test);

        }


    }

    /**
     * Refer the test
     *
     * @param specimenId
     * @return View
     */
    public function showRefer($specimenId) {
        $specimen = Specimen::find($specimenId);
        $facilities = Facility::all();
        //Referral facilities
        return View::make('test.refer')
                        ->with('specimen', $specimen)
                        ->with('facilities', $facilities);
    }

    /**
     * Refer action
     *
     * @return View
     */
    public function referAction() {
        //Validate
        $rules = array(
            'referral-status' => 'required',
            'facility_id' => 'required|non_zero_key',
            'person',
            'contacts'
        );
        $validator = Validator::make(Input::all(), $rules);
        $specimenId = Input::get('specimen_id');
								$transactions=[];

        if ($validator->fails()) {
            return Redirect::route('test.refer', array($specimenId))->withInput()->withErrors($validator);
        }

        //Insert into referral table
        $referral = new Referral();
        $referral->status = Input::get('referral-status');
        $referral->facility_id = Input::get('facility_id');
        $referral->person = Input::get('person');
        $referral->contacts = Input::get('contacts');
        $referral->user_id = Auth::user()->id;

        //Update specimen referral status
        $specimen = Specimen::find($specimenId);

        //DB::transaction(function() use ($referral, $specimen) {
            DB::beginTransaction();
												$transactions[]=$referral->save();
            $specimen->referral_id = $referral->id;
            $transactions[]=$specimen->save();
												$transactions[]=false;
        //});
												if(in_array(false,$transactions , true)){
									DB::rollback();
									return Redirect::route('test.refer', [$specimenId])->withInput(Input::all())	->withErrors('Sorry!!An error has occured and request was not trensfered');
								}
									DB::commit();

        //Start test
        Input::merge(array('id' => $specimen->test->id)); //Add the testID to the Input
        $this->start();

        //Return view
        $url = Session::get('SOURCE_URL');

        return Redirect::to($url)->with('message', trans('messages.specimen-successful-refer'))
                        ->with('activeTest', array($specimen->test->id));
    }

    /**
     * Culture worksheet for Test
     *
     * @param
     * @return
     */
    public function culture() {
        $test = Test::find(Input::get('testID'));
        $test->test_status_id = Test::VERIFIED;
        $test->time_verified = date('Y-m-d H:i:s');
        $test->verified_by = Auth::user()->id;
        $test->save();

        //Fire of entry verified event
        Event::fire('test.verified', array($testID));

        return View::make('test.viewDetails')->with('test', $test);
    }

}
