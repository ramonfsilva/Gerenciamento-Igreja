<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            "church.view", "church.create", "church.edit", "church.delete",
            "user.view", "user.create", "user.edit", "user.delete",
            "member.view", "member.create", "member.edit", "member.delete",
            "financial.view",
            "income.view", "income.create", "income.edit", "income.delete",
            "expense.view", "expense.create", "expense.edit", "expense.delete",
            "category.view", "category.create", "category.edit", "category.delete",
            "report.view", "report.member", "report.financial",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        $master = Role::firstOrCreate(["name" => "Master"]);
        $admin = Role::firstOrCreate(["name" => "Admin"]);
        $tesoureiro = Role::firstOrCreate(["name" => "Tesoureiro"]);
        $secretaria = Role::firstOrCreate(["name" => "Secretaria"]);

        $master->syncPermissions(Permission::all());

        $admin->syncPermissions([
            "church.view",
            "user.view", "user.create", "user.edit", "user.delete",
            "member.view", "member.create", "member.edit", "member.delete",
            "financial.view",
            "income.view", "income.create", "income.edit", "income.delete",
            "expense.view", "expense.create", "expense.edit", "expense.delete",
            "category.view", "category.create", "category.edit", "category.delete",
            "report.view", "report.member", "report.financial",
        ]);

        $tesoureiro->syncPermissions([
            "financial.view",
            "income.view", "income.create", "income.edit", "income.delete",
            "expense.view", "expense.create", "expense.edit", "expense.delete",
            "category.view", "category.create", "category.edit", "category.delete",
            "report.view", "report.financial",
        ]);

        $secretaria->syncPermissions([
            "member.view", "member.create", "member.edit",
            "report.view", "report.member",
        ]);

        $incomeCategories = ["Dízimo", "Oferta", "Doação", "Campanha", "Outros"];
        $expenseCategories = ["Água", "Energia", "Internet", "Aluguel", "Manutenção", "Ação Social", "Outros"];

        foreach ($incomeCategories as $name) {
            Category::firstOrCreate(["name" => $name, "type" => "income"], ["church_id" => null]);
        }
        foreach ($expenseCategories as $name) {
            Category::firstOrCreate(["name" => $name, "type" => "expense"], ["church_id" => null]);
        }
    }
}
