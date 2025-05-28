<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Ranking;

class RankingsController extends Controller
{
    // List all rankings (optionally, you can filter by student_id or user)
    public function index(Request $request)
    {
        // Optionally filter by student_id query param
        $studentId = $request->query('student_id');

        if ($studentId) {
            $rankings = Ranking::where('student_id', $studentId)->get();
        } else {
            $rankings = Ranking::all();
        }

        return response()->json($rankings);
    }

    // Store a new ranking
    public function store(Request $request)
    {
        $user = Auth::user();
        
        \Log::info($user);

        $student_id = $user->student->student_id;

        $data = $request->all();

        // Validate input as an array of rankings
        $validator = Validator::make($request->all(), [
            '*.residency_id' => ['required', 'exists:residencies,id'],
            '*.position'     => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create rankings
        $createdRankings = [];
        foreach ($data as $rankingData) {
            $createdRankings[] = Ranking::create([
                'student_id' => $student_id,
                'residency_id' => $rankingData['residency_id'],
                'position' => $rankingData['position'],
            ]);
        }

        return response()->json([
            'message' => 'Rankings created successfully',
            'rankings' => $createdRankings
        ], 201);
    }
}
