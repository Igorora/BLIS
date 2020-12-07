<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		DB::transaction(function()
			{
			// $this->call('KBLISSeeder');
			//$this->call('TestDataSeeder');
			//$this->call('ConfigSettingSeeder');
			//$this->call('CountrySeeder');
			//$this->call('ProvinceDistritSeeder');
			//$this->call('SectorSeeder');
			//$this->call('CellSeeder');
			//$this->call('VillageSeeder');
			//$this->call('PermissionsUpdater');
			// $this->call('EditVerifiedSeeder');
			});
		
	}
}