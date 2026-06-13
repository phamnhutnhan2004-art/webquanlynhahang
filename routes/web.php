<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LiveOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', function (string $path) {
    $basePath = realpath(storage_path('app/public'));
    $fullPath = realpath($basePath.DIRECTORY_SEPARATOR.$path);

    abort_unless($basePath && $fullPath && str_starts_with($fullPath, $basePath) && is_file($fullPath), 404);

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.public');

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
    Route::post('/categories', [DashboardController::class, 'storeCategory'])->name('categories.store');
    Route::post('/products', [DashboardController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [DashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/tables', [DashboardController::class, 'storeTable'])->name('tables.store');
    Route::post('/menu-galleries', [DashboardController::class, 'storeMenuGallery'])->name('menu-galleries.store');
    Route::delete('/menu-galleries/{menuGallery}', [DashboardController::class, 'destroyMenuGallery'])->name('menu-galleries.destroy');
    Route::post('/gallery-images', [DashboardController::class, 'storeGalleryImage'])->name('gallery-images.store');
    Route::delete('/gallery-images/{galleryImage}', [DashboardController::class, 'destroyGalleryImage'])->name('gallery-images.destroy');
    Route::get('/{section}', [DashboardController::class, 'adminSection'])
        ->whereIn('section', ['employees', 'products', 'categories', 'tables', 'orders', 'menu-galleries', 'gallery-images', 'stats'])
        ->name('section');
});

Route::middleware(['auth', 'staff'])->prefix('nhan-vien')->name('staff.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
    Route::get('/bep', [LiveOrderController::class, 'kitchen'])->name('kitchen');
    Route::get('/thu-ngan', [LiveOrderController::class, 'cashier'])->name('cashier');
    Route::get('/live/orders', [LiveOrderController::class, 'stream'])->name('live-orders.stream');
    Route::patch('/dat-ban/{reservation}', [DashboardController::class, 'updateReservationStatus'])->name('reservations.update-status');
    Route::patch('/don-hang/{order}', [DashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
});

Route::middleware(['auth', 'customer'])->prefix('khach-hang')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
    Route::post('/dat-ban', [DashboardController::class, 'reserve'])->name('reservations.store');
});
