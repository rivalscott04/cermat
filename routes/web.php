<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\KecermatanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SoalKecermatanController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});


//Routes Harga Paket
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
// Routes for Tes Kecermatan



// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('post.register');;
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/trial', function () {
    return view('kecermatan.trial');
})->name('trial');

// Subscription Routes
Route::post('/subscription/process', [SubscriptionController::class, 'process'])->name('subscription.process');
Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
Route::get('/subscription/check', [SubscriptionController::class, 'check'])->name('subscription.check');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{userId}', [UserController::class, 'show'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // API routes for location data
    Route::get('/api/provinces', [UserController::class, 'getProvinces']);
    Route::get('/api/regencies/{provinceId}', [UserController::class, 'getRegencies']);

    Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');


    Route::middleware(['subscription'])->group(function () {
        Route::get('/tes-kecermatan', [KecermatanController::class, 'index'])->name('kecermatan');
        Route::post('/kecermatan/generate', [KecermatanController::class, 'generateKarakter'])->name('kecermatan.generateKarakter');
        Route::get('/tes-kecermatan/soal', [SoalKecermatanController::class, 'index'])->name('kecermatan.soal');
        Route::post('/tes-kecermatan/next-soal', [SoalKecermatanController::class, 'getNextSoal'])->name('kecermatan.nextSoal');
        Route::post('/tes-kecermatan/simpan-hasil', [SoalKecermatanController::class, 'simpanHasil'])->name('kecermatan.simpanHasil');
        Route::get('/tes-kecermatan/hasil', [SoalKecermatanController::class, 'hasilTes'])->name('kecermatan.hasil');
    });

    Route::get('/tes-kecermatan/riwayat/{userId}', [KecermatanController::class, 'riwayat'])->name('kecermatan.riwayat');
    Route::get('/tes-kecermatan/detail/{id}', [KecermatanController::class, 'detailTes'])->name('kecermatan.detail');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'userList'])->name('users.index');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
    Route::get('/subscriptions', [AdminController::class, 'subscriptionList'])->name('subscriptions.index');
    Route::get('/revenue', [AdminController::class, 'revenueReport'])->name('revenue.report');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
});
