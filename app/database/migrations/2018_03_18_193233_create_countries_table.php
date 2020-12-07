<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('countries', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('country_id', 2)->index()->nullable();
			$table->string('name', 100)->nullable();
			$table->timestamps();

                        });
		Schema::create('provinces', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name', 100)->nullable();
			$table->string('country_id')->index();
			$table->timestamps();
//                                 $table->foreign('country_id')->references('country_id')->on('countries');
		});
                Schema::create('districts', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name', 100)->nullable();
			$table->integer('province_id')->index();
			$table->timestamps();
//                                 $table->foreign('province_id')->references('id')->on('provinces');
		});
                Schema::create('sectors', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name', 100)->nullable();
			$table->integer('district_id')->index();
			$table->timestamps();
//                                 $table->foreign('district_id')->references('id')->on('districts');
		});
                Schema::create('cells', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name', 100)->nullable();
			$table->integer('sector_id')->index();
			$table->timestamps();
//                                 $table->foreign('sector_id')->references('id')->on('sectors');
		});
                Schema::create('villages', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name', 100)->nullable();
			$table->integer('cell_id')->index();
			$table->timestamps();
//                                 $table->foreign('cell_id')->references('id')->on('cells');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::dropIfExists('countries');
            Schema::dropIfExists('provinces');	
            Schema::dropIfExists('districts');	
            Schema::dropIfExists('sectors');	
            Schema::dropIfExists('cells');	
            Schema::dropIfExists('villages');	

	}

}
