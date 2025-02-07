<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Subscription;
use Illuminate\Http\Request;
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

    public function process()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }

            // Cek apakah user sudah memiliki transaksi yang belum selesai
            $subscription = Subscription::where('user_id', $user->id)
                ->whereIn('payment_status', ['pending', 'failed'])
                ->latest()
                ->first();

            if (!$subscription) {
                // Jika tidak ada transaksi sebelumnya, buat yang baru
                $transaction_id = 'ORD-' . time() . '-' . $user->id;
                $subscription = Subscription::create([
                    'transaction_id' => $transaction_id,
                    'user_id' => $user->id,
                    'amount_paid' => 105000,
                    'payment_status' => 'pending',
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                ]);
            } else {
                // Jika ada transaksi pending, generate transaction_id baru
                $subscription->update([
                    'transaction_id' => 'ORD-' . time() . '-' . $user->id,
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $subscription->transaction_id,
                    'gross_amount' => $subscription->amount_paid,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'PAKET_CERMAT',
                        'price' => $subscription->amount_paid,
                        'quantity' => 1,
                        'name' => 'Paket Cermat - Persiapan Tes BINTARA POLRI',
                    ],
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'mandiri_va',
                    'permata_va',
                    'gopay',
                    'shopeepay',
                    'qris'
                ],
            ];

            // Generate Snap Token baru
            try {
                $snapToken = Snap::getSnapToken($params);

                // Update payment details dengan token baru
                $subscription->update([
                    'payment_details' => json_encode([
                        'snap_token' => $snapToken,
                        'package' => 'PAKET_CERMAT',
                        'package_type' => 'PAKET_CERMAT'
                    ])
                ]);

                return view('subscription.payment', [
                    'subscription' => $subscription,
                    'snap_token' => $snapToken
                ]);
            } catch (\Exception $e) {
                Log::error('Midtrans Error for user ' . $user->id . ': ' . $e->getMessage());
                Log::error('Params: ' . json_encode($params));

                throw new \Exception('Gagal membuat token pembayaran. Silakan coba beberapa saat lagi.');
            }
        } catch (\Exception $e) {
            Log::error('Subscription Process Error: ' . $e->getMessage());

            return redirect()->route('subscription.checkout')
                ->with('error', $e->getMessage());
        }
    }


    public function notification(Request $request)
    {
        try {
            // Log raw request untuk debugging
            \Log::info('Raw notification received:', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Ambil data dari request langsung
            $order_id = $request->input('order_id');
            $transaction_status = $request->input('transaction_status');
            $payment_type = $request->input('payment_type');
            $transaction_id = $request->input('transaction_id');

            // Log notification data
            \Log::info('Parsed Notification Data:', [
                'order_id' => $order_id,
                'transaction_status' => $transaction_status,
                'payment_type' => $payment_type,
                'transaction_id' => $transaction_id
            ]);

            // Validate required fields
            if (empty($order_id)) {
                throw new \Exception('Order ID tidak ditemukan dalam notifikasi');
            }

            // Find subscription
            $subscription = Subscription::where('transaction_id', $order_id)->first();
            if (!$subscription) {
                \Log::error('Subscription not found:', ['order_id' => $order_id]);
                throw new \Exception('Subscription dengan order ID ' . $order_id . ' tidak ditemukan');
            }

            // Build payment details
            $paymentDetails = [
                'payment_type' => $payment_type,
                'transaction_status' => $transaction_status,
                'transaction_time' => $request->input('transaction_time'),
                'package' => 'PAKET_CERMAT',
                'gross_amount' => $request->input('gross_amount'),
                'transaction_id' => $transaction_id,
            ];

            if ($payment_type === 'qris') {
                $paymentDetails['platform'] = 'QRIS';
                $paymentDetails['acquirer'] = $request->input('acquirer');
            }

            \Log::info('Processing transaction:', [
                'order_id' => $order_id,
                'status' => $transaction_status,
                'payment_type' => $payment_type
            ]);

            try {
                \DB::beginTransaction();

                switch ($transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $subscription->update([
                            'payment_status' => 'paid',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);

                        // Update user status
                        $subscription->user()->update(['is_active' => true]);
                        \Log::info('Payment completed successfully:', ['order_id' => $order_id]);
                        break;

                    case 'pending':
                        $subscription->update([
                            'payment_status' => 'pending',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);
                        \Log::info('Payment pending:', ['order_id' => $order_id]);
                        break;

                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $subscription->update([
                            'payment_status' => 'failed',
                            'payment_method' => $payment_type,
                            'payment_details' => json_encode($paymentDetails)
                        ]);
                        \Log::info('Payment failed:', ['order_id' => $order_id]);
                        break;
                }

                \DB::commit();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Database transaction failed:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return 200 status untuk mencegah Midtrans melakukan retry
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
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
