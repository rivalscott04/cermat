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

    public function process(Request $request)
    {
        // Di sini paymaant gateway
        
        $user = auth()->user();
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYear(),
            'amount_paid' => 1000000, // Rp 1.000.000
            'payment_status' => 'paid',
            'payment_method' => 'transfer_bank',
            'transaction_id' => 'TRX-' . time()
        ]);

        return redirect()->route('dashboard')->with('success', 'Berlangganan berhasil diaktifkan!');
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
}