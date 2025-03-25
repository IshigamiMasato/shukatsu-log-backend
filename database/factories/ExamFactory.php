<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apply_id'   => Apply::factory(),
            'exam_date'  => date('Y-m-d'),
            'content'    => $this->faker->paragraph,
            'memo'       => $this->faker->optional()->paragraph,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
