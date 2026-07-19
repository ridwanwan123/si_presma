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
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.1rem 1.25rem;
        }

        .filter-field label {
            display: block;
            font-size: .76rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: .35rem;
        }

        .filter-field select {
            min-width: 240px;
            border-radius: 10px;
        }

        .filter-bar .btn-outline-secondary {
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

        .stat-icon.bg-green {
            background: #0f8a43;
        }

        .stat-icon.bg-blue {
            background: #2563eb;
        }

        .stat-icon.bg-amber {
            background: #f59e0b;
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

        /* ============ TABLE ============ */

        .arsip-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .87rem;
        }

        .arsip-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .arsip-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .arsip-table tbody tr:hover {
            background: #fbfcfe;
        }

        .periode-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(15, 138, 67, .1);
            color: #0f8a43;
            font-weight: 800;
            font-size: 1.1rem;
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
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <div>
                <h2>Arsip Ranking</h2>
                <p>Snapshot ranking yang sudah dibekukan per periode — datanya tidak ikut berubah walau ada koreksi data di
                    kemudian hari.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('ranking.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Ranking Live
                </a>
                <a href="{{ route('ranking-arsip.manual.create') }}" class="btn btn-success">
                    <i class="bi bi-pencil-square"></i> Input Manual (Data Lama)
                </a>
            </div>
        </div>

        <div class="container-fluid">

            {{-- FILTER JENJANG --}}
            <div class="content-card">
                <form method="GET" class="filter-bar">
                    <div class="filter-field">
                        <label>Filter Jenjang</label>
                        <select name="jenjang" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenjang</option>
                            @foreach ($daftarJenjang as $item)
                                <option value="{{ $item }}" {{ $jenjangFilter == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($jenjangFilter)
                        <a href="{{ route('ranking-arsip.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- STAT STRIP --}}
            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-green"><i class="bi bi-archive"></i></div>
                        <div>
                            <div class="stat-label">Total Arsip</div>
                            <div class="stat-value">{{ $daftarArsip->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-blue"><i class="bi bi-calendar-range"></i></div>
                        <div>
                            <div class="stat-label">Rentang Periode</div>
                            <div class="stat-value" style="font-size:1.1rem">
                                @if ($daftarArsip->isNotEmpty())
                                    {{ $daftarArsip->min('periode') }} – {{ $daftarArsip->max('periode') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-amber"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="stat-label">Total Baris Madrasah Terarsip</div>
                            <div class="stat-value">{{ $daftarArsip->sum('details_count') }}</div>
                        </div>
                    </div>
                </div>
            </div>

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
                                <th style="width:190px"></th>
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
                                        <a href="{{ route('ranking-arsip.show', $arsip->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('ranking-arsip.kelola', $arsip->id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Kelola">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="{{ route('ranking-arsip.export', $arsip->id) }}"
                                            class="btn btn-sm btn-outline-success" title="Export Excel">
                                            <i class="bi bi-file-earmark-excel"></i>
                                        </a>
                                        <form action="{{ route('ranking-arsip.destroy', $arsip->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Hapus arsip periode {{ $arsip->periode }}? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding:0">
                                        <div class="empty-state">
                                            <i class="bi bi-archive"></i>
                                            Belum ada arsip ranking.<br>
                                            <div class="mt-3 d-flex gap-2 justify-content-center">
                                                <a href="{{ route('ranking.index') }}" class="btn btn-success btn-sm">
                                                    <i class="bi bi-trophy"></i> Arsipkan dari Ranking Live
                                                </a>
                                                <a href="{{ route('ranking-arsip.manual.create') }}"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil-square"></i> Input Manual
                                                </a>
                                            </div>
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
