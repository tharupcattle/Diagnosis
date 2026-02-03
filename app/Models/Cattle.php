<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_alive' => 'boolean',
        'health_score' => 'decimal:2',
        'average_daily_yield' => 'decimal:2',
        'peak_yield' => 'decimal:2',
        'expected_yield' => 'decimal:2',
        'last_calving_date' => 'date',
        'last_checked_at' => 'datetime',
    ];

    // Relationships
    public function healthLogs()
    {
        return $this->hasMany(HealthLog::class);
    }

    public function latestLog()
    {
        return $this->hasOne(HealthLog::class)->latestOfMany();
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function unresolvedAlerts()
    {
        return $this->hasMany(Alert::class)->where('is_resolved', false);
    }

    public function milkProduction()
    {
        return $this->hasMany(MilkProduction::class);
    }

    public function latestMilkProduction()
    {
        return $this->hasOne(MilkProduction::class)->latestOfMany('production_date');
    }

    // Query Scopes
    public function scopeHealthy($query)
    {
        return $query->where('health_score', '>=', 70);
    }

    public function scopeAtRisk($query)
    {
        return $query->where('health_score', '<', 70)->orWhereHas('unresolvedAlerts');
    }

    public function scopeCritical($query)
    {
        return $query->where('health_score', '<', 40)->orWhereHas('unresolvedAlerts', function ($q) {
            $q->where('severity', 'critical');
        });
    }

    public function scopeHighProducers($query)
    {
        return $query->whereNotNull('average_daily_yield')
            ->whereRaw('average_daily_yield >= expected_yield * 0.95');
    }

    public function scopeLowProducers($query)
    {
        return $query->whereNotNull('average_daily_yield')
            ->whereRaw('average_daily_yield < expected_yield * 0.80');
    }

    // Health Analysis Methods
    public function getHealthTrend()
    {
        $logs = $this->healthLogs()
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        if ($logs->count() < 2) {
            return 'stable';
        }

        $recent = $logs->take(3)->avg('health_score');
        $older = $logs->skip(3)->avg('health_score');

        if ($recent > $older + 5)
            return 'improving';
        if ($recent < $older - 5)
            return 'declining';
        return 'stable';
    }

    public function calculateHealthScore()
    {
        $latestLog = $this->latestLog;
        if (!$latestLog)
            return null;

        return $latestLog->calculateHealthScore();
    }

    public function getRiskLevel()
    {
        $score = $this->health_score;
        if (!$score)
            return 'unknown';

        if ($score >= 80)
            return 'low';
        if ($score >= 60)
            return 'moderate';
        if ($score >= 40)
            return 'high';
        return 'critical';
    }

    // Milk Production Methods
    public function getMilkYieldTrend()
    {
        $production = $this->milkProduction()
            ->orderBy('production_date', 'desc')
            ->take(7)
            ->get();

        if ($production->count() < 2) {
            return 'stable';
        }

        $recent = $production->take(3)->avg('total_daily_yield');
        $older = $production->skip(3)->avg('total_daily_yield');

        if ($recent > $older + 1)
            return 'improving';
        if ($recent < $older - 1)
            return 'declining';
        return 'stable';
    }

    public function calculateAverageDailyYield($days = 30)
    {
        return $this->milkProduction()
            ->where('production_date', '>=', now()->subDays($days))
            ->avg('total_daily_yield') ?? 0;
    }

    public function getMilkYieldPotential()
    {
        if (!$this->expected_yield || !$this->average_daily_yield) {
            return 0;
        }

        $gap = $this->expected_yield - $this->average_daily_yield;
        return max(0, $gap);
    }

    public function getLactationDay()
    {
        if (!$this->last_calving_date) {
            return null;
        }

        return now()->diffInDays($this->last_calving_date);
    }
}
