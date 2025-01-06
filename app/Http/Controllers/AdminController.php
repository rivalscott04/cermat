<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Mengambil statistik untuk dashboard
        $totalUsers = User::where('role', 'user')->count();
        $activeSubscriptions = Subscription::where('end_date', '>', now())
                                         ->where('payment_status', 'paid')
                                         ->count();
        $totalRevenue = Subscription::where('payment_status', 'paid')
                                   ->sum('amount_paid');
        
        return view('admin.dashboard', compact('totalUsers', 'activeSubscriptions', 'totalRevenue'));
    }

    public function userList()
    {
        $users = User::where('role', 'user')
                    ->with('subscription')
                    ->latest()
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function subscriptionList()
    {
        $subscriptions = Subscription::with('user')
                                   ->latest()
                                   ->paginate(10);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function revenueReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $revenues = Subscription::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_subscriptions'),
                DB::raw('SUM(amount_paid) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.revenue', compact('revenues', 'startDate', 'endDate'));
    }

    public function userDetail($id)
    {
        $user = User::with(['subscription' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        $subscriptionHistory = Subscription::where('user_id', $id)
                                         ->latest()
                                         ->get();

        return view('admin.users.detail', compact('user', 'subscriptionHistory'));
    }
}