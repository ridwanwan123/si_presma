@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
        }

        .page-title {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
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
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.25rem;
        }

        /* ============ FILTER ============ */

        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-field label {
            display: block;
            font-size: .76rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: .35rem;
        }

        .filter-field select {
            min-width: 220px;
            border-radius: 10px;
        }

        .filter-note {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            margin-top: 1rem;
            padding: .75rem 1rem;
            border-radius: 12px;
            font-size: .82rem;
            line-height: 1.5;
        }

        .filter-note i {
            font-size: 1rem;
            margin-top: .15rem;
            flex-shrink: 0;
        }

        .filter-note-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .filter-note-warning i {
            color: #d97706;
        }

        .filter-note-active {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .filter-note-active i {
            color: #16a34a;
        }

        .badge-inline {
            display: inline-block;
            font-size: .68rem;
            font-weight: 700;
            background: rgba(0, 0, 0, .06);
            padding: 0 5px;
            border-radius: 999px;
        }

        /* ============ STAT STRIP ============ */

        .stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .stat-col {
            flex: 1 1 200px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: .85rem;
            height: 100%;
            margin-bottom: 0;
        }

        .stat-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
        }

        .stat-icon.bg-blue {
            background: #2563eb;
        }

        .stat-icon.bg-green {
            background: #16a34a;
        }

        .stat-icon.bg-gold {
            background: #ca8a04;
        }

        .stat-icon.bg-purple {
            background: #8b5cf6;
        }

        .stat-label {
            font-size: .76rem;
            color: #64748b;
            margin-bottom: .1rem;
        }

        .stat-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-value-sub {
            font-size: .72rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* ============ TABLE CARD HEADER ============ */

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid #f1f5f9;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .table-card-header .title {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-weight: 700;
            color: #0f172a;
            font-size: .96rem;
        }

        /* ============ TABLE ============ */

        .kelola-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .83rem;
        }

        .kelola-table thead th {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
            white-space: nowrap;
        }

        .kelola-table thead th:nth-child(2) {
            text-align: left;
        }

        .kelola-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .kelola-table tbody td:nth-child(2) {
            text-align: left;
        }

        .kelola-table tbody tr:hover {
            background: #fbfcfe;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: 700;
            font-size: .8rem;
            background: #f1f5f9;
            color: #475569;
        }

        .rank-badge.rank-1 {
            background: #fff8db;
            color: #a16207;
        }

        .rank-badge.rank-2 {
            background: #f1f5f9;
            color: #475569;
        }

        .rank-badge.rank-3 {
            background: #fef2e8;
            color: #b45309;
        }

        .madrasah-name {
            font-weight: 600;
            color: #0f172a;
        }

        .madrasah-sub {
            font-size: .72rem;
            color: #94a3b8;
        }

        .total-nilai {
            font-weight: 700;
            color: #0f8a43;
        }

        .potongan-nilai {
            color: #dc2626;
            font-weight: 600;
            font-size: .8rem;
        }

        .potongan-none {
            color: #cbd5e1;
        }

        .bidang-rank-badge {
            display: inline-block;
            font-size: .68rem;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 999px;
            margin-left: 3px;
            white-space: nowrap;
        }

        .action-icon-btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .empty-state {
            text-align: center;
            color: #94a3b8;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 2.2rem;
            color: #cbd5e1;
            display: block;
            margin-bottom: .75rem;
        }

        /* ============ MODAL FORM ============ */

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: .9rem 1rem;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        .form-grid .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .3rem;
        }

        .form-grid .form-control,
        .form-grid .form-select {
            border-radius: 10px;
        }

        .grid-section-label {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: .4rem;
            margin-top: .5rem;
        }

        .grid-section-label:first-child {
            margin-top: 0;
        }

        /* ============ SEARCH SELECT CUSTOM (pola dari register_blade.php) ============ */

        .search-select {
            position: relative;
        }

        .search-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 230px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, .09);
            margin-top: 5px;
            z-index: 60;
        }

        .dropdown-item-custom {
            padding: 9px 14px;
            font-size: .84rem;
            color: #334155;
            cursor: pointer;
            border-bottom: 1px solid #f8fafc;
        }

        .dropdown-item-custom:last-child {
            border-bottom: none;
        }

        .dropdown-item-custom:hover {
            background: #f1f5f9;
        }

        .search-dropdown .no-result {
            padding: 12px 14px;
            font-size: .82rem;
            color: #94a3b8;
            text-align: center;
        }

        .search-select-hint {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: .3rem;
            display: block;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <div>
                <h2>Kelola Arsip — Periode {{ $ranking_arsip->periode }}</h2>
                <p>Input data madrasah dari dokumen arsip. Peringkat &amp; total dihitung otomatis.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('ranking-arsip.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Daftar Arsip
                </a>
                <a href="{{ route('ranking-arsip.show', $ranking_arsip->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-eye"></i> Lihat Hasil
                </a>
            </div>
        </div>

        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- FILTER JENJANG --}}
            <div class="content-card">
                <form method="GET" class="filter-bar">
                    <div class="filter-field">
                        <label>Filter Jenjang</label>
                        <select name="jenjang" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenjang</option>
                            @foreach ($daftarJenjangArsip as $item)
                                <option value="{{ $item }}" {{ $jenjangFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($jenjangFilter)
                        <a href="{{ route('ranking-arsip.kelola', $ranking_arsip->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </form>

                @if ($jenjangFilter)
                    <div class="filter-note filter-note-active">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Sedang menampilkan peringkat resmi jenjang {{ $jenjangFilter }}.</strong>
                            Angka <span class="badge-inline">#</span> di tiap kolom nilai (Akademik, Non Akademik, dst)
                            adalah peringkat per bidang khusus jenjang ini — itulah yang menentukan juara.
                            Kolom <strong>Peringkat</strong> paling kiri tidak berubah walau difilter, karena itu peringkat
                            gabungan lintas jenjang untuk referensi saja.
                        </div>
                    </div>
                @else
                    <div class="filter-note filter-note-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            <strong>Belum difilter ke satu jenjang.</strong>
                            Angka <span class="badge-inline">#</span> di tiap kolom nilai saat ini dihitung lintas semua
                            jenjang, jadi <u>belum</u> mencerminkan juara yang sesungguhnya. Pilih satu jenjang di atas
                            dulu untuk melihat peringkat per bidang yang benar-benar berlaku.
                        </div>
                    </div>
                @endif
            </div>

            {{-- STAT STRIP --}}
            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-blue"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="stat-label">Total Madrasah</div>
                            <div class="stat-value">{{ $detail->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-green"><i class="bi bi-graph-up"></i></div>
                        <div>
                            <div class="stat-label">Total Nilai</div>
                            <div class="stat-value">{{ number_format($detail->sum('total_nilai_akhir'), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-purple"><i class="bi bi-bar-chart"></i></div>
                        <div>
                            <div class="stat-label">Rata-rata Nilai</div>
                            <div class="stat-value">
                                {{ $detail->count() > 0 ? number_format($detail->avg('total_nilai_akhir'), 2, ',', '.') : '0' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-gold"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Peringkat 1</div>
                            <div class="stat-value" style="font-size:1rem">
                                {{ $detail->firstWhere('peringkat', 1)->nama_madrasah ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="content-card p-0">
                <div class="table-card-header">
                    <div class="title"><i class="bi bi-table text-primary"></i> Data Madrasah</div>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalTambah">
                        <i class="bi bi-plus-lg"></i> Tambah Madrasah
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="kelola-table">
                        <thead>
                            <tr>
                                <th style="width:80px"
                                    title="Peringkat gabungan seluruh arsip (lintas jenjang) berdasarkan total nilai akhir -- tetap sama walau difilter jenjang, bukan penentu juara">
                                    Peringkat<br><span
                                        style="font-weight:400;text-transform:none;font-size:.6rem">(Gabungan, lintas
                                        jenjang)</span></th>
                                <th>Madrasah</th>
                                <th>Akademik</th>
                                <th>Non Akademik</th>
                                <th>Keagamaan</th>
                                <th>GTK</th>
                                <th>Lembaga</th>
                                <th>Potongan</th>
                                <th>Nilai Akhir</th>
                                <th style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detail as $item)
                                <tr>
                                    <td><span class="rank-badge rank-{{ $item->peringkat }}">{{ $item->peringkat }}</span>
                                    </td>
                                    <td>
                                        <div class="madrasah-name">{{ $item->nama_madrasah }}</div>
                                        <div class="madrasah-sub">
                                            {{ $item->jenjang_madrasah }}{{ $item->npsn ? ' · NPSN ' . $item->npsn : '' }}
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->nilai_akademik, 2, ',', '.') }} <span
                                            class="bidang-rank-badge">#{{ $peringkatPerBidang[$item->id]['Akademik'] ?? '-' }}</span>
                                    </td>
                                    <td>{{ number_format($item->nilai_non_akademik, 2, ',', '.') }} <span
                                            class="bidang-rank-badge">#{{ $peringkatPerBidang[$item->id]['Non Akademik'] ?? '-' }}</span>
                                    </td>
                                    <td>{{ number_format($item->nilai_keagamaan, 2, ',', '.') }} <span
                                            class="bidang-rank-badge">#{{ $peringkatPerBidang[$item->id]['Keagamaan'] ?? '-' }}</span>
                                    </td>
                                    <td>{{ number_format($item->nilai_gtk, 2, ',', '.') }} <span
                                            class="bidang-rank-badge">#{{ $peringkatPerBidang[$item->id]['GTK'] ?? '-' }}</span>
                                    </td>
                                    <td>{{ number_format($item->nilai_lembaga, 2, ',', '.') }} <span
                                            class="bidang-rank-badge">#{{ $peringkatPerBidang[$item->id]['Lembaga'] ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @php $totalPotongan = $item->potongan_aduan + $item->potongan_keterlambatan; @endphp
                                        @if ($totalPotongan > 0)
                                            <span class="potongan-nilai"
                                                title="Aduan: -{{ number_format($item->potongan_aduan, 2, ',', '.') }} · Keterlambatan: -{{ number_format($item->potongan_keterlambatan, 2, ',', '.') }}">
                                                -{{ number_format($totalPotongan, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="potongan-none">-</span>
                                        @endif
                                    </td>
                                    <td><span
                                            class="total-nilai">{{ number_format($item->total_nilai_akhir, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary action-icon-btn"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form
                                            action="{{ route('ranking-arsip.detail.destroy', [$ranking_arsip->id, $item->id]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus data {{ $item->nama_madrasah }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-icon-btn"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="padding: 0;">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            Belum ada data madrasah di arsip ini.<br>
                                            <button type="button" class="btn btn-success btn-sm mt-3"
                                                data-bs-toggle="modal" data-bs-target="#modalTambah">
                                                <i class="bi bi-plus-lg"></i> Tambah Madrasah Pertama
                                            </button>
                                        </div>
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

@foreach ($detail as $item)
    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('ranking-arsip.detail.update', [$ranking_arsip->id, $item->id]) }}"
                    method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square text-primary me-1"></i> Edit —
                            {{ $item->nama_madrasah }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="madrasah_id" value="{{ $item->madrasah_id }}">
                        <div class="form-grid">
                            <div class="grid-section-label"><i class="bi bi-building"></i>
                                IDENTITAS</div>

                            <div>
                                <label class="form-label">Nama Madrasah *</label>
                                <input type="text" name="nama_madrasah" class="form-control" required
                                    value="{{ $item->nama_madrasah }}">
                            </div>
                            <div>
                                <label class="form-label">NPSN</label>
                                <input type="text" name="npsn" class="form-control"
                                    value="{{ $item->npsn }}">
                            </div>
                            <div>
                                <label class="form-label">Jenjang</label>
                                <input type="text" name="jenjang_madrasah" class="form-control"
                                    value="{{ $item->jenjang_madrasah }}">
                            </div>
                            <div>
                                <label class="form-label">Kota</label>
                                <input type="text" name="kota" class="form-control"
                                    value="{{ $item->kota }}">
                            </div>

                            <div class="grid-section-label"><i class="bi bi-clipboard-data"></i> NILAI PER BIDANG
                            </div>

                            <div>
                                <label class="form-label">Akademik *</label>
                                <input type="number" step="0.01" min="0" name="nilai_akademik"
                                    class="form-control" required value="{{ $item->nilai_akademik }}">
                            </div>
                            <div>
                                <label class="form-label">Non Akademik *</label>
                                <input type="number" step="0.01" min="0" name="nilai_non_akademik"
                                    class="form-control" required value="{{ $item->nilai_non_akademik }}">
                            </div>
                            <div>
                                <label class="form-label">Keagamaan *</label>
                                <input type="number" step="0.01" min="0" name="nilai_keagamaan"
                                    class="form-control" required value="{{ $item->nilai_keagamaan }}">
                            </div>
                            <div>
                                <label class="form-label">GTK *</label>
                                <input type="number" step="0.01" min="0" name="nilai_gtk"
                                    class="form-control" required value="{{ $item->nilai_gtk }}">
                            </div>
                            <div>
                                <label class="form-label">Lembaga *</label>
                                <input type="number" step="0.01" min="0" name="nilai_lembaga"
                                    class="form-control" required value="{{ $item->nilai_lembaga }}">
                            </div>

                            <div class="grid-section-label"><i class="bi bi-dash-circle"></i>
                                POTONGAN & INFO TAMBAHAN</div>

                            <div>
                                <label class="form-label">Potongan Aduan</label>
                                <input type="number" step="0.01" min="0" name="potongan_aduan"
                                    class="form-control" value="{{ $item->potongan_aduan }}">
                            </div>
                            <div>
                                <label class="form-label">Potongan Keterlambatan</label>
                                <input type="number" step="0.01" min="0" name="potongan_keterlambatan"
                                    class="form-control" value="{{ $item->potongan_keterlambatan }}">
                            </div>
                            <div>
                                <label class="form-label">Jumlah Prestasi Dinilai</label>
                                <input type="number" min="0" name="jumlah_prestasi_dinilai"
                                    class="form-control" value="{{ $item->jumlah_prestasi_dinilai }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('ranking-arsip.detail.store', $ranking_arsip->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle text-success me-1"></i> Tambah Data
                        Madrasah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-grid">

                        <div class="full-width">
                            <label class="form-label">Cari dari Master Madrasah (opsional — otomatis isi
                                identitas)</label>
                            <div class="search-select">
                                <input type="text" id="madrasahSearch" class="form-control"
                                    placeholder="Ketik nama madrasah atau NPSN..." autocomplete="off">
                                <input type="hidden" name="madrasah_id" id="inputMadrasahId">
                                <div class="search-dropdown" id="madrasahDropdown">
                                    @foreach ($daftarMadrasah as $m)
                                        <div class="dropdown-item-custom" data-id="{{ $m->id }}"
                                            data-nama="{{ $m->nama_madrasah }}" data-npsn="{{ $m->npsn }}"
                                            data-jenjang="{{ $m->jenjang_madrasah }}"
                                            data-kota="{{ $m->kota }}">
                                            {{ $m->nama_madrasah }} — NPSN {{ $m->npsn ?: '-' }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <span class="search-select-hint">Madrasah tidak terdaftar / sudah tidak ada di master
                                data? Lewati saja, isi manual di kolom bawah.</span>
                        </div>

                        <div class="grid-section-label"><i class="bi bi-building"></i> IDENTITAS</div>

                        <div>
                            <label class="form-label">Nama Madrasah *</label>
                            <input type="text" name="nama_madrasah" id="inputNama" class="form-control" required
                                value="{{ old('nama_madrasah') }}">
                        </div>
                        <div>
                            <label class="form-label">NPSN</label>
                            <input type="text" name="npsn" id="inputNpsn" class="form-control"
                                value="{{ old('npsn') }}">
                        </div>
                        <div>
                            <label class="form-label">Jenjang</label>
                            <input type="text" name="jenjang_madrasah" id="inputJenjang" class="form-control"
                                placeholder="MI / MTs / MA" value="{{ old('jenjang_madrasah') }}">
                        </div>
                        <div>
                            <label class="form-label">Kota</label>
                            <input type="text" name="kota" id="inputKota" class="form-control"
                                value="{{ old('kota') }}">
                        </div>

                        <div class="grid-section-label"><i class="bi bi-clipboard-data"></i> NILAI PER BIDANG
                        </div>

                        <div>
                            <label class="form-label">Akademik *</label>
                            <input type="number" step="0.01" min="0" name="nilai_akademik"
                                class="form-control" required value="{{ old('nilai_akademik', 0) }}">
                        </div>
                        <div>
                            <label class="form-label">Non Akademik *</label>
                            <input type="number" step="0.01" min="0" name="nilai_non_akademik"
                                class="form-control" required value="{{ old('nilai_non_akademik', 0) }}">
                        </div>
                        <div>
                            <label class="form-label">Keagamaan *</label>
                            <input type="number" step="0.01" min="0" name="nilai_keagamaan"
                                class="form-control" required value="{{ old('nilai_keagamaan', 0) }}">
                        </div>
                        <div>
                            <label class="form-label">GTK *</label>
                            <input type="number" step="0.01" min="0" name="nilai_gtk"
                                class="form-control" required value="{{ old('nilai_gtk', 0) }}">
                        </div>
                        <div>
                            <label class="form-label">Lembaga *</label>
                            <input type="number" step="0.01" min="0" name="nilai_lembaga"
                                class="form-control" required value="{{ old('nilai_lembaga', 0) }}">
                        </div>

                        <div class="grid-section-label"><i class="bi bi-dash-circle"></i> POTONGAN & INFO TAMBAHAN
                            (KOSONGKAN KALAU TIDAK ADA)</div>

                        <div>
                            <label class="form-label">Potongan Aduan</label>
                            <input type="number" step="0.01" min="0" name="potongan_aduan"
                                class="form-control" value="{{ old('potongan_aduan') }}">
                        </div>
                        <div>
                            <label class="form-label">Potongan Keterlambatan</label>
                            <input type="number" step="0.01" min="0" name="potongan_keterlambatan"
                                class="form-control" value="{{ old('potongan_keterlambatan') }}">
                        </div>
                        <div>
                            <label class="form-label">Jumlah Prestasi Dinilai</label>
                            <input type="number" min="0" name="jumlah_prestasi_dinilai" class="form-control"
                                value="{{ old('jumlah_prestasi_dinilai') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-lg"></i> Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | SEARCH SELECT CUSTOM — pola yang sama persis dengan yang dipakai di
            | halaman Register (search-select + dropdown-item-custom), supaya
            | konsisten. Bedanya di sini setiap item juga bawa data npsn/jenjang/
            | kota untuk auto-fill ke field Identitas di bawahnya.
            |--------------------------------------------------------------------------
            */
            const search = document.getElementById('madrasahSearch');
            const dropdown = document.getElementById('madrasahDropdown');
            const hiddenId = document.getElementById('inputMadrasahId');

            if (search) {
                search.addEventListener('focus', () => {
                    dropdown.style.display = 'block';
                });

                search.addEventListener('keyup', () => {
                    const keyword = search.value.toLowerCase();
                    let adaHasil = false;

                    dropdown.querySelectorAll('.dropdown-item-custom').forEach(item => {
                        const cocok = item.innerText.toLowerCase().includes(keyword);
                        item.style.display = cocok ? 'block' : 'none';
                        if (cocok) adaHasil = true;
                    });

                    let noResult = dropdown.querySelector('.no-result');
                    if (!adaHasil && keyword.length > 0) {
                        if (!noResult) {
                            noResult = document.createElement('div');
                            noResult.className = 'no-result';
                            noResult.textContent = 'Tidak ditemukan — isi manual di kolom bawah';
                            dropdown.appendChild(noResult);
                        }
                        noResult.style.display = 'block';
                    } else if (noResult) {
                        noResult.style.display = 'none';
                    }

                    // User mengetik ulang setelah sebelumnya sudah pilih dari
                    // dropdown -- anggap batal pilih, kembali ke mode manual.
                    if (hiddenId.value) {
                        hiddenId.value = '';
                    }
                });

                dropdown.querySelectorAll('.dropdown-item-custom').forEach(item => {
                    item.addEventListener('click', () => {
                        search.value = item.dataset.nama;
                        hiddenId.value = item.dataset.id;

                        document.getElementById('inputNama').value = item.dataset.nama || '';
                        document.getElementById('inputNpsn').value = item.dataset.npsn || '';
                        document.getElementById('inputJenjang').value = item.dataset.jenjang || '';
                        document.getElementById('inputKota').value = item.dataset.kota || '';

                        dropdown.style.display = 'none';
                    });
                });

                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.search-select')) {
                        dropdown.style.display = 'none';
                    }
                });
            }

            // Kalau validasi gagal (misal nama madrasah duplikat) dan ada old()
            // input, buka lagi modal Tambah otomatis supaya user tidak perlu
            // klik "Tambah Madrasah" ulang buat lihat pesan errornya.
            @if ($errors->any() || old('nama_madrasah'))
                const modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
                modalTambah.show();
            @endif
        });
    </script>
@endpush
