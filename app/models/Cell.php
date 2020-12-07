<?php

class Cell extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cells';

	//public $timestamps = false;

	

	/**
	 * Country relationship
	 */
	public function villages()
	{
		return $this->hasMany('Village');
	}
         public function sector()
	{
		return $this->belongsTo('Sector');
	}


}
