<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTestTypesColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		Schema::table('test_types', function(Blueprint $table)
		{
						$table->renameColumn('tarif_A','tarif_A');
						$table->renameColumn('tarif_C','tarif_C');
						$table->renameColumn('tarif_b','tarif_B');
						$table->renameColumn('tarif_d','tarif_D');
						$table->renameColumn('tarif_e','tarif_E');
			//
		});
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
			//
		});
	}

}
