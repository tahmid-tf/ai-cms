<?php

use App\Http\Controllers\ExportSharingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('dashboard');
    } else if (auth()->user()->hasRole('editor')) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard');
});

Route::middleware(['auth', 'role:editor'])->group(function () {
    Route::view('/editor/dashboard', 'editor.dashboard');
});

Route::middleware(['auth', 'role:viewer'])->group(function () {
    Route::view('/viewer/dashboard', 'viewer.dashboard');
});

Route::get('/content/{slug}', [ExportSharingController::class, 'publicShow'])->name('content.public');

// --------------------------- Admin Dashboards ---------------------------

require __DIR__ . '/auth.php';
// require __DIR__ . '/admin.php';
