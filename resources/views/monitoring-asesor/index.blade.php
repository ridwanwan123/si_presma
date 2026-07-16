@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
        }

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

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

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
            gap: .9rem;
            height: 100%;
        }

        .stat-icon {
            flex-shrink: 0;
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #fff;
        }

        .stat-icon.bg-blue { background: #2563eb; }
        .stat-icon.bg-green { background: #16a34a; }
        .stat-icon.bg-amber { background: #f59e0b; }
        .stat-icon.bg-cyan { background: #0ea5e9; }

        .stat-label {
            font-size: .78rem;
            color: #64748b;
            margin-bottom: .15rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
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
            min-width: 160px;
        }

        .monitoring-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .87rem;
        }

        .monitoring-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
        }

        .monitoring-table thead th:first-child,
        .monitoring-table thead th:nth-child(2) {
            text-align: left;
        }

        .monitoring-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            color: #334155;
            vertical-align: middle;
        }

        .monitoring-table tbody td:first-child,
        .monitoring-table tbody td:nth-child(2) {
            text-align: left;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-weight: 700;
            font-size: .78rem;
            background: #f1f5f9;
            color: #475569;
        }

        .badge-count {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
        }

        .badge-count.selesai { background: rgba(22, 163, 74, .1); color: #16a34a; }
        .badge-count.dikerjakan { background: rgba(245, 158, 11, .12); color: #b45309; }
        .badge-count.belum { background: #f1f5f9; color: #64748b; }

        .progress-track {
            width: 100%;
            height: 8px;
            border-radius: 6px;
            background: #eef2f7;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 6px;
        }

        .progress-fill.low { background: linear-gradient(90deg, #f87171, #ef4444); }
        .progress-fill.mid { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
        .progress-fill.high { background: linear-gradient(90deg, #4ade80, #16a34a); }

        .progress-percent {
            font-size: .78rem;
            font-weight: 700;
            color: #0f172a;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Monitoring Asesor</h2>
            <p>Rangkuman progress penilaian seluruh asesor untuk periode {{ $periode }}. Diurutkan dari yang paling
                tertinggal.</p>
        </div>

        <div class="container-fluid">

            {{-- FILTER PERIODE --}}
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
                </form>
            </div>

            {{-- RINGKASAN --}}
            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-blue"><i class="bi bi-people"></i></div>
                        <div>
                            <div class="stat-label">Total Asesor</div>
                            <div class="stat-value">{{ $totalAsesor }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-green"><i class="bi bi-check-circle"></i></div>
                        <div>
                            <div class="stat-label">Sudah Selesai Semua</div>
                            <div class="stat-value">{{ $asesorSelesaiSemua }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-amber"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <div class="stat-label">Belum Mulai Sama Sekali</div>
                            <div class="stat-value">{{ $asesorBelumMulai }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-cyan"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <div class="stat-label">Progress Keseluruhan</div>
                            <div class="stat-value">{{ $progresKeseluruhan }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL PER ASESOR --}}
            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="monitoring-table">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Asesor</th>
                                <th>Madrasah</th>
                                <th>Selesai</th>
                                <th>Dikerjakan</th>
                                <th>Belum Mulai</th>
                                <th style="width:200px">Progress Penilaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarAsesor as $i => $item)
                                <tr>
                                    <td>
                                        <span class="rank-badge">{{ $i + 1 }}</span>
                                    </td>
                                    <td class="fw-semibold">{{ $item->nama_asesor }}</td>
                                    <td>{{ $item->total_madrasah }}</td>
                                    <td>
                                        <span class="badge-count selesai">{{ $item->selesai }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-count dikerjakan">{{ $item->dikerjakan }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-count belum">{{ $item->belum_mulai }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-track flex-grow-1">
                                                @php
                                                    $warnaProgress = $item->progress >= 75 ? 'high' : ($item->progress >= 40 ? 'mid' : 'low');
                                                @endphp
                                                <div class="progress-fill {{ $warnaProgress }}" style="width: {{ $item->progress }}%"></div>
                                            </div>
                                            <span class="progress-percent">{{ $item->progress }}%</span>
                                        </div>
                                        <div class="text-muted" style="font-size:.72rem">
                                            {{ $item->sudah_dinilai }} / {{ $item->total_prestasi }} prestasi
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Belum ada asesor yang ditugaskan untuk periode {{ $periode }}.
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