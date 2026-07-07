<?php

namespace Database\Factories;

use App\Models\Church;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            "church_id" => Church::factory(),
            "name" => fake()->name(),
            "phone" => fake()->phoneNumber(),
            "email" => fake()->email(),
            "birth_date" => fake()->date("Y-m-d", "2000-01-01"),
            "gender" => fake()->randomElement(["Masculino", "Feminino"]),
            "status" => "Ativo",
            "active" => true,
        ];
    }
}
