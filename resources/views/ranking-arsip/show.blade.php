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
            margin-bottom: 1.25rem;
        }

        /* ============ FILTER ============ */

        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
            padding: 1.1rem 1.25rem;
        }

        .filter-bar .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-bar .form-select {
            min-width: 220px;
            border-radius: 10px;
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
            padding: 1.1rem 1.25rem;
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

        .stat-icon.bg-purple {
            background: #8b5cf6;
        }

        .stat-icon.bg-gold {
            background: #ca8a04;
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

        /* ============ INFO ARSIP ============ */

        .info-arsip {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: .85rem;
            color: #475569;
        }

        .info-arsip strong {
            color: #0f172a;
        }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: .9rem 1.1rem;
            color: #1e40af;
            font-size: .85rem;
            margin-bottom: 1.25rem;
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

        /* ============ TABLE ============ */

        .detail-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .82rem;
        }

        .detail-table thead th {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 10px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
            white-space: nowrap;
        }

        .detail-table thead th:first-child,
        .detail-table thead th:nth-child(2) {
            text-align: left;
        }

        .detail-table tbody td {
            padding: 10px 10px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .detail-table tbody td:first-child,
        .detail-table tbody td:nth-child(2) {
            text-align: left;
        }

        .detail-table tbody tr:hover {
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

        .madrasah-npsn {
            font-size: .72rem;
            color: #94a3b8;
        }

        .total-nilai {
            font-weight: 700;
            color: #0f8a43;
        }

        .total-nilai-abu {
            font-weight: 700;
            color: #64748b;
        }

        .potongan-nilai {
            color: #dc2626;
            font-weight: 600;
        }

        .potongan-none {
            color: #cbd5e1;
        }

        /* ============ TOTAL (REFERENSI) ============ */

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
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <div>
                <h2>Arsip Ranking — Periode {{ $ranking_arsip->periode }}</h2>
                <p>Snapshot beku, tidak berubah walau ada koreksi data setelah tanggal arsip ini dibuat.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('ranking-arsip.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('ranking-arsip.kelola', $ranking_arsip->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil-square"></i> Kelola Data
                </a>
                <a href="{{ route('ranking-arsip.export', $ranking_arsip->id) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="container-fluid">

            {{-- FILTER JENJANG --}}
            <div class="content-card">
                <form method="GET" class="filter-bar">
                    <div>
                        <label class="form-label">Filter Jenjang</label>
                        <select name="jenjang" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenjang</option>
                            @foreach ($daftarJenjangArsip as $item)
                                <option value="{{ $item }}" {{ $jenjangFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($jenjangFilter)
                        <a href="{{ route('ranking-arsip.show', $ranking_arsip->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- STAT STRIP --}}
            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-blue"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="stat-label">Total Madrasah</div>
                            <div class="stat-value">{{ $hasil['total']->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-green"><i class="bi bi-graph-up"></i></div>
                        <div>
                            <div class="stat-label">Total Nilai Sistem</div>
                            <div class="stat-value">
                                {{ number_format($hasil['total']->sum('total_nilai_akhir'), 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-purple"><i class="bi bi-bar-chart"></i></div>
                        <div>
                            <div class="stat-label">Rata-rata Nilai</div>
                            <div class="stat-value">
                                {{ $hasil['total']->count() > 0 ? number_format($hasil['total']->avg('total_nilai_akhir'), 2, ',', '.') : '0' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-gold"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Peringkat 1 (Referensi)</div>
                            <div class="stat-value" style="font-size:1rem">
                                {{ $hasil['total']->firstWhere('peringkat_tampil', 1)->nama_madrasah ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="info-arsip">
                    <div><i class="bi bi-person-check text-muted me-1"></i> Diarsipkan oleh:
                        <strong>{{ $ranking_arsip->diarsipkanOleh->nama ?? '-' }}</strong></div>
                    <div><i class="bi bi-clock text-muted me-1"></i> Pada:
                        <strong>{{ $ranking_arsip->diarsipkan_pada->format('d M Y H:i') }}</strong></div>
                    @if ($ranking_arsip->catatan)
                        <div><i class="bi bi-sticky text-muted me-1"></i> Catatan:
                            <strong>{{ $ranking_arsip->catatan }}</strong></div>
                    @endif
                </div>
            </div>

            <div class="info-note">
                <i class="bi bi-info-circle-fill"></i>
                <span>
                    JMA menentukan juara <strong>per Bidang, per Jenjang</strong>. Setiap bidang di bawah punya
                    peringkatnya sendiri-sendiri (dihitung ulang dari data arsip ini). Tabel "Total Keseluruhan" di
                    paling bawah cuma referensi/statistik, <strong>bukan</strong> dasar penentuan juara.
                </span>
            </div>

            {{-- ================= 5 PAPAN PER BIDANG ================= --}}
            @php
                $ikonBidang = [
                    'Akademik' => 'bi-mortarboard',
                    'Non Akademik' => 'bi-award',
                    'Keagamaan' => 'bi-book',
                    'GTK' => 'bi-people',
                    'Lembaga' => 'bi-building',
                ];
            @endphp

            @foreach ($hasil['per_bidang'] as $bidang => $papan)
                <div class="content-card bidang-card">
                    <div class="bidang-card-header">
                        <div class="bidang-icon bidang-{{ str_replace(' ', '-', strtolower($bidang)) }}">
                            <i class="bi {{ $ikonBidang[$bidang] ?? 'bi-trophy' }}"></i>
                        </div>
                        <div>
                            <div class="title">Juara Bidang {{ $bidang }}</div>
                            <div class="subtitle">{{ $papan->count() }} madrasah berpartisipasi di bidang ini</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>Peringkat</th>
                                    <th>Madrasah</th>
                                    <th>Jenjang</th>
                                    <th>Wilayah</th>
                                    <th>Potongan</th>
                                    <th>Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($papan as $item)
                                    <tr>
                                        <td><span
                                                class="rank-badge rank-{{ $item->peringkat }}">{{ $item->peringkat }}</span>
                                        </td>
                                        <td>
                                            <div class="madrasah-name">{{ $item->nama_madrasah }}</div>
                                            <div class="madrasah-npsn">NPSN: {{ $item->npsn ?: '-' }}</div>
                                        </td>
                                        <td>{{ $item->jenjang_madrasah }}</td>
                                        <td>{{ $item->kota }}</td>
                                        <td class="{{ $item->total_potongan > 0 ? 'potongan-nilai' : 'potongan-none' }}"
                                            title="Aduan: -{{ number_format($item->potongan_aduan, 2, ',', '.') }} &middot; Jatah Keterlambatan: -{{ number_format($item->potongan_keterlambatan, 2, ',', '.') }}">
                                            {{ $item->total_potongan > 0 ? '-' . number_format($item->total_potongan, 2, ',', '.') : '-' }}
                                        </td>
                                        <td><span
                                                class="total-nilai">{{ number_format($item->nilai_akhir, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Belum ada madrasah dengan prestasi bidang {{ $bidang }} pada arsip ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            {{-- ================= TABEL TOTAL KESELURUHAN (REFERENSI) ================= --}}
            <div class="total-section-divider">
                <span class="label">TOTAL KESELURUHAN (REFERENSI — BUKAN PENENTU JUARA)</span>
                <span class="line"></span>
            </div>

            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Madrasah</th>
                                <th>Jenjang</th>
                                <th>Akademik</th>
                                <th>Non Akademik</th>
                                <th>Keagamaan</th>
                                <th>GTK</th>
                                <th>Lembaga</th>
                                <th>Total Asesor</th>
                                <th>Total Potongan</th>
                                <th>Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hasil['total'] as $item)
                                <tr>
                                    <td><span class="rank-badge">{{ $item->peringkat_tampil }}</span></td>
                                    <td>
                                        <div class="madrasah-name">{{ $item->nama_madrasah }}</div>
                                        <div class="madrasah-npsn">NPSN: {{ $item->npsn ?: '-' }}</div>
                                    </td>
                                    <td>{{ $item->jenjang_madrasah }}</td>
                                    <td>{{ number_format($item->nilai_akademik, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->nilai_non_akademik, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->nilai_keagamaan, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->nilai_gtk, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->nilai_lembaga, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->total_nilai_asesor, 2, ',', '.') }}</td>
                                    @php $totalPotonganRow = $item->potongan_aduan + $item->potongan_keterlambatan; @endphp
                                    <td class="{{ $totalPotonganRow > 0 ? 'potongan-nilai' : 'potongan-none' }}">
                                        {{ $totalPotonganRow > 0 ? '-' . number_format($totalPotonganRow, 2, ',', '.') : '-' }}
                                    </td>
                                    <td><span
                                            class="total-nilai-abu">{{ number_format($item->total_nilai_akhir, 2, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-5">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
