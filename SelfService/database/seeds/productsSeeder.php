<?php

use Illuminate\Database\Seeder;

class productsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $ProductList = [
    ['name' => 'Kersen',
    'rfid' => '042ab9a2',
    'price' => 2.18,
    'kcal' => 18,
    'info' => 'Verse Heerlijke Kersen'],
    ['name' => 'Aardbeien',
    'rfid' => '042cb9a2',
    'price' => 3.99,
    'kcal' => 9,
    'info' => 'Heerlijke Aardbeien'],
    ['name' => 'Paprika',
    'rfid' => '0426bba2',
    'price' => 3.69,
    'kcal' => 31,
    'info' => 'Paprika chill lekker'],
    ['name' => 'Snack Tomaat',
    'rfid' => '0415bba2',
    'price' => 2.00,
    'kcal' => 2,
    'info' => 'lekker snacktomaten'],
    ['name' => 'Druiven',
    'rfid' => '041ebba2',
    'price' => 5.00,
    'kcal' => 4,
    'info' => 'Verse Druiven'],
    ];

    foreach ($ProductList as $product) {
     DB::table("products")->insert($product);
   }
    }
}
