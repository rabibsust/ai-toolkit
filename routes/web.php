<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiToolsController;

Route::get('/', function () {
    return inertia('AiTools/Dashboard');
});

Route::prefix('ai-tools')->group(function () {
    Route::get('/dashboard', [AiToolsController::class, 'dashboard']);
    Route::post('/analyze-code', [AiToolsController::class, 'analyzeCode']);
});
