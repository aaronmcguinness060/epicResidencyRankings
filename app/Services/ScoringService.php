<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoringService
{
    public function calculateScoresForAllStudents(): void
    {
        $students = Student::with('user')->get();

        foreach ($students as $student) {
            // In a real-world application, fetch this data from the UL Academic Registry API.
            $qca        = $this->randomFloat(0.0, 4.0);
            $attendance = $this->randomFloat(60.0, 100.0); // realistic range
            $pp1        = $this->randomFloat(0.0, 100.0);
            $pp2        = $this->randomFloat(0.0, 100.0);
            $pp3        = $this->randomFloat(0.0, 100.0);
            $pp4        = $this->randomFloat(0.0, 100.0);

            $weightedQCA  = ($qca / 4.0) * 70.0;          // 70%
            $weightedAtt  = ($attendance / 100.0) * 20.0; // 20%
            $projectAvg   = ($pp1 + $pp2 + $pp3 + $pp4) / 4.0;
            $weightedProj = ($projectAvg / 100.0) * 10.0; // 10%

            $finalScore = round($weightedQCA + $weightedAtt + $weightedProj, 2);

            // Manual update using query builder instead of Eloquent model update
            DB::table('students')
                ->where('user_id', $student->user_id)
                ->update(['score' => $finalScore]);

            Log::info("Student {$student->user_id} score updated to {$finalScore}");
        }
    }

    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
