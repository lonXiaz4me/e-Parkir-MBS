<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController; // FIX #18
use App\Http\Controllers\ProfileController; // Added for Profile Management
use App\Http\Controllers\SettingsController; // Tetapan page
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {

    // --- Auth ---
    Route::get('/', function () { return view('guest.login'); })->name('welcome');

    // ── FIX #2: Throttle login attempts — max 10 per minute per IP.
    Route::post('/', [UserController::class, 'authenticate'])
        ->middleware('throttle:10,1')
        ->name('login');

    // --- Register ---
    Route::get('/register', [UserController::class, 'index'])->name('register');

    // ── FIX #2: Throttle registration — max 5 per minute per IP.
    Route::post('/register', [UserController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('register.store');

    // --- Password Reset (OTP) ---
    Route::get('/forgot-password', [UserController::class, 'showLinkRequestForm'])
        ->name('password.otp.request');

    // ── FIX #2: Throttle OTP send — max 3 per minute per IP.
    Route::post('/forgot-password/send-otp', [UserController::class, 'sendOtp'])
        ->middleware('throttle:3,1')
        ->name('password.otp.send');

    // ── FIX #2: Throttle OTP verify — max 5 per minute per IP.
    Route::post('/forgot-password/verify-otp', [UserController::class, 'verifyOtp'])
        ->middleware('throttle:5,1')
        ->name('password.otp.verify');

    Route::post('/forgot-password/reset-with-otp', [UserController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.otp.resetSubmit');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Auth ---
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    // Throttled password changes (max 3 updates per minute per IP) to prevent password manipulation abuse
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('throttle:3,1')
        ->name('profile.password');

    // --- Settings (Tetapan) ---
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // --- Application ---
    Route::get('/application', [ApplicationController::class, 'index'])->name('application');
    Route::post('/application', [ApplicationController::class, 'store'])->name('application.store');
    Route::get('/application/camera-capture', [ApplicationController::class, 'show'])->name('camera.capture');
    Route::get('/application/download/{appNo}', [ApplicationController::class, 'download'])->name('application.download');

    // --- Payment ---
    Route::get('/payment/receipt/{id}', [PaymentController::class, 'receipt'])->name('payment.receipt');
    Route::get('/payment/{id?}', [PaymentController::class, 'index'])->name('payment.index');

    // ── FIX #2: Throttle payment submissions — max 5 per minute per IP.
    Route::post('/payment', [PaymentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('payment.store');

    // --- Notifications ---
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // routes/web.php — outside the 'auth' middleware, gateway can't send your session cookie
    Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
});