@extends('layouts.base')

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
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

        .container-fluid {
            padding: 0 1rem;
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .filter-form .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-form .form-select {
            border-radius: 10px;
            min-width: 220px;
        }

        .btn-reset-filter {
            border-radius: 10px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .ranking-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .ranking-table tbody td {
            padding: 12px 14px;
            font-size: .88rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .ranking-table tbody tr:hover {
            background: #f8fafc;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            font-weight: 700;
            font-size: .85rem;
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

        .madrasah-npsn {
            font-size: .76rem;
            color: #94a3b8;
        }

        .jenjang-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 600;
            background: rgba(13, 110, 253, .1);
            color: #0d6efd;
        }

        .total-nilai {
            font-weight: 700;
            color: #0f8a43;
            font-size: .95rem;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h2>Hasil & Ranking Prestasi</h2>
                <p>Peringkat madrasah berdasarkan total nilai akhir yang telah difinalisasi asesor, periode {{ $periode }}.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('ranking.export', ['periode' => $periode, 'jenjang' => $jenjangFilter]) }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i>
                    Export Excel
                </a>
                <a href="{{ route('ranking-arsip.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-archive"></i>
                    Lihat Arsip
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalArsipkan">
                    <i class="bi bi-save"></i>
                    Arsipkan Ranking
                </button>
            </div>
        </div>

        <div class="container-fluid">

            {{-- FILTER PERIODE & JENJANG --}}
            <div class="content-card">
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
                        <label class="form-label">Jenjang Madrasah</label>
                        <select name="jenjang" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenjang (gabungan)</option>
                            @foreach ($daftarJenjang as $item)
                                <option value="{{ $item }}" {{ $jenjangFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($jenjangFilter)
                        <a href="{{ route('ranking.index', ['periode' => $periode]) }}" class="btn btn-outline-secondary btn-reset-filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- TABEL RANKING --}}
            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th style="width:70px" class="text-center">Peringkat</th>
                                <th>Madrasah</th>
                                <th>Jenjang</th>
                                <th>Wilayah</th>
                                <th class="text-center">Prestasi Dinilai</th>
                                <th class="text-end">Potongan</th>
                                <th class="text-end">Total Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ranking as $item)
                                <tr>
                                    <td class="text-center">
                                        <span class="rank-badge rank-{{ $item->peringkat }}">
                                            {{ $item->peringkat }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="madrasah-name">{{ $item->nama_madrasah }}</div>
                                        <div class="madrasah-npsn">NPSN: {{ $item->npsn }}</div>
                                    </td>
                                    <td>
                                        <span class="jenjang-badge">{{ $item->jenjang_madrasah }}</span>
                                    </td>
                                    <td>{{ $item->kota }}</td>
                                    <td class="text-center">{{ $item->jumlah_dinilai }}</td>
                                    <td class="text-end">
                                        @if ($item->total_potongan > 0)
                                            <span class="text-danger fw-semibold" style="font-size:.8rem"
                                                title="Aduan Masyarakat: -{{ number_format($item->potongan_aduan, 2, ',', '.') }} &middot; Keterlambatan: -{{ number_format($item->potongan_keterlambatan, 2, ',', '.') }}">
                                                -{{ number_format($item->total_potongan, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:.8rem">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="total-nilai">{{ number_format($item->total_nilai, 2, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Belum ada madrasah yang penilaiannya sudah difinalisasi untuk periode {{ $periode }}.
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
        {{-- MODAL ARSIPKAN RANKING --}}
        <div class="modal fade" id="modalArsipkan" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('ranking-arsip.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-archive text-success"></i>
                                Arsipkan Ranking Periode {{ $periode }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="periode" value="{{ $periode }}">

                            <div class="alert-warning-soft mb-3" style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:.9rem 1.1rem;color:#92400e;font-size:.85rem;">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Ini akan menyimpan snapshot ranking <strong>SEMUA jenjang gabungan</strong> untuk
                                periode {{ $periode }} — termasuk nilai mentah per bidang, total nilai asesor,
                                dan rincian potongan. Kalau periode ini sudah pernah diarsipkan sebelumnya, arsip
                                lama akan <strong>digantikan</strong> oleh data terbaru.
                            </div>

                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2"
                                placeholder="Mis. Hasil resmi JMA {{ $periode }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Arsipkan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
