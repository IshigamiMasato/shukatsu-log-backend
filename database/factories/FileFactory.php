<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'document_id' => Document::factory(),
            'name'        => $this->faker->word . '.' . $this->faker->fileExtension,
            'path'        => '/documents/' . $this->faker->word . '.' . $this->faker->fileExtension,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
    }
}
