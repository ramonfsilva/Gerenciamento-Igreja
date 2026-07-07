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

class E2ERoleFlowsTest extends TestCase
{
    use RefreshDatabase;

    private Church $church;
    private User $tesoureiro;
    private User $secretaria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->church = Church::factory()->create();
        $this->tesoureiro = User::factory()->create(["church_id" => $this->church->id]);
        $this->tesoureiro->assignRole("Tesoureiro");
        $this->secretaria = User::factory()->create(["church_id" => $this->church->id]);
        $this->secretaria->assignRole("Secretaria");
    }

    public function test_tesoureiro_can_access_financial(): void
    {
        $this->actingAs($this->tesoureiro)
            ->get("/financial")
            ->assertOk();
    }

    public function test_tesoureiro_cannot_access_members(): void
    {
        $this->actingAs($this->tesoureiro)
            ->get("/members")
            ->assertForbidden();
    }

    public function test_tesoureiro_full_income_flow(): void
    {
        $this->actingAs($this->tesoureiro);
        $cat = Category::create(["name" => "Dízimo", "type" => "income"]);

        Volt::test("financial.incomes")
            ->set("category_id", (string) $cat->id)
            ->set("date", now()->format("Y-m-d"))
            ->set("amount", "75,00")
            ->set("payment_method", "Cartão")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("incomes", [
            "church_id" => $this->church->id,
            "amount" => 75.00,
        ]);
    }

    public function test_tesoureiro_full_expense_flow(): void
    {
        $this->actingAs($this->tesoureiro);
        $cat = Category::create(["name" => "Internet", "type" => "expense"]);

        Volt::test("financial.expenses")
            ->set("category_id", (string) $cat->id)
            ->set("date", now()->format("Y-m-d"))
            ->set("amount", "120,00")
            ->set("payment_method", "Boleto")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("expenses", [
            "church_id" => $this->church->id,
            "amount" => 120.00,
        ]);
    }

    public function test_tesoureiro_can_view_categories(): void
    {
        Category::create(["name" => "Teste", "type" => "income"]);

        $this->actingAs($this->tesoureiro)
            ->get("/financial/categories")
            ->assertOk()
            ->assertSee("Teste");
    }

    public function test_secretaria_can_access_members(): void
    {
        $this->actingAs($this->secretaria)
            ->get("/members")
            ->assertOk();
    }

    public function test_secretaria_cannot_access_financial(): void
    {
        $this->actingAs($this->secretaria)
            ->get("/financial")
            ->assertForbidden();
    }

    // ponytail: Secretaria has member.view/edit/create but NOT member.delete
    public function test_secretaria_member_create_and_edit(): void
    {
        $this->actingAs($this->secretaria);

        // Create
        Volt::test("members.index")
            ->set("name", "Membro Secretaria")
            ->set("gender", "Feminino")
            ->set("status", "Visitante")
            ->call("save")
            ->assertHasNoErrors();

        $member = Member::where("name", "Membro Secretaria")->first();
        $this->assertNotNull($member);
        $this->assertEquals($this->church->id, $member->church_id);

        // Edit
        Volt::test("members.index")
            ->call("edit", $member->id)
            ->set("name", "Membro Secretaria Editado")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("members", ["id" => $member->id, "name" => "Membro Secretaria Editado"]);
    }

    public function test_secretaria_can_toggle_member(): void
    {
        // ponytail: toggleActive requires member.edit (which Secretaria has)
        $member = Member::factory()->create(["church_id" => $this->church->id]);
        $this->actingAs($this->secretaria);

        Volt::test("members.index")
            ->call("toggleActive", $member->id);

        $this->assertDatabaseHas("members", ["id" => $member->id, "active" => false]);
    }

    public function test_secretaria_member_list_search(): void
    {
        Member::factory()->create(["church_id" => $this->church->id, "name" => "Ana Buscável"]);
        Member::factory()->create(["church_id" => $this->church->id, "name" => "Carlos Outro"]);

        $this->actingAs($this->secretaria);

        $response = $this->get("/members");
        $response->assertOk()->assertSee("Ana Buscável")->assertSee("Carlos Outro");
    }

    public function test_secretaria_reports(): void
    {
        Member::factory()->count(2)->create(["church_id" => $this->church->id]);

        $this->actingAs($this->secretaria)
            ->get("/reports/members")
            ->assertOk()
            ->assertSee("2");
    }

    public function test_tesoureiro_reports(): void
    {
        $cat = Category::create(["name" => "Dízimo", "type" => "income"]);
        \App\Models\Income::create([
            "church_id" => $this->church->id,
            "category_id" => $cat->id,
            "user_id" => $this->tesoureiro->id,
            "date" => now(),
            "amount" => 1000,
            "payment_method" => "PIX",
        ]);

        $this->actingAs($this->tesoureiro)
            ->get("/financial/reports")
            ->assertOk();
    }
}
