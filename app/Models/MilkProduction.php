<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilkProduction extends Model
{
    protected $table = 'milk_production';
    protected $guarded = [];

    protected $casts = [
        'production_date' => 'date',
        'morning_yield' => 'decimal:2',
        'evening_yield' => 'decimal:2',
        'total_daily_yield' => 'decimal:2',
        'milk_fat_percentage' => 'decimal:2',
        'milk_protein_percentage' => 'decimal:2',
    ];

    public function cattle()
    {
        return $this->belongsTo(Cattle::class);
    }

    /**
     * Scope: Last N days of production
     */
    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('production_date', '>=', now()->subDays($days))
            ->orderBy('production_date', 'desc');
    }

    /**
     * Scope: Filter by lactation stage
     */
    public function scopeByLactationStage($query, $stage)
    {
        return $query->where('lactation_stage', $stage);
    }

    /**
     * Get lactation stage name
     */
    public function getLactationStageNameAttribute()
    {
        return match ($this->lactation_stage) {
            'early' => 'Early Lactation (0-60 days)',
            'peak' => 'Peak Lactation (60-120 days)',
            'mid' => 'Mid Lactation (120-200 days)',
            'late' => 'Late Lactation (200+ days)',
            default => 'Unknown',
        };
    }
}
