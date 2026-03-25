<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    /*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

    if (auth()->user()->hasRole('admin')) {
        return view('layouts.admin');
    } else if (auth()->user()->hasRole('editor')) {
        return "editor";
    } else {
        return "viewer";
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// return view('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard');
});

Route::middleware(['auth', 'role:editor'])->group(function () {
    Route::view('/editor/dashboard', 'editor.dashboard');
});

Route::middleware(['auth', 'role:viewer'])->group(function () {
    Route::view('/viewer/dashboard', 'viewer.dashboard');
});

// --------------------------- Admin Dashboards ---------------------------

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

    });

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
