<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CattleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cow1 = \App\Models\Cattle::create([
            'name' => 'Ganga',
            'tag_id' => 'TAG-001',
            'breed' => 'Gir',
            'dob' => '2022-01-15',
            'gender' => 'female',
            'last_checked_at' => now(),
        ]);

        $cow2 = \App\Models\Cattle::create([
            'name' => 'Yamuna',
            'tag_id' => 'TAG-002',
            'breed' => 'Sahiwal',
            'dob' => '2021-05-10',
            'gender' => 'female',
            'last_checked_at' => now(),
        ]);

        // Healthy logs for Ganga
        \App\Models\HealthLog::create([
            'cattle_id' => $cow1->id,
            'temperature' => 38.5,
            'ph_level' => 6.8,
            'activity_level' => 'normal',
            'rumination_rate' => 55,
            'heart_rate' => 60,
        ]);

        // Failing health logs for Yamuna (Digestive Issue)
        \App\Models\HealthLog::create([
            'cattle_id' => $cow2->id,
            'temperature' => 39.8,
            'ph_level' => 5.2, // Acidosis
            'activity_level' => 'low',
            'rumination_rate' => 20, // Very low
            'heart_rate' => 85,
        ]);

        // Alert for Yamuna
        \App\Models\Alert::create([
            'cattle_id' => $cow2->id,
            'type' => 'Digestive Crisis',
            'severity' => 'critical',
            'description' => 'Abnormal pH drop (5.2) and low rumination detected. High risk of Acidosis/Bloat. Consult vet immediately.',
            'is_resolved' => false,
        ]);
    }
}
