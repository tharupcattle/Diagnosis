<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('milk_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->onDelete('cascade');
            $table->date('production_date');
            $table->decimal('morning_yield', 8, 2)->default(0);
            $table->decimal('evening_yield', 8, 2)->default(0);
            $table->decimal('total_daily_yield', 8, 2)->storedAs('morning_yield + evening_yield');
            $table->decimal('milk_fat_percentage', 5, 2)->nullable();
            $table->decimal('milk_protein_percentage', 5, 2)->nullable();
            $table->integer('lactation_day')->nullable();
            $table->enum('lactation_stage', ['early', 'peak', 'mid', 'late'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate entries for same cattle on same date
            $table->unique(['cattle_id', 'production_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_production');
    }
};
