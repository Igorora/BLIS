<?php


class TransmittedResult extends Eloquent
{

	protected $table = 'transmitted_results';
	public $timestamps = false;

	/**
	 * Test relationship
	 *
	 */

	public function test()
	{
		return $this->belongsTo('Test');
	}

	/**
	 * Test relationship
	 *
	 */
	 
	public function transmittedBy()
	{
		return $this->belongsTo('User','transmitted_by','id');
	}

	
}