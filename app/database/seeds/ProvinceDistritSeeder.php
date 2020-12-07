<?php
class ProvinceDistritSeeder extends DatabaseSeeder
{
    public function run()
    {
		/* `chub_lab`.`provinces` */
$provinces = array(
  array('id' => '1','name' => 'Kigali Town/Umujyi wa Kigali','country_id' => 'RW','created_at' => '2018-03-22 14:02:27','updated_at' => '2018-03-22 14:02:27'),
  array('id' => '2','name' => 'South/Amajyepfo','country_id' => 'RW','created_at' => '2018-03-22 14:02:27','updated_at' => '2018-03-22 14:02:27'),
  array('id' => '3','name' => 'West/Iburengerazuba','country_id' => 'RW','created_at' => '2018-03-22 14:02:27','updated_at' => '2018-03-22 14:02:27'),
  array('id' => '4','name' => 'North/Amajyaruguru','country_id' => 'RW','created_at' => '2018-03-22 14:02:27','updated_at' => '2018-03-22 14:02:27'),
  array('id' => '5','name' => 'East/Iburasirazuba','country_id' => 'RW','created_at' => '2018-03-22 14:02:27','updated_at' => '2018-03-22 14:02:27')
);

foreach ($provinces as $province)
        {
            DB::table('provinces')->insert($province);
        }
        $this->command->info("Provinces table seeded");


/* `chub_lab`.`districts` */
$districts = array(
  array('id' => '11','name' => 'Nyarugenge','province_id' => '1','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '12','name' => 'Gasabo','province_id' => '1','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '13','name' => 'Kicukiro','province_id' => '1','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '21','name' => 'Nyanza','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '22','name' => 'Gisagara','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '23','name' => 'Nyaruguru','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '24','name' => 'Huye','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '25','name' => 'Nyamagabe','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '26','name' => 'Ruhango','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '27','name' => 'Muhanga','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '28','name' => 'Kamonyi','province_id' => '2','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '31','name' => 'Karongi','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '32','name' => 'Rutsiro','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '33','name' => 'Rubavu','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '34','name' => 'Nyabihu','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '35','name' => 'Ngororero','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '36','name' => 'Rusizi','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '37','name' => 'Nyamasheke','province_id' => '3','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '41','name' => 'Rulindo','province_id' => '4','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '42','name' => 'Gakenke','province_id' => '4','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '43','name' => 'Musanze','province_id' => '4','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '44','name' => 'Burera','province_id' => '4','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '45','name' => 'Gicumbi','province_id' => '4','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '51','name' => 'Rwamagana','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '52','name' => 'Nyagatare','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '53','name' => 'Gatsibo','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '54','name' => 'Kayonza','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '55','name' => 'Kirehe','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '56','name' => 'Ngoma','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44'),
  array('id' => '57','name' => 'Bugesera','province_id' => '5','created_at' => '2018-03-22 14:02:44','updated_at' => '2018-03-22 14:02:44')
);

foreach ($districts as $district)
        {
            DB::table('districts')->insert($district);
        }
        $this->command->info("Districts table seeded");

	}
}
