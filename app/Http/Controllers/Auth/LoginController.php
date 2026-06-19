<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATE
    |--------------------------------------------------------------------------
    */

    public function authenticate(Request $request)
    {
        $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | DETECT LOGIN FIELD
        |--------------------------------------------------------------------------
        */

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        /*
        |--------------------------------------------------------------------------
        | LOGIN ATTEMPT
        |--------------------------------------------------------------------------
        */

        $credentials = [
            $field => $request->login,
            'password' => $request->password,
            'is_active' => true,
        ];

        if (!Auth::attempt($credentials)) {
             activity()
                ->event('login-failed')
                ->withProperties([
                    'login' => $request->login,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('Percobaan login gagal');

            return back()
                ->withInput()
                ->with('error', 'Username/email atau password salah.');
        }

        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT DASHBOARD
        |--------------------------------------------------------------------------
        */

        ActivityLogger::log(
            event: 'login',
            description: 'Login ke sistem'
        );

        return redirect()->route('dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        ActivityLogger::log(
            event: 'logout',
            description: 'Logout dari sistem'
        );

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function changePassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 3 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        /*
        * Hash::check bersifat case-sensitive
        * Jadi huruf besar dan kecil dibedakan.
        */
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'Password lama tidak sesuai.'
                ])
                ->withInput();
        }

        // Cegah password baru sama dengan password lama
        if (Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'password' => 'Password baru tidak boleh sama dengan password lama.'
                ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        ActivityLogger::log(
            event: 'update',
            description: 'Mengubah password akun',
            subject: $user
        );

        return redirect()
            ->route('ubah-password')
            ->with('success', 'Password berhasil diperbarui.');
    }
}