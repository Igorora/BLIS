<?php

class District extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'districts';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	public function sectors()
	{
		return $this->hasMany('Sector');
	}
         public function province()
	{
		return $this->belongsTo('Provinces');
	}


}
