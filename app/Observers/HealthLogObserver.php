<?php

namespace App\Observers;

use App\Models\HealthLog;

class HealthLogObserver
{
    /**
     * Handle the HealthLog "created" event.
     */
    public function created(HealthLog $healthLog): void
    {
        // Calculate and store health score
        $healthScore = $healthLog->calculateHealthScore();
        $riskFactors = $healthLog->identifyRiskFactors();

        $healthLog->update([
            'health_score' => $healthScore,
            'risk_factors' => $riskFactors,
            'analyzed_at' => now(),
        ]);

        // Run AI health analysis to create alerts if needed
        $analysisService = new \App\Services\HealthAnalysisService();
        $analysisService->analyze($healthLog);

        // Update cattle's health metrics
        $cattle = $healthLog->cattle;
        $cattle->update([
            'health_score' => $healthScore,
            'risk_level' => $cattle->getRiskLevel(),
            'last_checked_at' => now()
        ]);
    }

    /**
     * Handle the HealthLog "updated" event.
     */
    public function updated(HealthLog $healthLog): void
    {
        //
    }

    /**
     * Handle the HealthLog "deleted" event.
     */
    public function deleted(HealthLog $healthLog): void
    {
        //
    }

    /**
     * Handle the HealthLog "restored" event.
     */
    public function restored(HealthLog $healthLog): void
    {
        //
    }

    /**
     * Handle the HealthLog "force deleted" event.
     */
    public function forceDeleted(HealthLog $healthLog): void
    {
        //
    }
}
