<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
//use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ResidenciesController;
use App\Http\Controllers\Api\RankingsController;

Route::post('/register', [RegisterController::class, 'register']);
//Route::post('/login', [LoginController::class, 'login']);


Route::get('/get-residencies', [ResidenciesController::class, 'index']);
Route::post('/create-residency', [ResidenciesController::class, 'store']);

Route::get('/get-rankings', [RankingsController::class, 'index']);
Route::post('/submit-rankings', [RankingsController::class, 'store']);