<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'              => User::factory(),
            'name'                 => $this->faker->company,
            'url'                  => $this->faker->optional()->url,
            'president'            => $this->faker->optional()->name,
            'address'              => $this->faker->optional()->address,
            'establish_date'       => $this->faker->optional()->date,
            'employee_number'      => $this->faker->optional()->numberBetween(1, 10000),
            'listing_class'        => $this->faker->optional()->word,
            'business_description' => $this->faker->optional()->paragraph,
            'benefit'              => $this->faker->optional()->text,
            'memo'                 => $this->faker->optional()->paragraph,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];
    }
}
