<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|digits_between:10,15',
            'province' => 'required|string',
            'regency' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        DB::beginTransaction();
        try {
            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'is_active' => false,
                'province' => $request->province,
                'regency' => $request->regency,
                'role' => 'user',
            ]);

            Auth::login($user);

            // Simpan session_id ke database
            $user->session_id = session()->getId();
            $user->save();

            DB::commit();

            return redirect()->route('user.profile', ['userId' => Auth::user()->id])
                ->with('success', 'Pendaftaran berhasil! Silakan pilih paket berlangganan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error during registration: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan dalam proses pendaftaran. Silakan coba lagi.');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            Log::info('User logged in: ' . $user->email);

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role == 'user') {
                return $user->hasActiveSubscription()
                    ? redirect()->route('kecermatan')
                    : redirect()->route('user.profile', ['userId' => $user->id]);
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Hapus session_id dari database
            $user->session_id = null;
            $user->save();
        }

        // Logout user
        Auth::logout();

        // Invalidate dan regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        Log::info('Password reset for user: ' . $user->email);

        return redirect()->route('login')
            ->with('message', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
