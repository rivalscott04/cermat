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
        try {
            $startTime = microtime(true);
            $user = Auth::user();
            $packages = Package::ordered()->get();

            if ($user->package === 'lengkap') {
                try {
                    // Tetap pakai logic optimasi untuk halaman status paket
                    $packageLimits = [];
                    $maxTryouts = 20;
                    try {
                        $packageLimits = $user->getPackageLimits();
                        $maxTryouts = $packageLimits['max_tryouts'] ?? 20;
                    } catch (\Throwable $e) {
                        Log::error('Error getting package limits', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                    }

                    $subscription = $user->subscriptions;
                    
                    $allowedCategories = [];
                    try {
                        $allowedCategories = $user->getAllowedCategories();
                    } catch (\Throwable $e) {
                        Log::error('Error getting allowed categories', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                    }
                    
                    $packageFeatures = [];
                    $packageDisplayName = 'Paket Lengkap';
                    try {
                        $packageFeatures = $user->getPackageFeaturesDescription();
                        $packageDisplayName = $user->getPackageDisplayName();
                    } catch (\Throwable $e) {
                        Log::error('Error getting package features/display name', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                    }

                    $hasActiveSubscription = $user->hasActiveSubscription();
                    $canAccessTryout = $user->canAccessTryout();
                    $canAccessKecermatan = $user->canAccessKecermatan();
                    $userPackage = $user->package;

                    // Paket lengkap pasti punya status dan progress - dengan error handling
                    $paketLengkapStatus = null;
                    $paketLengkapProgress = 0;
                    try {
                        $paketLengkapStatus = $user->getPaketLengkapStatus();
                        $paketLengkapProgress = $user->getPaketLengkapProgress();
                    } catch (\Throwable $e) {
                        Log::error('Error getting paket lengkap status/progress', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }

                    // Statistik user - dengan error handling
                    $userStatistics = [];
                    try {
                        $userStatistics = $user->getUserStatistics();
                    } catch (\Throwable $e) {
                        Log::error('Error getting user statistics', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id
                        ]);
                        $userStatistics = [
                            'total_tryouts' => 0,
                            'total_questions_answered' => 0,
                            'total_kecermatan_tests' => 0,
                            'last_activity' => null,
                            'last_test_date' => null
                        ];
                    }

                    // Additional info - dengan null safety
                    $additionalInfo = [
                        'subscription_start_date' => ($subscription && $subscription->created_at) ? $subscription->created_at : null,
                        'days_remaining' => ($subscription && $subscription->end_date)
                            ? max(0, now()->diffInDays($subscription->end_date, false))
                            : null,
                        'total_questions_answered' => isset($userStatistics['total_questions_answered']) ? (int)$userStatistics['total_questions_answered'] : 0,
                        'total_tryouts_completed' => isset($userStatistics['total_tryouts']) ? (int)$userStatistics['total_tryouts'] : 0,
                        'last_activity' => isset($userStatistics['last_activity']) ? $userStatistics['last_activity'] : null
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
                } catch (\Throwable $e) {
                    Log::error('Error loading paket lengkap status page', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id,
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    
                    // Fallback: tampilkan halaman packages biasa dengan error message
                    return view('subscription.packages', [
                        'packages' => $packages,
                        'error' => 'Terjadi kesalahan saat memuat status paket lengkap. Silakan coba lagi nanti.'
                    ]);
                }
            }

            return view('subscription.packages', ['packages' => $packages]);
        } catch (\Throwable $e) {
            Log::error('Error in packages method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return view('subscription.packages', [
                'packages' => Package::ordered()->get(),
                'error' => 'Terjadi kesalahan saat memuat halaman paket.'
            ]);
        }
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
                ->where('is_active', 1)
                ->first();

            if (!$package) {
                throw new \Exception('Paket tidak ditemukan');
            }

            // Ambil dari config, bukan env
            $apiKey = config('tripay.api_key');
            $privateKey = config('tripay.private_key');

            \Log::info('Tripay Config Check Full:', [
                'api_key' => substr($apiKey, 0, 10) . '***',
                'private_key' => substr($privateKey, 0, 10) . '***',
            ]);

            if (!$apiKey || !$privateKey) {
                throw new \Exception('Tripay configuration not found in config/tripay.php');
            }

            // Buat subscription
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
                    'end_date' => now()->addDays($package->duration_days ?? 30),
                    // Pastikan kolom payment_details selalu punya nilai awal
                    'payment_details' => json_encode([]),
                ]
            );

            $merchantRef = $subscription->transaction_id;

            $payload = [
                'method' => $request->input('payment_method', 'BRIVA'),
                'merchant_ref' => $merchantRef,
                'amount' => intval($subscription->amount_paid),
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone_number ?? '08123456789',
                'callback_url' => config('tripay.callback_url'),
                'return_url' => route('subscription.finish'),
                'order_items' => [
                    [
                        'sku' => $this->generatePackageId($package->name),
                        'name' => $package->name,
                        'price' => intval($subscription->amount_paid),
                        'quantity' => 1
                    ]
                ]
            ];

            $merchantCode = config('tripay.merchant_code');
            $amount = intval($subscription->amount_paid);

            $data = $merchantCode . $merchantRef . $amount;

            $signature = hash_hmac('sha256', $data, $privateKey);

            $payload['signature'] = $signature;
            $payload['merchant_code'] = $merchantCode;


            \Log::info('Tripay signature generated', [
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'data_string' => $data,
                'signature' => $signature,
                'user_id' => $user->id,
            ]);

            \Log::info('Sending transaction to Tripay', [
                'merchant_ref' => $merchantRef,
                'amount' => intval($subscription->amount_paid),
                'package_id' => $package->id,
                'user_id' => $user->id,
                'timestamp' => now()->toDateTimeString(),
            ]);

            // Kirim ke Tripay
            $response = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://tripay.co.id/api/transaction/create', $payload);

            // Log HTTP response untuk debugging
            $result = $response->json();
            \Log::info('Tripay HTTP response received', [
                'status_code' => $response->status(),
                'success' => $response->ok(),
                'merchant_ref' => $merchantRef,
                'response_success' => $result['success'] ?? false,
                'response_message' => $result['message'] ?? 'N/A',
                'timestamp' => now()->toDateTimeString(),
            ]);

            if (!$response->successful() || !$result || ($result['success'] ?? false) === false) {
                Log::error('Tripay Error Detail:', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'result'  => $result,
                ]);
                throw new \Exception($result['message'] ?? 'Gagal membuat transaksi Tripay');
            }

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
        $json = $request->getContent(); // raw body
        Log::info('Tripay Callback Raw:', ['raw' => $json]);

        $data = json_decode($json, true);

        $callbackSignature = $request->header('X-Callback-Signature');
        $privateKey = config('tripay.private_key');

        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($callbackSignature !== $signature) {
            Log::error('Invalid Tripay signature', [
                'expected' => $signature,
                'received' => $callbackSignature
            ]);
            return response()->json(['success' => false], 400);
        }

        // Temukan subscription
        $subscription = Subscription::where('transaction_id', $data['merchant_ref'])->first();
        if (!$subscription) {
            Log::error('Subscription not found');
            return response()->json(['success' => false], 404);
        }

        if ($data['status'] === 'PAID') {
            $subscription->update([
                'payment_status' => 'paid',
                'payment_method' => $data['payment_method'],
                'payment_details' => $json
            ]);

            // Get package type from access_tier
            $package = Package::with('accessTier')->find($subscription->package_id);
            $packageType = 'free'; // default
            
            if ($package && $package->accessTier) {
                $packageType = $package->accessTier->key;
            } else {
                // Fallback: try to determine from package name
                if ($package) {
                    $packageName = strtolower($package->name);
                    if (strpos($packageName, 'gold') !== false || strpos($packageName, 'platinum') !== false) {
                        $packageType = 'lengkap';
                    } elseif (strpos($packageName, 'silver') !== false) {
                        $packageType = 'kecerdasan'; // or adjust based on your logic
                    }
                }
                Log::warning('Package access_tier not found, using fallback', [
                    'package_id' => $subscription->package_id,
                    'package_type' => $packageType
                ]);
            }

            $subscription->user->update([
                'is_active'   => true,
                'package'  => $packageType,
            ]);
        } else {
            $subscription->update([
                'payment_status' => 'failed',
                'payment_details' => $json
            ]);
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
