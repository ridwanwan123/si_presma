@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
        }

        .dash-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .dash-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: .25rem;
        }

        .dash-header p {
            color: #64748b;
            margin: 0;
        }

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.1rem 1.25rem;
        }

        /* ============ DASHBOARD ROW/COL ============ */

        .dash-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .dash-col-7 {
            flex: 1 1 58%;
            min-width: 320px;
        }

        .dash-col-5 {
            flex: 1 1 34%;
            min-width: 280px;
        }

        .dash-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .dash-stat-col {
            flex: 1 1 180px;
        }

        .card-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .card-title-row .title {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-weight: 700;
            color: #0f172a;
            font-size: .96rem;
        }

        .card-title-row .title i {
            font-size: 1.05rem;
        }

        /* ============ STAT CARDS ============ */

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

        .stat-icon.bg-blue {
            background: #2563eb;
        }

        .stat-icon.bg-green {
            background: #16a34a;
        }

        .stat-icon.bg-amber {
            background: #f59e0b;
        }

        .stat-icon.bg-slate {
            background: #64748b;
        }

        .stat-icon.bg-purple {
            background: #8b5cf6;
        }

        .stat-icon.bg-cyan {
            background: #0ea5e9;
        }

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

        .stat-underline {
            width: 26px;
            height: 4px;
            border-radius: 4px;
            margin-top: .5rem;
        }

        /* ============ CHART CONTAINERS ============ */

        .chart-box {
            position: relative;
            height: 240px;
        }

        .chart-box-sm {
            position: relative;
            height: 210px;
        }

        /* ============ KECOCOKAN RUBRIK ============ */
        .rubrik-callout {
            font-size: .82rem;
            color: #334155;
            line-height: 1.5;
            margin-bottom: .6rem;
        }

        .rubrik-callout strong {
            color: #0f8a43;
        }

        .rubrik-legend {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
        }

        .rubrik-legend-item {
            font-size: .74rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .rubrik-legend .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-sesuai {
            background: #16a34a;
        }

        .dot-beda {
            background: #f59e0b;
        }

        .dot-na {
            background: #94a3b8;
        }

        .rubrik-beda-title {
            font-size: .78rem;
            font-weight: 700;
            color: #92400e;
            margin: 1rem 0 .5rem;
        }

        .content-card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .content-card.h-100 .chart-box {
            flex: 1 1 auto;
            height: auto;
            min-height: 200px;
        }

        /* ============ LEGEND LIST ============ */

        .legend-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0 0;
        }

        .legend-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .35rem 0;
            font-size: .85rem;
            color: #334155;
        }

        .legend-dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-right: .5rem;
        }

        .legend-label {
            display: flex;
            align-items: center;
        }

        .legend-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* ============ TABLE ============ */

        .dash-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .85rem;
        }

        .dash-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 8px 10px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: left;
        }

        .dash-table tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .badge-status-madrasah {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-status-madrasah.completed {
            background: rgba(22, 163, 74, .1);
            color: #16a34a;
        }

        .badge-status-madrasah.in_progress {
            background: rgba(245, 158, 11, .12);
            color: #b45309;
        }

        .badge-status-madrasah.assigned,
        .badge-status-madrasah.not_assigned {
            background: #f1f5f9;
            color: #64748b;
        }

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
            background: linear-gradient(90deg, #60a5fa, #2563eb);
        }

        /* ============ INSIGHT ============ */

        .insight-card {
            background: #fffbeb;
            border: 1px solid #fde68a;
            height: 100%;
        }

        .insight-item {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            padding: .6rem 0;
            font-size: .87rem;
            color: #57534e;
        }

        .insight-item:not(:last-child) {
            border-bottom: 1px dashed #fde68a;
        }

        .insight-icon {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: #fef3c7;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
        }

        .btn-lanjut {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .78rem;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-lanjut:hover {
            color: #1d4ed8;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid pt-3">

            {{-- HEADER --}}
            <div class="dash-header">
                <div>
                    <h2>Dashboard Asesor</h2>
                    <p>Progress penilaian prestasi madrasah yang ditugaskan kepada Anda.</p>
                </div>
            </div>

            {{-- STAT CARDS --}}
            <div class="dash-stat-row">
                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-blue"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="stat-label">Total Madrasah</div>
                            <div class="stat-value">{{ $totalMadrasah }}</div>
                            <div class="stat-underline bg-blue"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-green"><i class="bi bi-check-circle"></i></div>
                        <div>
                            <div class="stat-label">Sudah Selesai</div>
                            <div class="stat-value">{{ $madrasahCompleted }}</div>
                            <div class="stat-underline bg-green"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-amber"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <div class="stat-label">Sedang Dikerjakan</div>
                            <div class="stat-value">{{ $madrasahInProgress }}</div>
                            <div class="stat-underline bg-amber"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-slate"><i class="bi bi-pause-circle"></i></div>
                        <div>
                            <div class="stat-label">Belum Dimulai</div>
                            <div class="stat-value">{{ $madrasahBelumMulai }}</div>
                            <div class="stat-underline bg-slate"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-purple"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Total Prestasi</div>
                            <div class="stat-value">{{ $totalPrestasi }}</div>
                            <div class="stat-underline bg-purple"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-cyan"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <div class="stat-label">Progress Keseluruhan</div>
                            <div class="stat-value">{{ $progresKeseluruhan }}%</div>
                            <div class="stat-underline bg-cyan"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KECOCOKAN RUBRIK JUKNIS + STATUS ASSIGNMENT --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-journal-check text-primary"></i> Kecocokan dengan Rubrik
                                Juknis</div>
                        </div>

                        @if ($kecocokanRubrikRingkasan['total'] === 0)
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox"
                                    style="font-size:1.6rem;color:#cbd5e1;display:block;margin-bottom:.4rem"></i>
                                Belum ada prestasi yang Anda nilai.
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="chart-box" style="width:140px;height:140px;flex-shrink:0">
                                    <canvas id="chartRubrik"></canvas>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="rubrik-callout">
                                        <strong>{{ $kecocokanRubrikRingkasan['persen_sesuai'] }}%</strong>
                                        penilaian Anda ({{ $kecocokanRubrikRingkasan['sesuai'] }} dari
                                        {{ $kecocokanRubrikRingkasan['total'] }})
                                        sudah <strong>sesuai</strong> rubrik resmi Juknis.
                                    </div>
                                    <div class="rubrik-legend">
                                        <span class="rubrik-legend-item"><span class="dot dot-sesuai"></span> Sesuai
                                            ({{ $kecocokanRubrikRingkasan['sesuai'] }})</span>
                                        <span class="rubrik-legend-item"><span class="dot dot-beda"></span> Beda
                                            ({{ $kecocokanRubrikRingkasan['beda'] }})</span>
                                        <span class="rubrik-legend-item"><span class="dot dot-na"></span> Belum Ada Rubrik
                                            ({{ $kecocokanRubrikRingkasan['belum_ada'] }})</span>
                                    </div>
                                </div>
                            </div>

                            @if ($daftarBedaRubrik->isNotEmpty())
                                <div class="rubrik-beda-title">
                                    <i class="bi bi-exclamation-triangle-fill text-warning"></i> Perlu Dicek Ulang — Beda
                                    dari Rubrik
                                </div>
                                <div class="table-responsive">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left">Madrasah / Kegiatan</th>
                                                <th>Skor Anda</th>
                                                <th>Rubrik</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($daftarBedaRubrik as $item)
                                                <tr>
                                                    <td style="text-align:left">
                                                        <div class="fw-semibold" style="font-size:.8rem">
                                                            {{ $item->nama_madrasah }}</div>
                                                        <div class="text-muted" style="font-size:.72rem">
                                                            {{ $item->nama_kegiatan }}</div>
                                                    </td>
                                                    <td>{{ number_format($item->skor_madrasah, 0, ',', '.') }}</td>
                                                    <td class="text-warning fw-bold">
                                                        {{ number_format($item->skor_rubrik, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="dash-col-5">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-pie-chart text-primary"></i> Status Assignment Madrasah
                            </div>
                        </div>
                        <div class="chart-box-sm">
                            <canvas id="chartStatus"></canvas>
                        </div>
                        <ul class="legend-list">
                            <li>
                                <span class="legend-label">
                                    <span class="legend-dot" style="background:#16a34a"></span>
                                    Sudah Selesai
                                </span>
                                <span class="legend-value">{{ $madrasahCompleted }}</span>
                            </li>
                            <li>
                                <span class="legend-label">
                                    <span class="legend-dot" style="background:#f59e0b"></span>
                                    Sedang Dikerjakan
                                </span>
                                <span class="legend-value">{{ $madrasahInProgress }}</span>
                            </li>
                            <li>
                                <span class="legend-label">
                                    <span class="legend-dot" style="background:#94a3b8"></span>
                                    Belum Dimulai
                                </span>
                                <span class="legend-value">{{ $madrasahBelumMulai }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- PROGRESS PER BIDANG + DISTRIBUSI PERSENTASE --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-bar-chart text-primary"></i> Progress Penilaian per
                                Bidang</div>
                        </div>
                        <div class="chart-box">
                            <canvas id="chartBidang"></canvas>
                        </div>
                    </div>
                </div>

                <div class="dash-col-5">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-percent text-primary"></i> Distribusi Persentase Nilai
                            </div>
                        </div>
                        <div class="chart-box-sm">
                            <canvas id="chartPersentase"></canvas>
                        </div>
                        <ul class="legend-list">
                            @forelse ($distribusiPersentase as $item)
                                <li>
                                    <span class="legend-label">
                                        <span class="legend-dot" style="background: {{ $item['warna'] }}"></span>
                                        {{ $item['label'] }}
                                    </span>
                                    <span class="legend-value">{{ $item['jumlah'] }}</span>
                                </li>
                            @empty
                                <li class="text-muted">Belum ada nilai yang diberikan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- DAFTAR MADRASAH + INSIGHT --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-list-check text-primary"></i> Daftar Madrasah
                                Ditugaskan</div>
                        </div>

                        <div class="table-responsive">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Madrasah</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th style="width:160px">Progress</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($daftarMadrasah as $item)
                                        <tr>
                                            <td>{{ $item->nama_madrasah }}</td>
                                            <td>{{ $item->periode }}</td>
                                            <td>
                                                <span class="badge-status-madrasah {{ $item->status }}">
                                                    {{ str_replace('_', ' ', ucfirst($item->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress-track flex-grow-1">
                                                        <div class="progress-fill" style="width: {{ $item->progress }}%">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold"
                                                        style="font-size:.78rem">{{ $item->progress }}%</span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('asesor.show', ['madrasah' => $item->madrasah_id]) }}"
                                                    class="btn-lanjut">
                                                    Lanjutkan <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada madrasah yang
                                                ditugaskan kepada Anda.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="dash-col-5">
                    <div class="content-card insight-card">
                        <div class="card-title-row mb-0">
                            <div class="title"><i class="bi bi-lightbulb text-warning"></i> Insight</div>
                        </div>

                        @forelse ($insight as $item)
                            <div class="insight-item">
                                <div class="insight-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                                <div>{!! $item['text'] !!}</div>
                            </div>
                        @empty
                            <div class="insight-item">
                                <div class="insight-icon"><i class="bi bi-info-circle"></i></div>
                                <div>Belum cukup data untuk menampilkan insight.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw(chart) {
                    if (!chart.config.options.centerText) return;

                    const {
                        ctx,
                        chartArea: {
                            left,
                            right,
                            top,
                            bottom
                        }
                    } = chart;

                    const centerX = (left + right) / 2;
                    const centerY = (top + bottom) / 2;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    ctx.font = '700 22px sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.fillText(chart.config.options.centerText.value, centerX, centerY - 10);

                    ctx.font = '400 12px sans-serif';
                    ctx.fillStyle = '#64748b';
                    ctx.fillText(chart.config.options.centerText.label, centerX, centerY + 12);

                    ctx.restore();
                }
            };

            Chart.register(centerTextPlugin);

            /* ============ KECOCOKAN RUBRIK JUKNIS ============ */
            const rubrikRingkasan = @json($kecocokanRubrikRingkasan);

            if (rubrikRingkasan.total > 0) {
                new Chart(document.getElementById('chartRubrik'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Sesuai', 'Beda', 'Belum Ada Rubrik'],
                        datasets: [{
                            data: [rubrikRingkasan.sesuai, rubrikRingkasan.beda, rubrikRingkasan
                                .belum_ada
                            ],
                            backgroundColor: ['#16a34a', '#f59e0b', '#94a3b8'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            /* ============ STATUS ASSIGNMENT (DONUT) ============ */
            new Chart(document.getElementById('chartStatus'), {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Selesai', 'Sedang Dikerjakan', 'Belum Dimulai'],
                    datasets: [{
                        data: [{{ $madrasahCompleted }}, {{ $madrasahInProgress }},
                            {{ $madrasahBelumMulai }}
                        ],
                        backgroundColor: ['#16a34a', '#f59e0b', '#94a3b8'],
                        borderWidth: 3,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    centerText: {
                        value: '{{ $totalMadrasah }}',
                        label: 'Madrasah'
                    }
                }
            });

            /* ============ PROGRESS PER BIDANG (GROUPED BAR) ============ */
            const bidangLabels = @json($progresPerBidang->pluck('bidang'));
            const bidangTotal = @json($progresPerBidang->pluck('total'));
            const bidangSudah = @json($progresPerBidang->pluck('sudah'));

            new Chart(document.getElementById('chartBidang'), {
                type: 'bar',
                data: {
                    labels: bidangLabels,
                    datasets: [{
                            label: 'Total Prestasi',
                            data: bidangTotal,
                            backgroundColor: '#dbeafe',
                            borderRadius: 4,
                            maxBarThickness: 28,
                        },
                        {
                            label: 'Sudah Dinilai',
                            data: bidangSudah,
                            backgroundColor: '#2563eb',
                            borderRadius: 4,
                            maxBarThickness: 28,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 0,
                            right: 6,
                            bottom: 0,
                            left: 0
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            /* ============ DISTRIBUSI PERSENTASE (DONUT) ============ */
            const persentaseLabels = @json($distribusiPersentase->pluck('label'));
            const persentaseJumlah = @json($distribusiPersentase->pluck('jumlah'));
            const persentaseWarna = @json($distribusiPersentase->pluck('warna'));

            new Chart(document.getElementById('chartPersentase'), {
                type: 'doughnut',
                data: {
                    labels: persentaseLabels,
                    datasets: [{
                        data: persentaseJumlah,
                        backgroundColor: persentaseWarna,
                        borderWidth: 3,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    centerText: {
                        value: '{{ $sudahDinilai }}',
                        label: 'Sudah Dinilai'
                    }
                }
            });

        });
    </script>
@endpush
