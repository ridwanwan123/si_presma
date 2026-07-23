<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Models\Role;
use App\Models\Madrasah;
use App\Models\WilayahPengawas;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with([
                'role:id,nama',
                'madrasah:id,nama_madrasah',
                'wilayahPengawas:id,unit_kerja',
            ]);

        // Filter Role
        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('nama', $request->role);
            });
        }

        // Filter Status -- cuma 2 kondisi, langsung dari is_active
        if ($request->filled('status')) {
            match ($request->status) {
                'aktif'    => $query->where('is_active', true),
                'nonaktif' => $query->where('is_active', false),
                default    => null,
            };
        }

        // Akun nonaktif ditaruh paling atas supaya yang butuh diaktifkan
        // langsung kelihatan, baru diurutkan terbaru seperti biasa.
        $users = $query
            ->orderBy('is_active')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Dihitung terpisah (tanpa filter role/status) supaya badge jumlah
        // selalu akurat walau user sedang memfilter.
        $jumlahNonaktif = User::where('is_active', false)->count();

        // Dropdown Role
        $roles = Role::orderBy('nama')->get();

        // Dropdown Madrasah
        $madrasahs = Madrasah::orderBy('nama_madrasah')->get();

        // Dropdown Wilayah Pengawas
        $wilayahPengawas = WilayahPengawas::orderBy('unit_kerja')->get();

        $breadcrumb = breadcrumb([
            'Management Akun'
        ]);

        return view('userManagement.index', compact(
            'users',
            'roles',
            'madrasahs',
            'wilayahPengawas',
            'jumlahNonaktif',
            'breadcrumb'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'role_id'  => ['required', 'exists:roles,id'],
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'unique:users,username'],
            'password' => ['required', 'min:8'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];

        // validasi role_id dulu supaya findOrFail aman dipanggil setelahnya
        $request->validate(array_intersect_key($rules, array_flip(['role_id'])));

        $role = Role::findOrFail($request->role_id);

        if ($role->nama === 'Madrasah') {
            $rules['madrasah_id'] = ['required', 'exists:madrasahs,id'];
        }

        if ($role->nama === 'Pengawas') {
            $rules['wilayah_pengawas_id'] = ['required', 'exists:wilayah_pengawas,id'];
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $user = User::create([
                'role_id'             => $role->id,
                'madrasah_id'         => $role->nama === 'Madrasah' ? $request->madrasah_id : null,
                'wilayah_pengawas_id' => $role->nama === 'Pengawas' ? $request->wilayah_pengawas_id : null,
                'nama'     => $request->nama,
                'email'    => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'no_hp'    => $request->no_hp,
                'is_active' => $request->boolean('is_active'),
            ]);

            ActivityLogger::log(
                event: 'create',
                description: "Menambahkan akun baru {$user->nama} ({$role->nama})"
            );

            DB::commit();

            return redirect()
                ->route('user-management.index')
                ->with('success', 'Akun berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('USER MANAGEMENT STORE ERROR', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan akun.');
        }
    }

    public function edit(Request $request, User $user)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $user->load(['role', 'madrasah', 'wilayahPengawas']);
    
            return response()->json([
                'user' => $user,
            ]);
        }
    
        return redirect()->route('user-management.index');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);
    
        $role = Role::findOrFail($request->role_id);
    
        $rules = [
            'role_id'  => ['required', 'exists:roles,id'],
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
            'username' => ['required', 'string', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'min:8'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    
        if ($role->nama === 'Madrasah') {
            $rules['madrasah_id'] = ['required', 'exists:madrasahs,id'];
        }
    
        if ($role->nama === 'Pengawas') {
            $rules['wilayah_pengawas_id'] = ['required', 'exists:wilayah_pengawas,id'];
        }
    
        $request->validate($rules);
    
        try {
            DB::beginTransaction();
    
            $data = [
                'role_id'             => $role->id,
                'madrasah_id'         => $role->nama === 'Madrasah' ? $request->madrasah_id : null,
                'wilayah_pengawas_id' => $role->nama === 'Pengawas' ? $request->wilayah_pengawas_id : null,
                'nama'      => $request->nama,
                'email'     => $request->email,
                'username'  => $request->username,
                'no_hp'     => $request->no_hp,
                'is_active' => $request->boolean('is_active'),
            ];
    
            // password opsional: hanya diupdate jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
    
            $user->update($data);
    
            ActivityLogger::log(
                event: 'update',
                description: "Memperbarui akun {$user->nama} ({$role->nama})"
            );
    
            DB::commit();
    
            return redirect()
                ->route('user-management.index')
                ->with('success', 'Akun berhasil diperbarui.');
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            Log::error('USER MANAGEMENT UPDATE ERROR', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);
    
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui akun.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AKTIFKAN AKUN -- dipakai baik untuk akun baru hasil self-register
    | maupun akun lama yang sebelumnya dinonaktifkan manual. Endpoint
    | terpisah dari update() biasa supaya aksi ini punya activity log
    | sendiri yang jelas.
    |--------------------------------------------------------------------------
    */
    public function approve(User $user)
    {
        $user->update([
            'is_active' => true,
        ]);

        ActivityLogger::log(
            event: 'update',
            description: "Mengaktifkan akun {$user->nama} ({$user->role?->nama})"
        );

        return back()->with('success', "Akun {$user->nama} berhasil diaktifkan dan sudah bisa login.");
    }
}