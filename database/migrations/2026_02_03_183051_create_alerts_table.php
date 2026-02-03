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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->onDelete('cascade');
            $table->string('type'); // e.g. 'Digestive', 'Fever', 'Rumination Drop'
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->text('description');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
