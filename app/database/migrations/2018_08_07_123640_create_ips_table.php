<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ips',function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('ip', 15)->nullable();
			$table->integer('test_category_id')->unsigned();
			$table->foreign('test_category_id')->references('id')->on('test_categories');

		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('ips');
		
	}

}
