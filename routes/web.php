<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.store');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.store');
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/{section}', [DashboardController::class, 'adminSection'])
        ->whereIn('section', ['employees', 'products', 'categories', 'tables', 'orders', 'stats'])
        ->name('section');
});

Route::middleware(['auth', 'staff'])->prefix('nhan-vien')->name('staff.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
});

Route::middleware(['auth', 'customer'])->prefix('khach-hang')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
});
