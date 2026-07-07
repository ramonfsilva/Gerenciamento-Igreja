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

class E2EMasterTest extends TestCase
{
    use RefreshDatabase;

    private User $master;
    private Church $church;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->master = User::factory()->create();
        $this->master->assignRole("Master");
        $this->church = Church::factory()->create(["name" => "Igreja Principal"]);
    }

    public function test_master_full_login_dashboard_logout(): void
    {
        Volt::test("pages.auth.login")
            ->set("form.email", $this->master->email)
            ->set("form.password", "password")
            ->call("login")
            ->assertHasNoErrors()
            ->assertRedirect(route("dashboard", absolute: false));

        $this->actingAs($this->master)
            ->get("/dashboard")
            ->assertOk()
            ->assertSee("Igrejas")
            ->assertSee("Membros");
    }

    public function test_master_church_full_crud(): void
    {
        $this->actingAs($this->master);

        Volt::test("churches.index")
            ->set("name", "Igreja Nova")
            ->set("city", "São Paulo")
            ->set("state", "SP")
            ->call("save")
            ->assertHasNoErrors();

        $church = Church::where("name", "Igreja Nova")->first();
        $this->assertNotNull($church);

        Volt::test("churches.index")
            ->call("edit", $church->id)
            ->set("name", "Igreja Nova Editada")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("churches", ["id" => $church->id, "name" => "Igreja Nova Editada"]);

        $this->actingAs($this->master)
            ->get("/churches")
            ->assertOk()
            ->assertSee("Igreja Nova Editada");
    }

    public function test_master_member_listing(): void
    {
        Member::factory()->create(["church_id" => $this->church->id, "name" => "João da Silva"]);

        $this->actingAs($this->master)
            ->get("/members")
            ->assertOk()
            ->assertSee("João da Silva");
    }

    public function test_master_user_full_crud(): void
    {
        $this->actingAs($this->master);

        Volt::test("users.index")
            ->set("name", "Novo Usuário")
            ->set("email", "novo@test.com")
            ->set("password", "123456")
            ->set("password_confirmation", "123456")
            ->set("role", "Admin")
            ->set("church_id", (string) $this->church->id)
            ->call("save")
            ->assertHasNoErrors();

        $user = User::where("email", "novo@test.com")->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole("Admin"));

        Volt::test("users.index")
            ->call("edit", $user->id)
            ->set("name", "Usuário Editado")
            ->set("role", "Tesoureiro")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("users", ["id" => $user->id, "name" => "Usuário Editado"]);

        $this->actingAs($this->master)
            ->get("/users")
            ->assertOk()
            ->assertSee("Usuário Editado");
    }

    public function test_master_financial_category_full_crud(): void
    {
        $this->actingAs($this->master);

        Volt::test("financial.categories")
            ->set("name", "Dízimo")
            ->set("type", "income")
            ->call("save")
            ->assertHasNoErrors();

        $cat = Category::where("name", "Dízimo")->where("type", "income")->first();
        $this->assertNotNull($cat);

        Volt::test("financial.categories")
            ->call("edit", $cat->id)
            ->set("name", "Dízimo Atualizado")
            ->call("save")
            ->assertHasNoErrors();

        $this->assertDatabaseHas("financial_categories", ["id" => $cat->id, "name" => "Dízimo Atualizado"]);

        Volt::test("financial.categories")
            ->call("toggleActive", $cat->id);

        $this->assertDatabaseHas("financial_categories", ["id" => $cat->id, "active" => false]);
    }

    // ponytail: Master has null church_id, incomes/expenses require church_id.
    // Test Master can view financial pages and see existing data.
    public function test_master_financial_pages_accessible(): void
    {
        $this->actingAs($this->master);

        $cat = Category::create(["name" => "Dízimo", "type" => "income"]);
        \App\Models\Income::create([
            "church_id" => $this->church->id,
            "category_id" => $cat->id,
            "user_id" => $this->master->id,
            "date" => now(),
            "amount" => 150.50,
            "payment_method" => "Dinheiro",
        ]);
        \App\Models\Expense::create([
            "church_id" => $this->church->id,
            "category_id" => $cat->id,
            "user_id" => $this->master->id,
            "date" => now(),
            "amount" => 300.00,
            "payment_method" => "Boleto",
        ]);

        $this->get("/financial")->assertOk();
        $this->get("/financial/categories")->assertOk()->assertSee("Dízimo");
        $this->get("/financial/reports")->assertOk();
    }

    public function test_master_dashboard_shows_all_stats(): void
    {
        Member::factory()->count(5)->create(["church_id" => $this->church->id]);
        Member::factory()->create(["church_id" => $this->church->id, "status" => "Visitante"]);
        User::factory()->count(2)->create(["church_id" => $this->church->id]);

        $response = $this->actingAs($this->master)->get("/dashboard");
        $response->assertOk();
        $response->assertSee("6");
    }

    public function test_master_reports_page(): void
    {
        Member::factory()->count(3)->create(["church_id" => $this->church->id]);

        $response = $this->actingAs($this->master)->get("/reports/members");
        $response->assertOk()
            ->assertSee("3");
    }
}
