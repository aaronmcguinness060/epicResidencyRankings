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

    public function store(Request $request)
    {
        $user = Auth::user();
        $studentId = $user->student->student_id;
        \Log::info($studentId);

        // Validate incoming rankings data like before...

        // Save rankings data (or replace old rankings for this student)
        // You might want to first delete existing rankings for this student or update them
        Ranking::where('student_id', $studentId)->delete();

        foreach ($request->all() as $rankingData) {
            Ranking::create([
                'student_id' => $studentId,
                'residency_id' => $rankingData['residency_id'],
                'position' => $rankingData['position'],
            ]);
        }

        return response()->json([
            'message' => 'Rankings processed and residencies assigned',
        ], 200);
    }
}
