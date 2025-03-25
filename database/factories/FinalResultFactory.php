<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\FinalResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinalResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinalResult::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apply_id'   => Apply::factory(),
            'status'     => $this->faker->numberBetween(1, 3),
            'memo'       => $this->faker->optional()->paragraph,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
