<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntervalToMeasureRangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('measure_ranges', function(Blueprint $table)
		{
			$table->integer('interval')->default(1);
			//
		});
		//
		//
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('measure_ranges', function(Blueprint $table)
		{
			$table->dropColumn('interval');
		});
		//
	}

}
