<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        // Validasi ditambah untuk payment
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|digits_between:10,15',
            'province' => 'required',
            'regency' => 'required',
            'payment_method' => 'required',
            'terms' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'is_active' => false,
                'province' => $request->province,
                'regency' => $request->regency,
                'role' => 'user'
            ]);

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'start_date' => now(),
                'end_date' => now()->addDays(30), // Sesuaikan dengan durasi paket
                'amount_paid' => 49900, // Dari harga paket yang dipilih
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_details' => json_encode([
                    'package' => 'SILVER',
                    'package_type' => 'TRYOUT'
                ]),
                'transaction_id' => 'TRX-' . time() // Generate transaction ID
            ]);

            DB::commit();

            // Redirect ke halaman pembayaran dengan membawa transaction_id
            return redirect()->route('payment.process', ['transaction_id' => $subscription->transaction_id]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan dalam proses pendaftaran');
        }
    }

    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Proses autentikasi
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // Jika user adalah admin
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Jika user adalah user biasa
            if (Auth::user()->role == 'user') {
                // Cek status langganan
                if (Auth::user()->hasActiveSubscription()) {
                    // Jika memiliki langganan aktif, redirect ke 'kecermatan'
                    return redirect()->route('kecermatan');
                } else {
                    // Jika tidak memiliki langganan, redirect ke 'profile'
                    return redirect()->route('user.profile', ['userId' => Auth::user()->id]);
                }
            }

            // Redirect default jika role tidak dikenali
            return redirect()->intended('dashboard');
        }

        // Jika autentikasi gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Add this to your existing AuthController class

    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'message' => 'Email tidak cocok.'
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()
            ->route('login')
            ->with('message', 'Password berhasil diubah. Silahkan login dengan password baru.');
    }
}
