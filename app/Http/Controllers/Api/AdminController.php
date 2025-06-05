<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Company;
use App\Models\Residency;
use App\Models\Assignment;
use App\Services\ScoringService;

class AdminController extends Controller
{
    protected $scoringService;

    public function __construct(ScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Get all students
     */
    public function getAllStudents()
    {
        // Get all students with their users
        $students = Student::with('user')->get();

        // Get all student_ids from the collection
        $studentIds = $students->pluck('student_id')->toArray();

        // Find all student_ids that have assignments
        $assignedStudentIds = Assignment::whereIn('student_id', $studentIds)
            ->distinct()
            ->pluck('student_id')
            ->toArray();

        // Map students to add hasRanked property
        $students = $students->map(function ($student) use ($assignedStudentIds) {
            $student->hasRanked = in_array($student->student_id, $assignedStudentIds);
            return $student;
        });

        return response()->json([
            'students' => $students
        ], 200);
    }

    /**
     * Get all companies
     */
    public function getAllCompanies()
    {
        // Load companies with count of residencies
        $companies = Company::withCount('residencies')->get();

        // Get all residency IDs across companies to check assignments
        $residencyIds = Residency::pluck('id')->toArray();

        // Find residency IDs which have assignments
        $assignedResidencyIds = Assignment::whereIn('residency_id', $residencyIds)
            ->pluck('residency_id')
            ->unique()
            ->toArray();

        // Map companies to add capacity and hasRanked
        $companies = $companies->map(function ($company) use ($assignedResidencyIds) {
            $capacity = $company->residencies_count;
            
            // Check if any residency of this company has an assignment
            $hasRanked = $company->residencies->pluck('id')->intersect($assignedResidencyIds)->isNotEmpty();

            return [
                'company_id' => $company->company_id,
                'user_id' => $company->user_id,
                'company_name' => $company->company_name,
                'capacity' => $capacity,
                'hasRanked' => $hasRanked,
            ];
        });

        return response()->json([
            'companies' => $companies
        ], 200);
    }

    public function generateStudentScores() {
        $this->scoringService->calculateScoresForAllStudents();

        return response()->json([
            'message' => 'Student scores processed and assigned',
        ], 200);
    }
}
