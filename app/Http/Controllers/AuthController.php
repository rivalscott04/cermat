<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|digits_between:10,15',
            'province' => 'required',
            'regency' => 'required',
        ]);

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

        if (Auth::check()) {
            return redirect()->route('user.profile', ['userId' => Auth::user()->id]);
        } else {
            return redirect()
                ->route('login')
                ->with('message', 'Silahkan login menggunakan email dan password yang telah dibuat');
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
}
