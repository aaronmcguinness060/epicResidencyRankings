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
        $residencies = Residency::all();
        return response()->json($residencies);
    }

    // Create a new residency/job
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'        => ['required', 'exists:companies,company_id'],
            'salary'            => ['required', 'integer', 'min:0', 'max:9999'],
            'description'       => ['nullable', 'string', 'max:255'],
            'line_manager_name' => ['nullable', 'string', 'max:100'],
            'line_manager_email'=> ['nullable', 'email', 'max:100'],
            'title'             => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $residency = Residency::create($validator->validated());

        return response()->json([
            'message' => 'Residency (job) created successfully.',
            'residency' => $residency
        ], 201);
    }
}
