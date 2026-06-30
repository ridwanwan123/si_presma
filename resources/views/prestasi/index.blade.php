@extends('layouts.base')
@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-title h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }

        .page-title p {
            font-size: .9rem;
            color: #64748b;
            margin: 0;
        }

        .action-group {
            display: flex;
            gap: .5rem;
        }

        /* CARD */
        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        /* FILTER */
        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: .75rem;
            border-radius: 12px;
        }

        /* ITEM ROW */
        .prestasi-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem;
            transition: .2s;
        }

        .prestasi-item:hover {
            background: #f8fafc;
        }

        .title {
            font-weight: 700;
            font-size: .95rem;
            color: #0f172a;
        }

        /* =====================================================
          TABLE CARD
        ===================================================== */
        .table-header {
            padding: 18px 22px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(to right, #fff, #f8fafc);
        }

        .table-header h6 {
            margin: 0;
            font-weight: 700;
        }

        .table-header small {
            color: #64748b;
        }

        /* =====================================================
                                               TABLE
                                            ===================================================== */
        #tablePrestasi {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #tablePrestasi thead th {
            background: #f8fafc !important;
            color: #334155;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 14px;
            white-space: nowrap;
        }

        #tablePrestasi tbody td {
            padding: 15px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: .85rem;
        }

        #tablePrestasi tbody tr {
            transition: .2s;
        }

        #tablePrestasi tbody tr:hover {
            background: #f8fafc;
        }

        /* =====================================================
                                               DATATABLE WRAPPER
                                            ===================================================== */
        .dataTables_wrapper {
            padding: 20px;
        }

        /* =====================================================
                                               TOP AREA
                                            ===================================================== */
        .dataTables_wrapper .row:first-child {
            margin-bottom: 18px;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_length {
            text-align: left;
        }

        .dataTables_wrapper .dataTables_filter {
            text-align: right;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .85rem;
            color: #64748b;
            font-weight: 600;
        }

        /* =====================================================
                                               SELECT
                                            ===================================================== */
        .dataTables_wrapper .dataTables_length select {
            width: 80px;
            border-radius: 10px;
            border: 1px solid #dbe3ec;
            background: #fff;
            padding: .45rem 2rem .45rem .75rem;
            font-size: .85rem;
            margin: 0 !important;
        }

        /* =====================================================
                                               SEARCH
                                            ===================================================== */
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0 !important;
            width: 260px;
            border-radius: 10px;
            border: 1px solid #dbe3ec;
            padding: .55rem .9rem;
            transition: .25s;
            font-size: .85rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
            outline: none;
        }

        /* =====================================================
                                               INFO
                                            ===================================================== */
        .dataTables_wrapper .dataTables_info {
            color: #64748b;
            font-size: .85rem;
            padding-top: 18px;
        }

        /* =====================================================
                                               PAGINATION (BOOTSTRAP 5)
                                            ===================================================== */
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 12px;
        }

        .dataTables_wrapper .pagination {
            gap: .35rem;
            margin: 0;
        }

        .dataTables_wrapper .page-item .page-link {
            border: 1px solid #e2e8f0;
            border-radius: 10px !important;
            color: #334155;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
            font-size: .85rem;
            background: #fff;
        }

        .dataTables_wrapper .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .dataTables_wrapper .page-item:not(.active) .page-link:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .dataTables_wrapper .page-item.disabled .page-link {
            color: #94a3b8;
            background: #f8fafc;
        }

        /* =====================================================
                                               PROCESSING
                                            ===================================================== */
        .dataTables_processing {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        /* ==========================
                                   BADGE
                                ========================== */
        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .4rem .8rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            line-height: 1;
        }

        /* Status */
        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-primary {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-secondary {
            background: #e2e8f0;
            color: #475569;
        }

        /* Juara */
        .badge-gold {
            background: #fff7d6;
            color: #b7791f;
        }

        .badge-silver {
            background: #eef2f7;
            color: #64748b;
        }

        .badge-bronze {
            background: #fbe9dc;
            color: #9a5a22;
        }

        /* =====================================================
                                    RESPONSIVE
                                ===================================================== */
        @media (max-width:768px) {
            .dataTables_wrapper {
                padding: 15px;
            }

            .dataTables_wrapper .row:first-child>div,
            .dataTables_wrapper .row:last-child>div {
                width: 100%;
                text-align: left !important;
                margin-bottom: 10px;
            }

            .dataTables_wrapper .dataTables_filter {
                text-align: left;
            }

            .dataTables_wrapper .dataTables_filter label {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: flex-start;
            }
        }
    </style>
@endpush
@section('content')
    <main class="content"> {{-- HEADER --}}
        <div class="page-title">
            <div>
                <h2>Prestasi {{ ucfirst($jenis) }}</h2>
                <p>Data lomba & hasil verifikasi assessor</p>
            </div>
            <div class="action-group"> <button class="btn btn-success btn-sm">
                    <i class="bi bi-plus"></i>
                    Tambah
                </button> <a href="{{ route('prestasi.import', $jenis) }}"
                    class="btn btn-primary btn-sm {{ request()->routeIs('prestasi.import') ? 'active' : '' }}"">
                    <i class="bi bi-upload"></i>
                    Import
                </a> </div>
        </div>
        {{-- SUMMARY --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Prestasi
                            </div>
                            <div class="fw-bold fs-2">
                                {{ $summary->total ?? '0' }}
                            </div>
                        </div> <i class="bi bi-trophy fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Sudah Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-success">
                                {{ $summary->verified ?? '0' }}
                            </div>
                        </div> <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Belum Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-warning">
                                {{ $summary->pending ?? '0' }}
                            </div>
                        </div> <i class="bi bi-clock fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Tidak Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-danger">
                                {{ $summary->rejected ?? '0' }}
                            </div>
                        </div> <i class="bi bi-x-circle fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="content-card">
            <div style="padding:1rem">
                <div class="filter-box">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Cari kegiatan / lembaga">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Tingkat</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Juara</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Lembaga</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="" class="btn btn-light border btn-sm w-100">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="table-header">
                <div>
                    <h6 class="mb-0 fw-bold">Daftar Prestasi</h6>
                    <small class="text-muted">
                        Seluruh data prestasi yang telah diinput
                    </small>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tablePrestasi" class="table align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Kegiatan</th>
                            <th width="110">Tingkat</th>
                            <th width="120">Kategori</th>
                            <th width="90">Juara</th>
                            <th>Lembaga</th>
                            <th width="150">Penyelenggara</th>
                            <th width="120">Tanggal</th>
                            <th width="120">Status</th>
                            <th width="90" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </main>
@endsection
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $('#tablePrestasi').DataTable({
            ajax: "{{ route('prestasi.data', $jenis) }}",
            dom: "<'row align-items-center mb-3'<'col-md-6'l><'col-md-6 d-flex justify-content-md-end'f>>" +
                "rt" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7 d-flex justify-content-md-end'p>>",
            columns: [{
                    data: 'id'
                },
                {
                    data: 'nama_kegiatan'
                },
                {
                    data: 'tingkat'
                },
                {
                    data: 'kategori'
                },
                {
                    data: 'juara'
                },
                {
                    data: 'lembaga'
                },
                {
                    data: 'penyelenggara'
                },
                {
                    data: 'tanggal'
                },
                {
                    data: 'status_verifikasi'
                }
            ]
        });
    </script>
@endpush
