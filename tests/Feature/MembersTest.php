<?php

namespace Tests\Feature;

use App\Models\Church;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class MembersTest extends TestCase
{
    use RefreshDatabase;

    private Church $church;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->church = Church::factory()->create();
        $this->admin = User::factory()->create(["church_id" => $this->church->id]);
        $this->admin->assignRole("Admin");
    }

    public function test_index_shows_members(): void
    {
        Member::factory()->count(3)->create(["church_id" => $this->church->id]);

        $this->actingAs($this->admin)->get("/members")->assertOk();
    }

    public function test_can_create_member(): void
    {
        $this->actingAs($this->admin);

        Volt::test("members.index")
            ->set("name", "Novo Membro")
            ->set("gender", "Masculino")
            ->set("status", "Ativo")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("members", [
            "name" => "Novo Membro",
            "church_id" => $this->church->id,
        ]);
    }

    public function test_can_update_member(): void
    {
        $member = Member::factory()->create([
            "church_id" => $this->church->id,
            "name" => "Original",
        ]);

        $this->actingAs($this->admin);

        Volt::test("members.index")
            ->call("edit", $member->id)
            ->set("name", "Atualizado")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("members", ["id" => $member->id, "name" => "Atualizado"]);
    }

    public function test_can_toggle_member_active(): void
    {
        $member = Member::factory()->create([
            "church_id" => $this->church->id,
            "active" => true,
        ]);

        $this->actingAs($this->admin);

        Volt::test("members.index")
            ->call("toggleActive", $member->id);

        $this->assertDatabaseHas("members", ["id" => $member->id, "active" => false]);
    }

    // ponytail: no delete action exists, toggleActive is the only status toggle
}
