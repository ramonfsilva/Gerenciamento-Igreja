<?php

use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get("/", function () {
    return auth()->check()
        ? redirect("/dashboard")
        : redirect("/login");
});

Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", Dashboard::class)->name("dashboard");

    Route::middleware("can:church.view")->group(function () {
        Volt::route("churches", "churches.index")
            ->name("churches.index");
    });

    Route::middleware("can:user.view")->group(function () {
        Volt::route("users", "users.index")
            ->name("users.index");
    });
});

Route::view("profile", "profile")
    ->middleware(["auth"])
    ->name("profile");

require __DIR__ . "/auth.php";
