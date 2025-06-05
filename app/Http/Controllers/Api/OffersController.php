<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RankingService;
use App\Models\Offer;
use App\Models\Assignment;

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

    public function acceptStudentOffers(Request $request)
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

        // Validate incoming data
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.residency_id' => 'required|integer|exists:residencies,id',
            'assignments.*.student_id' => 'required|integer',
        ]);

        // Insert assignments
        foreach ($validated['assignments'] as $assignment) {
            Assignment::create([
                'residency_id' => $assignment['residency_id'],
                'student_id' => $assignment['student_id'],
            ]);
        }

        return response()->json([
            'message' => 'Offers for company accepted and assignments created',
        ], 200);
    }

    public function index(Request $request)
    {
        // Get logged-in user
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Determine whether to load all offers (admin) or only company-specific ones
        if ($user->user_type == 2) {
            // Admin: load all offers
            $offers = Offer::with(['student.user', 'residency'])->get();
        } else {
            // Regular user: load offers linked to the user's company
            $company = $user->company;

            if (!$company) {
                return response()->json(['error' => 'No company found for user'], 404);
            }

            $residencyIds = $company->residencies()->pluck('id')->toArray();

            $offers = Offer::with(['student.user', 'residency'])
                ->whereIn('residency_id', $residencyIds)
                ->get();
        }

        // Format the response
        $formattedOffers = $offers->map(function ($offer) {
            return [
                'id' => $offer->id,
                'student_id' => $offer->student_id,
                'residency_id' => $offer->residency_id,
                'created_at' => $offer->created_at,
                'updated_at' => $offer->updated_at,
                'student' => [
                    'student_id' => $offer->student->student_id,
                    'score' => $offer->student->score,
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

    public function checkAcceptedOffers(Request $request)
    {
        $user = auth()->user();

        $company = $user->company;

        if (!$company) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'assignments' => [],
                'message' => 'No company found for user.',
            ]);
        }

        // Get assignments without eager loading student
        $assignments = Assignment::whereHas('residency', function ($query) use ($company) {
            $query->where('company_id', $company->company_id);
        })
        ->with('residency')  // still eager load residency if needed
        ->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'assignments' => [],
            ]);
        }

        // Extract student_ids from assignments
        $studentIds = $assignments->pluck('student_id')->unique()->toArray();

        // Manually query students to get user_ids
        $students = \DB::table('students')
            ->whereIn('student_id', $studentIds)
            ->select('student_id', 'user_id')
            ->get();

        $userIds = $students->pluck('user_id')->unique()->toArray();

        // Manually query users to get emails
        $users = \DB::table('users')
            ->whereIn('user_id', $userIds)
            ->select('user_id', 'email')
            ->get()
            ->keyBy('user_id'); // key by user_id for easy lookup

        // Map student_id to user email
        $studentUserMap = [];
        foreach ($students as $student) {
            $studentUserMap[$student->student_id] = $users[$student->user_id]->email ?? null;
        }

        // Attach email info manually to assignments
        $assignments->transform(function ($assignment) use ($studentUserMap) {
            $assignment->student_email = $studentUserMap[$assignment->student_id] ?? null;
            return $assignment;
        });

        return response()->json([
            'hasAcceptedOffers' => true,
            'assignments' => $assignments,
        ]);
    }

    public function checkMyOffers(Request $request)
    {
        $user = auth()->user();

        // Get student record from user
        $student = \DB::table('students')
            ->where('user_id', $user->user_id)
            ->first();

        if (!$student) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'message' => 'No student profile found for user.',
            ]);
        }

        // Check if any assignment exists for this student
        $assignment = \DB::table('assignments')
            ->where('student_id', $student->student_id)
            ->first();

        if (!$assignment) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'message' => 'No accepted assignment found for this student.',
            ]);
        }

        // Get residency details
        $residency = \DB::table('residencies')
            ->where('id', $assignment->residency_id)
            ->first();

        if (!$residency) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'message' => 'Residency not found.',
            ]);
        }

        // Get company details
        $company = \DB::table('companies')
            ->where('company_id', $residency->company_id)
            ->first();

        if (!$company) {
            return response()->json([
                'hasAcceptedOffers' => false,
                'message' => 'Company not found.',
            ]);
        }

        // Get company user details
        $companyUser = \DB::table('users')
            ->where('user_id', $company->user_id)
            ->first();

        return response()->json([
            'hasAcceptedOffers' => true,
            'residency' => [
                'salary' => $residency->salary,
                'description' => $residency->description,
            ],
            'company' => [
                'company_name' => $company->company_name,
                'email' => $companyUser->email ?? null,
            ],
        ]);
    }
}
