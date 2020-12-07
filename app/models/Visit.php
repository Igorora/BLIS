<?php

class Visit extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'visits';

	public $timestamps = true;

	/**
	 * Test relationship
	 */
    public function tests()
    {
        return $this->hasMany('Test');
    }

	/**
	 * Patient relationship
	 */
	public function patient()
	{
		return $this->belongsTo('Patient');
	}
	
	/**
	 * Users relationship
	 */
	public function registeredBy()
	{
		return $this->belongsTo('User', 'registered_by', 'id');
	}

	/**
	 * Measures relationship
	 */
	public function numOfMeasures()
	{
	 
	 return $this->tests->count();
	  
	}

	/**
	 * Reported measures relationship
	 */
	public function numResMeasures()
	{
	   return $this->tests()->where('tested_by','!=',0)->count();
	  
	}


	/**
	 * Printed result relationship
	 */
	public function numPrintedTests()
	{
	  $verifiedTestIds=$this->tests()->where('verified_by','!=',0)->lists('id');
	  return TransmittedResult::distinct()->whereIn('test_id',$verifiedTestIds)->count('test_id'); 
	}



	public function areCompletedTestsVerified(){
		$numCompletedTests=$this->tests()->where('tested_by','!=',0)->count();
		$numVerifiedTests=$this->tests()->where('verified_by','!=',0)->count();

		if ($numCompletedTests!=0 && $numCompletedTests===$numVerifiedTests) {
			return true;
			
		}
		return false;

	}

	public function setTests($tests,$requestID=null, $physiscian){
		$visitId=$this->id;
		$transactions=[];
		
		if ($requestID) {
		
		$currentTestTypeIds= Test::where('visit_id',$visitId)->lists('test_type_id');
		
		$testTypeIdsToDelete=array_diff($currentTestTypeIds,$tests);
		
		if (is_array($testTypeIdsToDelete) && !empty($testTypeIdsToDelete)) {

			$specimenIdsToDelete= Test::where('visit_id',$visitId)->whereIn('test_type_id',$testTypeIdsToDelete)->lists('specimen_id');

			$this->tests()->whereIn('test_type_id',$testTypeIdsToDelete)->delete();
			Specimen::destroy($specimenIdsToDelete);
		}

		$testTypeIdsToAdd=array_diff($tests,$currentTestTypeIds);
		//$activeTest = array();
		} else {
			$testTypeIdsToAdd=$tests;
			
		}

				

		if (is_array($testTypeIdsToAdd) && !empty($testTypeIdsToAdd)) {
            foreach ($testTypeIdsToAdd as $value) {
                $testTypeID = (int) $value;
                // Create Specimen - specimen_type_id, accepted_by, referred_from, referred_to
                $specimen = new Specimen;
                $specimen->specimen_type_id = TestType::find($testTypeID)->specimenTypes->lists('id')[0];
                //$specimen->accepted_by = Auth::user()->id;
                $transactions[]= $specimen->save();

                $test = new Test;
                $test->visit_id = $visitId;
                $test->test_type_id = $testTypeID;
                $test->specimen_id = $specimen->id;
                $test->test_status_id = Test::NOT_RECEIVED;
                //$test->time_created = date('Y-m-d H:i:s');
                //$test->created_by = Auth::user()->id;
                $test->requested_by = $physiscian;
                $transactions[] = $test->save();
				//$transactions[]=false;

               // $activeTest[] = $test->id;
            }
        }

        return $transactions;

	}

	public function destroyRequest() {

        $isItDeleted=[];
        $testAndSpecimenIdsToDelete= Test::where('visit_id',$this->id)->lists('specimen_id','id');
        //dd($testAndSpecimenIdsToDelete);
        //$TestIdsToDeleted=Test::where('visit_id',$this->id)->lists('id');
         \DB::statement("SET foreign_key_checks = 0");
         //dd($testAndSpecimenIdsToDelete,array_keys($testAndSpecimenIdsToDelete),array_flip($testAndSpecimenIdsToDelete));
        $isItDeleted[]=Specimen::destroy($testAndSpecimenIdsToDelete);
        $isItDeleted[]=Test::destroy(array_keys($testAndSpecimenIdsToDelete));
        
        $isItDeleted[]=$this->delete();
        \DB::statement("SET foreign_key_checks = 1");

        return $isItDeleted;

    }



}
