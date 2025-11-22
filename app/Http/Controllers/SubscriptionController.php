<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Subscription;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout()
    {
        return view('subscription.checkout');
    }

    public function packages()
    {
        $startTime = microtime(true);
        $user = Auth::user();
        $packages = Package::ordered()->get();

        if ($user->package === 'lengkap') {

            // Tetap pakai logic optimasi untuk halaman status paket
            $packageLimits = $user->getPackageLimits();
            $maxTryouts = $packageLimits['max_tryouts'];

            $subscription = $user->subscriptions;
            $allowedCategories = $user->getAllowedCategories();
            $packageFeatures = $user->getPackageFeaturesDescription();
            $packageDisplayName = $user->getPackageDisplayName();

            $hasActiveSubscription = $user->hasActiveSubscription();
            $canAccessTryout = $user->canAccessTryout();
            $canAccessKecermatan = $user->canAccessKecermatan();
            $userPackage = $user->package;

            // Paket lengkap pasti punya status dan progress
            $paketLengkapStatus = $user->getPaketLengkapStatus();
            $paketLengkapProgress = $user->getPaketLengkapProgress();

            // Statistik user
            $userStatistics = $user->getUserStatistics();

            // Additional info
            $additionalInfo = [
                'subscription_start_date' => $subscription ? $subscription->created_at : null,
                'days_remaining' => $subscription && $subscription->end_date
                    ? max(0, now()->diffInDays($subscription->end_date, false))
                    : null,
                'total_questions_answered' => $userStatistics['total_questions_answered'] ?? 0,
                'total_tryouts_completed' => $userStatistics['total_tryouts'] ?? 0,
                'last_activity' => $userStatistics['last_activity'] ?? null
            ];

            // Logging waktu eksekusi
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            Log::info("User with paket lengkap redirected to status page in {$executionTime}ms (user {$user->id})");

            return view('subscription.user-package-status', [
                'user' => $user,
                'packageLimits' => $packageLimits,
                'subscription' => $subscription,
                'allowedCategories' => $allowedCategories,
                'maxTryouts' => $maxTryouts,
                'packageFeatures' => $packageFeatures,
                'packageDisplayName' => $packageDisplayName,
                'hasActiveSubscription' => $hasActiveSubscription,
                'canAccessTryout' => $canAccessTryout,
                'canAccessKecermatan' => $canAccessKecermatan,
                'userPackage' => $userPackage,
                'paketLengkapStatus' => $paketLengkapStatus,
                'paketLengkapProgress' => $paketLengkapProgress,
                'userStatistics' => $userStatistics,
                'additionalInfo' => $additionalInfo
            ]);
        }

        return view('subscription.packages', ['packages' => $packages]);
    }


    public function packagesAdmin()
    {
        // Halaman pembelian paket khusus untuk admin
        return view('subscription.packages');
    }

    public function expired()
    {
        return view('subscription.expired');
    }

    public function check()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        return response()->json([
            'has_active_subscription' => $user->hasActiveSubscription(),
            'subscription' => $subscription
        ]);
    }

    public function processSubscription(Request $request, $package)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // DEBUG: Log parameter yang diterima
            \Log::info('Package parameter received:', ['package' => $package, 'type' => gettype($package)]);

            // Cek apakah package ada di database
            $packageData = Package::where('id', $package)->first();
            \Log::info('Package data:', ['package' => $packageData ? $packageData->toArray() : 'NOT FOUND']);

            // Validasi dengan detail
            $packageExists = Package::where('id', $package)
                ->where('is_active', 1)
                ->first();

            \Log::info('Package exists check:', ['result' => $packageExists ? 'YES' : 'NO']);

            if (!$packageExists) {
                \Log::warning('Package not available:', [
                    'searched_id' => $package,
                    'all_active_packages' => Package::where('is_active', 1)->pluck('id', 'name')->toArray()
                ]);
                return redirect()->back()->with('error', 'Paket yang dipilih tidak tersedia.');
            }

            return redirect()->route('subscription.checkout', ['package' => $package]);
        } catch (\Exception $e) {
            \Log::error('Error processing subscription: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses paket berlangganan. Silakan coba lagi.');
        }
    }

    public function showCheckout($package)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            // Get package from database berdasarkan ID
            $selectedPackage = Package::where('id', $package)
                ->where('is_active', 1)
                ->first();

            if (!$selectedPackage) {
                return redirect()->route('subscription.packages')->with('error', 'Paket yang dipilih tidak tersedia.');
            }

            // Convert package to array format expected by view
            $packageData = [
                'id' => $selectedPackage->id,
                'name' => $selectedPackage->name,
                'description' => $selectedPackage->description,
                'price' => $selectedPackage->price,
                'old_price' => $selectedPackage->old_price,
                'label' => $selectedPackage->label,
                'features' => $selectedPackage->features,
                'duration' => $selectedPackage->duration_days ?? 30,
            ];

            return view('subscription.checkout', [
                'package' => $packageData,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing checkout: ' . $e->getMessage());
            return redirect()->route('subscription.packages')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function process(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }

            $packageName = $request->input('package');
            if (!$packageName) {
                throw new \Exception('Paket tidak dipilih');
            }

            $package = Package::where('name', 'like', '%' . $packageName . '%')
                ->active()
                ->first();

            if (!$package) {
                throw new \Exception('Paket tidak ditemukan');
            }

            // Buat subscription baru atau update pending
            $subscription = Subscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'payment_status' => 'pending'
                ],
                [
                    'transaction_id' => 'ORD-' . time() . '-' . $user->id,
                    'package_id' => $package->id,
                    'amount_paid' => $package->price,
                    'start_date' => now(),
                    'end_date' => now()->addDays($package->duration ?? 30),
                ]
            );

            // ==== TRIPAY REQUEST ====
            $merchantRef = $subscription->transaction_id;

            $payload = [
                'method' => 'QRIS', // ganti sesuai kebutuhan
                'merchant_ref' => $merchantRef,
                'amount' => $subscription->amount_paid,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone_number,
                'callback_url' => env('TRIPAY_CALLBACK_URL'),
                'return_url' => route('subscription.finish'),
                'order_items' => [
                    [
                        'sku' => $this->generatePackageId($package->name),
                        'name' => $package->name,
                        'price' => $subscription->amount_paid,
                        'quantity' => 1
                    ]
                ]
            ];

            $privateKey = env('TRIPAY_PRIVATE_KEY');
            $payload['signature'] = hash_hmac('sha256', $merchantRef . $subscription->amount_paid, $privateKey);

            // Kirim ke Tripay
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . env('TRIPAY_API_KEY')
            ])->post('https://tripay.co.id/api-sandbox/transaction/create', $payload);


            $result = $response->json();

            if (!$result || ($result['success'] ?? false) === false) {
                Log::error('Tripay Error: ' . json_encode($result));
                throw new \Exception('Gagal membuat transaksi Tripay.');
            }

            // Simpan checkout URL & reference
            $subscription->update([
                'payment_details' => json_encode($result['data']),
            ]);

            return redirect($result['data']['checkout_url']);
        } catch (\Exception $e) {
            Log::error('Tripay Process Error: ' . $e->getMessage());

            return redirect()->route('subscription.packages')
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Generate package ID dari nama package
     */
    private function generatePackageId($packageName)
    {
        return 'PKG_' . strtoupper(str_replace(' ', '_', $packageName));
    }

    /**
     * Get enabled payment methods berdasarkan package
     * Bisa dikustomisasi sesuai package tertentu
     */
    private function getEnabledPaymentMethods($package)
    {
        $defaultMethods = [
            'credit_card',
            'bca_va',
            'bni_va',
            'bri_va',
            'mandiri_va',
            'permata_va',
            'gopay',
            'shopeepay',
            'qris'
        ];

        // Contoh: Jika package memiliki kolom payment_methods, gunakan itu
        if ($package->payment_methods) {
            return json_decode($package->payment_methods, true) ?: $defaultMethods;
        }

        return $defaultMethods;
    }

    public function notification(Request $request)
    {
        $data = $request->all();
        Log::info('Tripay Callback:', $data);

        $signature = hash_hmac(
            'sha256',
            $data['merchant_ref'] . $data['status'] . $data['reference'],
            env('TRIPAY_PRIVATE_KEY')
        );

        if ($signature !== $data['signature']) {
            Log::error('Invalid Tripay signature');
            return response()->json(['success' => false], 400);
        }

        $subscription = Subscription::where('transaction_id', $data['merchant_ref'])->first();
        if (!$subscription) {
            Log::error('Subscription not found for Tripay callback');
            return response()->json(['success' => false], 404);
        }

        switch ($data['status']) {
            case 'PAID':
                $subscription->update([
                    'payment_status' => 'paid',
                    'payment_method' => $data['payment_method'],
                    'payment_details' => json_encode($data)
                ]);
                $subscription->user()->update(['is_active' => true]);
                break;

            case 'EXPIRED':
            case 'FAILED':
                $subscription->update([
                    'payment_status' => 'failed',
                    'payment_details' => json_encode($data)
                ]);
                break;
        }

        return response()->json(['success' => true]);
    }


    public function finish(Request $request)
    {
        return redirect()->route('user.profile', ['userId' => Auth::user()->id])
            ->with('message', 'Pembayaran berhasil, anda bisa melakukan tes');
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('subscription.checkout')
            ->with('warning', 'Pembayaran belum selesai');
    }

    public function error(Request $request)
    {
        return redirect()->route('subscription.checkout')
            ->with('error', 'Pembayaran gagal');
    }
}
