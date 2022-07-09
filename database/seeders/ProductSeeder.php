<?php

namespace Database\Seeders;

use App\Models\product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    protected static function newFactory()
    {
        return ProductFactory::class;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        product::factory(100)->create();
    }
}
