<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
Route::get('/tes-kecermatan/soal', [SoalKecermatanController::class, 'index'])->name('kecermatan.soal');
Route::post('/tes-kecermatan/next-soal', [SoalKecermatanController::class, 'getNextSoal'])->name('kecermatan.nextSoal');
Route::post('/tes-kecermatan/simpan-hasil', [SoalKecermatanController::class, 'simpanHasil'])->name('kecermatan.simpanHasil');
// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Subscription Routes
Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::post('/subscription/process', [SubscriptionController::class, 'process'])->name('subscription.process');
Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
Route::get('/subscription/check', [SubscriptionController::class, 'check'])->name('subscription.check');
// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
Route::get('/users', [AdminController::class, 'userList'])->name('users.index');
Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
Route::get('/subscriptions', [AdminController::class, 'subscriptionList'])->name('subscriptions.index');
Route::get('/revenue', [AdminController::class, 'revenueReport'])->name('revenue.report');
});