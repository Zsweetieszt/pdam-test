<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
                   ->where('is_active', true)
                   ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            
            // Redirect based on role
            $role = $user->role->name ?? 'customer';
            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'keuangan':
                    return redirect()->route('keuangan.dashboard');
                case 'manajemen':
                    return redirect()->route('manajemen.dashboard');
                case 'customer':
                    return redirect()->route('customer.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'phone' => 'Nomor telepon atau password salah.',
        ])->withInput($request->only('phone'));
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'ktp_number' => 'required|string|size:16',
            'ktp_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'addresses' => 'required|array|min:1',
            'addresses.*' => 'required|string|min:10|max:255',
            'meter_numbers' => 'required|array|min:1',
            'meter_numbers.*' => 'required|string|min:3|max:50',
        ]);

        // Customer role (id = 3)
        $customerRole = DB::table('roles')->where('name', 'customer')->first();

        $user = User::create([
            'role_id' => $customerRole->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Akun berhasil dibuat!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
