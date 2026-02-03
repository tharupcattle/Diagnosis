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
        Schema::table('health_logs', function (Blueprint $table) {
            $table->decimal('health_score', 5, 2)->nullable()->after('heart_rate');
            $table->json('risk_factors')->nullable()->after('health_score');
            $table->timestamp('analyzed_at')->nullable()->after('risk_factors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_logs', function (Blueprint $table) {
            $table->dropColumn(['health_score', 'risk_factors', 'analyzed_at']);
        });
    }
};
