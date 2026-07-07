<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get("/dashboard")->assertRedirect("/login");
    }

    public function test_master_can_login(): void
    {
        $user = User::factory()->create();
        $user->assignRole("Master");

        Volt::test("pages.auth.login")
            ->set("form.email", $user->email)
            ->set("form.password", "password")
            ->call("login")
            ->assertHasNoErrors()
            ->assertRedirect(route("dashboard", absolute: false));

        $this->assertAuthenticated();
    }

    public function test_admin_can_login(): void
    {
        $church = \App\Models\Church::factory()->create();
        $user = User::factory()->create(["church_id" => $church->id]);
        $user->assignRole("Admin");

        Volt::test("pages.auth.login")
            ->set("form.email", $user->email)
            ->set("form.password", "password")
            ->call("login")
            ->assertHasNoErrors()
            ->assertRedirect(route("dashboard", absolute: false));

        $this->assertAuthenticated();
    }

    // ponytail: no middleware blocks active=false — documents the gap, fix in security sprint
    public function test_inactive_user_is_not_blocked(): void
    {
        $user = User::factory()->create(["active" => false]);
        $user->assignRole("Master");

        $this->actingAs($user)
            ->get("/dashboard")
            ->assertOk();
    }
}
