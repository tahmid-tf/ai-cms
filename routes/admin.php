<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AIContentController;
use App\Http\Controllers\AIContentEditController;
use App\Http\Controllers\AITranslationController;
use App\Http\Controllers\VersionControlController;
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

    // -------------------------------------- ai translation content --------------------------------------
    Route::get('/ai/translation', [AITranslationController::class, 'translationPage'])->name('ai_translation.index');
    Route::post('/ai/translation', [AITranslationController::class, 'processTranslation'])->name('ai_translation.process');
    Route::get('/admin/translation-list', [AITranslationController::class, 'list'])->name('ai_translation.list');
    Route::put('/translations/{id}', [AITranslationController::class, 'update'])->name('ai_translation.update');
    Route::delete('/translations/{id}', [AITranslationController::class, 'destroy'])->name('ai_translation.destroy');

    // -------------------------------------- version control & drafts --------------------------------------
    Route::get('/version-control', [VersionControlController::class, 'index'])->name('version_control.index');
    Route::post('/version-control', [VersionControlController::class, 'store'])->name('version_control.store');
    Route::get('/admin/version-control-list', [VersionControlController::class, 'list'])->name('version_control.list');
    Route::put('/version-control/{id}', [VersionControlController::class, 'update'])->name('version_control.update');
    Route::delete('/version-control/{id}', [VersionControlController::class, 'destroy'])->name('version_control.destroy');
    Route::get('/version-control/{id}/history', [VersionControlController::class, 'history'])->name('version_control.history');
    Route::post('/version-control/{contentId}/restore/{versionId}', [VersionControlController::class, 'restore'])->name('version_control.restore');
});
