<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'health_score' => 'decimal:2',
        'risk_factors' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function cattle()
    {
        return $this->belongsTo(Cattle::class);
    }

    /**
     * Calculate health score from vital parameters (0-100 scale)
     */
    public function calculateHealthScore()
    {
        $score = 100;

        // pH Level (30% weight) - Optimal: 6.0-7.0
        if ($this->ph_level) {
            if ($this->ph_level < 5.5) {
                $score -= 30; // Critical acidosis
            } elseif ($this->ph_level < 6.0) {
                $score -= 15; // Sub-acute acidosis
            } elseif ($this->ph_level < 6.5) {
                $score -= 5;
            } elseif ($this->ph_level > 7.5) {
                $score -= 10;
            }
        }

        // Rumination Rate (25% weight) - Optimal: 45-65/min
        if ($this->rumination_rate) {
            if ($this->rumination_rate < 25) {
                $score -= 25; // Critical
            } elseif ($this->rumination_rate < 35) {
                $score -= 15; // Low
            } elseif ($this->rumination_rate < 45) {
                $score -= 8;
            } elseif ($this->rumination_rate > 70) {
                $score -= 5;
            }
        }

        // Temperature (20% weight) - Optimal: 38.0-39.3°C
        if ($this->temperature) {
            if ($this->temperature > 40.5) {
                $score -= 20; // Critical fever
            } elseif ($this->temperature > 39.5) {
                $score -= 10; // Mild fever
            } elseif ($this->temperature < 37.5) {
                $score -= 15; // Hypothermia
            }
        }

        // Activity Level (15% weight)
        if ($this->activity_level === 'low') {
            $score -= 15;
        } elseif ($this->activity_level === 'very_low') {
            $score -= 20;
        }

        // Heart Rate (10% weight) - Optimal: 60-80 bpm
        if ($this->heart_rate) {
            if ($this->heart_rate > 100 || $this->heart_rate < 50) {
                $score -= 10;
            } elseif ($this->heart_rate > 90 || $this->heart_rate < 55) {
                $score -= 5;
            }
        }

        return max(0, min(100, $score));
    }

    /**
     * Identify specific risk factors from vital signs
     */
    public function identifyRiskFactors()
    {
        $risks = [];

        if ($this->ph_level && $this->ph_level < 5.8) {
            $risks[] = 'acidosis';
        }

        if ($this->rumination_rate && $this->rumination_rate < 30) {
            $risks[] = 'low_rumination';
        }

        if ($this->temperature && $this->temperature > 39.5) {
            $risks[] = 'fever';
        }

        if ($this->activity_level === 'low' && $this->rumination_rate < 30) {
            $risks[] = 'bloat_risk';
        }

        return $risks;
    }
}
