<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    => User::factory(),
            'title'      => $this->faker->word,
            'type'       => $this->faker->numberBetween(1, 6),
            'start_at'   => date('Y-m-d H:i:s'),
            'end_at'     => date('Y-m-d H:i:s'),
            'memo'       => $this->faker->optional()->paragraph,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
