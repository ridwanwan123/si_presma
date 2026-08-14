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

        /* =========================
           STAT CARDS (DASHBOARD)
        ========================= */

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 0 1rem 1rem;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: .9rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.1rem 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .04);
        }

        .stat-icon {
            flex-shrink: 0;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.total {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-icon.negeri {
            background: #dcfce7;
            color: #0f8a43;
        }

        .stat-icon.swasta {
            background: #fef3c7;
            color: #b45309;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .stat-info .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-info .stat-label {
            font-size: .8rem;
            font-weight: 600;
            color: #64748b;
            margin-top: .2rem;
        }

        @media(max-width:768px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
                margin: 0 .5rem 1rem;
            }
        }

        @media(max-width:480px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 1rem;
            }
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
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .toolbar-action {
            display: flex;
            gap: .75rem;
            flex-shrink: 0;
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

        /* =========================
           TABLE (COMPACT + SCROLL)
        ========================= */

        .content-card-body {
            padding: 1.25rem;
        }

        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #eef2f7;
            border-radius: 12px;
        }

        .modern-table {
            margin-bottom: 0;
            min-width: 900px;
            font-size: .85rem;
        }

        .modern-table thead th {
            position: sticky;
            top: 0;
            white-space: nowrap;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: .78rem;
            letter-spacing: .02em;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem .9rem;
        }

        .modern-table tbody td {
            padding: .65rem .9rem;
            vertical-align: middle;
        }

        .modern-table tbody tr:not(:last-child) td {
            border-bottom: 1px solid #f1f5f9;
        }

        .modern-table tbody tr:hover {
            background: #f8fffb;
        }

        /* first column (NO) pinned so it stays readable while scrolling */
        .modern-table thead th:first-child,
        .modern-table tbody td:first-child {
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 1;
        }

        .modern-table thead th:first-child {
            background: #f8fafc;
            z-index: 2;
        }

        .modern-table tbody tr:hover td:first-child {
            background: #f8fffb;
        }

        .pegawai-info {
            display: flex;
            flex-direction: column;
        }

        .pegawai-info strong {
            color: #0f172a;
            font-size: .85rem;
        }

        .pegawai-info small {
            color: #94a3b8;
            font-size: .74rem;
        }

        .action-cell {
            display: flex;
            gap: .35rem;
            white-space: nowrap;
        }

        .btn-success {
            background: #0f8a43;
            border-color: #0f8a43;
        }

        .btn-success:hover {
            background: #0c7438;
            border-color: #0c7438;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .table-footer .result-count {
            color: #64748b;
            font-size: .82rem;
        }

        @media(max-width:992px) {

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-action .btn {
                flex: 1;
            }
        }

        @media(max-width:768px) {

            .content-card {
                margin: 0 .5rem 1rem;
            }

            .content-card-body {
                padding: 1rem;
            }
        }

        /* PAGINATION */
        .pagination {
            gap: .35rem;
            margin-bottom: 0;
            flex-wrap: wrap;
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

        .pagination .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .pagination .page-link:hover {
            background: #eff6ff;
            color: #2563eb;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Data Madrasah</h2>
            <p>Kelola data madrasah pada sistem PRESMA.</p>
        </div>

        {{-- Dashboard Info: Total / Negeri / Swasta --}}
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ number_format($totalMadrasah) }}</span>
                    <span class="stat-label">Total Madrasah</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon negeri">
                    <i class="bi bi-bank"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ number_format($totalNegeri) }}</span>
                    <span class="stat-label">Negeri</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon swasta">
                    <i class="bi bi-house-door"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ number_format($totalSwasta) }}</span>
                    <span class="stat-label">Swasta</span>
                </div>
            </div>
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

                        <div class="row g-2 align-items-end">

                            {{-- Status --}}
                            <div class="col-6 col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status_madrasah" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Negeri" {{ request('status_madrasah') == 'Negeri' ? 'selected' : '' }}>
                                        Negeri</option>
                                    <option value="Swasta" {{ request('status_madrasah') == 'Swasta' ? 'selected' : '' }}>
                                        Swasta</option>
                                </select>
                            </div>

                            {{-- Jenjang --}}
                            <div class="col-6 col-md-2">
                                <label class="form-label">Jenjang</label>
                                <select name="jenjang_madrasah" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="RA" {{ request('jenjang_madrasah') == 'RA' ? 'selected' : '' }}>RA
                                    </option>
                                    <option value="MI" {{ request('jenjang_madrasah') == 'MI' ? 'selected' : '' }}>MI
                                    </option>
                                    <option value="MTs" {{ request('jenjang_madrasah') == 'MTs' ? 'selected' : '' }}>MTs
                                    </option>
                                    <option value="MA" {{ request('jenjang_madrasah') == 'MA' ? 'selected' : '' }}>MA
                                    </option>
                                </select>
                            </div>

                            {{-- Kota --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label">Kota</label>
                                <select name="kota" class="form-select">
                                    <option value="">Semua Kota</option>
                                    @foreach ($kotas as $kota)
                                        <option value="{{ $kota }}"
                                            {{ request('kota') == $kota ? 'selected' : '' }}>
                                            {{ $kota }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Nama Search --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label">Nama Madrasah</label>
                                <input type="text" name="nama_madrasah" class="form-control" placeholder="Cari..."
                                    value="{{ request('nama_madrasah') }}">
                            </div>

                            {{-- Button --}}
                            <div class="col-12 col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-success w-100">
                                    Filter
                                </button>

                                <a href="{{ route('madrasah.index') }}" class="btn btn-outline-secondary w-100">
                                    Reset
                                </a>
                            </div>

                        </div>

                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="content-card-body">

                <div class="table-scroll">

                    <table class="table modern-table align-middle">

                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NPSN</th>
                                <th>NAMA MADRASAH</th>
                                <th>KOTA</th>
                                <th>KEPALA MADRASAH</th>
                                <th>KEPALA URUSAN TATA USAHA</th>
                                <th width="90"></th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($madrasahs as $index => $madrasah)
                                <tr>
                                    <td>{{ $madrasahs->firstItem() + $index }}</td>
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
                                                {{ $madrasah->nama_kepala_madrasah ?? '-' }}
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
                                        <div class="action-cell">
                                            <a href="{{ route('madrasah.edit', $madrasah->id) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form action="{{ route('madrasah.destroy', $madrasah->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?');">
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
                                    <td colspan="7" class="text-center py-4">
                                        Data madrasah belum ada.
                                    </td>
                                </tr>
                            @endforelse


                        </tbody>

                    </table>

                </div>

                <div class="table-footer">
                    <span class="result-count">
                        Menampilkan {{ $madrasahs->firstItem() ?? 0 }}–{{ $madrasahs->lastItem() ?? 0 }}
                        dari {{ $madrasahs->total() }} data
                    </span>

                    {{ $madrasahs->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>

    </main>
@endsection