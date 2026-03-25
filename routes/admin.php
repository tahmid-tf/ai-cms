<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);

    });

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/ai-content', [App\Http\Controllers\AIContentController::class, 'index'])->name('ai.content');
    Route::post('/admin/ai-content/generate', [App\Http\Controllers\AIContentController::class, 'generate'])->name('ai.content.generate');
});
