<?php

class Country extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'countries';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	public function provinces()
	{
		return $this->hasMany('Province','country_id','country_id');
	}


}
