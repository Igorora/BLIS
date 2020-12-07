<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeepDeletedTable extends Migration {


	/* 	public $tables=['assigned_roles','audit_results','barcode_settings','blood_bank','cells','clinicians','control_measure_ranges','control_measures','control_results','control_tests','controls','countries','critical','critical_report','culture_worksheet','diseases','districts','drug_susceptibility','drugs','equip_config','external_dump','external_users','facilities','ii_quickcodes','instrument_testtypes','instruments','interfaced_equipment','inv_items','inv_supply','inv_usage','ips','lots','measure_ranges','measure_types','measures','micro_critical','migrations','organism_drugs','organisms','panel','patients','permission_role','permissions','provinces','referrals','rejection_reasons','report_diseases','requests','require_verifications','roles','sectors','specimen_statuses','specimen_types','specimens','suppliers','test_categories','test_phases','test_results','test_statuses','test_type_panels','test_types','tests','testtype_measures','testtype_organisms','testtype_specimentypes','tokens','users','villages','visits']; */
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tables=['assigned_roles','audit_results','barcode_settings','blood_bank','cells','clinicians','control_measure_ranges','control_measures','control_results','control_tests','controls','countries','critical','critical_report','culture_worksheet','diseases','districts','drug_susceptibility','drugs','equip_config','external_dump','external_users','facilities','ii_quickcodes','instrument_testtypes','instruments','interfaced_equipment','inv_items','inv_supply','inv_usage','ips','lots','measure_types','measures','micro_critical','migrations','organism_drugs','organisms','panel','patients','permission_role','permissions','provinces','referrals','rejection_reasons','report_diseases','requests','require_verifications','roles','sectors','specimen_statuses','specimen_types','specimens','suppliers','test_categories','test_phases','test_results','test_statuses','test_type_panels','test_types','tests','testtype_measures','testtype_organisms','testtype_specimentypes','tokens','users','villages','visits'];
		
		Schema::create('keep_deleted',function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('tableName', 200);
			$table->integer('rowID')->unsigned()->nullable();
			$table->string('colName', 2000)->nullable();
			$table->string('colVal', 2000)->nullable();
				//$table->string('delBy', 200)->nullable();
			$table->timestamp('time_deleted')->default(DB::raw('CURRENT_TIMESTAMP'));

		});
		
	
		
	 foreach($tables as $table){

			$oldID='OLD.id';
			//$old='OLD.';
			$tableColumns=Schema::getColumnlisting($table);
		
			$oldColumns1=implode(",",$tableColumns);
			$oldColumns="'".$oldColumns1."'";
			$oldValues="IFNULL(".implode(",'zero'),IFNULL(",$tableColumns).",'zero')";
			$table1="'".$table."'";
			$table2=$table.'_before_delete';
			if(!in_array('id',$tableColumns)){
				continue;
			}
			
			 
			
		DB::unprepared(<<<TRG

		CREATE TRIGGER 
		$table2
		BEFORE DELETE
			ON $table FOR EACH ROW
		BEGIN
			
			
			INSERT INTO keep_deleted (tableName, rowID, colName, colVal)
							SELECT $table1,$oldID,$oldColumns,CONCAT_WS('#',$oldValues) FROM `$table`
							WHERE id=$oldID;
							
			
			END;	

					
TRG
		
				);
		

	
	}
}
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('keep_deleted');
		
		foreach($tables as $table){
			
			$table2=$table.'_before_delete';			 
			
		DB::unprepared(<<<TRG

		DROP TRIGGER IF EXISTS 
		$table2		
					
TRG
		
				);
	
	}
	}

}
