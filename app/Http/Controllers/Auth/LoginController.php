<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return redirect()->route('dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}