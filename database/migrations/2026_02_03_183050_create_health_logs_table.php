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
        Schema::create('health_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->onDelete('cascade');
            $table->decimal('temperature', 5, 2)->comment('In Celsius');
            $table->decimal('ph_level', 4, 2)->comment('Critical for digestive health (6.0-7.0 is normal)');
            $table->enum('activity_level', ['low', 'normal', 'high'])->default('normal');
            $table->integer('rumination_rate')->comment('Frequency per minute');
            $table->integer('heart_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_logs');
    }
};
