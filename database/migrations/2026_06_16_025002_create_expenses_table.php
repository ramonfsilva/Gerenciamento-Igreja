<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("expenses", function (Blueprint $table) {
            $table->id();
            $table->foreignId("church_id")->constrained()->cascadeOnDelete();
            $table->foreignId("category_id")->constrained("financial_categories");
            $table->foreignId("user_id")->constrained();
            $table->date("date");
            $table->decimal("amount", 10, 2);
            $table->string("payment_method")->nullable();
            $table->text("description")->nullable();
            $table->boolean("active")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("expenses");
    }
};
