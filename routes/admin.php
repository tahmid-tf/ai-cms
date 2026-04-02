<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AIContentController;
use App\Http\Controllers\AIContentEditController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);

    });

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/ai-content', [AIContentController::class, 'index'])->name('ai.content');
    Route::post('/admin/ai-content/generate', [AIContentController::class, 'generate'])->name('ai.content.generate');
    Route::post('/admin/ai-content/save', [AIContentController::class, 'save'])->name('ai.content.save');
    Route::get('/admin/ai-contents-list', [AIContentController::class, 'list'])->name('ai.content.list');
    Route::delete('/contents/{id}', [AIContentController::class, 'destroy'])->name('contents.destroy');
    Route::put('/contents/{id}', [AIContentController::class, 'update'])->name('contents.update');

    // -------------------------------------- ai edit content --------------------------------------

    Route::get('/ai/editor', [AIContentEditController::class, 'editorPage'])->name('ai_editor.editor');
    Route::post('/ai/editor', [AIContentEditController::class, 'processEditor']);
    Route::get('/admin/ai-editor-list', [AIContentEditController::class, 'list'])->name('ai_editor.list');
    Route::put('/content-edits/{id}', [AIContentEditController::class, 'update'])->name('ai_editor.update');
    Route::delete('/content-edits/{id}', [AIContentEditController::class, 'destroy'])->name('ai_editor.destroy');
});
