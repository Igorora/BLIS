<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToPatientTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			$table->string('country', 100)->nullable();
			$table->string('province', 100)->nullable();
			$table->string('district', 100)->nullable();
			$table->string('sector', 100)->nullable();
			$table->string('cell', 100)->nullable();
			$table->string('village', 100)->nullable();
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
					Schema::table('patients', function(Blueprint $table)
									{
										$table->dropColumn('country');
										$table->dropColumn('province');
										$table->dropColumn('district');
										$table->dropColumn('sector');
										$table->dropColumn('cell');
										$table->dropColumn('village');
									});
			
		//
	}

}
