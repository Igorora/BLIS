<?php

use Illuminate\Database\QueryException;

/**
 * Contains functions for managing test types
 *
 */
class TestTypeController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // List all the active testtypes
        $testtypes = TestType::orderBy('name', 'ASC')->get();
        // Load the view and pass the testtypes
        if (Input::has('raw')) {
            return Response::json($testtypes);
        } else {
            return View::make('testtype.index')->with('testtypes', $testtypes);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $measures = Measure::orderBy('name')->get();
        $specimentypes = SpecimenType::orderBy('name')->get();
        $testcategory = TestCategory::all()->sortBy('name');
        $paneltype = Panel::all();
        $measuretype = MeasureType::all()->sortBy('id');
        $organisms = Organism::orderBy('name')->get();

        //Create TestType
        return View::make('testtype.create')
                        ->with('testcategory', $testcategory)
                        ->with('measures', $measures)
                        ->with('measuretype', $measuretype)
                        ->with('specimentypes', $specimentypes)
                        ->with('organisms', $organisms)
                        ->with('paneltype', $paneltype);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
        $rules = array(
            'name' => 'required|unique:test_types,name',
            'test_category_id' => 'required|non_zero_key',
            'specimentypes' => 'required',
            'new-measures' => 'required',
            'paneltypes' => 'required',
			'targetTAT'=>'required|numeric',
			'tarif_A'=>'required|numeric',
			'tarif_B'=>'required|numeric',
			'tarif_C'=>'required|numeric',
			'tarif_D'=>'required|numeric',
			'tarif_E'=>'required|numeric'
        );
        
		$new_measures=Input::get('new-measures');
		if (!isset($new_measures)) {
            return Redirect::route('testtype.create')->withErrors('A test has to have at least one measure!')->withInput(Input::all());
        }
		
		foreach ($new_measures as $key => $value) {
            $rules['new-measures.' . $key . '.name'] = 'required';
            $rules['new-measures.' . $key . '.measure_type_id'] = 'required';
            if (Input::get('new-measures.' . $key . '.measure_type_id') == Measure::NUMERIC) {
                $rules['new-measures.' . $key . '.agemin'] = 'required';
                $rules['new-measures.' . $key . '.agemax'] = 'required';
                $rules['new-measures.' . $key . '.rangemin'] = 'required';
                $rules['new-measures.' . $key . '.rangemax'] = 'required';
            }
        }
        $validator = Validator::make(Input::all(), $rules);
        //array to be split here and sent to appropriate place! man! with ids and all possibilities
        //dd(Input::get('new-measures'));
        // process the login
        if ($validator->fails()) {
            return Redirect::route('testtype.create')->withErrors($validator)->withInput(Input::all());
        } else {
            // store
            $testtype = new TestType;
            $testtype->name = trim(Input::get('name'));
            $testtype->description = Input::get('description');
            $testtype->test_category_id = Input::get('test_category_id');
            $testtype->targetTAT = Input::get('targetTAT');
            $testtype->prevalence_threshold = Input::get('prevalence_threshold');
            $testtype->orderable_test = Input::get('orderable_test');
            $testtype->accredited = Input::get('accredited');
			$testtype->tarif_A = Input::get('tarif_A');
			$testtype->tarif_B = Input::get('tarif_B');
			$testtype->tarif_C= Input::get('tarif_C');
			$testtype->tarif_D = Input::get('tarif_D');
			$testtype->tarif_E = Input::get('tarif_E');
            try {
                $testtype->save();
                $measureIds = array();
                $inputNewMeasures = Input::get('new-measures');

                $measures = New MeasureController;
                $measureIds = $measures->store($inputNewMeasures);
                $testtype->setMeasures($measureIds);
                $testtype->setSpecimenTypes(Input::get('specimentypes'));
                $testtype->setPanelTypes(Input::get('paneltypes'));
                $testtype->setOrganisms(Input::get('organisms'));

                return Redirect::route('testtype.index')
                                ->with('message', trans('messages.success-creating-test-type'));
            } catch (QueryException $e) {
                Log::error($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //Show a testtype
        $testtype = TestType::find($id);

        //Show the view and pass the $testtype to it
        return View::make('testtype.show')->with('testtype', $testtype);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //Get the testtype
        $testtype = TestType::find($id);
        $measures = Measure::all();
        $measuretype = MeasureType::all()->sortBy('id');
        $paneltype = Panel::orderBy('name')->get();
        $specimentypes = SpecimenType::orderBy('name')->get();
        $testcategory = TestCategory::all();
        $organisms = Organism::orderBy('name')->get();

        //Open the Edit View and pass to it the $testtype
        return View::make('testtype.edit')
                        ->with('testtype', $testtype)
                        ->with('testcategory', $testcategory)
                        ->with('measures', $measures)
                        ->with('measuretype', $measuretype)
                        ->with('specimentypes', $specimentypes)
                        ->with('organisms', $organisms)
                        ->with('paneltype', $paneltype);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $rules = array(
            'name' => 'required',
            'test_category_id' => 'required|non_zero_key',
            'specimentypes' => 'required',
            'paneltypes' => 'required',
			'targetTAT'=>'required|numeric',
			'tarif_A'=>'required|numeric',
			'tarif_B'=>'required|numeric',
			'tarif_C'=>'required|numeric',
			'tarif_D'=>'required|numeric',
			'tarif_E'=>'required|numeric'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput(Input::all());
        } else {
            // Update
            $testtype = TestType::find($id);
            $testtype->name = trim(Input::get('name'));
            $testtype->description = Input::get('description');
            $testtype->test_category_id = Input::get('test_category_id');
            $testtype->targetTAT = Input::get('targetTAT');
            $testtype->prevalence_threshold = Input::get('prevalence_threshold');
            $testtype->orderable_test = Input::get('orderable_test');
            $testtype->accredited = Input::get('accredited');
			$testtype->tarif_A = Input::get('tarif_A');
			$testtype->tarif_B = Input::get('tarif_B');
			$testtype->tarif_C= Input::get('tarif_C');
			$testtype->tarif_D = Input::get('tarif_D');
			$testtype->tarif_E = Input::get('tarif_E');

            try {
                $testtype->save();
                $testtype->setOrganisms(Input::get('organisms'));
                $testtype->setSpecimenTypes(Input::get('specimentypes'));
                $testtype->setPanelTypes(Input::get('paneltypes'));
                $measureIds = array();
                if (Input::get('new-measures')) {
                    $inputNewMeasures = Input::get('new-measures');

                    $measures = New MeasureController;
                    $measureIds = $measures->store($inputNewMeasures);
                }

                if (Input::get('measures')) {
                    $inputMeasures = Input::get('measures');
                    foreach ($inputMeasures as $key => $value) {
                        $measureIds[] = $key;
                    }
                    $measures = New MeasureController;
                    $measures->update($inputMeasures);
                }
                $testtype->setMeasures($measureIds);
            } catch (QueryException $e) {
                Log::error($e);
            }

            // redirect
            $url = Session::get('SOURCE_URL');

            return Redirect::to($url)
                            ->with('message', trans('messages.success-updating-test-type'))->with('activetesttype', $testtype->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id) {
        //Soft delete the testtype
        $testtype = TestType::find($id);
        $inUseByTests = $testtype->tests->toArray();

        if (empty($inUseByTests)) {
            // The test type is not in use
            $testtype->delete();
        } else {
            // The test type is in use
            return Redirect::route('testtype.index')
                            ->with('message', 'messages.failure-test-type-in-use');
        }
        // redirect
        return Redirect::route('testtype.index')
                        ->with('message', trans('messages.success-deleting-test-type'));
    }

}
