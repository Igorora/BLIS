<?php


class Ip extends Eloquent
{

	protected $table = 'ips';

	/**
	 * Test category (Lab section) values relationship
	 *
	 */

	public function testCategory()
	{
		return $this->belongsTo('TestCategory');
	}

	/**
	 * Pending test by lab section
	 *
	 */

	public static function TestCountByStatus($labSectionIp,$testStatus=NULL)
	{
	  	$currentTime=date('H:i:s');
	  	$today = date('Y-m-d');
	  	$yesterday = date_sub(new DateTime($today), date_interval_create_from_date_string('1 day'))->format('Y-m-d');
		
		$testCount=DB::table('tests')						
						->select(DB::raw('Count(DISTINCT tests.id) AS test_count'))
						->join('test_types', 'tests.test_type_id', '=', 'test_types.id')
						->join('specimens', 'tests.specimen_id', '=', 'specimens.id')
						->join('test_categories', 'test_types.test_category_id', '=', 'test_categories.id')
						->join('ips', 'ips.test_category_id', '=', 'test_categories.id')
						->where('ips.ip', '=', $labSectionIp);
		if ($testStatus) {
			$testCount->where('tests.test_status_id', '=', $testStatus);
			
		} else {
			$testCount->whereRaw('Unix_Timestamp() - Unix_Timestamp(specimens.time_accepted) BETWEEN test_types.targetTAT*60*60/2 AND test_types.targetTAT*60*60 AND tests.test_status_id NOT IN (4,5)');
		}
	    if ($currentTime > '07:30:00' && $currentTime < '23:59:59') {

	    	$testCount ->whereBetween('tests.time_created',  [$today. ' 07:30:00', $today. ' 23:59:59']);
	    	
	    	
	    } else {
	    	$testCount ->whereBetween('tests.time_created',  [$yesterday.' 07:30:00', $today. ' 07:30:00']);
	    	
	    }
						 
		return $testCount->count();
	}



}