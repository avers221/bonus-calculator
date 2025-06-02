<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bonus_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->foreignUuid('condition_id')->nullable()->constrained('conditions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('formula_id')->nullable()->constrained('formulas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('priority');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_rules');
    }
};
