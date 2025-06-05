<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Models\Residency;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Base validation
        $rules = [
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'user_type'     => ['required', 'in:0,1,2'],
            'linkedin_url'  => ['nullable', 'url', 'max:255'],
        ];

        if ($request->has('student_id')) {
            $rules['student_id'] = ['integer', 'digits:8'];
        }

        if ($request->has('company_name')) {
            $rules['company_name'] = ['string', 'max:50'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'user_type'     => $request->user_type,
            'linkedin_url'  => $request->linkedin_url ?? null,
        ]);

        $company = null;

        // Create related Student or Company record
        if ($user->user_type == 0) {
            $user->student()->create([
                'score' => '1', // In a real world application, use an API to retrieve students QCA from UL Academic Registry
                'student_id' => $request->student_id,
            ]);
        } elseif ($user->user_type == 1) {
            $company = $user->company()->create([
                'company_name' => $request->company_name,
            ]);
        }

        Auth::login($user);

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'company_id' => $company ? $company->id : null,
        ], 201);
    }
}
