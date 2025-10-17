<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display user profile page
     * [REQ-F-2.1] Menampilkan halaman profil pengguna dengan foto Gravatar
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('role', 'customer');
        
        // Get Gravatar URL
        $gravatarUrl = $this->getGravatarUrl($user->email ?: $user->phone . '@example.com');
        
        return view('profile.index', compact('user', 'gravatarUrl'));
    }

    /**
     * Show edit profile form
     * [REQ-F-2.2] Form edit profil untuk mengubah informasi personal dan kontak
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('role', 'customer');
        
        // Get Gravatar URL
        $gravatarUrl = $this->getGravatarUrl($user->email ?: $user->phone . '@example.com');
        
        return view('profile.edit', compact('user', 'gravatarUrl'));
    }

    /**
     * Update user profile
     * [REQ-F-2.2] Memproses update informasi personal dan kontak
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255', 'min:2', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/', 'unique:users,phone,' . $user->id],
        ];

        // Add customer-specific validation if user is customer
        if ($user->isCustomer() && $user->customer) {
            $rules['address'] = ['required', 'string', 'min:10', 'max:500'];
        }

        $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan tanda baca yang valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid. Contoh: 08123456789',
            'phone.unique' => 'Nomor telepon sudah digunakan oleh user lain.',
            'address.required' => 'Alamat wajib diisi.',
            'address.min' => 'Alamat minimal 10 karakter.',
        ]);

        // Store old values for audit log
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email ?: null;
        $user->phone = $request->phone;
        $user->save();

        // Update customer data if applicable
        if ($user->isCustomer() && $user->customer) {
            $oldValues['address'] = $user->customer->address;
            
            $user->customer->address = $request->address;
            $user->customer->save();
        }

        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->isCustomer() && $user->customer ? $user->customer->address : null,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show change password form
     */
    public function password()
    {
        return view('profile.password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        /** @var User $user */
        $user = Auth::user();

        // Create audit log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'change_password',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_values' => ['password' => '[hidden]'],
            'new_values' => ['password' => '[hidden]'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah.');
    }

    /**
     * Display user activity history
     * [REQ-F-2.3] Menampilkan riwayat aktivitas pengguna dalam sistem
     */
    public function activity(Request $request)
    {
        $user = Auth::user();
        
        // Query audit logs
        $query = AuditLog::where('user_id', $user->id)
                         ->orderBy('created_at', 'desc');

        // Filter by action if provided
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(20);

        // Get unique actions for filter dropdown
        $availableActions = AuditLog::where('user_id', $user->id)
                                   ->distinct()
                                   ->pluck('action')
                                   ->sort();

        return view('profile.activity', compact('activities', 'availableActions'));
    }

    /**
     * Generate Gravatar URL
     * [REQ-F-2.1] Sesuai requirement foto dari Gravatar
     */
    private function getGravatarUrl($email, $size = 200, $default = 'identicon')
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . 
               "?s={$size}&d={$default}";
    }

    /**
     * Get user initials for avatar fallback
     * [REQ-F-2.1] Sesuai constraint C-16 (avatar menggunakan inisial nama)
     */
    private function getUserInitials($name)
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: strtoupper(substr($name, 0, 2));
    }
}