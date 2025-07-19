<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiToolsController;

Route::prefix('ai-tools')->name('ai-tools.')->group(function () {
    Route::get('/dashboard', [AiToolsController::class, 'dashboard'])->name('dashboard');
});
