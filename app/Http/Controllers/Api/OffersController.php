<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RankingService;
use App\Models\Offer;

class OffersController extends Controller
{
    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function assignOffers()
    {
        $this->rankingService->processRankings();

        return response()->json([
            'message' => 'Rankings processed and residencies assigned',
        ], 200);
    }

    public function index(Request $request)
    {
        // Get logged-in user
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Load company for the user
        $company = $user->company;

        if (!$company) {
            return response()->json(['error' => 'No company found for user'], 404);
        }

        // Get residency IDs for that company
        $residencyIds = $company->residencies()->pluck('id')->toArray();

        // Get offers with nested relationships
        $offers = Offer::with(['student.user', 'residency'])
            ->whereIn('residency_id', $residencyIds)
            ->get();

        // Format the response
        $formattedOffers = $offers->map(function ($offer) {
            return [
                'id' => $offer->id,
                'student_id' => $offer->student_id,
                'residency_id' => $offer->residency_id,
                'status' => $offer->status,
                'created_at' => $offer->created_at,
                'updated_at' => $offer->updated_at,
                'student' => [
                    'student_id' => $offer->student->student_id,
                    'qca' => $offer->student->qca,
                    'first_name' => $offer->student->user->first_name ?? null,
                    'last_name' => $offer->student->user->last_name ?? null,
                    'email' => $offer->student->user->email ?? null,
                ],
                'residency' => [
                    'id' => $offer->residency->id,
                    'company_id' => $offer->residency->company_id,
                    'salary' => $offer->residency->salary,
                    'description' => $offer->residency->description,
                    'created_at' => $offer->residency->created_at,
                    'updated_at' => $offer->residency->updated_at,
                ]
            ];
        });

        return response()->json([
            'offers' => $formattedOffers,
        ], 200);
    }
}
