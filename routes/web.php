<?php

use App\Http\Controllers\ExportSharingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    abort_unless(auth()->user()->hasAnyRole(['admin', 'editor', 'viewer']), 403);

    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/content/{slug}', [ExportSharingController::class, 'publicShow'])->name('content.public');

// --------------------------- Admin Dashboards ---------------------------

require __DIR__ . '/auth.php';
// require __DIR__ . '/admin.php';
