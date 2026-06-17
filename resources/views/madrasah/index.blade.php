@extends('layouts.base')

@section('breadcrumb')
    <div class="breadcrumb-modern">
        <div class="crumb">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-house-door-fill home-icon"></i>
                Home
            </a>
        </div>

        <span class="separator">
            <i class="bi bi-chevron-right"></i>
        </span>

        <div class="crumb">
            <a href="#">
                Data Pegawai
            </a>
        </div>

        <span class="separator">
            <i class="bi bi-chevron-right"></i>
        </span>

        <div class="active">
            Detail Pegawai
        </div>
    </div>
@endsection

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

        /* CARD */

        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        }

        /* TOOLBAR */

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .toolbar-search {
            position: relative;
            width: 420px;
        }

        .toolbar-search i {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .toolbar-search input {
            padding-left: 42px;
            height: 46px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
        }

        .toolbar-search input:focus {
            border-color: #0f8a43;
            box-shadow: 0 0 0 .15rem rgba(15, 138, 67, .15);
        }

        .toolbar-action {
            display: flex;
            gap: .75rem;
        }

        /* FILTER */

        .filter-panel {
            padding: 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
        }

        .filter-panel .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: #475569;
        }

        .filter-panel .form-control,
        .filter-panel .form-select {
            border-radius: 10px;
        }

        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            border-color: #0f8a43;
            box-shadow: 0 0 0 .15rem rgba(15, 138, 67, .15);
        }

        /* TABLE HEADER */

        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h6 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .table-header span {
            color: #64748b;
            font-size: .9rem;
        }

        /* TABLE */

        .content-card-body {
            padding: 1.5rem;
        }

        .modern-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table tbody tr:hover {
            background: #f8fffb;
        }

        .pegawai-info {
            display: flex;
            flex-direction: column;
        }

        .pegawai-info strong {
            color: #0f172a;
        }

        .pegawai-info small {
            color: #94a3b8;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
        }

        .badge-status.success {
            background: #dcfce7;
            color: #0f8a43;
        }

        .btn-success {
            background: #0f8a43;
            border-color: #0f8a43;
        }

        .btn-success:hover {
            background: #0c7438;
            border-color: #0c7438;
        }

        @media(max-width:992px) {

            .toolbar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .toolbar-search {
                width: 100%;
            }

            .toolbar-action {
                width: 100%;
            }

            .toolbar-action .btn {
                flex: 1;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Detail Pegawai</h2>
            <p>Kelola data pegawai pada sistem SIPRESMA.</p>
        </div>

        <div class="content-card">

            {{-- Toolbar --}}
            <div class="toolbar">

                <div class="toolbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari NIP, nama pegawai, madrasah...">
                </div>

                <div class="toolbar-action">

                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterArea">

                        <i class="bi bi-funnel"></i>
                        Filter

                    </button>

                    <button class="btn btn-success">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Data
                    </button>

                </div>

            </div>

            {{-- Filter --}}
            <div class="collapse" id="filterArea">

                <div class="filter-panel">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Jabatan</label>
                            <select class="form-select">
                                <option>Semua Jabatan</option>
                                <option>Guru</option>
                                <option>Staff</option>
                                <option>Kepala Madrasah</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Madrasah</label>
                            <select class="form-select">
                                <option>Semua Madrasah</option>
                                <option>MAN 01 Jakarta</option>
                                <option>MAN 02 Jakarta</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option>Semua Status</option>
                                <option>Aktif</option>
                                <option>Non Aktif</option>
                            </select>
                        </div>

                    </div>

                </div>

            </div>

            {{-- Info --}}
            <div class="table-header">
                <div>
                    <h6>Data Pegawai</h6>
                    <span>1 Data Ditemukan</span>
                </div>

            </div>

            {{-- Table --}}
            <div class="content-card-body">

                <div class="table-responsive">

                    <table class="table modern-table align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Madrasah</th>
                                <th>Nama Kamad</th>
                                <th>Prestasi</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>

                                <td>1</td>

                                <td>
                                    <span class="text-muted">
                                        20010410202505007
                                    </span>
                                </td>

                                <td>
                                    <div class="pegawai-info">
                                        <strong>Ahmad Fauzi Datang</strong>
                                        <small>pegawai@email.com</small>
                                    </div>
                                </td>

                                <td>Staff</td>

                                <td>MAN 01 Jakarta</td>

                                <td>Ahmad Fauzi</td>

                                <td>Aktif</td>

                                <td>
                                    <span class="badge-status success">
                                        Aktif
                                    </span>
                                </td>

                                <td>

                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>
@endsection
