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
        Schema::create('cattle', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tag_id')->unique();
            $table->string('breed')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('female');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cattle');
    }
};
