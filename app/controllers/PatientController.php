<?php

use Illuminate\Database\QueryException;

/**
 *Contains functions for managing patient records 
 *
 */
class PatientController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
		{

		$search = trim(Input::get('search'));
		$patients= null;
		$message=null;
		if (!$search) {

			return View::make('patient.index')->with('patients', $patients)->with('message','Please search the patient first' );
		}
		$patients = Patient::search($search)->orderBy('id', 'desc')->paginate(Config::get('kblis.page-items'))->appends(Input::except('_token'));


		if (count($patients) == 0) {
		 	Session::flash('message', trans('messages.no-match'));
		}

		// Load the view and pass the patients
		return View::make('patient.index')->with('patients', $patients)->with('message',$message )->withInput(Input::all());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		 $countries= Country::orderBy('name', 'ASC')->get();
			if($countries->count()>0){
				$countr_options='<option value="">Select Country</option>';
				foreach($countries as $country){
					$countr_options .= '<option data-country="'.$country->country_id .'" value="'. $country->name .'">'. $country->name .'</option>';												
				}
				
			}
		//Create Patient
		$lastInsertId = DB::table('patients')->max('id')+1;
		return View::make('patient.create')->with('countr_options', $countr_options)->with('lastInsertId', $lastInsertId);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		//$lastInsertId = DB::table('patients')->max('id')+1;
		$coutry=Input::get('country');
		//Input::merge(['patient_number'=>$lastInsertId.'A']);		
		if($coutry=='Rwanda') {
			$rules = array(
			'patient_number' => 'required|unique:patients,patient_number',
			'name'       => 'required',
			'gender' => 'required',
			'country'=>'required',
			'province'=> 'required',
			'district'=> 'required',
			'sector'=> 'required',
			'cell'=> 'required',
			'village'=> 'required',
			'age'=>Input::get('ageselector')==0 ? 'required|numeric|min:1|max:110': '',
			'dob'=>Input::get('ageselector')!=0 ? 'required|date_format:Y-m-d': '',
			);	

		}else{
			$rules = array(
			'patient_number' => 'required|unique:patients,patient_number',
			'name'       => 'required',
			'gender' => 'required',
			'country'=>'required',
			'age'=>Input::get('ageselector')==0 ? 'required|numeric|min:1|max:110': '',
			'dob'=>Input::get('ageselector')!=0 ? 'required|date_format:Y-m-d': '',
		);
		}

		$patient = new Patient;
		$patient->patient_number = Input::get('patient_number');
		$patient->name = ucwords(strtolower(Input::get('name')));
		$patient->gender = Input::get('gender');
		if(Input::get('ageselector')==0){
			//calculate date from age
			$age=Input::get('age');
			$patient->dob =$this->getDobFromAge($age,date("Y-m-d"));
		}else{
			$patient->dob = Input::get('dob');
		}
		
		$patient->email = Input::get('email');
		$patient->country = $coutry;
		$patient->province = Input::get('province');
		$patient->district = Input::get('district');
		$patient->sector = Input::get('sector');
		$patient->cell = Input::get('cell');
		$patient->village = Input::get('village');
		//$patient->address = Input::get('address');
		$patient->phone_number = Input::get('phone_number');
		$patient->created_by = Auth::user()->id;

		
		$validator = Validator::make(Input::all(), $rules);
		

		if ($validator->fails()) {

			return Redirect::back()->withErrors($validator)->withInput(Input::all());
		} else {
			// store
			
			try{
				DB::beginTransaction();
				if(!$patient->save()){
					DB::rollback();
					return Redirect::back()->withErrors("Some thing went wrong!! Patient was not created")->withInput(Input::all());
					}
			DB::commit();
			//$url = Session::get('SOURCE_URL');
			//dd($url);
			return Redirect::to('/patient?search='.$patient->patient_number)->with('message', 'Successfully created patient!');
			}catch(QueryException $e){
				Log::error($e);
			}
			
			// redirect
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//Show a patient
		$patient = Patient::find($id);

		//Show the view and pass the $patient to it
		return View::make('patient.show')->with('patient', $patient);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//Get the patient
		$patient = Patient::find($id);

		//Open the Edit View and pass to it the $patient
		return View::make('patient.edit')->with('patient', $patient);
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
		$rules = array(
			'patient_number' => 'required',
			'name'       => 'required',
			'gender' => 'required',
			'dob' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('patient/' . $id . '/edit')
				->withErrors($validator)
				->withInput(Input::except('password'));
		} else {
			// Update
			$patient = Patient::find($id);
			$patient->patient_number = Input::get('patient_number');
			$patient->name = ucwords(strtolower(Input::get('name')));
			$patient->gender = Input::get('gender');
			$patient->dob = Input::get('dob');
			$patient->email = Input::get('email');
			$patient->address = Input::get('address');
			$patient->phone_number = Input::get('phone_number');
			$patient->created_by = Auth::user()->id;
			DB::beginTransaction();
				if(!$patient->save()){
					DB::rollback();
					return Redirect::back()->withErrors("Some thing went wrong!! Patient was not created")->withInput(Input::all());
					}
			DB::commit();

			// redirect
			//$url = Session::get('SOURCE_URL');
			return Redirect::to('/patient?search='.$patient->patient_number)->with('message', 'The patient details were successfully updated!') 
									->with('activepatient',$patient ->id);

		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage (soft delete).
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($id)
	{
		//Soft delete the patient
		$patient = Patient::find($id);

		DB::beginTransaction();
				if(!$patient->delete()){
					DB::rollback();
					return Redirect::back()->withErrors("Some thing went wrong!! Patient was not created")->withInput(Input::all());
					}
			DB::commit();

		// redirect
			$url = Session::get('SOURCE_URL');
			return Redirect::to($url)
			->with('message', 'The patient was successfully deleted!');
	}

	/**
	 * Return a Patients collection that meets the searched criteria as JSON.
	 *
	 * @return Response
	 */
	public function search()
	{
        return Patient::search(Input::get('text'))->take(Config::get('kblis.limit-items'))->get()->toJson();
	}
	
	public function getDobFromAge($age, $requestDate)
    {
        $requestDate = new DateTime($requestDate);
        $dob = date_sub($requestDate,date_interval_create_from_date_string($age.' years'));
        return $dob;
    }

}