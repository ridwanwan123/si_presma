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

        .potongan-nilai {
            color: #dc2626;
            font-weight: 600;
        }

        .potongan-none {
            color: #cbd5e1;
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
                            <div class="stat-label">Total Nilai Sistem</div>
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

                <div class="table-responsive">
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Madrasah</th>
                                <th>Jenjang</th>
                                <th>Akademik</th>
                                <th>Non Akademik</th>
                                <th>Keagamaan</th>
                                <th>GTK</th>
                                <th>Lembaga</th>
                                <th>Total Asesor</th>
                                <th>Potongan Aduan</th>
                                <th>Potongan Telat</th>
                                <th>Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detail as $item)
                                <tr>
                                    <td><span class="rank-badge rank-{{ $item->peringkat }}">{{ $item->peringkat }}</span>
                                    </td>
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
                                    <td class="{{ $item->potongan_aduan > 0 ? 'potongan-nilai' : 'potongan-none' }}">
                                        {{ $item->potongan_aduan > 0 ? '-' . number_format($item->potongan_aduan, 2, ',', '.') : '-' }}
                                    </td>
                                    <td
                                        class="{{ $item->potongan_keterlambatan > 0 ? 'potongan-nilai' : 'potongan-none' }}">
                                        {{ $item->potongan_keterlambatan > 0 ? '-' . number_format($item->potongan_keterlambatan, 2, ',', '.') : '-' }}
                                    </td>
                                    <td><span
                                            class="total-nilai">{{ number_format($item->total_nilai_akhir, 2, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-5">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
