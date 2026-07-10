<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        // =========================
        // FILTER ROLE
        // =========================
        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('nama', $request->role);
            });
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // =========================
        // ROLE FILTER
        // =========================
        $roles = \App\Models\Role::query()
            ->orderBy('nama')
            ->get();

        $breadcrumb = breadcrumb([
            'Management Akun'
        ]);

        return view('userManagement.index', compact(
            'users',
            'roles',
            'breadcrumb'
        ));
    }
}
