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
use Illuminate\Support\Facades\Log;

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
            'Madrasah',
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
        try {

            // Dicari dinamis (bukan hardcode role_id 2/3) -- supaya tidak
            // ikut rusak kalau urutan seed Role berubah di kemudian hari.
            $roleMadrasahId = Role::where('nama', 'Madrasah')->value('id');
            $rolePengawasId = Role::where('nama', 'Pengawas')->value('id');

            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'nama' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:50|unique:users,username',
                // Samain persis dengan cek JS di form: minimal 8 karakter,
                // wajib ada minimal 1 angka, dan wajib cocok dengan
                // password_confirmation.
                'password' => 'required|min:8|regex:/[0-9]/|confirmed',
                // Nomor HP cuma boleh angka -- regex, bukan tipe HTML
                // "number" (itu di sisi tampilan sudah diperbaiki juga,
                // soalnya input type=number bikin angka 0 di depan hilang).
                'no_hp' => 'required|regex:/^[0-9]+$/|max:20',
                'madrasah_id' => 'required_if:role_id,' . $roleMadrasahId . '|nullable|exists:madrasahs,id',
                'wilayah_pengawas_id' => 'required_if:role_id,' . $rolePengawasId . '|nullable',
            ], [
                'role_id.required' => 'Jenis akun wajib dipilih.',
                'nama.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau login.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username ini sudah dipakai. Silakan pilih username lain.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password harus mengandung minimal 1 angka.',
                'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
                'no_hp.required' => 'Nomor HP wajib diisi.',
                'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, tanpa spasi/simbol.',
                'madrasah_id.required_if' => 'Nama madrasah wajib dipilih.',
                'madrasah_id.exists' => 'Madrasah yang dipilih tidak valid.',
                'wilayah_pengawas_id.required_if' => 'Wilayah pengawas wajib dipilih.',
            ]);

            DB::beginTransaction();

            // is_active SENGAJA selalu false di sini -- akun hasil
            // self-register TIDAK BOLEH langsung bisa dipakai, harus
            // diaktifkan dulu oleh Admin lewat User Management.
            $user = User::create([
                'role_id' => $request->role_id,
                'madrasah_id' => $request->madrasah_id,
                'wilayah_pengawas_id' => $request->wilayah_pengawas_id,
                'nama' => $request->nama,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
                'is_active' => false,
            ]);

            ActivityLogger::log(
                event: 'create',
                description: "Register user baru (menunggu aktivasi): {$user->nama} ({$user->email})"
            );

            DB::commit();

            return redirect()
                ->route('login.form')
                ->with('show_aktivasi_modal', true)
                ->with('aktivasi_modal_judul', 'Akun Berhasil Dibuat!');

        } catch (\Illuminate\Validation\ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('REGISTER ERROR', [
                'message' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal register. Silakan coba lagi.');
        }
    }
}