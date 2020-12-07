<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTariffToTestTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('test_types', function(Blueprint $table)
		{
			$table->integer('tarif_A')->unsigned()->nullable();
			$table->integer('tarif_C')->unsigned()->nullable();
			$table->integer('tarif_b')->unsigned()->nullable();
			$table->integer('tarif_d')->unsigned()->nullable();
			$table->integer('tarif_e')->unsigned()->nullable();
			//
		});
		//
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('test_types', function(Blueprint $table)
		{
			$table->dropColumn('MUSA');
			$table->dropColumn('RSSB');
			$table->dropColumn('MMI, MS_UNR and Other Institutes');
			$table->dropColumn('Commercial and pivate insurances');
			$table->dropColumn('PRIVATE');
		});
		//
	}

}
