<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            "church.view",
            "church.create",
            "church.edit",
            "church.delete",
            "user.view",
            "user.create",
            "user.edit",
            "user.delete",
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
            "user.view",
            "user.create",
            "user.edit",
            "user.delete",
            "church.view",
        ]);

        $tesoureiro->syncPermissions([]);
        $secretaria->syncPermissions([]);
    }
}
