<?php

use App\Livewire\Dashboard;
use App\Livewire\FinancialReports;
use App\Livewire\FinancialDashboard;
use App\Livewire\MemberReports;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get("/", function () {
    return auth()->check() ? redirect("/dashboard") : redirect("/login");
});

Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", Dashboard::class)->name("dashboard");

    Route::middleware("can:member.view")->group(function () {
        Volt::route("members", "members.index")->name("members.index");
    });

    Route::middleware("can:financial.view")->group(function () {
        Route::get("/financial", FinancialDashboard::class)->name("financial.dashboard");
        Route::get("/financial/reports", FinancialReports::class)->name("financial.reports");
        Volt::route("financial/incomes", "financial.incomes")->name("financial.incomes");
        Volt::route("financial/expenses", "financial.expenses")->name("financial.expenses");
    });

    Route::middleware("can:church.view")->group(function () {
        Volt::route("churches", "churches.index")->name("churches.index");
    });

    Route::middleware("can:category.view")->group(function () {
        Volt::route("financial/categories", "financial.categories")->name("financial.categories");
    });

    Route::middleware("can:report.view")->group(function () {
        Route::get("/reports/members", MemberReports::class)->name("reports.members");
    });

    Route::middleware("can:user.view")->group(function () {
        Volt::route("users", "users.index")->name("users.index");
    });
});

Route::view("profile", "profile")
    ->middleware(["auth"])
    ->name("profile");

require __DIR__ . "/auth.php";
