<?php

namespace Database\Seeders;

use App\Models\cart;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        cart::factory(100)->create();
    }
}
