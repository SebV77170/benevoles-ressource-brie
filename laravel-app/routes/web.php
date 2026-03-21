<?php

use App\Http\Controllers\Admin\PlanningAdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::view('/', 'planning.dashboard')->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/profil', [VolunteerController::class, 'profile'])->name('volunteers.profile');
    Route::put('/profil', [VolunteerController::class, 'updateProfile'])->name('volunteers.profile.update');

    Route::get('/planning', [PlanningController::class, 'index'])->name('planning.index');
    Route::post('/planning/inscriptions', [PlanningController::class, 'register'])->name('planning.register');

    Route::get('/benevoles', [VolunteerController::class, 'index'])
        ->middleware('can:viewAny,App\\Models\\EventSlot')
        ->name('volunteers.index');

    Route::get('/admin/planning', [PlanningAdminController::class, 'index'])
        ->middleware('can:viewAny,App\\Models\\EventSlot')
        ->name('admin.planning.index');

    Route::view('/pending', 'auth.pending')->name('pending.notice');
});
