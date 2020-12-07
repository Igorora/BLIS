<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeepDeletedTable extends Migration {


	/* 	public $tables=['assigned_roles','audit_results','barcode_settings','blood_bank','cells','clinicians','control_measure_ranges','control_measures','control_results','control_tests','controls','countries','critical','critical_report','culture_worksheet','diseases','districts','drug_susceptibility','drugs','equip_config','external_dump','external_users','facilities','ii_quickcodes','instrument_testtypes','instruments','interfaced_equipment','inv_items','inv_supply','inv_usage','ips','lots','measure_ranges','measure_types','measures','micro_critical','migrations','organism_drugs','organisms','panel','patients','permission_role','permissions','provinces','referrals','rejection_reasons','report_diseases','requests','require_verifications','roles','sectors','specimen_statuses','specimen_types','specimens','suppliers','test_categories','test_phases','test_results','test_statuses','test_type_panels','test_types','tests','testtype_measures','testtype_organisms','testtype_specimentypes','tokens','users','villages','visits']; 
	
	$sqlView="SELECT `tableName`,`time_created`,`rowID`,GROUP_CONCAT(`colName` SEPARATOR ', ') as colName,GROUP_CONCAT(`colVal` SEPARATOR ', ') as colVal FROM `keep_deleted` GROUP BY `tableName`,`time_created`,`rowID`";
	
	*/
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tables=['assigned_roles','audit_results','barcode_settings','blood_bank','cells','clinicians','control_measure_ranges','control_measures','control_results','control_tests','controls','countries','critical','critical_report','culture_worksheet','diseases','districts','drug_susceptibility','drugs','equip_config','external_dump','external_users','facilities','ii_quickcodes','instrument_testtypes','instruments','interfaced_equipment','inv_items','inv_supply','inv_usage','ips','lots','measure_ranges','measure_types','measures','micro_critical','migrations','organism_drugs','organisms','panel','patients','permission_role','permissions','provinces','referrals','rejection_reasons','report_diseases','requests','require_verifications','roles','sectors','specimen_statuses','specimen_types','specimens','suppliers','test_categories','test_phases','test_results','test_statuses','test_type_panels','test_types','tests','testtype_measures','testtype_organisms','testtype_specimentypes','tokens','users','villages','visits'];
		//
		Schema::create('keep_deleted',function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('tableName', 200);
			$table->integer('rowID')->unsigned()->nullable();
			$table->string('colName', 200)->nullable();
			$table->string('colVal', 200)->nullable();
				//$table->string('delBy', 200)->nullable();
			$table->timestamp('time_created')->default(DB::raw('CURRENT_TIMESTAMP'));

		});
		
	
		
	 foreach($tables as $table){
			$values=Null;
			$oldID='OLD.id';
		
			$tableColumns=Schema::getColumnlisting($table);
					$table1="'".$table."'";
					$table2=$table.'_after_delete';
					if(!in_array('id',$tableColumns)){
						continue;
					}
			
			foreach($tableColumns as $key=> $columnName){
				$oldColumnValue='OLD.'.$columnName;
			
				$columnName="'".$columnName."'";
		 if($key==0){
				$values.="($table1,$oldID,$columnName,$oldColumnValue)";
			}else{
				$values.=",($table1,$oldID,$columnName,$oldColumnValue)";
			}
			}
			
					DB::unprepared(<<<TRG

					CREATE TRIGGER 
					$table2
					AFTER DELETE
						ON $table FOR EACH ROW
					BEGIN
						INSERT INTO keep_deleted (tableName, rowID, colName, colVal)
													VALUES $values;
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
			
			$table2=$table.'_after_delete';			 
			
		DB::unprepared(<<<TRG

		DROP TRIGGER IF EXISTS 
		$table2		
					
TRG
		
				);
	
	}
	}

}
