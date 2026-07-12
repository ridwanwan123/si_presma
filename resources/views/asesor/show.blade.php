@extends('layouts.base')

@push('styles')
    <style>
        /* ==========================================================
                                                   GENERAL
                                                ========================================================== */
        .content {
            background-color: #f5f7fb;
        }

        /* ==========================================================
                                                   PAGE HEADER
                                                ========================================================== */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: #1c2b2d;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .page-subtitle {
            color: #6b7785;
            font-size: 0.9rem;
            margin-bottom: 0;
            max-width: 620px;
        }

        .badge-status-header {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            font-size: 0.76rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .progress-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            min-width: 260px;
        }

        .progress-card .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .progress-card .progress-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
        }

        .progress-card .progress-percent {
            font-size: 0.9rem;
            font-weight: 700;
            color: #198754;
        }

        .progress-card .progress {
            height: 8px;
            border-radius: 999px;
            background-color: #eef1f5;
            margin-bottom: 14px;
        }

        .progress-card .progress-bar {
            background-color: #198754;
            border-radius: 999px;
        }

        .btn-kumpulkan {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 12px;
            padding: 9px 18px;
            width: 100%;
            box-shadow: 0 6px 16px rgba(25, 135, 84, 0.22);
        }

        .btn-kumpulkan:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }

        /* ==========================================================
                                                   INFO CARD (KOLOM KIRI) - sticky
                                                ========================================================== */
        .info-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            position: sticky;
            top: 20px;
        }

        .info-card .madrasah-logo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 14px;
        }

        .info-card .info-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1c2b2d;
            margin-bottom: 14px;
        }

        .info-card .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 0;
            border-bottom: 1px dashed #eef1f5;
            font-size: 0.85rem;
        }

        .info-card .info-row:last-child {
            border-bottom: none;
        }

        .info-card .info-row .info-key {
            color: #9aa4b2;
        }

        .info-card .info-row .info-value {
            color: #344054;
            font-weight: 600;
            text-align: right;
        }

        .info-card .info-asesor {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 4px;
        }

        .info-card .avatar-mini {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #198754;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .info-card .info-divider {
            border-top: 1px solid #eef1f5;
            margin: 16px 0;
        }

        .info-card .rubrik-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 0.83rem;
            font-weight: 600;
            color: #198754;
            text-decoration: none;
            padding: 10px 12px;
            background-color: rgba(25, 135, 84, 0.06);
            border-radius: 12px;
        }

        .info-card .rubrik-link:hover {
            background-color: rgba(25, 135, 84, 0.12);
            color: #157347;
        }

        /* ==========================================================
                                                   QUICK STATS STRIP
                                                ========================================================== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .quick-stat-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 16px;
            padding: 16px 14px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            text-align: center;
        }

        .quick-stat-card .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1c2b2d;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .quick-stat-card.stat-highlight .stat-value {
            color: #198754;
        }

        .quick-stat-card .stat-label {
            font-size: 0.74rem;
            color: #6b7785;
            font-weight: 500;
        }

        @media (max-width: 767.98px) {
            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ==========================================================
                                                   FILTER CARD
                                                ========================================================== */
        .filter-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            margin-bottom: 20px;
        }

        .filter-card .form-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .filter-card .form-select {
            border-radius: 12px;
            border-color: #e3e7ed;
            font-size: 0.86rem;
            padding: 8px 14px;
        }

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
            font-size: 0.85rem;
            padding: 8px 20px;
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
            font-size: 0.85rem;
            padding: 8px 14px;
        }

        .filter-card .btn-reset:hover {
            background-color: #f5f7fb;
            color: #495057;
        }

        /* ==========================================================
                                                   PANDUAN PENILAIAN - inline alert bar
                                                ========================================================== */
        .panduan-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            background-color: rgba(25, 135, 84, 0.06);
            border: 1px solid rgba(25, 135, 84, 0.15);
            border-radius: 16px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        .panduan-bar .panduan-text {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #157347;
            font-weight: 500;
        }

        .panduan-bar .panduan-text i {
            font-size: 1rem;
        }

        .btn-rubrik {
            background-color: #fff;
            border: 1px solid #198754;
            color: #198754;
            font-weight: 600;
            font-size: 0.82rem;
            border-radius: 10px;
            padding: 7px 16px;
            white-space: nowrap;
        }

        .btn-rubrik:hover {
            background-color: #198754;
            color: #fff;
        }

        /* ==========================================================
                                                   ASSESSMENT CARD (KOLOM KANAN)
                                                ========================================================== */
        .assessment-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 20px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            overflow: hidden;
        }

        .assessment-card .nav-tabs {
            border-bottom: 1px solid #eef1f5;
            padding: 10px 20px 0;
        }

        .assessment-card .nav-tabs .nav-link {
            border: none;
            color: #6b7785;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 18px;
            border-radius: 12px 12px 0 0;
        }

        .assessment-card .nav-tabs .nav-link.active {
            color: #198754;
            background-color: rgba(25, 135, 84, 0.08);
        }

        .assessment-card .tab-content {
            padding: 20px;
        }

        .assessment-table {
            margin-bottom: 0;
            width: 100%;
        }

        .assessment-table thead th {
            background-color: #f5f7fb;
            color: #6b7785;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: none;
            padding: 12px 12px;
            white-space: nowrap;
        }

        .assessment-table tbody td {
            padding: 12px;
            vertical-align: middle;
            font-size: 0.86rem;
            color: #344054;
            border-bottom: 1px solid #f1f3f7;
        }

        .assessment-table tbody tr:hover {
            background-color: #f8faf9;
        }

        .assessment-table tbody tr:last-child td {
            border-bottom: none;
        }

        .prestasi-name {
            font-weight: 600;
            color: #1c2b2d;
        }

        .prestasi-meta {
            font-size: 0.76rem;
            color: #9aa4b2;
        }

        .category-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .category-badge.cat-akademik {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .category-badge.cat-nonakademik {
            background-color: rgba(111, 66, 193, 0.1);
            color: #6f42c1;
        }

        .category-badge.cat-keagamaan {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .bobot-value {
            color: #9aa4b2;
            font-weight: 600;
            font-size: 0.82rem;
        }

        .nilai-akhir-value {
            font-weight: 700;
            color: #198754;
            font-size: 0.9rem;
        }

        .nilai-belum {
            font-size: 0.78rem;
            color: #b0b8c1;
            font-style: italic;
        }

        .assessment-table .action-icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            border: 1px solid #e3e7ed;
            background-color: #fff;
            color: #6b7785;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            margin-right: 4px;
            transition: all 0.15s ease;
        }

        .assessment-table .action-icon-btn:hover {
            background-color: #f5f7fb;
            color: #198754;
            border-color: #198754;
        }

        .btn-nilai {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.76rem;
            padding: 6px 12px;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-nilai.btn-beri-nilai {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }

        .btn-nilai.btn-beri-nilai:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }

        .btn-nilai.btn-ubah-nilai {
            background-color: #fff;
            border-color: #e3e7ed;
            color: #495057;
        }

        .btn-nilai.btn-ubah-nilai:hover {
            background-color: #f5f7fb;
            color: #198754;
            border-color: #198754;
        }

        /* Ringkasan Nilai tab */
        .ringkasan-mini-card {
            background-color: #f5f7fb;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            height: 100%;
        }

        .ringkasan-mini-card .ringkasan-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1c2b2d;
            margin-bottom: 4px;
        }

        .ringkasan-mini-card .ringkasan-label {
            font-size: 0.78rem;
            color: #6b7785;
            font-weight: 500;
        }

        /* ==========================================================
                                                   MODAL NILAI PRESTASI
                                                ========================================================== */
        .nilai-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 50px rgba(20, 30, 40, 0.18);
        }

        .nilai-modal .modal-header {
            border-bottom: 1px solid #f1f3f7;
            padding: 22px 26px;
        }

        .nilai-modal .modal-title {
            font-weight: 700;
            color: #1c2b2d;
            font-size: 1.05rem;
        }

        .nilai-modal .modal-body {
            padding: 24px 26px;
        }

        .nilai-modal .modal-footer {
            border-top: 1px solid #f1f3f7;
            padding: 18px 26px;
        }

        .nilai-modal .prestasi-modal-name {
            font-weight: 700;
            color: #1c2b2d;
            font-size: 0.98rem;
            margin-bottom: 4px;
        }

        .nilai-modal .prestasi-modal-meta {
            font-size: 0.8rem;
            color: #6b7785;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nilai-modal .form-label {
            font-size: 0.83rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .nilai-modal .form-select,
        .nilai-modal .form-control {
            border-radius: 12px;
            border-color: #e3e7ed;
            font-size: 0.9rem;
            padding: 10px 14px;
        }

        .nilai-modal .form-select:focus,
        .nilai-modal .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
        }

        .nilai-modal .bobot-preview {
            background-color: #f5f7fb;
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
        }

        .nilai-modal .bobot-preview .preview-item {
            text-align: center;
            flex: 1;
        }

        .nilai-modal .bobot-preview .preview-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1c2b2d;
        }

        .nilai-modal .bobot-preview .preview-value.text-success {
            color: #198754 !important;
        }

        .nilai-modal .bobot-preview .preview-label {
            font-size: 0.72rem;
            color: #9aa4b2;
            font-weight: 500;
        }

        .nilai-modal .bobot-preview .preview-divider {
            width: 1px;
            height: 32px;
            background-color: #e3e7ed;
        }

        .nilai-modal .btn-modal-cancel {
            background-color: #fff;
            border: 1px solid #e3e7ed;
            color: #6b7785;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 20px;
        }

        .nilai-modal .btn-modal-cancel:hover {
            background-color: #f5f7fb;
            color: #495057;
        }

        .nilai-modal .btn-modal-save {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 9px 22px;
        }

        .nilai-modal .btn-modal-save:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .info-card {
                position: static;
            }
        }

        /* PAGINATION */
        .pagination {
            gap: .35rem;
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            border-radius: 8px;
            min-width: 38px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 .75rem;
            font-size: .875rem;
            color: #475569;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Active */
        .pagination .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        /* Hover */

        .pagination .page-link:hover {
            background: #eff6ff;
            color: #2563eb;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        @php
            // Helper label & class kategori (presentasional saja)
            if (!function_exists('kategoriInfo')) {
                function kategoriInfo($kategori)
                {
                    return match ($kategori) {
                        'akademik' => ['label' => 'Akademik', 'class' => 'cat-akademik'],
                        'nonakademik' => ['label' => 'Non Akademik', 'class' => 'cat-nonakademik'],
                        'keagamaan' => ['label' => 'Keagamaan', 'class' => 'cat-keagamaan'],
                        default => ['label' => '-', 'class' => ''],
                    };
                }
            }
        @endphp

        {{-- ================================================================
         HEADER + PROGRESS
    ================================================================= --}}
        <div class="page-header">
            <div>
                <div class="page-title">
                    {{ $madrasah['nama'] }}
                    <span class="badge-status-header">
                        <i class="bi bi-pencil-square"></i> Sedang Dinilai
                    </span>
                </div>
                <p class="page-subtitle">Silakan lakukan penilaian setiap prestasi satu per satu sesuai bukti yang telah
                    diunggah.</p>
            </div>

            <div class="progress-card">
                <div class="progress-header">
                    <span class="progress-label">Progress Penilaian</span>
                    <span class="progress-percent">{{ $progresPenilaian }}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{ $progresPenilaian }}%"></div>
                </div>
                <button type="button" class="btn btn-kumpulkan">
                    <i class="bi bi-send-check-fill me-1"></i> Kumpulkan Penilaian
                </button>
            </div>
        </div>

        {{-- ================================================================
         LAYOUT 2 KOLOM
    ================================================================= --}}
        <div class="row g-3">

            {{-- ============================================================
             KOLOM KIRI - Info Madrasah (sticky)
        ============================================================= --}}
            <div class="col-lg-3">

                <div class="info-card">
                    <div class="madrasah-logo">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="info-name">{{ $madrasah['nama'] }}</div>

                    <div class="info-row">
                        <span class="info-key">NPSN</span>
                        <span class="info-value">{{ $madrasah['npsn'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Jenjang</span>
                        <span class="info-value">{{ $madrasah['jenjang'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Wilayah</span>
                        <span class="info-value">{{ $madrasah['wilayah'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Jumlah Prestasi</span>
                        <span class="info-value">{{ $madrasah['jumlah_prestasi'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-key">Asesor</span>
                        <span class="info-value">
                            <div class="info-asesor">
                                <div class="avatar-mini">{{ $inisialAsesor }}</div>
                                {{ $madrasah['asesor'] }}
                            </div>
                        </span>
                    </div>

                    <div class="info-divider"></div>

                    <a href="#" class="rubrik-link">
                        <span><i class="bi bi-journal-text me-1"></i> Lihat Rubrik Penilaian</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

            </div>

            {{-- ============================================================
             KOLOM KANAN - Penilaian (full lebar)
        ============================================================= --}}
            <div class="col-lg-9">

                {{-- Quick stats --}}
                <div class="quick-stats">
                    <div class="quick-stat-card stat-highlight">
                        <div class="stat-value">{{ $totalNilaiAkhir }}</div>
                        <div class="stat-label">Total Nilai Akhir</div>
                    </div>
                    <div class="quick-stat-card">
                        <div class="stat-value">{{ $rataRata }}</div>
                        <div class="stat-label">Rata-rata</div>
                    </div>
                    <div class="quick-stat-card">
                        <div class="stat-value">{{ $sudahDinilai }} / {{ $totalPrestasi }}</div>
                        <div class="stat-label">Prestasi Dinilai</div>
                    </div>
                    <div class="quick-stat-card">
                        <div class="stat-value">{{ $belumDinilai }}</div>
                        <div class="stat-label">Belum Dinilai</div>
                    </div>
                </div>

                {{-- Panduan penilaian --}}
                <div class="panduan-bar">
                    <div class="panduan-text">
                        <i class="bi bi-info-circle-fill"></i>
                        Nilai setiap prestasi satu per satu sesuai bukti yang telah diunggah oleh madrasah.
                    </div>
                    <button type="button" class="btn btn-rubrik">
                        <i class="bi bi-journal-text me-1"></i> Lihat Rubrik
                    </button>
                </div>

                {{-- Filter --}}
                <div class="filter-card">
                    <form>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Kategori Prestasi</label>
                                <select class="form-select">
                                    <option selected>Semua Kategori</option>
                                    <option>Akademik</option>
                                    <option>Non Akademik</option>
                                    <option>Keagamaan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tingkat</label>
                                <select class="form-select">
                                    <option selected>Semua Tingkat</option>
                                    @foreach ($daftarTingkat as $tingkat)
                                        <option>{{ $tingkat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Penyelenggara</label>
                                <select class="form-select">
                                    <option selected>Semua Penyelenggara</option>
                                    @foreach ($daftarPenyelenggara as $penyelenggara)
                                        <option>{{ $penyelenggara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-filter flex-fill">
                                        <i class="bi bi-funnel-fill me-1"></i> Filter
                                    </button>
                                    <button type="reset" class="btn btn-reset">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="assessment-card">
                    <ul class="nav nav-tabs" id="penilaianTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="daftar-tab" data-bs-toggle="tab"
                                data-bs-target="#daftar-prestasi" type="button" role="tab">
                                <i class="bi bi-list-check me-1"></i> Daftar Prestasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ringkasan-tab" data-bs-toggle="tab"
                                data-bs-target="#ringkasan-nilai" type="button" role="tab">
                                <i class="bi bi-bar-chart-fill me-1"></i> Ringkasan Nilai
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- TAB 1: DAFTAR PRESTASI --}}
                        <div class="tab-pane fade show active" id="daftar-prestasi" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table assessment-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Prestasi</th>
                                            <th>Kategori</th>
                                            <th>Tingkat / Tahun</th>
                                            <th>Penyelenggara</th>
                                            <th class="text-center">Bobot</th>
                                            <th class="text-center">Nilai Akhir</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftarPrestasi as $index => $prestasi)
                                            @php
                                                $kat = kategoriInfo($prestasi['kategori']);
                                                $sudah = $prestasi['ada_penilaian'];
                                                $nilaiAkhir = $prestasi['nilai_akhir'];
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="prestasi-name">{{ $prestasi['nama'] }}</div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="category-badge {{ $kat['class'] }}">{{ $kat['label'] }}</span>
                                                </td>
                                                <td>
                                                    {{ $prestasi['tingkat'] }}
                                                    <div class="prestasi-meta">{{ $prestasi['tahun'] }}</div>
                                                </td>
                                                <td>{{ $prestasi['penyelenggara'] }}</td>
                                                <td class="text-center">
                                                    <span class="bobot-value">{{ $prestasi['bobot'] }}%</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($sudah)
                                                        <span class="nilai-akhir-value">{{ $nilaiAkhir }}</span>
                                                    @else
                                                        <span class="nilai-belum">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a href="{{ $prestasi['link_drive'] }}" class="action-icon-btn"
                                                        title="Lihat Bukti" data-bs-toggle="tooltip" target="_blank">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn-nilai {{ $sudah ? 'btn-ubah-nilai' : 'btn-beri-nilai' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalNilai{{ $index }}">
                                                        <i class="bi {{ $sudah ? 'bi-pencil' : 'bi-star-fill' }}"></i>
                                                        {{ $sudah ? 'Ubah Nilai' : 'Beri Nilai' }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-center">
                                {{ $daftarPrestasi->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                        {{-- TAB 2: RINGKASAN NILAI --}}
                        <div class="tab-pane fade" id="ringkasan-nilai" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="ringkasan-mini-card">
                                        <div class="ringkasan-value">{{ $totalPrestasi }}</div>
                                        <div class="ringkasan-label">Total Prestasi</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="ringkasan-mini-card">
                                        <div class="ringkasan-value">{{ $sudahDinilai }}</div>
                                        <div class="ringkasan-label">Prestasi Dinilai</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="ringkasan-mini-card">
                                        <div class="ringkasan-value">{{ $belumDinilai }}</div>
                                        <div class="ringkasan-label">Belum Dinilai</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="ringkasan-mini-card">
                                        <div class="ringkasan-value">{{ $rataRata }}</div>
                                        <div class="ringkasan-label">Nilai Rata-rata</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="ringkasan-mini-card text-start px-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="ringkasan-label mb-0">Progress Penilaian</span>
                                            <span class="fw-bold text-success">{{ $progresPenilaian }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 999px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $progresPenilaian }}%; border-radius: 999px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>
@endsection

{{-- ================================================================
         MODAL NILAI - satu modal per prestasi
    ================================================================= --}}
@foreach ($daftarPrestasi as $index => $prestasi)
    @php
        $kat = kategoriInfo($prestasi['kategori']);
        $sudah = $prestasi['ada_penilaian'];
        $nilaiAkhir = $prestasi['nilai_akhir'];
    @endphp
    <div class="modal fade nilai-modal" id="modalNilai{{ $index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Beri Nilai Prestasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="prestasi-modal-name">{{ $prestasi['nama'] }}</div>
                    <div class="prestasi-modal-meta">
                        <span class="category-badge {{ $kat['class'] }}">{{ $kat['label'] }}</span>
                        <span>{{ $prestasi['tingkat'] }} &middot; {{ $prestasi['tahun'] }}</span>
                        <span>&middot; {{ $prestasi['penyelenggara'] }}</span>
                    </div>

                    <form method="POST"
                        action="{{ route('asesor.nilai.store', ['madrasah' => $madrasah['id'], 'prestasi' => $prestasi['id']]) }}"
                        id="formNilai{{ $index }}">
                        @csrf
                        <label class="form-label">Persentase Nilai</label>
                        <select class="form-select" name="persentase" required>
                            <option value="" disabled {{ $sudah ? '' : 'selected' }}>-- Pilih Persentase --
                            </option>
                            @foreach ($opsiPersentase as $opsi)
                                <option value="{{ $opsi }}"
                                    {{ $sudah && $prestasi['nilai'] == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}%
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <div class="bobot-preview">
                        <div class="preview-item">
                            <div class="preview-value">{{ $prestasi['bobot'] }}%</div>
                            <div class="preview-label">Bobot</div>
                        </div>
                        <div class="preview-divider"></div>
                        <div class="preview-item">
                            <div class="preview-value">{{ $sudah ? $prestasi['nilai'] . '%' : '-' }}</div>
                            <div class="preview-label">Persentase Nilai</div>
                        </div>
                        <div class="preview-divider"></div>
                        <div class="preview-item">
                            <div class="preview-value text-success">{{ $nilaiAkhir ?? '-' }}</div>
                            <div class="preview-label">Nilai Akhir</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" form="formNilai{{ $index }}" class="btn btn-modal-save">Simpan
                        Nilai</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
