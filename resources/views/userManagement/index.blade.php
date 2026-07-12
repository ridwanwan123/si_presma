@extends('layouts.base')
@push('styles')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }

        .page-title {
            padding: .25rem 1rem 0;
            margin-bottom: 1.25rem;
        }

        .page-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .25rem;
            letter-spacing: -.02em;
        }

        .page-title p {
            color: #64748b;
            margin: 0;
        }

        .page-title .btn-success {
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(25, 135, 84, .25);
            border: none;
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
            padding: 1.5rem;
        }

        /* =====================================
                                       FILTER CARD
                                    ===================================== */
        .filter-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .filter-card-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            background: #fff;
        }

        .filter-card-header i {
            font-size: 1.05rem;
            color: #2563eb;
        }

        .filter-card-header span {
            font-size: .85rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .filter-card-body {
            padding: 1.1rem 1.25rem;
        }

        .filter-card .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .4rem;
        }

        .filter-card .form-select {
            height: 42px;
            border-radius: 10px;
            border: 1px solid #d9dee7;
        }

        .filter-card .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12);
        }

        .filter-card .btn {
            height: 42px;
            border-radius: 10px;
            font-weight: 600;
        }

        .filter-card .btn-secondary {
            background: #fff;
            border: 1px solid #d9dee7;
            color: #475569;
        }

        .filter-card .btn-secondary:hover {
            background: #f1f5f9;
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
            flex-shrink: 0;
        }

        .modern-table {
            vertical-align: middle;
        }

        .modern-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .modern-table tbody td {
            white-space: nowrap;
        }

        .modern-table tbody tr:hover {
            background: #f8fafc;
        }

        .modern-table .badge {
            font-weight: 600;
            padding: .45em .8em;
            border-radius: 8px;
        }

        .btn-detail {
            border-radius: 8px;
            font-weight: 600;
            font-size: .82rem;
            padding: .4rem .9rem;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        .btn-detail:hover {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .empty-state {
            padding: 3rem;
        }

        .pagination {
            gap: .35rem;
            margin-bottom: 0;
        }

        /* =====================================
                                       PRESMA ACCOUNT MODAL
                                    ===================================== */
        .presma-dialog {
            max-width: 920px;
            width: calc(100% - 2rem);
            margin: 1.75rem auto;
        }

        @media (min-width: 992px) {

            #modalTambahAkun,
            #modalDetailAkun {
                padding-left: var(--sidebar-width, 260px);
            }
        }

        .presma-account-modal {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(15, 23, 42, .25);
        }

        /* HEADER */
        .presma-account-modal .modal-header {
            background: linear-gradient(135deg,
                    #198754,
                    #0f5132);
            color: white;
            padding: 28px 40px;
        }

        .modal-tag {
            font-size: 11px;
            letter-spacing: 1.5px;
            opacity: .8;
        }

        .presma-account-modal .modal-title {
            font-size: 24px;
            font-weight: 800;
            margin-top: 5px;
        }

        /* BODY */
        .presma-account-modal .modal-body {
            padding: 35px 40px;
            max-height: 75vh;
            overflow-y: auto;
        }

        /* FOOTER */
        .presma-account-modal .modal-footer {
            background: #f8fafc;
            padding: 22px 40px;
            border-top: 1px solid #e5e7eb;
        }

        /* SECTION */
        .section-label {
            display: block;
            font-size: 13px;
            font-weight: 800;
            color: #198754;
            margin-bottom: 14px;
        }

        /* ROLE */
        .role-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .role-card {
            cursor: pointer;
        }

        .role-radio {
            display: none;
        }

        .role-body {
            height: 90px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: .25s;
            background: white;
        }

        .role-body:hover {
            transform: translateY(-2px);
            border-color: #198754;
        }

        .role-radio:checked+.role-body {
            border: 2px solid #198754;
            background: #f0fdf4;
        }

        .role-icon {
            width: 52px;
            height: 52px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            color: #198754;
            background: #dcfce7;
        }

        .role-body h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
        }

        .role-body small {
            color: #64748b;
        }

        /* FORM */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-grid label,
        .role-fields label {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .presma-account-modal .form-control,
        .presma-account-modal .form-select {
            height: 46px;
            border-radius: 12px;
            border: 1px solid #d9dee7;
        }

        .presma-account-modal .form-control:focus,
        .presma-account-modal .form-select:focus {
            border-color: #198754;
            box-shadow:
                0 0 0 .2rem rgba(25, 135, 84, .12);
        }

        /* SEARCH */
        .search-select {
            position: relative;
        }

        .search-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            border: 1px solid #ddd;
            max-height: 220px;
            overflow: auto;
            z-index: 1056;
        }

        .dropdown-item-custom {
            padding: 12px 15px;
            cursor: pointer;
        }

        .dropdown-item-custom:hover {
            background: #f0fdf4;
        }

        /* BUTTON */
        .btn-register {
            background: #198754;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 700;
        }
    </style>
@endpush
@section('content')
    <main class="content">
        <div class="page-title d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2>Management Akun</h2>
                <p>Kelola akun pengguna, peran, dan hak akses sistem</p>
            </div>
            <a href="#" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah Akun
            </a>
        </div>
        <div class="content-card">
            <div class="content-card-body">
                {{-- ================= FILTER ================= --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <i class="bi bi-funnel"></i>
                        <span>Filter Data</span>
                    </div>
                    <form method="GET">
                        <div class="filter-card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Role</label>
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
                                <div class="col-6 col-md-auto">
                                    <button class="btn btn-primary px-4 w-100">
                                        <i class="bi bi-funnel"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-6 col-md-auto">
                                    <a href="{{ route('user-management.index') }}" class="btn btn-secondary px-4 w-100">
                                        <i class="bi bi-arrow-clockwise"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailAkun" data-id="{{ $user->id }}">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </button>
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
<div class="modal fade" id="modalTambahAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered presma-dialog">
        <form action="{{ route('user-management.store') }}" method="POST" id="formTambahAkun">
            @csrf
            <input type="hidden" name="form_source" value="tambah">
            <div class="modal-content presma-account-modal">
                {{-- HEADER --}}
                <div class="modal-header">
                    <div>
                        <span class="modal-tag">
                            PRESMA ACCOUNT
                        </span>
                        <h5 class="modal-title">
                            <i class="bx bx-user-plus"></i>
                            Tambah Akun Baru
                        </h5>
                        <small>
                            Pilih jenis akun kemudian lengkapi data pengguna.
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>
                </div>
                {{-- BODY --}}
                <div class="modal-body">
                    {{-- ROLE --}}
                    <span class="section-label">
                        Jenis Akun
                    </span>
                    <div class="role-container">
                        @foreach ($roles as $role)
                            <label class="role-card">
                                <input type="radio" name="role_id" value="{{ $role->id }}" class="role-radio"
                                    data-role="{{ strtolower($role->nama) }}">
                                <div class="role-body">
                                    <div class="role-icon">
                                        @if ($role->nama == 'Administrator')
                                            <i class="bx bxs-crown"></i>
                                        @elseif($role->nama == 'Madrasah')
                                            <i class="bx bxs-school"></i>
                                        @elseif($role->nama == 'Pengawas')
                                            <i class="bx bxs-map-pin"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h5>
                                            {{ $role->nama }}
                                        </h5>
                                        <small>
                                            @if ($role->nama == 'Administrator')
                                                Pengelola sistem
                                            @elseif($role->nama == 'Madrasah')
                                                Admin Madrasah
                                            @else
                                                Pengawas wilayah
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    {{-- ERROR ROLE --}}
                    <div id="roleError" class="text-danger text-center d-none mt-2">
                        Silakan pilih jenis akun
                    </div>
                    {{-- DINAMIS INSTANSI --}}
                    <div id="dynamicAccountField">
                        {{-- MADRASAH --}}
                        <div id="madrasahField" class="role-fields d-none mt-3">
                            <label class="form-label">
                                Nama Madrasah
                            </label>
                            <div class="search-select">
                                <input type="text" id="madrasahSearch" class="form-control"
                                    placeholder="Cari nama madrasah..." autocomplete="off">
                                <input type="hidden" name="madrasah_id" id="madrasahValue">
                                <div class="search-dropdown" id="madrasahDropdown">
                                    @foreach ($madrasahs as $madrasah)
                                        <div class="dropdown-item-custom" data-id="{{ $madrasah->id }}">
                                            {{ $madrasah->nama_madrasah }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- PENGAWAS --}}
                        <div id="pengawasField" class="role-fields d-none mt-3">
                            <label class="form-label">
                                Wilayah Pengawas
                            </label>
                            <select name="wilayah_pengawas_id" class="form-select">
                                <option value="">
                                    Pilih Wilayah
                                </option>
                                @foreach ($wilayahPengawas as $wilayah)
                                    <option value="{{ $wilayah->id }}">
                                        {{ $wilayah->unit_kerja }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- DATA AKUN --}}
                    <span class="section-label mt-4">
                        Data Akun
                    </span>
                    <div class="form-grid">
                        <div>
                            <label>
                                Nama Lengkap
                            </label>
                            <input type="text" name="nama" value="{{ old('nama') }}"
                                class="form-control @error('nama') is-invalid @enderror" placeholder="Nama lengkap">
                            @error('nama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label>
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="email@gmail.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label>
                                Username
                            </label>
                            <input type="text" name="username" value="{{ old('username') }}"
                                class="form-control @error('username') is-invalid @enderror" placeholder="Username">
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label>
                                No HP
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                class="form-control @error('no_hp') is-invalid @enderror" placeholder="08xxxx">
                        </div>
                        <div>
                            <label>
                                Password
                            </label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="tambahIsActive"
                                    name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="tambahIsActive">
                                    Akun Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn-register">
                        <i class="bx bx-save"></i>
                        Simpan Akun
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- ================= MODAL DETAIL / EDIT AKUN ================= --}}
<div class="modal fade" id="modalDetailAkun" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered presma-dialog">
        <form method="POST" id="formDetailAkun">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_source" value="edit">
            <input type="hidden" name="user_id" value="{{ old('user_id') }}">
            <div class="modal-content presma-account-modal">
                {{-- HEADER --}}
                <div class="modal-header">
                    <div>
                        <span class="modal-tag">
                            PRESMA ACCOUNT
                        </span>
                        <h5 class="modal-title">
                            <i class="bx bx-user-check"></i>
                            Detail Akun
                        </h5>
                        <small>
                            Lihat dan perbarui data pengguna.
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>
                </div>
                {{-- BODY --}}
                <div class="modal-body">
                    <div id="editLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-success" role="status"></div>
                        <div class="mt-2 text-muted">Memuat data akun...</div>
                    </div>
                    <div id="editFormWrapper">
                        {{-- ROLE --}}
                        <span class="section-label">
                            Jenis Akun
                        </span>
                        <div class="role-container">
                            @foreach ($roles as $role)
                                <label class="role-card">
                                    <input type="radio" name="role_id" value="{{ $role->id }}"
                                        class="role-radio role-radio-edit" data-role="{{ strtolower($role->nama) }}"
                                        {{ old('role_id') == $role->id ? 'checked' : '' }}>
                                    <div class="role-body">
                                        <div class="role-icon">
                                            @if ($role->nama == 'Administrator')
                                                <i class="bx bxs-crown"></i>
                                            @elseif($role->nama == 'Madrasah')
                                                <i class="bx bxs-school"></i>
                                            @elseif($role->nama == 'Pengawas')
                                                <i class="bx bxs-map-pin"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h5>
                                                {{ $role->nama }}
                                            </h5>
                                            <small>
                                                @if ($role->nama == 'Administrator')
                                                    Pengelola sistem
                                                @elseif($role->nama == 'Madrasah')
                                                    Admin Madrasah
                                                @else
                                                    Pengawas wilayah
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div id="roleErrorEdit" class="text-danger text-center d-none mt-2">
                            Silakan pilih jenis akun
                        </div>
                        {{-- DINAMIS INSTANSI --}}
                        <div id="dynamicAccountFieldEdit">
                            {{-- MADRASAH --}}
                            <div id="madrasahFieldEdit" class="role-fields d-none mt-3">
                                <label class="form-label">
                                    Nama Madrasah
                                </label>
                                <div class="search-select">
                                    <input type="text" id="madrasahSearchEdit" class="form-control"
                                        value="{{ old('madrasah_id') ? optional(collect($madrasahs)->firstWhere('id', old('madrasah_id')))->nama_madrasah : '' }}"
                                        placeholder="Cari nama madrasah..." autocomplete="off">
                                    <input type="hidden" name="madrasah_id" id="madrasahValueEdit"
                                        value="{{ old('madrasah_id') }}">
                                    <div class="search-dropdown" id="madrasahDropdownEdit">
                                        @foreach ($madrasahs as $madrasah)
                                            <div class="dropdown-item-custom" data-id="{{ $madrasah->id }}">
                                                {{ $madrasah->nama_madrasah }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- PENGAWAS --}}
                            <div id="pengawasFieldEdit" class="role-fields d-none mt-3">
                                <label class="form-label">
                                    Wilayah Pengawas
                                </label>
                                <select name="wilayah_pengawas_id" class="form-select">
                                    <option value="">
                                        Pilih Wilayah
                                    </option>
                                    @foreach ($wilayahPengawas as $wilayah)
                                        <option value="{{ $wilayah->id }}"
                                            {{ old('wilayah_pengawas_id') == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->unit_kerja }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- DATA AKUN --}}
                        <span class="section-label mt-4">
                            Data Akun
                        </span>
                        <div class="form-grid">
                            <div>
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    placeholder="Nama lengkap">
                                @error('nama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="email@gmail.com">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label>Username</label>
                                <input type="text" name="username" value="{{ old('username') }}"
                                    class="form-control @error('username') is-invalid @enderror"
                                    placeholder="Username">
                                @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label>No HP</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                    class="form-control @error('no_hp') is-invalid @enderror" placeholder="08xxxx">
                            </div>
                            <div>
                                <label>Password Baru</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Kosongkan jika tidak diubah">
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" role="switch" id="editIsActive"
                                        name="is_active" value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="editIsActive">
                                        Akun Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn-register">
                        <i class="bx bx-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                @if (old('form_source') === 'edit')
                    document.getElementById('formDetailAkun').action =
                        "{{ route('user-management.update', old('user_id')) }}";
                    new bootstrap.Modal(document.getElementById('modalDetailAkun')).show();
                @else
                    new bootstrap.Modal(document.getElementById('modalTambahAkun')).show();
                @endif
            });
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("formTambahAkun");
            const roles = document.querySelectorAll(".role-radio");
            const madrasahField = document.getElementById("madrasahField");
            const pengawasField = document.getElementById("pengawasField");
            const madrasahSearch = document.getElementById("madrasahSearch");
            const madrasahDropdown = document.getElementById("madrasahDropdown");
            const madrasahValue = document.getElementById("madrasahValue");
            const roleError = document.getElementById("roleError");
            // =====================================
            // REQUIRED DINAMIS
            // =====================================
            function setRequired(container, status) {
                container.querySelectorAll("input,select").forEach(el => {
                    if (status) {
                        el.setAttribute("required", "required");
                    } else {
                        el.removeAttribute("required");
                    }
                });
            }
            // =====================================
            // RESET INSTANSI
            // =====================================
            function resetInstansi() {
                madrasahSearch.value = "";
                madrasahValue.value = "";
                pengawasField.querySelector("select").value = "";
                setRequired(madrasahField, false);
                setRequired(pengawasField, false);
            }
            // =====================================
            // ROLE CHANGE
            // =====================================
            roles.forEach(role => {
                role.addEventListener("change", function() {
                    roleError.classList.add("d-none");
                    // sembunyikan semua
                    madrasahField.classList.add("d-none");
                    pengawasField.classList.add("d-none");
                    resetInstansi();
                    let type = this.dataset.role;
                    if (type === "madrasah") {
                        madrasahField.classList.remove("d-none");
                        setRequired(madrasahField, true);
                    } else if (type === "pengawas") {
                        pengawasField.classList.remove("d-none");
                        setRequired(pengawasField, true);
                    }
                    // administrator tidak ada instansi
                });
            });
            // =====================================
            // SEARCH MADRASAH
            // =====================================
            if (madrasahSearch) {
                madrasahSearch.addEventListener("focus", () => {
                    madrasahDropdown.style.display = "block";
                });
                madrasahSearch.addEventListener("keyup", function() {
                    let keyword = this.value.toLowerCase();
                    document.querySelectorAll(".dropdown-item-custom")
                        .forEach(item => {
                            let text = item.innerText.toLowerCase();
                            if (text.includes(keyword)) {
                                item.style.display = "block";
                            } else {
                                item.style.display = "none";
                            }
                        });
                    madrasahDropdown.style.display = "block";
                });
                document.querySelectorAll(".dropdown-item-custom")
                    .forEach(item => {
                        item.addEventListener("click", function() {
                            madrasahSearch.value = this.innerText;
                            madrasahValue.value = this.dataset.id;
                            madrasahDropdown.style.display = "none";
                        });
                    });
                document.addEventListener("click", function(e) {
                    if (!e.target.closest(".search-select")) {
                        madrasahDropdown.style.display = "none";
                    }
                });
            }
            // =====================================
            // VALIDASI SUBMIT
            // =====================================
            form.addEventListener("submit", function(e) {
                const roleChecked = document.querySelector(
                    'input[name="role_id"]:checked'
                );
                if (!roleChecked) {
                    e.preventDefault();
                    roleError.classList.remove("d-none");
                    document
                        .querySelector(".role-container")
                        .scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    return false;
                }
                let role = roleChecked.dataset.role;
                // validasi madrasah
                if (role === "madrasah" && !madrasahValue.value) {
                    e.preventDefault();
                    alert("Silakan pilih madrasah terlebih dahulu");
                    madrasahSearch.focus();
                    return false;
                }
                // validasi pengawas
                if (role === "pengawas") {
                    let wilayah =
                        pengawasField.querySelector("select").value;
                    if (!wilayah) {
                        e.preventDefault();
                        alert("Silakan pilih wilayah pengawas");
                        return false;
                    }
                }
            });
            // =====================================
            // RESET SAAT MODAL DITUTUP
            // =====================================
            const modal =
                document.getElementById("modalTambahAkun");
            modal.addEventListener("hidden.bs.modal", function() {
                form.reset();
                madrasahField.classList.add("d-none");
                pengawasField.classList.add("d-none");
                madrasahDropdown.style.display = "none";
                madrasahValue.value = "";
                roleError.classList.add("d-none");
            });
        });
    </script>
    {{-- =====================================
         SCRIPT DETAIL / EDIT AKUN
         ===================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editModalEl = document.getElementById("modalDetailAkun");
            const formEdit = document.getElementById("formDetailAkun");
            const loading = document.getElementById("editLoading");
            const formWrapper = document.getElementById("editFormWrapper");
            const madrasahFieldEdit = document.getElementById("madrasahFieldEdit");
            const pengawasFieldEdit = document.getElementById("pengawasFieldEdit");
            const madrasahSearchEdit = document.getElementById("madrasahSearchEdit");
            const madrasahDropdownEdit = document.getElementById("madrasahDropdownEdit");
            const madrasahValueEdit = document.getElementById("madrasahValueEdit");
            const roleErrorEdit = document.getElementById("roleErrorEdit");
            // template URL, token "__ID__" akan diganti dengan id user asli
            const showUrlTemplate = "{{ route('user-management.edit', '__ID__') }}";
            const updateUrlTemplate = "{{ route('user-management.update', '__ID__') }}";

            function resetInstansiEdit() {
                madrasahSearchEdit.value = "";
                madrasahValueEdit.value = "";
                pengawasFieldEdit.querySelector("select").value = "";
            }

            function revealRoleField(type) {
                madrasahFieldEdit.classList.add("d-none");
                pengawasFieldEdit.classList.add("d-none");
                if (type === "madrasah") {
                    madrasahFieldEdit.classList.remove("d-none");
                } else if (type === "pengawas") {
                    pengawasFieldEdit.classList.remove("d-none");
                }
            }
            // toggle field saat role diganti manual oleh user
            document.querySelectorAll(".role-radio-edit").forEach(radio => {
                radio.addEventListener("change", function() {
                    roleErrorEdit.classList.add("d-none");
                    resetInstansiEdit();
                    revealRoleField(this.dataset.role);
                });
            });
            // pencarian madrasah (khusus modal edit)
            madrasahSearchEdit.addEventListener("focus", () => {
                madrasahDropdownEdit.style.display = "block";
            });
            madrasahSearchEdit.addEventListener("keyup", function() {
                let keyword = this.value.toLowerCase();
                madrasahDropdownEdit.querySelectorAll(".dropdown-item-custom").forEach(item => {
                    item.style.display = item.innerText.toLowerCase().includes(keyword) ?
                        "block" : "none";
                });
                madrasahDropdownEdit.style.display = "block";
            });
            madrasahDropdownEdit.querySelectorAll(".dropdown-item-custom").forEach(item => {
                item.addEventListener("click", function() {
                    madrasahSearchEdit.value = this.innerText;
                    madrasahValueEdit.value = this.dataset.id;
                    madrasahDropdownEdit.style.display = "none";
                });
            });
            document.addEventListener("click", function(e) {
                if (!e.target.closest("#madrasahFieldEdit .search-select")) {
                    madrasahDropdownEdit.style.display = "none";
                }
            });
            // jika modal terbuka ulang akibat validasi gagal, tampilkan field yang sesuai
            const preCheckedRole = document.querySelector('.role-radio-edit:checked');
            if (preCheckedRole) {
                revealRoleField(preCheckedRole.dataset.role);
            }
            // saat tombol "Detail" diklik -> ambil data user via AJAX lalu isi form
            editModalEl.addEventListener("show.bs.modal", function(event) {
                const button = event.relatedTarget;
                if (!button) return; // dibuka lewat script error, bukan klik tombol
                const userId = button.getAttribute("data-id");
                formEdit.reset();
                madrasahFieldEdit.classList.add("d-none");
                pengawasFieldEdit.classList.add("d-none");
                roleErrorEdit.classList.add("d-none");
                formWrapper.classList.add("d-none");
                loading.classList.remove("d-none");
                formEdit.action = updateUrlTemplate.replace('__ID__', userId);
                formEdit.querySelector('[name="user_id"]').value = userId;
                fetch(showUrlTemplate.replace('__ID__', userId), {
                        headers: {
                            "Accept": "application/json"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        const user = data.user;
                        formEdit.querySelector('[name="nama"]').value = user.nama ?? "";
                        formEdit.querySelector('[name="email"]').value = user.email ?? "";
                        formEdit.querySelector('[name="username"]').value = user.username ?? "";
                        formEdit.querySelector('[name="no_hp"]').value = user.no_hp ?? "";
                        formEdit.querySelector('[name="is_active"]').checked = !!user.is_active;
                        const radio = formEdit.querySelector(
                            `input[name="role_id"][value="${user.role_id}"]`);
                        if (radio) {
                            radio.checked = true;
                            revealRoleField(radio.dataset.role);
                        }
                        if (user.role?.nama === "Madrasah" && user.madrasah) {
                            madrasahSearchEdit.value = user.madrasah.nama_madrasah;
                            madrasahValueEdit.value = user.madrasah_id;
                        }
                        if (user.role?.nama === "Pengawas") {
                            pengawasFieldEdit.querySelector("select").value = user
                                .wilayah_pengawas_id ?? "";
                        }
                    })
                    .catch(() => {
                        alert("Gagal memuat data akun. Silakan coba lagi.");
                    })
                    .finally(() => {
                        loading.classList.add("d-none");
                        formWrapper.classList.remove("d-none");
                    });
            });
            // validasi role sebelum submit
            formEdit.addEventListener("submit", function(e) {
                const roleChecked = formEdit.querySelector('input[name="role_id"]:checked');
                if (!roleChecked) {
                    e.preventDefault();
                    roleErrorEdit.classList.remove("d-none");
                    return false;
                }
                let role = roleChecked.dataset.role;
                if (role === "madrasah" && !madrasahValueEdit.value) {
                    e.preventDefault();
                    alert("Silakan pilih madrasah terlebih dahulu");
                    madrasahSearchEdit.focus();
                    return false;
                }
                if (role === "pengawas" && !pengawasFieldEdit.querySelector("select").value) {
                    e.preventDefault();
                    alert("Silakan pilih wilayah pengawas");
                    return false;
                }
            });
            editModalEl.addEventListener("hidden.bs.modal", function() {
                formEdit.reset();
                madrasahFieldEdit.classList.add("d-none");
                pengawasFieldEdit.classList.add("d-none");
                madrasahDropdownEdit.style.display = "none";
                roleErrorEdit.classList.add("d-none");
            });
        });
    </script>
@endpush
