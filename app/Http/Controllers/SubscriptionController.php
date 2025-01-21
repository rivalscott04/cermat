<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // Siapkan data instruksi pembayaran berdasarkan payment_method
        $paymentInstructions = $this->getPaymentInstructions($subscription->payment_method);

        return view('subscription.payment', [
            'subscription' => $subscription,
            'instructions' => $paymentInstructions
        ]);
    }

    private function getPaymentInstructions($method)
    {
        // Contoh instruksi pembayaran
        $instructions = [
            'bank_transfer' => [
                'Masuk ke menu Transfer',
                'Pilih Transfer ke rekening Bank XXX',
                'Masukkan nomor rekening: 123456789',
                'Masukkan nominal sesuai tagihan',
                'Konfirmasi dan selesaikan pembayaran'
            ],
            'qris' => [
                'Buka aplikasi e-wallet Anda',
                'Scan QRIS code di bawah ini',
                'Pastikan nominal pembayaran sesuai',
                'Selesaikan pembayaran'
            ],
            // Instruksi untuk metode pembayaran lain
        ];

        return $instructions[$method] ?? [];
    }
}
