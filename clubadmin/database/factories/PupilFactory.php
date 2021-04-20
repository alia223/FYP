<?php

namespace Database\Factories;
use App\Models\Pupil;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PupilFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pupil::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date,
            'food_arrangement' => Str::random(10)
        ];
    }
}
