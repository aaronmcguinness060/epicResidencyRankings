<?php

namespace App\Services;

use App\Models\Ranking;
use App\Models\Residency;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OffersService
{
    /**
     * Process all rankings and create up to 3 offers per student,
     * considering residency capacity and avoiding duplicate offers.
     */
    public function acceptStudentOffers(): void
    {
        DB::transaction(function () {
            // Optional: clear old pending offers before processing new ones
            Offer::query()->delete();
            Log::info('Cleared existing pending offers');

            // Step 1: Get company capacity (number of residencies per company)
            $companyCapacities = Residency::select('company_id', DB::raw('COUNT(*) as residency_count'))
                ->groupBy('company_id')
                ->pluck('residency_count', 'company_id')
                ->map(fn($count) => $count * 3); // 3 invitations per residency

                \Log::info($companyCapacities);

            // Track how many offers have been made for each company
            $offersPerCompany = [];

            // Step 2: Get all rankings grouped by student
            $rankings = Ranking::with(['residency', 'student'])
                ->orderBy('student_id')
                ->orderBy('position')
                ->get()
                ->groupBy('student_id');

            // Step 3: Process offers per student
            foreach ($rankings as $studentRankings) {
                $offersMade = 0;
                $student = $studentRankings->first()->student;
                $userId = optional($student)->student_id;

                if (!$userId) {
                    Log::warning('Skipping ranking: student has no student_id', [
                        'student_id' => optional($student)->id,
                    ]);
                    continue;
                }

                foreach ($studentRankings as $ranking) {
                    if ($offersMade >= 3) {
                        break;
                    }

                    $residency = $ranking->residency;
                    $companyId = $residency->company_id;

                    $currentCount = $offersPerCompany[$companyId] ?? 0;
                    $capacity = $companyCapacities[$companyId] ?? 0;

                    // Check if offer already exists (prevent duplicates)
                    $existingOffer = Offer::where('student_id', $student->student_id)
                        ->where('residency_id', $residency->id)
                        ->exists();

                    if ($existingOffer) {
                        Log::info('Duplicate offer skipped', [
                            'student_id' => $student->student_id,
                            'residency_id' => $residency->id,
                        ]);
                        continue;
                    }

                    if ($currentCount < $capacity) {
                        $offer = Offer::create([
                            'student_id' => $student->student_id,
                            'residency_id' => $residency->id,
                        ]);

                        Log::info('Offer created', [
                            'offer_id' => $offer->id,
                            'user_id' => $userId,
                            'student_id' => $student->id,
                            'residency_id' => $residency->id,
                            'company_id' => $companyId,
                            'position' => $ranking->position,
                        ]);

                        $offersPerCompany[$companyId] = $currentCount + 1;
                        $offersMade++;
                    } else {
                        Log::info('Company at full capacity, skipping offer', [
                            'company_id' => $companyId,
                            'residency_id' => $residency->id,
                            'user_id' => $userId,
                        ]);
                    }
                }
            }

            Log::info('Ranking processing complete');
        });
    }
}
