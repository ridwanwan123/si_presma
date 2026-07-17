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

        .arsip-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .87rem; }
        .arsip-table thead th {
            font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em;
            color: #64748b; padding: 10px 14px; border-bottom: 2px solid #e2e8f0; background: #f8fafc;
        }
        .arsip-table tbody td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        .periode-badge {
            display:inline-flex; align-items:center; justify-content:center;
            width: 52px; height: 52px; border-radius: 14px;
            background: rgba(15,138,67,.1); color:#0f8a43; font-weight:800; font-size:1.1rem;
        }
    </style>
@endpush

@section('content')
<main class="content">
    <div class="page-title">
        <div>
            <h2>Arsip Ranking</h2>
            <p>Snapshot ranking yang sudah dibekukan per periode — datanya tidak ikut berubah walau ada koreksi data di kemudian hari.</p>
        </div>
        <a href="{{ route('ranking.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Ranking Live
        </a>
    </div>

    <div class="container-fluid">
        <div class="content-card">
            <div class="table-responsive">
                <table class="arsip-table">
                    <thead>
                        <tr>
                            <th style="width:80px">Periode</th>
                            <th class="text-center">Jumlah Madrasah</th>
                            <th>Diarsipkan Oleh</th>
                            <th>Diarsipkan Pada</th>
                            <th>Catatan</th>
                            <th style="width:160px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarArsip as $arsip)
                            <tr>
                                <td><span class="periode-badge">{{ $arsip->periode }}</span></td>
                                <td class="text-center fw-semibold">{{ $arsip->details_count }}</td>
                                <td>{{ $arsip->diarsipkanOleh->nama ?? '-' }}</td>
                                <td>{{ $arsip->diarsipkan_pada->format('d M Y H:i') }}</td>
                                <td>{{ $arsip->catatan ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('ranking-arsip.show', $arsip->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('ranking-arsip.export', $arsip->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                    <form action="{{ route('ranking-arsip.destroy', $arsip->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus arsip periode {{ $arsip->periode }}? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    Belum ada arsip ranking. Buka <a href="{{ route('ranking.index') }}">Hasil & Ranking</a> lalu klik "Arsipkan Ranking".
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