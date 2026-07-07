<?php

namespace Tests\Feature;

use App\Models\Church;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $master;
    private User $admin;
    private Church $church;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->church = Church::factory()->create();
        $this->master = User::factory()->create();
        $this->master->assignRole("Master");
        $this->admin = User::factory()->create(["church_id" => $this->church->id]);
        $this->admin->assignRole("Admin");
    }

    public function test_master_can_access_churches(): void
    {
        $this->actingAs($this->master)->get("/churches")->assertOk();
    }

    public function test_admin_can_access_churches(): void
    {
        // Admin has church.view permission
        $this->actingAs($this->admin)->get("/churches")->assertOk();
    }

    public function test_master_can_access_users(): void
    {
        $this->actingAs($this->master)->get("/users")->assertOk();
    }

    public function test_admin_can_access_users(): void
    {
        $this->actingAs($this->admin)->get("/users")->assertOk();
    }

    public function test_secretaria_can_access_members(): void
    {
        $secretaria = User::factory()->create(["church_id" => $this->church->id]);
        $secretaria->assignRole("Secretaria");

        $this->actingAs($secretaria)->get("/members")->assertOk();
    }

    public function test_tesoureiro_cannot_access_members(): void
    {
        $tesoureiro = User::factory()->create(["church_id" => $this->church->id]);
        $tesoureiro->assignRole("Tesoureiro");

        $this->actingAs($tesoureiro)->get("/members")->assertForbidden();
    }
}
