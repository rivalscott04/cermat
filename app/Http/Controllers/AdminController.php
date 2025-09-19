<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        return view('admin.dashboard', compact('totalUsers', 'activeSubscriptions', 'totalRevenue'));
    }

    public function userList(Request $request)
    {
        $query = User::where('role', 'user')
            ->with(['subscriptions' => function ($query) {
                $query->latest();
            }]);

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function userDetail($id)
    {
        $user = User::with(['subscriptions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('admin.users.detail', compact('user'));
    }

    public function subscriptionList()
    {
        $subscriptions = Subscription::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function revenueReport()
    {
        $monthlyRevenue = Subscription::where('payment_status', 'paid')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount_paid) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.revenue.report', compact('monthlyRevenue'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cek apakah request hanya untuk update status
        if ($request->has('is_active') && !$request->has('name')) {
            $request->validate([
                'is_active' => 'required|in:0,1'
            ]);

            $user->update([
                'is_active' => (bool) $request->is_active
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Status pengguna berhasil diperbarui.');
        }

        // Untuk update data lengkap
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin',
            'is_active' => 'sometimes|in:0,1' // sometimes untuk optional
        ]);

        $updateData = $request->only(['name', 'email', 'role']);

        // Tambahkan is_active jika ada di request
        if ($request->has('is_active')) {
            $updateData['is_active'] = (bool) $request->is_active;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function updatePackage(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'package' => 'nullable|in:,free,kecermatan,psikologi,lengkap'
        ]);

        $package = $request->input('package');

        try {
            // Update package field directly in users table
            $user->update([
                'package' => $package === '' ? null : $package
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Package pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Gagal memperbarui package pengguna: ' . $e->getMessage());
        }
    }

    public function riwayatTes(Request $request)
    {
        // Get list of users with test statistics
        $query = User::where('role', 'user')
            ->withCount(['hasilTes as total_tests'])
            ->withCount(['hasilTes as today_tests' => function ($q) {
                $q->whereDate('tanggal_tes', today());
            }])
            ->withCount(['hasilTes as this_week_tests' => function ($q) {
                $q->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()]);
            }])
            ->withCount(['hasilTes as this_month_tests' => function ($q) {
                $q->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year);
            }]);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by package
        if ($request->has('package') && !empty($request->package)) {
            $query->where('package', $request->package);
        }

        $users = $query->orderBy('total_tests', 'desc')
                      ->orderBy('name', 'asc')
                      ->paginate(20);

        // Get statistics for overview cards
        $totalUsers = User::where('role', 'user')->count();
        $totalTests = DB::table('hasil_tes')->count();
        $todayTests = DB::table('hasil_tes')->whereDate('tanggal_tes', today())->count();
        $thisWeekTests = DB::table('hasil_tes')->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthTests = DB::table('hasil_tes')->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year)->count();

        // Get package distribution
        $packageStats = User::where('role', 'user')
            ->select('package', DB::raw('count(*) as total'))
            ->groupBy('package')
            ->get();

        return view('admin.riwayat-tes', compact(
            'users', 
            'totalUsers',
            'totalTests', 
            'todayTests', 
            'thisWeekTests', 
            'thisMonthTests',
            'packageStats'
        ));
    }

    public function riwayatTesUser($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        
        $query = DB::table('hasil_tes')
            ->where('user_id', $userId)
            ->select('hasil_tes.*');

        // Filter by jenis tes
        if ($request->has('jenis_tes') && !empty($request->jenis_tes)) {
            $query->where('hasil_tes.jenis_tes', $request->jenis_tes);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('hasil_tes.tanggal_tes', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('hasil_tes.tanggal_tes', '<=', $request->date_to);
        }

        $hasilTes = $query->orderBy('hasil_tes.tanggal_tes', 'desc')->paginate(20);

        // Get user statistics
        $userStats = [
            'total_tests' => DB::table('hasil_tes')->where('user_id', $userId)->count(),
            'today_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereDate('tanggal_tes', today())->count(),
            'this_week_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereBetween('tanggal_tes', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_tests' => DB::table('hasil_tes')->where('user_id', $userId)->whereMonth('tanggal_tes', now()->month)->whereYear('tanggal_tes', now()->year)->count(),
        ];

        // Get test type distribution for this user
        $testTypeStats = DB::table('hasil_tes')
            ->where('user_id', $userId)
            ->select('jenis_tes', DB::raw('count(*) as total'))
            ->whereNotNull('jenis_tes')
            ->groupBy('jenis_tes')
            ->get();

        return view('admin.riwayat-tes-user', compact(
            'user',
            'hasilTes', 
            'userStats',
            'testTypeStats'
        ));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus akun admin.');
        }

        // Delete related data first
        $user->subscriptions()->delete();
        DB::table('hasil_tes')->where('user_id', $id)->delete();

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
