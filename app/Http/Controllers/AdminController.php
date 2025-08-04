<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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

        // Get recent impersonation logs (last 10)
        $recentImpersonations = collect();
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            preg_match_all('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] .* Admin impersonation (started|ended).*admin_id.*?(\d+).*?target_user_id.*?(\d+)/s', $logContent, $matches, PREG_SET_ORDER);
            
            foreach (array_slice($matches, -10) as $match) {
                $recentImpersonations->push([
                    'timestamp' => $match[1],
                    'action' => $match[2],
                    'admin_id' => $match[3],
                    'target_user_id' => $match[4]
                ]);
            }
        }

        return view('admin.dashboard', compact('totalUsers', 'activeSubscriptions', 'totalRevenue', 'recentImpersonations'));
    }

    public function userList()
    {
        $users = User::where('role', 'user')
            ->with('subscriptions')
            ->latest()
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function subscriptionList()
    {
        $subscriptions = Subscription::with('user')
            ->latest()
            ->paginate(25);

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

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Hapus semua langganan terkait sebelum menghapus user
        Subscription::where('user_id', $id)->delete();

        // Hapus user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /**
     * Impersonate a user (admin only)
     */
    public function impersonate($id)
    {
        // Check if current user is admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $user = User::findOrFail($id);

        // Prevent admin from impersonating another admin
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Tidak dapat impersonate admin lain.');
        }

        // Prevent impersonating yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat impersonate diri sendiri.');
        }

        // Store original admin user ID in session
        Session::put('impersonate_id', Auth::id());
        Session::put('impersonating_user_id', $user->id);
        Session::put('impersonate_started_at', now());

        // Login as the target user
        Auth::login($user);

        // Log the impersonation for security
        \Log::info('Admin impersonation started', [
            'admin_id' => Session::get('impersonate_id'),
            'admin_name' => User::find(Session::get('impersonate_id'))->name,
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        return redirect()->route('user.tryout.index')->with('success', "Sekarang Anda login sebagai {$user->name}");
    }

    /**
     * Stop impersonating and return to admin account
     */
    public function stopImpersonating()
    {
        // Check if currently impersonating
        if (!Session::has('impersonate_id')) {
            return redirect()->back()->with('error', 'Tidak sedang dalam mode impersonate.');
        }

        $originalAdminId = Session::get('impersonate_id');
        $originalAdmin = User::find($originalAdminId);
        $impersonatedUser = Auth::user();

        if (!$originalAdmin) {
            return redirect()->route('login')->with('error', 'Admin account tidak ditemukan.');
        }

        // Log the impersonation end for security
        \Log::info('Admin impersonation ended', [
            'admin_id' => $originalAdminId,
            'admin_name' => $originalAdmin->name,
            'target_user_id' => $impersonatedUser->id,
            'target_user_name' => $impersonatedUser->name,
            'duration_minutes' => Session::has('impersonate_started_at') ? 
                now()->diffInMinutes(Session::get('impersonate_started_at')) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        // Clear impersonation session
        Session::forget('impersonate_id');
        Session::forget('impersonating_user_id');
        Session::forget('impersonate_started_at');

        // Login back as admin
        Auth::login($originalAdmin);

        return redirect()->route('admin.dashboard')->with('success', 'Kembali ke akun admin.');
    }
}
