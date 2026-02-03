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
            // Health scoring fields
            $table->decimal('health_score', 5, 2)->nullable()->after('last_checked_at');
            $table->enum('risk_level', ['low', 'moderate', 'high', 'critical'])->nullable()->after('health_score');

            // Mortality tracking
            $table->boolean('is_alive')->default(true)->after('risk_level');
            $table->foreignId('death_id')->nullable()->constrained('cattle_deaths')->onDelete('set null')->after('is_alive');

            // Milk production fields
            $table->decimal('average_daily_yield', 8, 2)->nullable()->after('death_id');
            $table->decimal('peak_yield', 8, 2)->nullable()->after('average_daily_yield');
            $table->date('last_calving_date')->nullable()->after('peak_yield');
            $table->decimal('expected_yield', 8, 2)->nullable()->after('last_calving_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cattle', function (Blueprint $table) {
            $table->dropConstrainedForeignId('death_id');
            $table->dropColumn([
                'health_score',
                'risk_level',
                'is_alive',
                'average_daily_yield',
                'peak_yield',
                'last_calving_date',
                'expected_yield'
            ]);
        });
    }
};
