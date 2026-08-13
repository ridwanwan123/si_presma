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
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            align-items: end;
            gap: .75rem;
        }

        .filter-form .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-form .form-select {
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

        @media (max-width: 992px) {
            .filter-form {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
        }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: .9rem 1.1rem;
            color: #1e40af;
            font-size: .85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: .6rem;
        }

        .info-note i {
            margin-top: 2px;
        }

        /* ============ PAPAN PER BIDANG ============ */

        .bidang-card {
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .bidang-card-header {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .bidang-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .bidang-icon.bidang-akademik {
            background: #2563eb;
        }

        .bidang-icon.bidang-non-akademik {
            background: #38bdf8;
        }

        .bidang-icon.bidang-keagamaan {
            background: #f59e0b;
        }

        .bidang-icon.bidang-gtk {
            background: #8b5cf6;
        }

        .bidang-icon.bidang-lembaga {
            background: #94a3b8;
        }

        .bidang-card-header .title {
            font-weight: 700;
            color: #0f172a;
            font-size: 1rem;
        }

        .bidang-card-header .subtitle {
            font-size: .78rem;
            color: #94a3b8;
        }

        .ranking-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-responsive {
            padding: 0 1.4rem 1.1rem;
        }

        .ranking-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            padding: 14px 18px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .ranking-table thead th:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .ranking-table thead th:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .ranking-table tbody td {
            padding: 16px 18px;
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

        /* ============ TABEL TOTAL (REFERENSI) ============ */

        .total-section-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 2rem 0 1.25rem;
        }

        .total-section-divider .label {
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .05em;
            color: #64748b;
            white-space: nowrap;
        }

        .total-section-divider .line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .total-nilai-abu {
            font-weight: 700;
            color: #64748b;
            font-size: .95rem;
        }

        /* ============ TAB JENJANG ============ */

        .jenjang-tabs {
            border-bottom: 2px solid #e2e8f0;
            gap: .3rem;
            margin-bottom: 1.5rem;
        }

        .jenjang-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 10px 10px 0 0;
            padding: .7rem 1.4rem;
            font-weight: 700;
            font-size: .92rem;
            color: #64748b;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .jenjang-tabs .nav-link .tab-count {
            font-size: .7rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #64748b;
            padding: 1px 8px;
            border-radius: 999px;
        }

        .jenjang-tabs .nav-link:hover {
            color: #0f8a43;
            border-color: transparent;
        }

        .jenjang-tabs .nav-link.active {
            color: #0f8a43;
            background: #f0fdf4;
            border-bottom-color: #0f8a43;
        }

        .jenjang-tabs .nav-link.active .tab-count {
            background: #0f8a43;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h2>Hasil Penilaian</h2>
                <p>Peringkat juara per Jenjang &amp; Bidang, periode
                    {{ $periode }}{{ $statusFilter ? ' — ' . $statusFilter : '' }}{{ $kotaFilter ? ' — ' . $kotaFilter : '' }}.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('ranking.export', ['periode' => $periode, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                    class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i>
                    Export Excel
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
                        <label class="form-label">Status Madrasah</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach ($daftarStatus as $item)
                                <option value="{{ $item }}" {{ $statusFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Kota</label>
                        <select name="kota" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Kota</option>
                            @foreach ($daftarKota as $item)
                                <option value="{{ $item }}" {{ $kotaFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <a href="{{ route('ranking.index', ['periode' => $periode]) }}"
                        class="btn btn-outline-secondary btn-reset-filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </a>
                </form>
            </div>

            <div class="info-note">
                <i class="bi bi-info-circle-fill"></i>
                <span>
                    Berikut ini adalah hasil penilaian prestasi madrasah untuk periode
                    <strong>{{ $periode }}</strong>.
                    Data diambil dari
                    <strong>hasil finalisasi penilaian</strong> oleh asesor. Nilai akhir sudah dikurangi potongan dari Aduan
                    Masyarakat dan Keterlambatan Berkas (jika ada). <strong>Hanya madrasah yang sudah difinalisasi
                        penilaiannya yang muncul di sini.</strong>
                </span>
            </div>

            {{-- ================= PAPAN PER JENJANG x PER BIDANG ================= --}}
            @php
                $ikonBidang = [
                    'Akademik' => 'bi-mortarboard',
                    'Non Akademik' => 'bi-award',
                    'Keagamaan' => 'bi-book',
                    'GTK' => 'bi-people',
                    'Lembaga' => 'bi-building',
                ];
            @endphp

            @php
                // Urutan tab sesuai jenjang pendidikan (bukan alfabetis).
                // Kalau ada nilai jenjang lain di luar 4 ini, tetap muncul
                // di akhir (tidak hilang), cuma urutannya paling belakang.
                $urutanJenjang = ['RA', 'MI', 'MTs', 'MA'];
                $perJenjangUrut = collect($hasil['per_jenjang'])->sortBy(function ($dataJenjang, $jenjang) use (
                    $urutanJenjang,
                ) {
                    $index = array_search($jenjang, $urutanJenjang);
                    return $index === false ? 999 : $index;
                });
            @endphp

            @if ($perJenjangUrut->isEmpty())
                <div class="content-card text-center text-muted py-5">
                    Belum ada madrasah yang penilaiannya sudah difinalisasi untuk periode {{ $periode }}.
                </div>
            @else
                <ul class="nav nav-tabs jenjang-tabs" id="jenjangTab" role="tablist">
                    @foreach ($perJenjangUrut as $jenjang => $dataJenjang)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="tab-jenjang-{{ $loop->index }}" data-bs-toggle="tab"
                                data-bs-target="#panel-jenjang-{{ $loop->index }}" type="button" role="tab"
                                aria-controls="panel-jenjang-{{ $loop->index }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="bi bi-mortarboard-fill"></i>
                                {{ $jenjang }}
                                <span class="tab-count">{{ $dataJenjang['total']->count() }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="jenjangTabContent">
                    @foreach ($perJenjangUrut as $jenjang => $dataJenjang)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="panel-jenjang-{{ $loop->index }}" role="tabpanel"
                            aria-labelledby="tab-jenjang-{{ $loop->index }}">

                            @foreach ($dataJenjang['per_bidang'] as $bidang => $papan)
                                <div class="content-card bidang-card">
                                    <div class="bidang-card-header">
                                        <div class="bidang-icon bidang-{{ str_replace(' ', '-', strtolower($bidang)) }}">
                                            <i class="bi {{ $ikonBidang[$bidang] ?? 'bi-trophy' }}"></i>
                                        </div>
                                        <div>
                                            <div class="title">Juara Bidang {{ $bidang }}</div>
                                            <div class="subtitle">{{ $papan->count() }} madrasah berpartisipasi di
                                                bidang ini</div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="ranking-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:70px" class="text-center">Peringkat</th>
                                                    <th>Madrasah</th>
                                                    <th>Status</th>
                                                    <th>Wilayah</th>
                                                    <th class="text-end">Potongan</th>
                                                    <th class="text-end">Nilai Akhir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($papan as $item)
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="rank-badge rank-{{ $item->peringkat }}">
                                                                {{ $item->peringkat }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="madrasah-name">{{ $item->nama_madrasah }}
                                                            </div>
                                                            <div class="madrasah-npsn">NPSN: {{ $item->npsn }}</div>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="jenjang-badge">{{ $item->status_madrasah ?? '-' }}</span>
                                                        </td>
                                                        <td>{{ $item->kota }}</td>
                                                        <td class="text-end">
                                                            @if ($item->total_potongan > 0)
                                                                <span class="text-danger fw-semibold"
                                                                    style="font-size:.8rem"
                                                                    title="Aduan Masyarakat: -{{ number_format($item->potongan_aduan, 2, ',', '.') }} &middot; Jatah Keterlambatan: -{{ number_format($item->potongan_keterlambatan, 2, ',', '.') }}">
                                                                    -{{ number_format($item->total_potongan, 2, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted" style="font-size:.8rem">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <span
                                                                class="total-nilai">{{ number_format($item->nilai_akhir, 0, ',', '.') }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">
                                                            Belum ada madrasah dengan prestasi bidang {{ $bidang }}
                                                            pada jenjang {{ $jenjang }} periode ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @endforeach
                </div>
            @endif

            {{-- ================= TABEL TOTAL KESELURUHAN (REFERENSI) ================= --}}
            {{-- <div class="total-section-divider">
                <span class="label">TOTAL KESELURUHAN (REFERENSI — BUKAN PENENTU JUARA)</span>
                <span class="line"></span>
            </div>

            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th style="width:70px" class="text-center">No</th>
                                <th>Madrasah</th>
                                <th>Jenjang</th>
                                <th>Wilayah</th>
                                <th class="text-center">Prestasi Dinilai</th>
                                <th class="text-end">Total Potongan</th>
                                <th class="text-end">Total Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hasil['total'] as $item)
                                <tr>
                                    <td class="text-center">
                                        <span class="rank-badge">{{ $item->peringkat }}</span>
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
                                            <span class="text-danger fw-semibold" style="font-size:.8rem">
                                                -{{ number_format($item->total_potongan, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:.8rem">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span
                                            class="total-nilai-abu">{{ number_format($item->total_nilai_akhir, 2, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Belum ada madrasah yang penilaiannya sudah difinalisasi untuk periode
                                        {{ $periode }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> --}}

        </div>
    </main>
@endsection

{{-- MODAL ARSIPKAN RANKING — sengaja di dalam <main>...@endsection,
             sebelumnya sempat berada DI LUAR @endsection sehingga tidak
             pernah ikut ter-render sama sekali. --}}
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

                    <div class="alert-warning-soft mb-3"
                        style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:.9rem 1.1rem;color:#92400e;font-size:.85rem;">
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
