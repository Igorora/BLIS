<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartedByToTestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('tests', function(Blueprint $table)
		{
			$table->integer('started_by')->unsigned()->default(0);
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
		Schema::table('tests', function(Blueprint $table)
		{
			$table->dropColumn('started_by');
		});
	}

}
