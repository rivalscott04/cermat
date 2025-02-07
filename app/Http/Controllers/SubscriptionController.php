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
        $user = auth()->user();

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
        }

        if ($subscription->payment_status == 'paid') {
            return redirect()->route('login')->with('message', 'Pembayaran sudah selesai, silakan login.');
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $subscription->transaction_id,
                    'gross_amount' => $subscription->amount_paid,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
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
                    'shopeepay'
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

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
            Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan dalam memproses pembayaran');
        }
    }


    public function notification(Request $request)
    {
        \Log::info('Raw notification received:', $request->all());
        try {
            $notif = new \Midtrans\Notification();

            \Log::info('Midtrans Notification:', [
                'order_id' => $notif->order_id,
                'transaction_status' => $notif->transaction_status,
                'payment_type' => $notif->payment_type,
                'raw' => $request->all()
            ]);

            $subscription = Subscription::where('transaction_id', $notif->order_id)->firstOrFail();

            // Get payment method specific details
            $additionalDetails = [];

            switch ($notif->payment_type) {
                case 'bank_transfer':
                    if (isset($notif->va_numbers[0])) {
                        $additionalDetails['bank'] = $notif->va_numbers[0]->bank;
                    } elseif (isset($notif->permata_va_number)) {
                        $additionalDetails['bank'] = 'permata';
                    }
                    break;

                case 'credit_card':
                    $additionalDetails['bank'] = $notif->bank ?? '';
                    $additionalDetails['card_type'] = $notif->card_type ?? '';
                    break;

                case 'gopay':
                    $additionalDetails['payment_platform'] = 'GOPAY';
                    break;

                case 'shopeepay':
                    $additionalDetails['payment_platform'] = 'SHOPEEPAY';
                    break;

                case 'qris':
                    $additionalDetails['payment_platform'] = 'QRIS';
                    break;
            }

            // Initialize payment details array with additional details merged
            $paymentDetails = array_merge([
                'payment_type' => $notif->payment_type,
                'transaction_status' => $notif->transaction_status,
                'transaction_time' => $notif->transaction_time,
                'package' => 'PAKET_CERMAT',
                'package_type' => 'PAKET_CERMAT',
                'gross_amount' => $notif->gross_amount,
                'transaction_id' => $notif->transaction_id,
            ], $additionalDetails);

            // Handle transaction status
            switch ($notif->transaction_status) {
                case 'capture':
                case 'settlement':
                    $subscription->update([
                        'payment_status' => 'paid',
                        'payment_method' => $notif->payment_type,
                        'payment_details' => json_encode($paymentDetails)
                    ]);
                    $subscription->user->update(['is_active' => true]);
                    break;

                case 'pending':
                    $subscription->update([
                        'payment_status' => 'pending',
                        'payment_method' => $notif->payment_type,
                        'payment_details' => json_encode($paymentDetails)
                    ]);
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    $subscription->update([
                        'payment_status' => 'failed',
                        'payment_method' => $notif->payment_type,
                        'payment_details' => json_encode($paymentDetails)
                    ]);
                    break;
            }

            return response()->json($paymentDetails);
        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
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
