@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid { padding: 0 1rem; }
        .page-title { padding: 0 1rem; margin-bottom: 1.5rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
        .page-title h2 { font-size: 1.8rem; font-weight: 700; color: #0f172a; margin-bottom: .25rem; }
        .page-title p { color: #64748b; margin: 0; }

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0,0,0,.04);
            padding: 0;
            margin-bottom: 1.25rem;
        }

        .info-arsip {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: .85rem;
            color: #475569;
        }

        .info-arsip strong { color: #0f172a; }

        .detail-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .82rem; }
        .detail-table thead th {
            font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em;
            color: #64748b; padding: 9px 10px; border-bottom: 2px solid #e2e8f0; background: #f8fafc;
            text-align: center; white-space: nowrap;
        }
        .detail-table thead th:first-child, .detail-table thead th:nth-child(2) { text-align: left; }
        .detail-table tbody td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: middle; white-space: nowrap; }
        .detail-table tbody td:first-child, .detail-table tbody td:nth-child(2) { text-align: left; }

        .rank-badge {
            display:inline-flex; align-items:center; justify-content:center;
            width: 28px; height: 28px; border-radius: 50%; font-weight: 700; font-size: .78rem;
            background: #f1f5f9; color: #475569;
        }

        .total-nilai { font-weight: 700; color: #0f8a43; }
        .potongan-nilai { color: #dc2626; font-weight: 600; }
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
            <a href="{{ route('ranking-arsip.export', $ranking_arsip->id) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="content-card">
            <div class="info-arsip">
                <div>Diarsipkan oleh: <strong>{{ $ranking_arsip->diarsipkanOleh->nama ?? '-' }}</strong></div>
                <div>Pada: <strong>{{ $ranking_arsip->diarsipkan_pada->format('d M Y H:i') }}</strong></div>
                <div>Jumlah madrasah: <strong>{{ $detail->count() }}</strong></div>
                @if ($ranking_arsip->catatan)
                    <div>Catatan: <strong>{{ $ranking_arsip->catatan }}</strong></div>
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
                                <td><span class="rank-badge">{{ $item->peringkat }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $item->nama_madrasah }}</div>
                                    <div class="text-muted" style="font-size:.7rem">NPSN: {{ $item->npsn }}</div>
                                </td>
                                <td>{{ $item->jenjang_madrasah }}</td>
                                <td>{{ number_format($item->nilai_akademik, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->nilai_non_akademik, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->nilai_keagamaan, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->nilai_gtk, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->nilai_lembaga, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->total_nilai_asesor, 2, ',', '.') }}</td>
                                <td class="potongan-nilai">
                                    {{ $item->potongan_aduan > 0 ? '-' . number_format($item->potongan_aduan, 2, ',', '.') : '-' }}
                                </td>
                                <td class="potongan-nilai">
                                    {{ $item->potongan_keterlambatan > 0 ? '-' . number_format($item->potongan_keterlambatan, 2, ',', '.') : '-' }}
                                </td>
                                <td><span class="total-nilai">{{ number_format($item->total_nilai_akhir, 2, ',', '.') }}</span></td>
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