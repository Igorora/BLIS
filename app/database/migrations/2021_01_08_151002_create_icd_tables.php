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
		DB::statement('ALTER TABLE signs ADD FULLTEXT search_sign (id)');
		Schema::create('medicines', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		DB::statement('ALTER TABLE medicines ADD FULLTEXT search_med (id)');
		Schema::create('icd10_diags', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		DB::statement('ALTER TABLE icd10_diags ADD FULLTEXT search_diag (id)');
		Schema::create('symptoms', function(Blueprint $table)
		{
			$table->string('id',250)->unsigned();
			$table->string('text', 250)->index();
			$table->primary('id');

		});
		DB::statement('ALTER TABLE symptoms ADD FULLTEXT search_symp (id)');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('symptoms', function($table) {
			$table->dropIndex('search_symp');
		});
		Schema::table('icd10_diags', function($table) {
			$table->dropIndex('search_diag');
		});
		Schema::table('medicines', function($table) {
			$table->dropIndex('search_med');
		});
		Schema::table('signs', function($table) {
			$table->dropIndex('search_sign');
		});
		Schema::dropIfExists('symptoms');
		Schema::dropIfExists('icd10_diags');
		Schema::dropIfExists('medicines');
		Schema::dropIfExists('signs');
	}

}
