<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultTransmissionRegisterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('transmitted_results', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('test_id')->unsigned()->default(0);
			$table->string('transmitted_to', 100);
			$table->string('designation', 100);
			$table->integer('transmitted_by')->unsigned()->default(0);			
			$table->timestamp('time_transmitted')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			

			$table->index('transmitted_by');
			$table->foreign('test_id')->references('id')->on('tests');

			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::dropIfExists('transmitted_results');
		
	}

}
