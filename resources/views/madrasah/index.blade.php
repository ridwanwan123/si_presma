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

        <div class="active">
            Data Madrasah
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

        .toolbar-title h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .toolbar-title span {
            color: #64748b;
            font-size: .85rem;
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
            <h2>Data Madrasah</h2>
            <p>Kelola data madrasah pada sistem PRESMA.</p>
        </div>

        <div class="content-card">

            {{-- Toolbar --}}
            <div class="toolbar">
                <div class="toolbar-title">
                    <h5>
                        Data Madrasah
                    </h5>
                    <span>
                        Kelola data madrasah pada sistem PRESMA
                    </span>
                </div>

                <div class="toolbar-action">
                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterArea">
                        <i class="bi bi-funnel"></i>
                        Filter
                    </button>

                    <a href="{{ route('madrasah.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Data
                    </a>
                </div>
            </div>

            {{-- Filter --}}
            <div class="collapse" id="filterArea">
                <form method="GET" action="{{ route('madrasah.index') }}">
                    <div class="filter-panel">
                        <div class="row g-3">

                            {{-- Jenjang Madrasah --}}
                            <div class="col-md-4">
                                <label class="form-label">Jenjang Madrasah</label>
                                <select name="jenjang_madrasah" class="form-select">
                                    <option value="">
                                        Semua Jenjang
                                    </option>
                                    <option value="RA" {{ request('jenjang_madrasah') == 'RA' ? 'selected' : '' }}>
                                        RA
                                    </option>
                                    <option value="MI" {{ request('jenjang_madrasah') == 'MI' ? 'selected' : '' }}>
                                        MI
                                    </option>
                                    <option value="MTs" {{ request('jenjang_madrasah') == 'MTs' ? 'selected' : '' }}>
                                        MTs
                                    </option>
                                    <option value="MA" {{ request('jenjang_madrasah') == 'MA' ? 'selected' : '' }}>
                                        MA
                                    </option>
                                </select>
                            </div>

                            {{-- Madrasah --}}
                            <div class="col-md-4">
                                <label class="form-label">Madrasah</label>
                                <select name="nama_madrasah" class="form-select">
                                    <option value="">
                                        Semua Madrasah
                                    </option>

                                    @foreach ($madrasahs as $item)
                                        <option value="{{ $item->nama_madrasah }}"
                                            {{ request('nama_madrasah') == $item->nama_madrasah ? 'selected' : '' }}>
                                            {{ $item->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Kota --}}
                            <div class="col-md-4">
                                <label class="form-label">Kota</label>
                                <select name="kota" class="form-select">
                                    <option value="">
                                        Semua Kota
                                    </option>
                                    @foreach ($kotas as $kota)
                                        <option value="{{ $kota }}"
                                            {{ request('kota') == $kota ? 'selected' : '' }}>
                                            {{ $kota }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Filter Action --}}
                        <div class="filter-action mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-funnel"></i>
                                Terapkan Filter
                            </button>
                            <a href="{{ route('madrasah.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Info --}}
            {{-- <div class="table-header">
                <div>
                    <h6>Data Madrasah DKI Jakarta</h6>
                    <span>? Data Ditemukan</span>
                </div>
            </div> --}}

            {{-- Table --}}
            <div class="content-card-body">

                <div class="table-responsive">

                    <table class="table modern-table align-middle">

                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NPSN</th>
                                <th>NAMA MADRASAH</th>
                                <th>KOTA</th>
                                <th>KEPALA MADRASAH</th>
                                <th>KEPALA URUSAN TATA USAHA</th>
                                <th width="120">
                                    <i class="bi bi-gear-fill"></i>
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($madrasahs as $index => $madrasah)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $madrasah->npsn }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $madrasah->nama_madrasah }}
                                    </td>
                                    <td>
                                        {{ $madrasah->kota }}
                                    </td>

                                    <td>
                                        <div class="pegawai-info">
                                            <strong>
                                                {{ $madrasah->nama_kepala_madrasah }}
                                            </strong>

                                            @if ($madrasah->nip_kepala_madrasah)
                                                <small>
                                                    NIP. {{ $madrasah->nip_kepala_madrasah }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>


                                    <td>
                                        <div class="pegawai-info">
                                            <strong>
                                                {{ $madrasah->nama_kepala_urusan_tata_usaha ?? '-' }}
                                            </strong>
                                            @if ($madrasah->nip_kepala_urusan_tata_usaha)
                                                <small>
                                                    NIP. {{ $madrasah->nip_kepala_urusan_tata_usaha }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ route('madrasah.edit', $madrasah->id) }}"
                                            class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('madrasah.destroy', $madrasah->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">
                                        Data madrasah belum ada.
                                    </td>
                                </tr>
                            @endforelse


                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>
@endsection
