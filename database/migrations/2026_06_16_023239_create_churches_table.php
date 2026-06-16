<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("churches", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("cnpj", 18)->nullable()->unique();
            $table->string("phone", 20)->nullable();
            $table->string("email")->nullable();
            $table->string("pastor_name")->nullable();
            $table->text("address")->nullable();
            $table->string("city")->nullable();
            $table->string("state", 2)->nullable();
            $table->boolean("active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("churches");
    }
};
