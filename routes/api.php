<?php

use App\Http\Controllers\Api\OffersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ResidenciesController;
use App\Http\Controllers\Api\RankingsController;
use App\Http\Controllers\Api\AdminController;

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/get-residencies', [ResidenciesController::class, 'index']);
Route::post('/create-residency', [ResidenciesController::class, 'store']);

Route::get('/get-rankings', [RankingsController::class, 'index']);
Route::post('/submit-rankings', [RankingsController::class, 'store']);

Route::get('/get-offers', [OffersController::class, 'index']);
Route::post('/assign-offers', [OffersController::class, 'assignOffers']);
Route::post('/accept-student-offers', [OffersController::class, 'acceptStudentOffers']);

Route::get('/admin/students', [AdminController::class, 'getAllStudents']);
Route::get('/admin/companies', [AdminController::class, 'getAllCompanies']);

Route::post('generate-student-scores', [AdminController::class, 'generateStudentScores']);

Route::get('check-has-accepted-offers', [OffersController::class, 'checkAcceptedOffers']);
Route::get('check-my-offers', [OffersController::class, 'checkMyOffers']);