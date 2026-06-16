<?php

namespace Database\Seeders;

use App\Models\Church;
use Illuminate\Database\Seeder;

class ChurchSeeder extends Seeder
{
    public function run(): void
    {
        Church::firstOrCreate(
            ["cnpj" => "00.000.000/0001-91"],
            [
                "name" => "Igreja Batista Exemplo",
                "phone" => "(11) 99999-9999",
                "email" => "contato@igrejaexemplo.com.br",
                "pastor_name" => "Pastor João Silva",
                "address" => "Rua da Igreja, 123",
                "city" => "São Paulo",
                "state" => "SP",
                "active" => true,
            ]
        );
    }
}
