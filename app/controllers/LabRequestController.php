<?php

use Illuminate\Database\QueryException;

/**
 * Contains test resources
 *
 */
class LabRequestController extends \BaseController {

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

            $tests = Test::search($searchString, $testStatusId, $dateFrom, $dateTo)->select('*',DB::raw("GROUP_CONCAT(test_type_id SEPARATOR ', ') as testTpe"))->take(200)->groupBy('visit_id');

            if (count($tests) == 0) {
                Session::flash('message', trans('messages.empty-search'));
            }
        } else {
            // List all the active tests
            $tests = Test::orderBy('id', 'DESC')->select('*',DB::raw("GROUP_CONCAT(test_type_id SEPARATOR ', ') as testTpe"))->take(200)->groupBy('visit_id');
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
        return View::make('labRequest.index')
                        ->with('testSet', $tests)
                        ->with('testStatus', $statuses)
                        ->with('barcode', $barcode)
                        ->withInput($input);
    }

    /**
     * Display a form for editing a request.
     *
     * @return Response
     */

    public function editRequest($requestId) {

        $department = ['IM'=>0, 'Obs gyn'=>1, 'Ped'=>2, 'ENT/ORL'=>3, 'Dermato'=>4, 'Stomato'=>5, 'Ophtalmo'=>6, 'Surg'=>7, 'Emergency'=>8,'ICU'=>9, 'Dialysis'=>10 ,'ARV PED'=>11,'ARV IM'=>12, 'Reffered'=>13];
        $visitTypes = ['Out-patient'=>0, 'In-patient'=>1];
        $request=Visit::find($requestId);
        $clinicians=Clinicians::orderBy('name', 'asc')->lists('name','name');
        $requestedTestIds= array_flip(Test::where('visit_id',$requestId)->lists('test_type_id'));
        //$currentTestTypeNameIds=TestType::whereIn('id',$currentTestTypeIds)->lists('id');
        $testTypes = TestType::where('orderable_test', 1)->orderBy('name', 'asc')->get();
        //dd($requestedTestIds);
        $patient=$request->patient;


        //Load Test Create View
        return View::make('labRequest.edit')
                        ->with('testtypes', $testTypes)
                        ->with('patient', $patient)
                        ->with('requestId', $requestId)
                        ->with('requestDprtmt', $department[$request->department])
                        ->with('requestType', $visitTypes[$request->visit_type])
                        ->with('requestWard', $request->ward)
                        ->with('requestBed', $request->bed)
                        ->with('requestUrgency', $request->visit_urgency)
                        ->with('clinicians', $clinicians)
                        ->with('requestClinicInfo', $request->clinicinfo)
                        ->with('requestby', $request->tests->first()->requested_by)
                        ->with('requestedTestIds', $requestedTestIds);

    }
				
	/**
     * Recieve a Request from and verify if tests ar paid
     *
     * @param
     * @return Response
     */
    public function receive() {
                                   
		$requestId=Input::get("request_ID");
		$testToverify=Test::where('visit_id','=',$requestId)->count();
		$testsubmited=Input::except('insurance','_token','request_ID');
		//$testIds = array_keys( array_filter($testsubmited));
        $testIds = array_keys(array_diff($testsubmited,array(0)));
        $testIdsUnpaid=array_keys(array_diff($testsubmited,array(1)));
        $request=Visit::find($requestId);
        $isTestPaid=[];
        $visit_amount=0;
		$activeRequest = [$requestId];

        if($testToverify != count($testsubmited) ){
        $rules = ['insurance'=>'required', 'test'=>'required'];
                                    
        }else{
            $rules = ['insurance'=>'required'];
        }
        $messages=['test.required'=>'Each of the tests has to be checked as paid or not'];
            
        $validator = Validator::make(Input::all(), $rules,$messages);

        if ($validator->fails()) {
            return Redirect::route('labRequest.viewDetails', [$requestId])->withErrors($validator)->withInput(Input::all());
        }
        

        DB::beginTransaction();                        
		//dd(count($testIds));
		if(!count($testIds)){
			
            if (count($testIdsUnpaid)) {
                $request=Visit::find($requestId);
                $request->visit_amount=$visit_amount;
                $isTestPaid[]=$request->save();
                if(in_array(false,$isTestPaid , true)){
                    DB::rollback();
                    return Redirect::route('labRequest.viewDetails', [$requestId])->withInput(Input::all()) ->withErrors('An error has occured and request was not submited');
                }
                    DB::commit();
                return Redirect::route('labRequest.index')->with('message','No test will be processed since none is paid')->with('activeRequest',$activeRequest);
                
            }
            return Redirect::route('labRequest.viewDetails', [$requestId])->withInput(Input::all())->withErrors('At least one test on a request need to be paid');
			
		}

		$insurance=Input::get("insurance");
		

		//dd($testPayStatuses);
		
		foreach($testIds as $testId){
			//dd(count($testId));
			$test=Test::find($testId);
			$testPrice=$test->testType->$insurance;
			$test->paid_amount=$testPrice;
			$test->test_status_id = Test::PENDING;
			$test->time_received= date('Y-m-d H:i:s');
			//$test->time_created = date('Y-m-d H:i:s');
			$test->created_by = Auth::user()->id;
			$isTestPaid[]=$test->save();
			$visit_amount += $testPrice;
			
		}
		
		$request->visit_amount=$visit_amount;
		$isTestPaid[]=$request->save();
		if(in_array(false,$isTestPaid , true)){
			DB::rollback();
			return Redirect::route('labRequest.viewDetails', [$requestId])->withInput(Input::all())	->withErrors('An error has occured and request was not submited');
		}
			DB::commit();
		return Redirect::route('labRequest.index')->with('message','messages.success-Lab-request-submited')->with('activeRequest',$activeRequest);
       
    }
					


    
    public function viewDetails($requestID) {
		$tests=Test::where('visit_id', '=', $requestID);
		$visit=Visit::find($requestID);
        return View::make('labRequest.viewDetails')
        		->with('requestedTests', $tests->get())
        		->with('requestID',$requestID)
        		->with('department', $visit->department)
        		->with('visit', $visit)
        		->with('visitType', $visit->visit_type)
        		->with('ward', $visit->ward?$visit->ward:'')
        		->with('room', $visit->room?$visit->room:'')
        		->with('requestedBY',$tests->first()->requested_by)
        		->with('recievedBy',$tests->first()->created_by?$tests->first()->createdBy:'')
        		->with('visitAmount',$visit->visit_amount?$visit->visit_amount:'')
        		->with('requestedOn',$tests->first()->time_created);
    }

    /**
     * Distroy request.
     *
     * @return Response
     */

    public function destroyRequest($requestId) {

        $request=Visit::find($requestId);
        $activeRequest = [$requestId];
        if (!is_null($request->visit_amount) ) {
            return Redirect::route('labRequest.index')->withErrors('This request can not be deleted some test were paid')->with('activeRequest',$activeRequest);
            
        }

        $deletionStatuses= $request->destroyRequest();

        if (in_array(false,$deletionStatuses , true) || in_array(0,$deletionStatuses , true)) {
			$activeRequest[]=$requestId;
            return Redirect::route('labRequest.index')->withErrors('Lab requests Number '.$requestId.' was not deleted please repeat the process')->with('activeRequest',$activeRequest);
            
        }

        return Redirect::route('labRequest.index')->with('message','Lab requests Number '.$requestId.' was successfully deleted');



    }


   

}
