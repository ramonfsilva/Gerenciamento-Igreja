<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("members", function (Blueprint $table) {
            $table->id();
            $table->foreignId("church_id")->constrained()->onDelete("cascade");
            $table->string("name");
            $table->string("phone", 20)->nullable();
            $table->string("whatsapp", 20)->nullable();
            $table->string("email")->nullable();
            $table->date("birth_date")->nullable();
            $table->string("gender")->nullable();
            $table->string("marital_status")->nullable();
            $table->string("role_function")->nullable();
            $table->string("status")->default("Ativo");
            $table->date("conversion_date")->nullable();
            $table->date("baptism_date")->nullable();
            $table->text("notes")->nullable();
            $table->boolean("active")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("members");
    }
};
