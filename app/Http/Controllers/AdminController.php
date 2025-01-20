<?php

namespace App\Http\Controllers;

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
        $user = User::with(['subscription' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        $subscriptionHistory = Subscription::where('user_id', $id)
            ->latest()
            ->get();

        return view('admin.users.detail', compact('user', 'subscriptionHistory'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user->update([
            'is_active' => $validatedData['is_active'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Status berhasil diperbarui.');
    }

    public function riwayatTes()
    {
        $hasil = \DB::table('users')
            ->join('hasil_tes', 'users.id', '=', 'hasil_tes.user_id')
            ->select('users.name', 'hasil_tes.*')
            ->whereIn('hasil_tes.id', function ($query) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('hasil_tes')
                    ->groupBy('user_id');
            })
            ->orderBy('hasil_tes.tanggal_tes', 'desc')
            ->get();

        return view('admin.riwayat-tes', compact('hasil'));
    }
}
