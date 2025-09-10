<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\KecermatanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SoalKecermatanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\TryoutController;
use App\Http\Controllers\KategoriSoalController;

Route::get('/', function () {
    return view('welcome');
});


//Routes Harga Paket
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');

// Authentication Routes
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('post.register');
Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('payment/process/{transaction_id}', [SubscriptionController::class, 'process'])->name('payment.process');

Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('post.reset-password');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy');

Route::get('/trial', function () {
    return view('kecermatan.trial');
})->name('trial');

// Special route for leaving impersonation (outside admin middleware)
Route::get('/leave-impersonation', [App\Http\Controllers\ImpersonateController::class, 'leave'])->name('leave.impersonation');

// Route untuk subscription dan proses Midtrans
// Route::get('subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::get('subscription/process', [SubscriptionController::class, 'process'])->name('subscription.process');
Route::get('subscription/get-fee', [SubscriptionController::class, 'getFee'])->name('subscription.get-fee');
Route::post('subscription/notification', [SubscriptionController::class, 'notification'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('subscription.notification');
Route::get('subscription/finish', [SubscriptionController::class, 'finish'])->name('subscription.finish');
Route::get('subscription/unfinish', [SubscriptionController::class, 'unfinish'])->name('subscription.unfinish');
Route::get('subscription/error', [SubscriptionController::class, 'error'])->name('subscription.error');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{userId}', [UserController::class, 'show'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // API routes for location data
    Route::get('/api/provinces', [UserController::class, 'getProvinces']);
    Route::get('/api/regencies/{provinceId}', [UserController::class, 'getRegencies']);

    // Route::get('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/packages', [SubscriptionController::class, 'packages'])->name('subscription.packages');
    Route::get('/subscription/process/{package}', [SubscriptionController::class, 'processSubscription'])->name('subscription.process.packages');
    Route::get('/subscription/checkout/{package}', [SubscriptionController::class, 'showCheckout'])->name('subscription.checkout');
    Route::get('/test', [UserController::class, 'showTest'])->name('show.test');


    Route::middleware(['subscription'])->group(function () {
        //Tes Kecermatan Routes dengan middleware package tambahan
        Route::middleware(['check.package:kecermatan'])->group(function () {
            Route::get('/tes-kecermatan', [KecermatanController::class, 'index'])->name('kecermatan');
            Route::post('/kecermatan/generate', [KecermatanController::class, 'generateKarakter'])->name('kecermatan.generateKarakter');
            Route::get('/tes-kecermatan/soal', [SoalKecermatanController::class, 'index'])->name('kecermatan.soal');
            Route::post('/tes-kecermatan/next-soal', [SoalKecermatanController::class, 'getNextSoal'])->name('kecermatan.nextSoal');
            Route::post('/tes-kecermatan/simpan-hasil', [SoalKecermatanController::class, 'simpanHasil'])->name('kecermatan.simpanHasil');
            Route::get('/tes-kecermatan/hasil', [SoalKecermatanController::class, 'hasilTes'])->name('kecermatan.hasil');
        });

        // Tryout CBT Routes dengan middleware package tambahan
        Route::middleware(['check.package:tryout'])->group(function () {
            Route::get('/tryout', [TryoutController::class, 'userIndex'])->name('user.tryout.index');
            Route::post('/tryout', [TryoutController::class, 'userIndex'])->name('user.tryout.index.post');
            Route::get('/tryout/{tryout}/start', [TryoutController::class, 'start'])->name('user.tryout.start');
            Route::get('/{tryout}/restart', [TryoutController::class, 'restart'])->name('user.tryout.restart');
            Route::get('/tryout/{tryout}/work', [TryoutController::class, 'work'])->name('user.tryout.work');
            Route::get('{tryout}/remaining-time', [TryoutController::class, 'getRemainingTime'])->name('remaining-time');
            Route::post('/tryout/{tryout}/reset-answer', [TryoutController::class, 'resetAnswer'])->name('user.tryout.reset-answer');
            Route::post('/tryout/{tryout}/submit-answer', [TryoutController::class, 'submitAnswer'])->name('user.tryout.submit-answer');
            Route::post('/tryout/{tryout}/toggle-mark', [TryoutController::class, 'toggleMark'])->name('user.tryout.toggle-mark');
            Route::get('/debug-seed/{tryout}', [TryoutController::class, 'debugSessionSeed'])->name('debug.session.seed');
            Route::get('/tryout/{tryout}/finish', [TryoutController::class, 'finish'])->name('user.tryout.finish');
        });
    });

    Route::get('/tes-kecermatan/riwayat/{userId}', [KecermatanController::class, 'riwayat'])->name('kecermatan.riwayat');
    Route::get('/tes-kecermatan/detail/{id}', [KecermatanController::class, 'detailTes'])->name('kecermatan.detail');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'userList'])->name('users.index');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('users.detail');
    Route::get('/subscriptions', [AdminController::class, 'subscriptionList'])->name('subscriptions.index');
    Route::get('/revenue', [AdminController::class, 'revenueReport'])->name('revenue.report');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::get('/riwayat-tes', [AdminController::class, 'riwayatTes'])->name('riwayat.tes');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::put('/users/{user}/package', [AdminController::class, 'updatePackage'])->name('users.updatePackage');

    // Impersonate Routes (using custom controller for debugging)
    Route::get('/impersonate/take/{id}/{guardName?}', [App\Http\Controllers\ImpersonateController::class, 'take'])->name('impersonate');
    Route::get('/impersonate/leave', [App\Http\Controllers\ImpersonateController::class, 'leave'])->name('impersonate.leave');

    // CBT Admin Routes
    Route::resource('kategori', KategoriSoalController::class);
    Route::post('kategori/{kategori}/toggle-status', [KategoriSoalController::class, 'toggleStatus'])->name('kategori.toggle-status');

    Route::resource('soal', SoalController::class);
    Route::post('soal/upload-word', [SoalController::class, 'uploadWord'])->name('soal.upload-word');

    Route::resource('tryout', TryoutController::class);
});
