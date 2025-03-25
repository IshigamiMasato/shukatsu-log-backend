<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apply_id'   => Apply::factory(),
            'offer_date' => date('Y-m-d'),
            'salary'     => $this->faker->optional()->numberBetween(1, 10000000),
            'condition'  => $this->faker->optional()->paragraph,
            'memo'       => $this->faker->optional()->paragraph,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
