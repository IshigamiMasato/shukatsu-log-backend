<?php

namespace Database\Factories;

use App\Models\Apply;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'apply_id'        => Apply::factory(),
            'submission_date' => date('Y-m-d'),
            'memo'            => $this->faker->optional()->paragraph,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
    }
}
