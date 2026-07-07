<?php

namespace Database\Factories;

use App\Models\Church;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChurchFactory extends Factory
{
    protected $model = Church::class;

    public function definition(): array
    {
        return [
            "name" => fake()->company() . " Church",
            "cnpj" => fake()->unique()->numerify("##.###.###/####-##"),
            "phone" => fake()->phoneNumber(),
            "email" => fake()->companyEmail(),
            "pastor_name" => fake()->name(),
            "address" => fake()->address(),
            "city" => fake()->city(),
            "state" => fake()->stateAbbr(),
            "active" => true,
        ];
    }
}
