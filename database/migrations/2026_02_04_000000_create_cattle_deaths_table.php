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
        Schema::create('cattle_deaths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->onDelete('cascade');
            $table->dateTime('death_date');
            $table->enum('cause', ['acidosis', 'bloat', 'infection', 'injury', 'old_age', 'other'])->default('other');
            $table->json('contributing_factors')->nullable();
            $table->boolean('was_alerted')->default(false);
            $table->integer('alert_duration_hours')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cattle_deaths');
    }
};
