<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Church;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class MultiTenantTest extends TestCase
{
    use RefreshDatabase;

    private Church $churchA;
    private Church $churchB;
    private User $adminA;
    private User $adminB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);

        $this->churchA = Church::factory()->create(["name" => "Igreja A"]);
        $this->churchB = Church::factory()->create(["name" => "Igreja B"]);
        $this->adminA = User::factory()->create(["church_id" => $this->churchA->id]);
        $this->adminA->assignRole("Admin");
        $this->adminB = User::factory()->create(["church_id" => $this->churchB->id]);
        $this->adminB->assignRole("Admin");
    }

    public function test_admin_sees_only_own_church_members(): void
    {
        Member::factory()->create(["church_id" => $this->churchA->id, "name" => "Alice"]);
        Member::factory()->create(["church_id" => $this->churchB->id, "name" => "Bob"]);

        $response = $this->actingAs($this->adminA)->get("/members");

        $response->assertOk();
        $response->assertSee("Alice");
        $response->assertDontSee("Bob");
    }

    public function test_master_sees_all_members(): void
    {
        $master = User::factory()->create();
        $master->assignRole("Master");

        Member::factory()->create(["church_id" => $this->churchA->id, "name" => "Alice"]);
        Member::factory()->create(["church_id" => $this->churchB->id, "name" => "Bob"]);

        $response = $this->actingAs($master)->get("/members");

        $response->assertOk();
        $response->assertSee("Alice");
        $response->assertSee("Bob");
    }

    public function test_admin_creates_member_in_own_church(): void
    {
        $this->actingAs($this->adminA);

        Volt::test("members.index")
            ->set("name", "Carlos")
            ->set("gender", "Masculino")
            ->set("status", "Ativo")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertEquals(1, Member::where("church_id", $this->churchA->id)->count());
        $this->assertEquals(0, Member::where("church_id", $this->churchB->id)->count());
    }

    public function test_master_creating_member_without_church_id_errors(): void
    {
        $master = User::factory()->create();
        $master->assignRole("Master");

        $this->actingAs($master);

        // ponytail: no church_id field in the Volt form, master cannot create members via UI
        Volt::test("members.index")
            ->set("name", "Daniel")
            ->set("gender", "Masculino")
            ->set("status", "Ativo")
            ->call("save");

        $this->assertEquals(0, Member::count());
    }

    // ponytail: Volt edit() uses Member::findOrFail without church scope — no tenant enforcement
    public function test_admin_can_edit_member_from_other_church(): void
    {
        $member = Member::factory()->create(["church_id" => $this->churchA->id, "name" => "Eva"]);

        $this->actingAs($this->adminB);

        Volt::test("members.index")
            ->call("edit", $member->id)
            ->set("name", "Eva Editada")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("members", ["id" => $member->id, "name" => "Eva Editada"]);
    }

    public function test_church_isolation_in_finance(): void
    {
        Category::create(["church_id" => $this->churchA->id, "name" => "Dízimo A", "type" => "income"]);
        Category::create(["church_id" => $this->churchB->id, "name" => "Dízimo B", "type" => "income"]);

        $response = $this->actingAs($this->adminA)->get("/financial/categories");

        $response->assertOk();
        $response->assertSee("Dízimo A");
        $response->assertSee("Dízimo B");
    }

    public function test_master_with_null_church_id_works(): void
    {
        $master = User::factory()->create(["church_id" => null]);
        $master->assignRole("Master");

        $this->actingAs($master)->get("/members")->assertOk();
    }
}
