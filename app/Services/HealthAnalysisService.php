<?php

namespace App\Services;

use App\Models\Cattle;
use App\Models\HealthLog;
use App\Models\Alert;

class HealthAnalysisService
{
    /**
     * Analyze the latest health log for a specific cattle.
     */
    public function analyze(HealthLog $log)
    {
        $cattle = $log->cattle;

        $this->detectAcidosis($log, $cattle);
        $this->detectBloat($log, $cattle);
        $this->detectFever($log, $cattle);
    }

    protected function detectAcidosis(HealthLog $log, Cattle $cattle)
    {
        // Normal Rumen pH is 6.0 - 7.0
        if ($log->ph_level < 5.8 && $log->ph_level >= 5.5) {
            $this->createAlert(
                $cattle,
                'Sub-acute Ruminal Acidosis (SARA)',
                'medium',
                "pH level is slightly low ({$log->ph_level}). Monitor feed intake and reduce concentrates."
            );
        } elseif ($log->ph_level < 5.5) {
            $this->createAlert(
                $cattle,
                'Acute Acidosis',
                'critical',
                "CRITICAL: pH level dropped to {$log->ph_level}. High risk of death. Consult vet and provide bicarbonate immediately."
            );
        }
    }

    protected function detectBloat(HealthLog $log, Cattle $cattle)
    {
        // Bloat often shows as low rumination + low activity + temperature changes
        if ($log->rumination_rate < 30 && $log->activity_level === 'low') {
            $this->createAlert(
                $cattle,
                'Potential Bloat',
                'high',
                "Low rumination ({$log->rumination_rate}/min) and inactivity detected. Check for left-side swelling (Bloat)."
            );
        }
    }

    protected function detectFever(HealthLog $log, Cattle $cattle)
    {
        // Normal cow temp is ~38.5 - 39.3 C
        if ($log->temperature > 39.5 && $log->temperature <= 40.5) {
            $this->createAlert(
                $cattle,
                'Mild Fever',
                'low',
                "Temperature elevated at {$log->temperature}°C. Could be heat stress or early infection."
            );
        } elseif ($log->temperature > 40.5) {
            $this->createAlert(
                $cattle,
                'High Fever / Infection',
                'critical',
                "CRITICAL: Severe fever detected ({$log->temperature}°C). Immediate medical attention required."
            );
        }
    }

    protected function createAlert(Cattle $cattle, $type, $severity, $description)
    {
        // Avoid duplicate active alerts of the same type
        $exists = Alert::where('cattle_id', $cattle->id)
            ->where('type', $type)
            ->where('is_resolved', false)
            ->exists();

        if (!$exists) {
            Alert::create([
                'cattle_id' => $cattle->id,
                'type' => $type,
                'severity' => $severity,
                'description' => $description,
                'is_resolved' => false,
            ]);
        }
    }
}
