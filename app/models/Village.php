<?php

class Village extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'villages';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	 public function cell()
	{
		return $this->belongsTo('Cell');
	}


}
