<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Interview::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apply_id'         => Apply::factory(),
            'interview_date'   => date('Y-m-d'),
            'interviewer_info' => $this->faker->optional()->paragraph,
            'memo'             => $this->faker->optional()->paragraph,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
    }
}
