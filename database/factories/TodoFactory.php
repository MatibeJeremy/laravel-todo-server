<?php

namespace Database\Factories;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'todo' => $this->faker->text(),
            'user_id' => 1,
            'created_at' => Carbon::today()->subDays(rand(0, 30)),
        ];
    }
}
