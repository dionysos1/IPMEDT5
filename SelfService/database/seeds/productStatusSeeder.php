<?php

use Illuminate\Database\Seeder;

class productStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $ProductStatusList = [
    ['id' => 1,
    'rfid' => '042ab9a2',
    'price' => 2.18,
    'kg' => '0',
    'name' => 'Kersen'],
    ['id' => 2,
    'rfid' => '042cb9a2',
    'price' => 2.18,
    'kg' => '0',
    'name' => 'Aardbeien'],
    ['id' => 3,
    'rfid' => '0426bba2',
    'price' => 2.18,
    'kg' => '0',
    'name' => 'Paprika'],
    ['id' => 4,
    'rfid' => '041ebba2',
    'price' => 2.18,
    'kg' => '0',
    'name' => 'Snack Tomaat'],
    ['id' => 5,
    'rfid' => '0415bba2',
    'price' => 2.18,
    'kg' => '0',
    'name' => 'Druiven'],
    ];


      foreach ($ProductStatusList as $productStatus) {
     DB::table("product_status")->insert($productStatus);
      }

    }
}
