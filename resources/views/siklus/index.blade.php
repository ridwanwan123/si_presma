@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: .25rem;
        }

        .page-header p {
            color: #64748b;
            margin: 0;
        }

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.25rem 1.4rem;
        }

        /* ============ WARNING BANNER ============ */

        .warning-banner {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .9rem 1.1rem;
            border-radius: 14px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: .85rem;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }

        .warning-banner i {
            font-size: 1.1rem;
            color: #d97706;
            flex-shrink: 0;
            margin-top: .1rem;
        }

        /* ============ FILTER ============ */

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            align-items: end;
            gap: .75rem;
        }

        .filter-form .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-form .form-select,
        .filter-form .form-control {
            border-radius: 10px;
            width: 100%;
        }

        .btn-reset-filter {
            border-radius: 10px;
            width: 100%;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            white-space: nowrap;
        }

        /* ============ TABLE ============ */

        .table-responsive {
            padding: 0 .1rem .1rem;
        }

        .siklus-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .85rem;
        }

        .siklus-table thead th {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            white-space: nowrap;
        }

        .siklus-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .madrasah-name {
            font-weight: 700;
            color: #0f172a;
        }

        .madrasah-sub {
            font-size: .74rem;
            color: #94a3b8;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .74rem;
            font-weight: 700;
            padding: .3rem .7rem;
            border-radius: 999px;
            white-space: nowrap;
        }

        .badge-open {
            background: #dcfce7;
            color: #166534;
        }

        .badge-submitted {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-assessment {
            background: #ede9fe;
            color: #5b21b6;
        }

        .badge-finished {
            background: #f1f5f9;
            color: #475569;
        }

        .progress-mini {
            width: 100%;
            min-width: 90px;
            height: 6px;
            border-radius: 999px;
            background: #f1f5f9;
            overflow: hidden;
        }

        .progress-mini-bar {
            height: 100%;
            background: #2563eb;
            border-radius: 999px;
        }

        .progress-text {
            font-size: .72rem;
            color: #64748b;
            margin-top: .25rem;
        }

        .asesor-kosong {
            font-size: .8rem;
            color: #cbd5e1;
            font-style: italic;
        }

        .btn-ubah-status {
            border-radius: 10px;
            font-size: .78rem;
            white-space: nowrap;
        }

        /* ============ MODAL WARNING ============ */

        .modal-warning-box {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            padding: .8rem 1rem;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: .82rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .modal-warning-box i {
            color: #dc2626;
            font-size: 1.05rem;
            flex-shrink: 0;
            margin-top: .1rem;
        }

        .modal-info-current {
            font-size: .82rem;
            color: #475569;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid pt-3">

            <div class="page-header">
                <h2>Manajemen Status Siklus</h2>
                <p>Monitoring & override manual status siklus prestasi seluruh madrasah, periode {{ $periode }}.</p>
            </div>

            <div class="warning-banner">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Gunakan dengan hati-hati.</strong> Mengubah status di sini <strong>hanya</strong> mengubah
                    status siklus madrasah yang bersangkutan. Status penugasan asesor (<code>assign_asesors</code>)
                    dan status penilaian (<code>penilaian_prestasis</code>) <strong>tidak ikut berubah otomatis</strong>
                    — cek dan sesuaikan manual di halaman terkait jika diperlukan. Setiap perubahan tercatat di log
                    aktivitas.
                </div>
            </div>

            {{-- FILTER --}}
            <div class="content-card mb-4">
                <form method="GET" class="filter-form">
                    <div>
                        <label class="form-label">Periode</label>
                        <select name="periode" class="form-select" onchange="this.form.submit()">
                            @foreach ($daftarPeriode as $item)
                                <option value="{{ $item }}" {{ $periode == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Status Siklus</label>
                        <select name="status_siklus" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ !$statusSiklusFilter ? 'selected' : '' }}>Semua Status</option>
                            @foreach ($statusInfo as $key => $info)
                                <option value="{{ $key }}" {{ $statusSiklusFilter == $key ? 'selected' : '' }}>
                                    {{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Jenjang</label>
                        <select name="jenjang" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ !$jenjangFilter ? 'selected' : '' }}>Semua Jenjang</option>
                            @foreach ($opsiFilter['jenjang'] as $item)
                                <option value="{{ $item }}" {{ $jenjangFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Status Madrasah</label>
                        <select name="status_madrasah" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ !$statusMadrasahFilter ? 'selected' : '' }}>Semua Status</option>
                            @foreach ($opsiFilter['status_madrasah'] as $item)
                                <option value="{{ $item }}"
                                    {{ $statusMadrasahFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Kota</label>
                        <select name="kota" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ !$kotaFilter ? 'selected' : '' }}>Semua Kota</option>
                            @foreach ($opsiFilter['kota'] as $item)
                                <option value="{{ $item }}" {{ $kotaFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Cari Madrasah</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama / NPSN"
                            value="{{ $search }}">
                    </div>

                    <button type="submit" class="btn btn-success btn-reset-filter">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('siklus.index', ['periode' => $periode]) }}"
                        class="btn btn-outline-secondary btn-reset-filter">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </form>
            </div>

            {{-- TABEL --}}
            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="siklus-table">
                        <thead>
                            <tr>
                                <th>Madrasah</th>
                                <th>Status Siklus</th>
                                <th>Asesor</th>
                                <th>Progres Penilaian</th>
                                <th style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarSiklus as $siklus)
                                @php $info = $statusInfo[$siklus->status] @endphp
                                <tr>
                                    <td>
                                        <div class="madrasah-name">{{ $siklus->madrasah->nama_madrasah ?? '-' }}</div>
                                        <div class="madrasah-sub">
                                            {{ $siklus->madrasah->jenjang_madrasah ?? '-' }} ·
                                            {{ $siklus->madrasah->kota ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $info['badge'] }}">
                                            <i class="bi {{ $info['icon'] }}"></i> {{ $info['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($siklus->info_asesor)
                                            {{ $siklus->info_asesor }}
                                        @else
                                            <span class="asesor-kosong">Belum ditugaskan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress-mini">
                                            <div class="progress-mini-bar" style="width: {{ $siklus->info_progress }}%">
                                            </div>
                                        </div>
                                        <div class="progress-text">{{ $siklus->info_sudah_dinilai }} /
                                            {{ $siklus->info_total_prestasi }} dinilai
                                            ({{ $siklus->info_progress }}%)
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-ubah-status"
                                            data-bs-toggle="modal" data-bs-target="#modalUbahStatus{{ $siklus->id }}">
                                            <i class="bi bi-pencil-square"></i> Ubah
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">Tidak ada data untuk filter
                                        ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection
{{-- ================= MODAL UBAH STATUS (1 PER BARIS) ================= --}}
@foreach ($daftarSiklus as $siklus)
    @php $infoSekarang = $statusInfo[$siklus->status] @endphp
    <div class="modal fade" id="modalUbahStatus{{ $siklus->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('siklus.update-status', $siklus->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Status Siklus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="modal-info-current">
                            <strong>{{ $siklus->madrasah->nama_madrasah ?? '-' }}</strong> — periode
                            {{ $siklus->periode }}<br>
                            Status saat ini:
                            <span class="status-badge {{ $infoSekarang['badge'] }}">
                                <i class="bi {{ $infoSekarang['icon'] }}"></i> {{ $infoSekarang['label'] }}
                            </span>
                        </div>

                        <div class="modal-warning-box">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                Perubahan ini <strong>tidak</strong> otomatis mengubah status penugasan asesor
                                maupun status penilaian yang sudah ada. Pastikan Anda memahami konsekuensinya
                                sebelum melanjutkan.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Baru</label>
                            <select name="status" class="form-select" required>
                                <option value="" disabled selected>— Pilih status baru —</option>
                                @foreach ($statusInfo as $key => $info)
                                    @if ($key !== $siklus->status)
                                        <option value="{{ $key }}">{{ $info['label'] }}
                                            ({{ $key }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-semibold">Alasan Perubahan <span
                                    class="text-danger">*</span></label>
                            <textarea name="alasan" class="form-control" rows="3" required maxlength="500"
                                placeholder="Jelaskan alasan override status ini (wajib diisi, akan tercatat di log aktivitas)."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
