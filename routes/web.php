<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoalKecermatanController;
use App\Http\Controllers\KecermatanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});


//Routes Harga Paket
Route::get('/pricing', [PriceController::class, 'index'])->name('harga.index');

// Routes for Tes Kecermatan



// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('post.register');;
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Subscription Routes
Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::post('/subscription/process', [SubscriptionController::class, 'process'])->name('subscription.process');
Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
Route::get('/subscription/check', [SubscriptionController::class, 'check'])->name('subscription.check');

Route::middleware('auth')->group(function () {
    Route::get('/tes-kecermatan', [KecermatanController::class, 'index'])->name('kecermatan');
    Route::post('/kecermatan/generate', [KecermatanController::class, 'generateKarakter'])->name('kecermatan.generateKarakter');
    Route::get('/tes-kecermatan/soal', [SoalKecermatanController::class, 'index'])->name('kecermatan.soal');
    Route::post('/tes-kecermatan/next-soal', [SoalKecermatanController::class, 'getNextSoal'])->name('kecermatan.nextSoal');
    Route::post('/tes-kecermatan/simpan-hasil', [SoalKecermatanController::class, 'simpanHasil'])->name('kecermatan.simpanHasil');
    Route::get('/tes-kecermatan/hasil', [SoalKecermatanController::class, 'hasilTes'])->name('kecermatan.hasil');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'userList'])->name('users.index');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
    Route::get('/subscriptions', [AdminController::class, 'subscriptionList'])->name('subscriptions.index');
    Route::get('/revenue', [AdminController::class, 'revenueReport'])->name('revenue.report');
});
