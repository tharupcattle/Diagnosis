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
        Schema::table('alerts', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->after('is_resolved');
            $table->string('resolved_by')->nullable()->after('resolved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['resolved_at', 'resolved_by']);
        });
    }
};
