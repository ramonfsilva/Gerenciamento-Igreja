<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $church = Church::first();

        $master = User::firstOrCreate(
            ["email" => "admin@system.local"],
            [
                "name" => "Administrador Master",
                "password" => bcrypt("12345678"),
                "church_id" => null,
                "active" => true,
            ]
        );
        $master->assignRole("Master");

        if ($church) {
            $admin = User::firstOrCreate(
                ["email" => "admin@igreja.local"],
                [
                    "name" => "Administrador da Igreja",
                    "password" => bcrypt("12345678"),
                    "church_id" => $church->id,
                    "active" => true,
                ]
            );
            $admin->assignRole("Admin");
        }
    }
}
