<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes..
]);

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('pending', [ReportController::class, 'pendingReports'])->name('pending-reports');
    Route::get('reports', [ReportController::class, 'reports'])->name('reports');
    Route::put('reports/{id}/toggle', [ReportController::class, 'reportStatusToggle'])->name('report.toggle');
    Route::get('/schedule-pdfs/{unit}/{filename}', [ReportController::class, 'downloadPDF'])->name('download.pdf');

    // Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    // Route::post('users', [UserController::class, 'store'])->name('users.store');
    // Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::middleware(['isAdmin'])->group(function () {
        Route::resource('users', UserController::class)->except(['update']);
    });
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
});
