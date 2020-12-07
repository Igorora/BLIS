<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegisteredByToVisitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('visits', function(Blueprint $table)
		{
			$table->integer('registered_by')->unsigned()->default(0);
			//
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
		Schema::table('visits', function(Blueprint $table)
		{
			$table->dropColumn('registered_by');
		});
	}

}
