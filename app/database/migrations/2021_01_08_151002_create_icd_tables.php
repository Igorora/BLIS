<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcdTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('signs', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		Schema::create('medicines', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		Schema::create('icd10_diags', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		Schema::create('symptoms', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::dropIfExists('symptoms');
            Schema::dropIfExists('icd10_diags');
            Schema::dropIfExists('medicines');
            Schema::dropIfExists('signs');
	}

}
