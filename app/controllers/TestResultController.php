<?php

class TestResultController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Show specific result transmission records on a bootsrap modal.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function resultTransmissionRegister($visitId)
	{
		$testIds=Test::where('visit_id','=',$visitId)->lists('id');
		$transmittedResults=TransmittedResult::orderBy('time_transmitted', 'DESC')->whereIn('test_id',$testIds)->get();
		
		return  View::make('reports.patient.resulttransreg')->with('transmittedResults', $transmittedResults);
	}
	
	/**
     * transmit test results ajax via boustrap model
     *
     * @param
     * @return
     */

    public function transmitTestResult() {

        
        $testids=explode(';',Input::get('testids'));
        $transmittedto=Input::get('transmittedto');
        $designation=Input::get('designation');
        $transmittedBY=Auth::user()->id;
        $isItTransmitted=[];
        DB::beginTransaction();

        foreach($testids as $testid){
                $trensmittedResult= new TransmittedResult();
                $trensmittedResult->test_id=$testid;
                $trensmittedResult->transmitted_to=$transmittedto;
                $trensmittedResult->designation=$designation;
                $trensmittedResult->transmitted_by=$transmittedBY;
                
                $isItTransmitted[]=$trensmittedResult->save();              
                }
             

                if (in_array(false,$isItTransmitted , true) ) {
                  DB::rollback(); 
                  return 'fail'; 
                }

                DB::commit(); 
                return 'success';

	}
	
	
	/**
     * Display data after applying the filters on the report uses patient ID
     *
     * @return Response
     */
    public function viewPatientReport($id, $visit = null, $testId = null) {
        $from = Input::get('start');
        $to = Input::get('end');
        $pending = Input::get('pending');
        $testCategory=input::get('testCategory');
        $labSections = TestCategory::all()->sortBy('name')->lists('name', 'id');
        $date = date('Y-m-d');
        $error = '';
        $visitId = Input::get('visit_id') ;

        //	Check checkbox if checked and assign the 'checked' value
        if (Input::get('tests') === '1') {
            $pending = 'checked';
        }
        //	Query to get tests of a particular patient
        if (($visit || $visitId) && $id && $testId) {
            $tests = Test::where('id', '=', $testId);
            $visitObj=Visit::find($visit ? $visit : $visitId);
			$visitObjRegBy=$visitObj->registeredBy;
            $allCompleteVerified=$visitObj->areCompletedTestsVerified();
			$department= $visitObj->department;
            $visitType= $visitObj->visit_type;
			$ward= $visitObj->ward;
			$bed= $visitObj->bed;
			$requestDate= $visitObj->created_at;
			$registeredBy= $visitObjRegBy->name;
			$registeredByPhone=$visitObjRegBy->phone;
            $clinicInfo=$visitObj->clinicinfo;
            $requestedBY='by '.$visitObj->tests->first()->requested_by;
        } else if (($visit || $visitId) && $id) {
            $tests = Test::where('visit_id', '=', $visit ? $visit : $visitId);
			$visitObj=Visit::find($visit ? $visit : $visitId);
			$visitObjRegBy=$visitObj->registeredBy;
			$requestDate= $visitObj->created_at;
			$registeredBy= $visitObjRegBy->name;
			$registeredByPhone=$visitObjRegBy->phone;
            $allCompleteVerified=$visitObj->areCompletedTestsVerified();
            $department= $visitObj->department;
            $visitType= $visitObj->visit_type;
            $ward= $visitObj->ward;
            $bed= $visitObj->bed;
            $clinicInfo=$visitObj->clinicinfo;
            $requestedBY='by '.$visitObj->tests->first()->requested_by;
        } else {
           
            $tests = Test::join('visits', 'visits.id', '=', 'tests.visit_id')
                    ->where('patient_id', '=', $id);
			$department= 'CHUB Pathology department';
            $visitType= '';
            $allCompleteVerified=true;
			$ward= '';
			$bed= '';
            $requestedBY='';
			$requestDate= '';
			$registeredBy='';
			$registeredByPhone='';
			$clinicInfo='';

        }
        //	Begin filters - include/exclude pending tests
        if (!$pending) {
            $tests = $tests->whereIn('tests.test_status_id', [Test::COMPLETED, Test::VERIFIED]);

        }
//        else {
//            $tests = $tests->whereIn('tests.test_status_id', [Test::COMPLETED, Test::VERIFIED]);
//        }
        //	Date filters
        if ($from || $to) {

            if (!$to)
                $to = $date;

            if (strtotime($from) > strtotime($to) || strtotime($from) > strtotime($date) || strtotime($to) > strtotime($date)) {
                $error = trans('messages.check-date-range');
            } else {
                $toPlusOne = date_add(new DateTime($to), date_interval_create_from_date_string('1 day'));
                $tests = $tests->whereBetween('time_created', array($from, $toPlusOne->format('Y-m-d H:i:s')));

            }
        }
        if ($testCategory) {
        $testTypeIds=TestType::where('test_category_id','=',$testCategory)->lists('id');
        $tests = $tests->whereIn('tests.test_type_id', $testTypeIds);
        }
        
        //	Get tests collection
        $tests = $tests->get(array('tests.*'));

        //	Get patient details
        $patient = Patient::find($id);
        //	Check if tests are accredited
        $accredited = $this->accredited($tests);
        $verified = array();
        foreach ($tests as $test) {
            if ($test->isVerified())
                array_push($verified, $test->id);
            else
                continue;
        }
        if (Input::get('adhoc') == '1') {

            return Response::json(array(
                        'patient' => $patient,
                        'tests' => $tests,
                        'pending' => $pending,
                        'error' => $error,
                        'visit' => $visit,
                        'accredited' => $accredited,
                        'verified' => $verified
            ));
        }

        if (Input::has('word')) {
            $date = date("Ymdhi");
            $fileName = str_replace(" ", "", $patient->name) . $id . "_" . $date . ".doc";
            $headers = array(
                "Content-type" => "text/html",
                "Content-Disposition" => "attachment;Filename=" . $fileName
            );
            $content = View::make('reports.patient.export')
                    ->with('patient', $patient)
                    ->with('tests', $tests)
                    ->with('from', $from)
                    ->with('to', $to)
                    ->with('visit', $visit ? $visit : $visitId)
                    ->with('department', $department)
                    ->with('accredited', $accredited);
            return Response::make($content, 200, $headers);
        } else {
            return View::make('reports.patient.report')
                            ->with('patient', $patient)
                            ->with('tests', $tests)
                            ->with('pending', $pending)
                            ->with('error', $error)
                            ->with('visit', $visit ? $visit : $visitId)
                            ->with('accredited', $accredited)
                            ->with('verified', $verified)
                            ->with('department', $department)
                            ->with('visitType', $visitType)
                            ->with('labSections', $labSections)
							->with('ward', $ward )
							->with('requestDate', $requestDate )
							->with('registeredBy', $registeredBy )
							->with('registeredByPhone', $registeredByPhone )
							->with('bed', $bed )
                            ->with('allCompleteVerified', $allCompleteVerified )
                            ->with('clinicInfo', $clinicInfo)
                            ->with('requestedBY', $requestedBY)
                            ->withInput(Input::all());
        }
    }
	
	/**
     * Display test report and its audit
     *
     * @return Response
     */
    public function viewTestAuditReport($testId) {

        $test = Test::find($testId);
        $patient= $test->visit->patient->name;
        if (Input::has('word')) {
            $date = date("Ymdhi");
            $fileName = str_replace(" ", "", $patient)."_" . $testId . "_" . $date . ".doc";
            $headers = array(
                "Content-type" => "text/html",
                "Content-Disposition" => "attachment;Filename=" . $fileName
            );
            $content = View::make('reports.audit.exportAudit')
                    ->with('test', $test);
            return Response::make($content, 200, $headers);
        } else {
            return View::make('reports.audit.testAudit')
                            ->with('test', $test);
        }
    }
	
	 /**
     * 	Function to check for accredited test types
     *
     */
    public function accredited($tests) {
        $accredited = array();
        foreach ($tests as $test) {
            if ($test->testType->isAccredited())
                array_push($accredited, $test->id);
        }
        return $accredited;
    }



}
