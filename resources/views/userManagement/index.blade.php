@extends('layouts.base')

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
        }

        .page-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .25rem;
        }

        .page-title p {
            color: #64748b;
            margin: 0;
        }

        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        }

        .content-card-body {
            padding: 1.25rem;
        }

        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 14px;
        }

        .filter-box .form-select {
            height: 42px;
            border-radius: 10px;
        }

        .filter-box .btn {
            height: 42px;
            border-radius: 10px;
        }


        .user-info {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: #fff;
            font-weight: 700;
        }

        .modern-table {
            vertical-align: middle;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .empty-state {
            padding: 3rem;
        }

        .pagination {
            gap: .35rem;
            margin-bottom: 0;
        }
    </style>
@endpush


@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Management Akun</h2>
            <p>Kelola akun pengguna, peran, dan hak akses sistem</p>
        </div>


        <div class="content-card">

            <div class="content-card-body">


                {{-- ================= FILTER ================= --}}
                <form method="GET">

                    <div class="filter-box mb-4">

                        <div class="d-flex align-items-end gap-2">

                            <div style="width: 350px;">

                                <label class="form-label">
                                    Role
                                </label>

                                <select name="role" class="form-select">

                                    <option value="">
                                        Semua Role
                                    </option>

                                    @foreach ($roles as $role)
                                        <option value="{{ $role->nama }}"
                                            {{ request('role') == $role->nama ? 'selected' : '' }}>

                                            {{ $role->nama }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            <div>

                                <button class="btn btn-primary px-4">
                                    <i class="bi bi-funnel"></i>
                                    Filter
                                </button>

                            </div>


                            <div>

                                <a href="{{ route('user-management.index') }}" class="btn btn-secondary px-4">

                                    <i class="bi bi-arrow-clockwise"></i>
                                    Reset

                                </a>

                            </div>


                        </div>

                    </div>

                </form>



                {{-- ================= TABLE ================= --}}

                <div class="table-responsive">

                    <table class="table modern-table">

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>Nama Akun</th>
                                <th>Role</th>
                                <th>Instansi / Unit Kerja</th>
                                <th>Bergabung</th>
                                <th class="text-center">
                                    Aksi
                                </th>
                            </tr>

                        </thead>


                        <tbody>


                            @forelse ($users as $user)
                                @php

                                    $initial = strtoupper(substr($user->nama ?? ($user->email ?? '-'), 0, 1));

                                    $roleName = $user->role?->nama ?? '-';

                                    $roleClass = match (strtolower($roleName)) {
                                        'administrator' => 'role-administrator',

                                        'madrasah' => 'role-madrasah',

                                        'pengawas' => 'role-pengawas',

                                        default => 'role-default',
                                    };

                                    $instansi = match ($roleName) {
                                        'Madrasah' => $user->madrasah?->nama_madrasah ?? '-',

                                        'Pengawas' => $user->wilayahPengawas?->unit_kerja ?? '-',

                                        default => '-',
                                    };

                                @endphp



                                <tr>


                                    <td>
                                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                    </td>


                                    <td>

                                        <div class="user-info">

                                            <div class="user-avatar">
                                                {{ $initial }}
                                            </div>


                                            <div>

                                                <strong>
                                                    {{ $user->nama ?? '-' }}
                                                </strong>

                                                <small>
                                                    {{ $user->email }}
                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="badge bg-light text-dark">
                                            {{ $roleName }}
                                        </span>

                                    </td>


                                    <td>
                                        {{ $instansi }}
                                    </td>


                                    <td>
                                        {{ $user->created_at?->format('d M Y') ?? '-' }}
                                    </td>


                                    <td>

                                        <div class="d-flex gap-2 justify-content-center">


                                            <a href="{{ route('user-management.edit', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary">

                                                <i class="bi bi-pencil"></i>

                                            </a>



                                            <form action="{{ route('user-management.destroy', $user->id) }}"
                                                method="POST">

                                                @csrf
                                                @method('DELETE')


                                                <button class="btn btn-sm btn-outline-danger">

                                                    <i class="bi bi-trash"></i>

                                                </button>


                                            </form>


                                        </div>

                                    </td>


                                </tr>


                            @empty


                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        Belum ada data akun.

                                    </td>

                                </tr>
                            @endforelse


                        </tbody>


                    </table>

                </div>


                {{-- ================= PAGINATION ================= --}}

                {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}


            </div>

        </div>


    </main>
@endsection
