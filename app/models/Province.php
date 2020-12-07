<?php

    class Province extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'provinces';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	public function districts()
	{
		return $this->hasMany('District');
	}
        public function country()
	{
		return $this->belongsTo('Country','country_id','country_id');
	}


}
