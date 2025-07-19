<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiToolsController;

Route::post('/analyze-code', [AiToolsController::class, 'analyzeCode']);
Route::post('/save-analysis', [AiToolsController::class, 'saveAnalysis']);

