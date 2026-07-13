@extends('layouts.base')

@push('styles')
    <style>
        /* ==========================================================
                           PAGE HEADER
                        ========================================================== */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 700;
            color: #1c2b2d;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: #6b7785;
            font-size: 0.925rem;
            margin-bottom: 0;
        }

        .btn-jma-primary {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(25, 135, 84, 0.25);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-jma-primary:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.32);
        }

        .btn-jma-outline {
            background-color: #fff;
            border: 1.5px solid #198754;
            color: #198754;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 9px 20px;
            border-radius: 12px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-jma-outline:hover {
            background-color: #198754;
            color: #fff;
        }

        /* ==========================================================
                           SUMMARY CARDS
                        ========================================================== */
        .summary-row {
            margin-bottom: 24px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            padding: 22px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(20, 30, 40, 0.07);
        }

        .summary-card .icon-box {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .summary-card .icon-box.icon-neutral {
            background: rgba(33, 37, 41, 0.06);
            color: #495057;
        }

        .summary-card .icon-box.icon-success {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
        }

        .summary-card .icon-box.icon-warning {
            background: rgba(255, 145, 0, 0.12);
            color: #fd7e14;
        }

        .summary-card .summary-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1c2b2d;
            line-height: 1.1;
        }

        .summary-card .summary-label {
            font-size: 0.82rem;
            color: #6b7785;
            font-weight: 500;
            margin-top: 2px;
        }

        /* ==========================================================
                           FILTER CARD
                        ========================================================== */
        .filter-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            padding: 22px 24px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            margin-bottom: 22px;
        }

        .filter-card .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 12px;
            border-color: #e3e7ed;
            font-size: 0.9rem;
            padding: 9px 14px;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
        }

        .filter-card .btn-filter {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 22px;
        }

        .filter-card .btn-filter:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }

        .filter-card .btn-reset {
            background-color: #fff;
            border: 1px solid #e3e7ed;
            color: #6b7785;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 18px;
        }

        .filter-card .btn-reset:hover {
            background-color: #f5f7fb;
            color: #495057;
        }

        /* ==========================================================
                           PROGRESS INFO
                        ========================================================== */
        .progress-info {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            padding: 18px 24px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .progress-info .progress-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            white-space: nowrap;
        }

        .progress-info .progress {
            height: 8px;
            border-radius: 999px;
            flex: 1;
            min-width: 160px;
            background-color: #eef1f5;
        }

        .progress-info .progress-bar {
            background-color: #198754;
            border-radius: 999px;
        }

        .progress-info .progress-percent {
            font-size: 0.85rem;
            font-weight: 700;
            color: #198754;
            white-space: nowrap;
        }

        /* ==========================================================
                           TOOLBAR
                        ========================================================== */
        .assign-toolbar {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 18px 18px 0 0;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: none;
        }

        .assign-toolbar .selected-badge {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 16px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .assign-toolbar .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-toolbar-assign {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 12px;
            padding: 8px 18px;
        }

        .btn-toolbar-assign:hover {
            background-color: #157347;
            color: #fff;
        }

        .btn-toolbar-export {
            background-color: #fff;
            border: 1px solid #e3e7ed;
            color: #495057;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 12px;
            padding: 8px 18px;
        }

        .btn-toolbar-export:hover {
            background-color: #f5f7fb;
            color: #1c2b2d;
        }

        /* ==========================================================
                           TABLE
                        ========================================================== */
        .assign-table-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .assign-table {
            margin-bottom: 0;
            width: 100%;
        }

        .assign-table thead th {
            background-color: #f5f7fb;
            color: #6b7785;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: none;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .assign-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #344054;
            border-bottom: 1px solid #f1f3f7;
        }

        .assign-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .assign-table tbody tr:hover {
            background-color: #f8faf9;
        }

        .assign-table tbody tr:last-child td {
            border-bottom: none;
        }

        .madrasah-name {
            font-weight: 600;
            color: #1c2b2d;
        }

        .madrasah-npsn {
            font-size: 0.76rem;
            color: #9aa4b2;
        }

        .jenjang-badge {
            background-color: rgba(13, 110, 253, 0.08);
            color: #0d6efd;
            font-weight: 600;
            font-size: 0.76rem;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .prestasi-count {
            font-weight: 700;
            color: #1c2b2d;
        }

        .asesor-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar-mini {
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 50%;
            background-color: #198754;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .asesor-empty {
            color: #b0b8c1;
            font-style: italic;
            font-size: 0.85rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
        }

        .status-badge.status-assigned {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .status-badge.status-unassigned {
            background-color: rgba(253, 126, 20, 0.12);
            color: #fd7e14;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #e3e7ed;
            background-color: #fff;
            color: #495057;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.15s ease;
        }

        .action-btn:hover {
            background-color: #f5f7fb;
            color: #198754;
            border-color: #198754;
        }

        .action-btn-assign {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 6px 14px;
            white-space: nowrap;
        }

        .action-btn-assign:hover {
            background-color: #157347;
            color: #fff;
        }

        .table-check {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* ==========================================================
                           PAGINATION FOOTER
                        ========================================================== */
        .table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 16px 22px;
            border-top: 1px solid #f1f3f7;
        }

        .table-footer .footer-info {
            font-size: 0.82rem;
            color: #6b7785;
        }

        .pagination .page-link {
            border-radius: 10px;
            margin: 0 3px;
            border: 1px solid #e3e7ed;
            color: #495057;
            font-size: 0.85rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }

        /* ==========================================================
                           MODAL
                        ========================================================== */
        .assign-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 50px rgba(20, 30, 40, 0.18);
        }

        .assign-modal .modal-header {
            border-bottom: 1px solid #f1f3f7;
            padding: 22px 26px;
        }

        .assign-modal .modal-title {
            font-weight: 700;
            color: #1c2b2d;
            font-size: 1.15rem;
        }

        .assign-modal .modal-body {
            padding: 24px 26px;
        }

        .assign-modal .modal-footer {
            border-top: 1px solid #f1f3f7;
            padding: 18px 26px;
        }

        .assign-modal .form-label {
            font-size: 0.83rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .assign-modal .form-control,
        .assign-modal .form-select {
            border-radius: 12px;
            border-color: #e3e7ed;
            font-size: 0.9rem;
            padding: 10px 14px;
        }

        .assign-modal .form-control:focus,
        .assign-modal .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
        }

        .assign-modal .info-box {
            background-color: rgba(25, 135, 84, 0.08);
            color: #157347;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .assign-modal .btn-modal-cancel {
            background-color: #fff;
            border: 1px solid #e3e7ed;
            color: #6b7785;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 20px;
        }

        .assign-modal .btn-modal-cancel:hover {
            background-color: #f5f7fb;
            color: #495057;
        }

        .assign-modal .btn-modal-save {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 22px;
        }

        .assign-modal .btn-modal-save:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <main class="content" style="background-color: #f5f7fb;">

        @php
            // Helper untuk inisial avatar asesor
            if (!function_exists('inisialAsesor')) {
                function inisialAsesor($nama)
                {
                    $bagian = explode(' ', trim($nama));
                    $inisial = '';
                    foreach ($bagian as $b) {
                        $inisial .= strtoupper(substr($b, 0, 1));
                    }
                    return substr($inisial, 0, 2);
                }
            }
        @endphp

        {{-- ================================================================
         1. PAGE HEADER
    ================================================================= --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Assign Asesor ke Madrasah</h1>
                <p class="page-subtitle">Kelola pembagian asesor terhadap madrasah peserta Jakarta Madrasah Awards.</p>
            </div>
            <div>
                <a href="{{ route('assign-asesor.export-pdf', request()->query()) }}" target="_blank"
                    class="btn btn-jma-outline me-2">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
                </a>
            </div>
        </div>

        {{-- ================================================================
         2. STATISTIK
    ================================================================= --}}
        <div class="row summary-row g-3">
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-neutral">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $totalAsesor }}</div>
                        <div class="summary-label">Total Asesor</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-neutral">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $totalMadrasah }}</div>
                        <div class="summary-label">Total Madrasah</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $sudahAssigned }}</div>
                        <div class="summary-label">Sudah Assigned</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-warning">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $belumAssigned }}</div>
                        <div class="summary-label">Belum Assigned</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
         3. FILTER
    ================================================================= --}}
        <div class="filter-card">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Cari Madrasah</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama madrasah / NPSN"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenjang</label>
                        <select class="form-select" name="jenjang">
                            <option value="" {{ request('jenjang') ? '' : 'selected' }}>Semua Jenjang</option>
                            @foreach ($jenjang as $item)
                                <option value="{{ $item }}" {{ request('jenjang') == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Wilayah</label>
                        <select class="form-select" name="wilayah">
                            <option value="" {{ request('wilayah') ? '' : 'selected' }}>Semua Wilayah</option>
                            @foreach ($wilayah as $item)
                                <option value="{{ $item }}" {{ request('wilayah') == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Assignment</label>
                        <select class="form-select" name="status">
                            <option value="" {{ request('status') ? '' : 'selected' }}>Semua</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned
                            </option>
                            <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Belum
                                Assigned</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Tahun</label>
                        <select class="form-select">
                            <option selected>2026</option>
                            <option>2025</option>
                            <option>2024</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-filter flex-fill">
                                <i class="bi bi-funnel-fill me-1"></i> Filter
                            </button>
                            <button type="reset" class="btn btn-reset"
                                onclick="window.location.href='{{ url()->current() }}'">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ================================================================
         PROGRESS INFO
    ================================================================= --}}
        <div class="progress-info">
            <div class="progress-label"><i class="bi bi-graph-up-arrow me-1 text-success"></i> Progres Assignment</div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{ $persenAssigned }}%"></div>
            </div>
            <div class="progress-percent">{{ $sudahAssigned }} dari {{ $totalMadrasah }} sudah assigned
                ({{ $persenAssigned }}%)</div>
        </div>

        {{-- ================================================================
         4. TOOLBAR
    ================================================================= --}}
        <div class="assign-toolbar">
            <span class="selected-badge">
                <i class="bi bi-check2-square"></i> 0 Madrasah dipilih
            </span>
            <div class="toolbar-actions">
                <button type="button" class="btn btn-toolbar-assign" data-bs-toggle="modal"
                    data-bs-target="#modalAssignMassal">
                    <i class="bi bi-person-check-fill me-1"></i> Assign Asesor
                </button>
            </div>
        </div>

        {{-- ================================================================
         5. TABEL
    ================================================================= --}}
        <div class="assign-table-card">
            <div class="table-responsive">
                <table class="table assign-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="table-check" id="checkAll">
                            </th>
                            <th>Madrasah</th>
                            <th>Jenjang</th>
                            <th>Wilayah</th>
                            <th class="text-center">Jumlah Prestasi</th>
                            <th>Asesor Saat Ini</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($madrasahs as $madrasah)
                            @php
                                $asesorAktif = $madrasah->assignAsesor?->asesor;
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" class="table-check row-check" value="{{ $madrasah->id }}">
                                </td>
                                <td>
                                    <div class="madrasah-name">{{ $madrasah->nama_madrasah }}</div>
                                    <div class="madrasah-npsn">NPSN: {{ $madrasah->npsn }}</div>
                                </td>
                                <td>
                                    <span class="jenjang-badge">{{ $madrasah->jenjang_madrasah }}</span>
                                </td>
                                <td>{{ $madrasah->kota }}</td>
                                <td class="text-center">
                                    <span class="prestasi-count">{{ $madrasah->prestasis_count }}</span>
                                </td>
                                <td>
                                    @if ($asesorAktif)
                                        <div class="asesor-cell">
                                            <div class="avatar-mini">{{ inisialAsesor($asesorAktif->nama) }}</div>
                                            <span>{{ $asesorAktif->nama }}</span>
                                        </div>
                                    @else
                                        <span class="asesor-empty">Belum ditugaskan</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($madrasah->assignAsesor)
                                        <span class="status-badge status-assigned">
                                            <i class="bi bi-check-circle-fill"></i> Assigned
                                        </span>
                                    @else
                                        <span class="status-badge status-unassigned">
                                            <i class="bi bi-exclamation-circle-fill"></i> Belum Assigned
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($madrasah->assignAsesor)
                                        <button type="button" class="action-btn" data-bs-toggle="modal"
                                            data-bs-target="#modalAssign" title="Ubah Asesor" data-bs-toggle="tooltip"
                                            data-id="{{ $madrasah->id }}" data-nama="{{ $madrasah->nama_madrasah }}"
                                            data-asesor="{{ $asesorAktif?->id }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn action-btn-assign" data-bs-toggle="modal"
                                            data-bs-target="#modalAssign" data-id="{{ $madrasah->id }}"
                                            data-nama="{{ $madrasah->nama_madrasah }}" data-asesor="">
                                            Assign
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada madrasah peserta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="footer-info">
                    Menampilkan {{ $madrasahs->firstItem() ?? 0 }}-{{ $madrasahs->lastItem() ?? 0 }} dari
                    {{ $madrasahs->total() }} madrasah
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item {{ $madrasahs->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $madrasahs->previousPageUrl() ?? '#' }}"><i
                                    class="bi bi-chevron-left"></i></a>
                        </li>
                        @foreach (range(1, $madrasahs->lastPage()) as $page)
                            <li class="page-item {{ $madrasahs->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $madrasahs->url($page) }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ $madrasahs->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $madrasahs->nextPageUrl() ?? '#' }}"><i
                                    class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>
@endsection
{{-- ================================================================
         6. MODAL ASSIGN (SATU MADRASAH)
    ================================================================= --}}
<div class="modal fade assign-modal" id="modalAssign" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Asesor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAssign">
                    @csrf
                    <input type="hidden" id="assignMadrasahId" value="">
                    <div class="mb-3">
                        <label class="form-label">Nama Madrasah</label>
                        <input type="text" id="assignMadrasahNama" class="form-control" value="" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Asesor</label>
                        <select class="form-select" id="assignAsesorId">
                            <option selected disabled value="">-- Pilih Asesor --</option>
                            @foreach ($daftarAsesor as $asesor)
                                <option value="{{ $asesor->id }}">{{ $asesor->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="assignCatatan" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-modal-save" id="btnSimpanAssign">Simpan Assignment</button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================
         7. MODAL ASSIGN MASSAL
    ================================================================= --}}
<div class="modal fade assign-modal" id="modalAssignMassal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Asesor Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="info-box">
                    <i class="bi bi-info-circle-fill"></i> <span id="massalSelectedCount">0</span> Madrasah dipilih
                </div>
                <form id="formAssignMassal">
                    @csrf
                    <div class="mb-0">
                        <label class="form-label">Pilih Asesor</label>
                        <select class="form-select" id="massalAsesorId">
                            <option selected disabled value="">-- Pilih Asesor --</option>
                            @foreach ($daftarAsesor as $asesor)
                                <option value="{{ $asesor->id }}">{{ $asesor->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-modal-save" id="btnSimpanAssignMassal">Assign Sekarang</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        const ASSIGN_STORE_URL = "{{ route('assign-asesor.store') }}";
        const CSRF_TOKEN = document.querySelector('#formAssign input[name="_token"]').value;

        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('checkAll');
            const rowChecks = document.querySelectorAll('.row-check');
            const selectedBadge = document.querySelector('.selected-badge');
            const massalCount = document.getElementById('massalSelectedCount');

            function getCheckedIds() {
                return Array.from(rowChecks)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
            }

            function updateSelectedCount() {
                const total = getCheckedIds().length;
                if (selectedBadge) {
                    selectedBadge.innerHTML = '<i class="bi bi-check2-square"></i> ' + total + ' Madrasah dipilih';
                }
                if (massalCount) {
                    massalCount.textContent = total;
                }
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    rowChecks.forEach(cb => cb.checked = checkAll.checked);
                    updateSelectedCount();
                });
            }

            rowChecks.forEach(cb => cb.addEventListener('change', updateSelectedCount));

            // ==========================================================
            // Modal Assign (single) — isi form dari data-* tombol Assign/Ubah
            // ==========================================================
            const modalAssign = document.getElementById('modalAssign');
            if (modalAssign) {
                modalAssign.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    if (!btn) return;

                    const madrasahId = btn.getAttribute('data-id');
                    const madrasahNama = btn.getAttribute('data-nama');
                    const asesorId = btn.getAttribute('data-asesor');

                    document.getElementById('assignMadrasahId').value = madrasahId || '';
                    document.getElementById('assignMadrasahNama').value = madrasahNama || '';

                    const asesorSelect = document.getElementById('assignAsesorId');
                    asesorSelect.value = asesorId || '';
                });
            }

            // ==========================================================
            // Submit Assign (single) — TODO: menunggu konfirmasi nama route
            // ==========================================================
            const btnSimpanAssign = document.getElementById('btnSimpanAssign');
            if (btnSimpanAssign) {
                btnSimpanAssign.addEventListener('click', function() {
                    const payload = {
                        madrasah_id: document.getElementById('assignMadrasahId').value,
                        asesor_id: document.getElementById('assignAsesorId').value,
                    };

                    if (!payload.asesor_id) {
                        alert('Silakan pilih asesor terlebih dahulu.');
                        return;
                    }

                    btnSimpanAssign.disabled = true;

                    fetch(ASSIGN_STORE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                            },
                            body: JSON.stringify(payload),
                        })
                        .then(async (res) => {
                            if (!res.ok) {
                                const err = await res.json().catch(() => null);
                                throw new Error(err?.message || 'Gagal menyimpan assignment.');
                            }
                            window.location.reload();
                        })
                        .catch((err) => {
                            alert(err.message);
                            btnSimpanAssign.disabled = false;
                        });
                });
            }

            // ==========================================================
            // Submit Assign Massal — TODO: menunggu konfirmasi nama route
            // ==========================================================
            const btnSimpanAssignMassal = document.getElementById('btnSimpanAssignMassal');
            if (btnSimpanAssignMassal) {
                btnSimpanAssignMassal.addEventListener('click', function() {
                    const madrasahIds = getCheckedIds();
                    const asesorId = document.getElementById('massalAsesorId').value;

                    if (madrasahIds.length === 0) {
                        alert('Pilih minimal satu madrasah terlebih dahulu.');
                        return;
                    }

                    if (!asesorId) {
                        alert('Silakan pilih asesor terlebih dahulu.');
                        return;
                    }

                    btnSimpanAssignMassal.disabled = true;

                    // Resource route hanya menyediakan satu-per-satu (store),
                    // jadi assign massal memanggil store berulang untuk tiap madrasah terpilih.
                    const requests = madrasahIds.map((id) =>
                        fetch(ASSIGN_STORE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                            },
                            body: JSON.stringify({
                                madrasah_id: id,
                                asesor_id: asesorId
                            }),
                        })
                    );

                    Promise.all(requests)
                        .then((responses) => {
                            const failed = responses.some((res) => !res.ok);
                            if (failed) {
                                throw new Error('Sebagian assignment gagal disimpan.');
                            }
                            window.location.reload();
                        })
                        .catch((err) => {
                            alert(err.message);
                            btnSimpanAssignMassal.disabled = false;
                        });
                });
            }
        });
    </script>
@endpush
