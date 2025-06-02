<?php

use Illuminate\Support\Facades\Route;

Route::post('/calculate-bonus', \App\Http\Controllers\BonusCalculationController::class);
