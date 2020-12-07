<?php

class Sector extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sectors';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	public function cells()
	{
		return $this->hasMany('Cell');
	}
         public function district()
	{
		return $this->belongsTo('Province');
	}


}
