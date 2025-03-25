<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Apply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => User::factory(),
            'company_id'  => Company::factory(),
            'status'      => $this->faker->numberBetween(0, 5),
            'occupation'  => $this->faker->word,
            'apply_route' => $this->faker->optional()->word,
            'memo'        => $this->faker->optional()->paragraph,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
    }
}
