<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Residency;

class ResidenciesController extends Controller
{
    // Get all residencies
    public function index()
    {
        $residencies = Residency::with('company')->get();

        $formatted = $residencies->map(function ($residency) {
            return [
                'id' => $residency->id,
                'company_id' => $residency->company_id,
                'company_name' => $residency->company->company_name ?? null,
                'salary' => $residency->salary,
                'description' => $residency->description,
                'created_at' => $residency->created_at,
                'updated_at' => $residency->updated_at,
            ];
        });

        return response()->json($formatted);
    }

    // Create a new residency/job
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'  => ['required', 'exists:companies,company_id'],
            'salary'      => ['required', 'integer', 'min:0', 'max:9999'],
            'description' => ['nullable', 'string', 'max:255'],
            'count'       => ['sometimes', 'integer', 'min:1', 'max:100'] // Optional count param
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $count = $validated['count'] ?? 1; // Default to 1 job if not provided

        $createdResidencies = [];

        for ($i = 0; $i < $count; $i++) {
            $residency = Residency::create([
                'company_id'  => $validated['company_id'],
                'salary'      => $validated['salary'],
                'description' => $validated['description'] ?? null,
            ]);

            $createdResidencies[] = $residency;
        }

        return response()->json([
            'message'    => "$count residency job(s) created successfully.",
            'residencies' => $createdResidencies
        ], 201);
    }
}
