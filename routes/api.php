<?php

use App\Http\Controllers\Api\OffersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ResidenciesController;
use App\Http\Controllers\Api\RankingsController;

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