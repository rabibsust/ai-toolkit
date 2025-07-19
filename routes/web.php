<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiToolsController;

Route::get('/', [AiToolsController::class, 'dashboard'])->name('dashboard');

Route::prefix('ai-tools')->name('ai-tools.')->group(function () {
    Route::get('/dashboard', [AiToolsController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [AiToolsController::class, 'history'])->name('history');
});
