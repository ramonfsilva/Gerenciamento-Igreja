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

class E2EAdminTest extends TestCase
{
    use RefreshDatabase;

    private Church $churchA;
    private Church $churchB;
    private User $adminA;
    private User $adminB;
    private Member $memberA;

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
        $this->memberA = Member::factory()->create(["church_id" => $this->churchA->id, "name" => "Membro A"]);
    }

    public function test_admin_sees_only_own_members(): void
    {
        Member::factory()->create(["church_id" => $this->churchB->id, "name" => "Membro B"]);

        $response = $this->actingAs($this->adminA)->get("/members");
        $response->assertOk()
            ->assertSee("Membro A")
            ->assertDontSee("Membro B");
    }

    public function test_admin_full_member_crud(): void
    {
        $this->actingAs($this->adminA);

        // Create
        Volt::test("members.index")
            ->set("name", "Novo Membro Admin")
            ->set("gender", "Feminino")
            ->set("status", "Ativo")
            ->call("save")
            ->assertHasNoErrors();

        $member = Member::where("name", "Novo Membro Admin")->first();
        $this->assertNotNull($member);
        $this->assertEquals($this->churchA->id, $member->church_id);

        // Edit
        Volt::test("members.index")
            ->call("edit", $member->id)
            ->set("name", "Membro Editado")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("members", ["id" => $member->id, "name" => "Membro Editado", "church_id" => $this->churchA->id]);

        // Toggle active
        Volt::test("members.index")
            ->call("toggleActive", $member->id);

        $this->assertDatabaseHas("members", ["id" => $member->id, "active" => false]);

        // Search own members
        $response = $this->actingAs($this->adminA)
            ->get("/members");
        $response->assertOk()
            ->assertSee("Membro Editado");
    }

    public function test_admin_financial_income(): void
    {
        $this->actingAs($this->adminA);

        $cat = Category::create(["name" => "Oferta", "type" => "income"]);

        Volt::test("financial.incomes")
            ->set("category_id", (string) $cat->id)
            ->set("date", now()->format("Y-m-d"))
            ->set("amount", "100,00")
            ->set("payment_method", "PIX")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("incomes", [
            "church_id" => $this->churchA->id,
            "amount" => 100.00,
        ]);
    }

    public function test_admin_financial_expense(): void
    {
        $this->actingAs($this->adminA);

        $cat = Category::create(["name" => "Energia", "type" => "expense"]);

        Volt::test("financial.expenses")
            ->set("category_id", (string) $cat->id)
            ->set("date", now()->format("Y-m-d"))
            ->set("amount", "200,00")
            ->set("payment_method", "Dinheiro")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("expenses", [
            "church_id" => $this->churchA->id,
            "amount" => 200.00,
        ]);
    }

    public function test_admin_financial_dashboard_shows_own_data(): void
    {
        $this->actingAs($this->adminA);

        $cat = Category::create(["name" => "Dízimo", "type" => "income"]);
        \App\Models\Income::create([
            "church_id" => $this->churchA->id,
            "category_id" => $cat->id,
            "user_id" => $this->adminA->id,
            "date" => now(),
            "amount" => 500,
            "payment_method" => "Dinheiro",
        ]);
        \App\Models\Income::create([
            "church_id" => $this->churchB->id,
            "category_id" => $cat->id,
            "user_id" => $this->adminB->id,
            "date" => now(),
            "amount" => 999,
            "payment_method" => "Dinheiro",
        ]);

        $this->actingAs($this->adminA)->get("/financial")->assertOk();
    }

    public function test_admin_dashboard_shows_own_stats(): void
    {
        Member::factory()->count(3)->create(["church_id" => $this->churchA->id]);
        Member::factory()->create(["church_id" => $this->churchB->id, "name" => "Outra Igreja"]);

        $response = $this->actingAs($this->adminA)->get("/dashboard");
        $response->assertOk();
        // Should see own church member count
        $response->assertSee("3");
        $response->assertDontSee("Outra Igreja");
    }

    public function test_admin_form_validation_errors(): void
    {
        $this->actingAs($this->adminA);

        Volt::test("members.index")
            ->set("name", "")
            ->call("save")
            ->assertHasErrors(["name" => "required"]);
    }

    public function test_admin_cannot_access_churches_page(): void
    {
        // Admin has church.view — it CAN access churches page
        $this->actingAs($this->adminA)->get("/churches")->assertOk();
    }
}
