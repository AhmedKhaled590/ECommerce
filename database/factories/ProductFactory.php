<?php

namespace Database\Factories;

use App\Models\category;
use App\Models\product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => $this->faker->randomFloat(2, 0, 100),
            'category_id' => category::factory(1)->create()[0]->id,
            'images' => $this->faker->imageUrl(300, 300),
            'currency' => $this->faker->currencyCode,
            'quantity_available' => $this->faker->numberBetween(1, 200),
            'review' => $this->faker->randomFloat(2, 0, 5),
        ];

    }
}
