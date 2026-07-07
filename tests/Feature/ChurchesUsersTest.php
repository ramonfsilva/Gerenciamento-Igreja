<?php

namespace Tests\Feature;

use App\Models\Church;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Tests\TestCase;

class ChurchesUsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_master_can_access_churches_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole("Master");
        $this->actingAs($user)
            ->get("/churches")
            ->assertOk();
    }

    public function test_master_can_access_users_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole("Master");
        $this->actingAs($user)
            ->get("/users")
            ->assertOk();
    }

    public function test_admin_can_access_users_page(): void
    {
        $church = Church::factory()->create();
        $user = User::factory()->create(["church_id" => $church->id]);
        $user->assignRole("Admin");
        $this->actingAs($user)
            ->get("/users")
            ->assertOk();
    }

    public function test_dashboard_renders_for_master(): void
    {
        $user = User::factory()->create();
        $user->assignRole("Master");
        $this->actingAs($user)
            ->get("/dashboard")
            ->assertOk()
            ->assertSee("Igrejas");
    }

    public function test_dashboard_renders_for_admin(): void
    {
        $church = Church::factory()->create();
        $user = User::factory()->create(["church_id" => $church->id]);
        $user->assignRole("Admin");
        $this->actingAs($user)
            ->get("/dashboard")
            ->assertOk()
            ->assertSee("Igreja");
    }
}
