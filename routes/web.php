<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAiChatbotController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminPaymentMethodController;
use App\Http\Controllers\AdminThemeSettingController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomePartyController;
use App\Http\Controllers\KitchenOrderController;
use App\Http\Controllers\LiveOrderController;
use App\Http\Controllers\MyAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', function (string $path) {
    $basePath = realpath(storage_path('app/public'));
    $fullPath = realpath($basePath.DIRECTORY_SEPARATOR.$path);

    abort_unless($basePath && $fullPath && str_starts_with($fullPath, $basePath) && is_file($fullPath), 404);

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.public');

Route::get('/', [DashboardController::class, 'home'])->name('home');
Route::get('/gioi-thieu', [DashboardController::class, 'about'])->name('about');
Route::get('/thuc-don', [DashboardController::class, 'menu'])->name('menu');
Route::get('/hinh-anh', [DashboardController::class, 'gallery'])->name('gallery');
Route::get('/lien-he', [ContactController::class, 'show'])->name('contact');
Route::post('/lien-he', [ContactController::class, 'store'])->name('contact.store');
Route::get('/dat-ban', [DashboardController::class, 'booking'])->name('reservations.create');
Route::post('/dat-ban', [DashboardController::class, 'reserve'])->name('reservations.store');
Route::get('/dat-tiec-tai-nha', [HomePartyController::class, 'show'])->name('home-parties.show');
Route::post('/dat-tiec-tai-nha', [HomePartyController::class, 'store'])->name('home-parties.store');
Route::get('/thuc-don/{categorySlug}', [DashboardController::class, 'menuCategory'])->name('menu.category');
Route::get('/mon-an/{product:slug}', [DashboardController::class, 'productDetail'])->name('products.show');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.store');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.store');
    Route::get('/xac-thuc-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/xac-thuc-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/gui-lai-ma-xac-thuc', [AuthController::class, 'resendVerificationOtp'])->name('verification.resend');
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan-cua-toi', [MyAccountController::class, 'show'])->name('account.show');
    Route::put('/tai-khoan-cua-toi', [MyAccountController::class, 'updateProfile'])->name('account.update');
    Route::put('/tai-khoan-cua-toi/mat-khau', [MyAccountController::class, 'updatePassword'])->name('account.password');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::post('/accounts', [AdminAccountController::class, 'store'])->name('accounts.store');
    Route::put('/accounts/{user}', [AdminAccountController::class, 'update'])->name('accounts.update');
    Route::patch('/accounts/{user}/status', [AdminAccountController::class, 'updateStatus'])->name('accounts.status');
    Route::put('/accounts/{user}/password', [AdminAccountController::class, 'updatePassword'])->name('accounts.password');
    Route::post('/accounts/{user}/reset-password', [AdminAccountController::class, 'resetPassword'])->name('accounts.reset-password');
    Route::delete('/accounts/{user}', [AdminAccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/categories', [DashboardController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [DashboardController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [DashboardController::class, 'destroyCategory'])->name('categories.destroy');
    Route::post('/products', [DashboardController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [DashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/tables', [DashboardController::class, 'storeTable'])->name('tables.store');
    Route::put('/tables/{table}', [DashboardController::class, 'updateTable'])->name('tables.update');
    Route::patch('/tables/{table}/status', [DashboardController::class, 'updateTableStatus'])->name('tables.status');
    Route::delete('/tables/{table}', [DashboardController::class, 'destroyTable'])->name('tables.destroy');
    Route::post('/menu-galleries', [DashboardController::class, 'storeMenuGallery'])->name('menu-galleries.store');
    Route::delete('/menu-galleries/{menuGallery}', [DashboardController::class, 'destroyMenuGallery'])->name('menu-galleries.destroy');
    Route::post('/gallery-images', [DashboardController::class, 'storeGalleryImage'])->name('gallery-images.store');
    Route::delete('/gallery-images/{galleryImage}', [DashboardController::class, 'destroyGalleryImage'])->name('gallery-images.destroy');
    Route::post('/payment-methods', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::put('/payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
    Route::put('/ai-chatbot', [AdminAiChatbotController::class, 'update'])->name('ai-chatbot.update');
    Route::post('/ai-chatbot/test', [AdminAiChatbotController::class, 'test'])->name('ai-chatbot.test');
    Route::put('/theme-settings', [AdminThemeSettingController::class, 'update'])->name('theme-settings.update');
    Route::post('/theme-settings/reset', [AdminThemeSettingController::class, 'reset'])->name('theme-settings.reset');
    Route::put('/theme-settings/pages/{pageKey}', [AdminThemeSettingController::class, 'updatePage'])->name('theme-settings.pages.update');
    Route::post('/theme-settings/pages/{pageKey}/reset', [AdminThemeSettingController::class, 'resetPage'])->name('theme-settings.pages.reset');
    Route::put('/auth-interface', [AdminThemeSettingController::class, 'updateAuthPage'])->name('auth-interface.update');
    Route::post('/auth-interface/reset', [AdminThemeSettingController::class, 'resetAuthPage'])->name('auth-interface.reset');
    Route::patch('/reservations/{reservation}/status', [DashboardController::class, 'updateReservationStatus'])->name('reservations.update-status');
    Route::patch('/home-parties/{homeParty}', [HomePartyController::class, 'update'])->name('home-parties.update');
    Route::get('/{section}', [DashboardController::class, 'adminSection'])
        ->whereIn('section', ['accounts', 'employees', 'products', 'categories', 'tables', 'orders', 'reservations', 'home-parties', 'customers', 'payments', 'payment-methods', 'chatbot', 'ai-chatbot', 'theme-settings', 'auth-interface', 'menu-galleries', 'gallery-images', 'news', 'stats'])
        ->name('section');
});

Route::middleware(['auth', 'staff'])->prefix('nhan-vien')->name('staff.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
    Route::get('/bep', [LiveOrderController::class, 'kitchen'])->name('kitchen');
    Route::get('/thu-ngan', [LiveOrderController::class, 'cashier'])->name('cashier');
    Route::get('/live/orders', [LiveOrderController::class, 'stream'])->name('live-orders.stream');
    Route::get('/live/thong-bao-bep', [KitchenOrderController::class, 'notifications'])->name('kitchen-orders.notifications');
    Route::post('/don-hang/{order}/gui-bep', [KitchenOrderController::class, 'send'])->name('kitchen-orders.send');
    Route::get('/don-hang/{order}/thanh-toan', [BillController::class, 'checkout'])->name('orders.checkout');
    Route::post('/don-hang/{order}/thanh-toan', [BillController::class, 'store'])->name('orders.pay');
    Route::get('/hoa-don/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/hoa-don/{bill}/tai-xuong', [BillController::class, 'download'])->name('bills.download');
    Route::patch('/bep/mon/{item}', [KitchenOrderController::class, 'updateItemStatus'])->name('kitchen-items.update-status');
    Route::patch('/bep/{kitchenOrder}/phuc-vu', [KitchenOrderController::class, 'serve'])->name('kitchen-orders.serve');
    Route::patch('/dat-ban/{reservation}', [DashboardController::class, 'updateReservationStatus'])->name('reservations.update-status');
    Route::patch('/don-hang/{order}', [DashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
});

Route::middleware(['auth', 'customer'])->prefix('khach-hang')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
    Route::get('/don-hang/{order}/thanh-toan', [BillController::class, 'checkout'])->name('orders.checkout');
    Route::get('/hoa-don/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/hoa-don/{bill}/tai-xuong', [BillController::class, 'download'])->name('bills.download');
    Route::post('/dat-ban', [DashboardController::class, 'reserve'])->name('reservations.store');
});
