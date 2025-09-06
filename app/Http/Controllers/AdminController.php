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

    public function userList()
    {
        $users = User::where('role', 'user')
            ->with(['subscriptions' => function ($query) {
                $query->latest();
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
            'package' => 'nullable|in:,kecermatan,psikologi,lengkap'
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

    public function riwayatTes()
    {
        $hasilTes = DB::table('hasil_tes')
            ->join('users', 'hasil_tes.user_id', '=', 'users.id')
            ->select('hasil_tes.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('hasil_tes.created_at', 'desc')
            ->paginate(15);

        return view('admin.riwayat.tes', compact('hasilTes'));
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
