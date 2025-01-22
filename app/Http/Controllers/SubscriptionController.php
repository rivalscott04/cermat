<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
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

    public function process($transaction_id)
    {
        $subscription = Subscription::where('transaction_id', $transaction_id)
            ->with('user')
            ->firstOrFail();

        // Cek status pembayaran
        if ($subscription->payment_status == 'paid') {
            return redirect()->route('login')
                ->with('message', 'Pembayaran sudah selesai, silahkan login');
        }

        // Dapatkan payment details
        $paymentDetails = json_decode($subscription->payment_details, true);
        $bank = $paymentDetails['bank'] ?? 'mandiri';

        // Dapatkan instruksi berdasarkan metode pembayaran
        $paymentInstructions = $this->getPaymentInstructions($subscription->payment_method, $bank);

        return view('subscription.payment', [
            'subscription' => $subscription,
            'instructions' => $paymentInstructions,
            'bank' => $bank  // tambahkan ini untuk debugging
        ]);
    }

    private function getPaymentInstructions($method, $bank = null)
    {
        $instructions = [
            'bank_transfer' => [
                'mandiri' => [
                    'Masuk ke menu Transfer',
                    'Pilih Transfer ke rekening Bank Mandiri',
                    'Masukkan nomor rekening: 1234567890',
                    'Masukkan nominal sesuai tagihan: Rp 100.000',
                    'Konfirmasi dan selesaikan pembayaran',
                    'Simpan bukti pembayaran'
                ],
                'bri' => [
                    'Masuk ke menu Transfer',
                    'Pilih Transfer ke rekening BRI',
                    'Masukkan nomor rekening: 9876543210',
                    'Masukkan nominal sesuai tagihan: Rp 100.000',
                    'Konfirmasi dan selesaikan pembayaran',
                    'Simpan bukti pembayaran'
                ],
                'bni' => [
                    'Masuk ke menu Transfer',
                    'Pilih Transfer ke rekening BNI',
                    'Masukkan nomor rekening: 5432167890',
                    'Masukkan nominal sesuai tagihan: Rp 100.000',
                    'Konfirmasi dan selesaikan pembayaran',
                    'Simpan bukti pembayaran'
                ]
            ],
            'qris' => [
                'Buka aplikasi e-wallet Anda (OVO, DANA, GoPay, dll)',
                'Scan QRIS code di bawah ini',
                'Pastikan nominal pembayaran sesuai: Rp 100.000',
                'Konfirmasi dan selesaikan pembayaran'
            ]
        ];

        if ($method === 'bank_transfer' && $bank) {
            return $instructions['bank_transfer'][$bank];
        }

        return $instructions[$method] ?? ['Mohon maaf, instruksi pembayaran untuk metode ini belum tersedia'];
    }
}
