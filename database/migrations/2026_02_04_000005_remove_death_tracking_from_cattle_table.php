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
        Schema::table('cattle', function (Blueprint $table) {
            // Remove death tracking - Focus on PREVENTION, not death records
            $table->dropConstrainedForeignId('death_id');
            $table->dropColumn('is_alive');
        });

        // Drop the entire cattle_deaths table - we're saving lives, not counting deaths!
        Schema::dropIfExists('cattle_deaths');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate if needed for rollback
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

        Schema::table('cattle', function (Blueprint $table) {
            $table->boolean('is_alive')->default(true);
            $table->foreignId('death_id')->nullable()->constrained('cattle_deaths')->onDelete('set null');
        });
    }
};
