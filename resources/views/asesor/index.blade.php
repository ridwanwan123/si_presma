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

        /* ==========================================================
                                           SUMMARY CARDS
                                        ========================================================== */
        .summary-row {
            margin-bottom: 24px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 18px;
            padding: 22px;
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
        }

        .summary-card .icon-box.icon-neutral {
            background: rgba(33, 37, 41, 0.06);
            color: #495057;
        }

        .summary-card .icon-box.icon-warning {
            background: rgba(253, 126, 20, 0.12);
            color: #fd7e14;
        }

        .summary-card .icon-box.icon-primary {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .summary-card .icon-box.icon-success {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
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
            border-radius: 18px;
            padding: 22px 24px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            margin-bottom: 24px;
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
                                           TABLE
                                        ========================================================== */
        .assign-table-card {
            background: #fff;
            border: 1px solid #eef1f5;
            border-radius: 18px;
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

        .row-number {
            color: #9aa4b2;
            font-weight: 600;
            font-size: 0.85rem;
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

        /* ==========================================================
                                           STATUS BADGE
                                        ========================================================== */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .badge-status.badge-belum {
            background-color: rgba(253, 126, 20, 0.12);
            color: #fd7e14;
        }

        .badge-status.badge-proses {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .badge-status.badge-selesai {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        /* ==========================================================
                                           PROGRESS STATUS
                                        ========================================================== */
        .progress-status {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 130px;
        }

        .progress-status .progress {
            height: 7px;
            border-radius: 999px;
            flex: 1;
            background-color: #eef1f5;
        }

        .progress-status .progress-bar {
            border-radius: 999px;
        }

        .progress-status .progress-bar.bg-belum {
            background-color: #fd7e14;
        }

        .progress-status .progress-bar.bg-proses {
            background-color: #0d6efd;
        }

        .progress-status .progress-bar.bg-selesai {
            background-color: #198754;
        }

        .progress-status .progress-percent {
            font-size: 0.78rem;
            font-weight: 700;
            color: #495057;
            min-width: 34px;
            text-align: right;
        }

        /* ==========================================================
                                           ACTION BUTTON
                                        ========================================================== */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 7px 16px;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .action-btn.btn-mulai {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }

        .action-btn.btn-mulai:hover {
            background-color: #157347;
            border-color: #157347;
            color: #fff;
        }

        .action-btn.btn-lanjutkan {
            background-color: #fff;
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .action-btn.btn-lanjutkan:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .action-btn.btn-hasil {
            background-color: #fff;
            border-color: #198754;
            color: #198754;
        }

        .action-btn.btn-hasil:hover {
            background-color: #198754;
            color: #fff;
        }

        /* ==========================================================
                                           TABLE FOOTER / PAGINATION
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
    </style>
@endpush

@section('content')
    <main class="content" style="background-color: #f5f7fb;">


        {{-- ================================================================
         HEADER
    ================================================================= --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Madrasah yang Dinilai</h1>
                <p class="page-subtitle">Daftar madrasah yang menjadi tanggung jawab penilaian Anda.</p>
            </div>
        </div>

        {{-- ================================================================
         SUMMARY CARD
    ================================================================= --}}
        <div class="row summary-row g-3">
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
                    <div class="icon-box icon-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $belumDinilai }}</div>
                        <div class="summary-label">Belum Dinilai</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-primary">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $sedangDinilai }}</div>
                        <div class="summary-label">Sedang Dinilai</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="icon-box icon-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="summary-value">{{ $selesaiDinilai }}</div>
                        <div class="summary-label">Selesai Dinilai</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
         FILTER
    ================================================================= --}}
        <div class="filter-card">
            <form>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Cari Madrasah</label>
                        <input type="text" class="form-control" placeholder="Nama madrasah / NPSN">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenjang</label>
                        <select class="form-select">
                            <option selected>Semua</option>
                            <option>MI</option>
                            <option>MTs</option>
                            <option>MA</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Wilayah</label>
                        <select class="form-select">
                            <option selected>Semua Wilayah</option>
                            <option>Jakarta Pusat</option>
                            <option>Jakarta Utara</option>
                            <option>Jakarta Barat</option>
                            <option>Jakarta Selatan</option>
                            <option>Jakarta Timur</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Penilaian</label>
                        <select class="form-select">
                            <option selected>Semua</option>
                            <option>Belum Dinilai</option>
                            <option>Sedang Dinilai</option>
                            <option>Selesai</option>
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

        {{-- ================================================================
         TABEL
    ================================================================= --}}
        <div class="assign-table-card">
            <div class="table-responsive">
                <table class="table assign-table">
                    <thead>
                        <tr>
                            <th style="width: 48px;">No</th>
                            <th>Madrasah</th>
                            <th>Jenjang</th>
                            <th>Wilayah</th>
                            <th class="text-center">Jumlah Prestasi</th>
                            <th>Status Penilaian</th>
                            <th>Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarMadrasah as $index => $madrasah)
                            <tr>
                                <td><span class="row-number">{{ $index + 1 }}</span></td>
                                <td>
                                    <div class="madrasah-name">{{ $madrasah['nama'] }}</div>
                                    <div class="madrasah-npsn">NPSN: {{ $madrasah['npsn'] }}</div>
                                </td>
                                <td>
                                    <span class="jenjang-badge">{{ $madrasah['jenjang'] }}</span>
                                </td>
                                <td>{{ $madrasah['wilayah'] }}</td>
                                <td class="text-center">
                                    <span class="prestasi-count">{{ $madrasah['prestasi'] }}</span>
                                </td>
                                <td>
                                    <span class="badge-status {{ $madrasah['status_badge'] }}">
                                        {{ $madrasah['status_label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-status">
                                        <div class="progress">
                                            <div class="progress-bar {{ $madrasah['status_bar'] }}" role="progressbar"
                                                style="width: {{ $madrasah['progress'] }}%"></div>
                                        </div>
                                        <span class="progress-percent">{{ $madrasah['progress'] }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('asesor.show', $madrasah['id']) }}"
                                        class="action-btn {{ $madrasah['aksi_class'] }}">
                                        <i class="bi {{ $madrasah['aksi_icon'] }}"></i>
                                        {{ $madrasah['aksi_label'] }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="footer-info">Menampilkan {{ $totalMadrasah }} dari {{ $totalMadrasah }} madrasah</div>

                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>

            </div>

        </div>

    </main>
@endsection