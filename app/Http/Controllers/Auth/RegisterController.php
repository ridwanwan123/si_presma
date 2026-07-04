<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | REGISTER PAGE
    |----------------------------------------------------------------------
    */
    public function showRegisterForm()
    {
        $madrasahs = Madrasah::orderBy('nama_madrasah')->get();

        $roles = Role::whereIn('nama', [
            'Administrator',
            'Operator Madrasah',
            'Pengawas'
        ])->get();

        return view('auth.register', compact('madrasahs', 'roles'));
    }

    /*
    |----------------------------------------------------------------------
    | REGISTER STORE
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string|max:20',
            'madrasah_id' => 'nullable|exists:madrasahs,id',
            'wilayah_pengawas_id' => 'nullable|exists:wilayah_pengawas,id',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'role_id' => $request->role_id,
                'madrasah_id' => $request->madrasah_id,
                'wilayah_pengawas_id' => $request->wilayah_pengawas_id,
                'nama' => $request->nama,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
                'is_active' => true,
            ]);

            ActivityLogger::log(
                event: 'register',
                description: "Register user baru: {$user->nama} ({$user->email})"
            );

            DB::commit();

            return redirect()
                ->route('login.form')
                ->with('success', 'Akun berhasil dibuat. Silakan login.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal register: ' . $e->getMessage());
        }
    }
}