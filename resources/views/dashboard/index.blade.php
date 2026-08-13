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
            padding: 1.25rem 1.4rem;
        }

        .section-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 2rem 0 1.25rem;
        }

        .section-divider:first-of-type {
            margin-top: 0;
        }

        .section-divider .label {
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .05em;
            color: #64748b;
            white-space: nowrap;
        }

        .section-divider .line {
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        /* ============ FILTER ============ */

        .filter-form {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 576px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
        }

        /* ============ ROW / COL ============ */

        .dash-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .dash-col-half {
            flex: 1 1 49%;
            min-width: 280px;
        }

        .stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .stat-col {
            flex: 1 1 260px;
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

        .stat-icon.bg-purple {
            background: #8b5cf6;
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

        .stat-value .stat-value-sub {
            font-size: .95rem;
            font-weight: 600;
            color: #94a3b8;
        }

        /* ============ CARD TITLE ============ */

        .card-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .card-title-row .title {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-weight: 700;
            color: #0f172a;
            font-size: .96rem;
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

        .content-card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .content-card.h-100 .chart-box,
        .content-card.h-100 .chart-box-sm {
            flex: 1 1 auto;
            height: auto;
            min-height: 190px;
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

        /* ============ TABLES ============ */

        .dash-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .85rem;
        }

        .table-responsive {
            padding: 0 .1rem .1rem;
        }

        .dash-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
        }

        .dash-table thead th:first-child {
            text-align: left;
        }

        .dash-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            color: #334155;
        }

        .dash-table tbody td:first-child {
            text-align: left;
            font-weight: 600;
        }

        .dash-table tbody tr.total-row td {
            font-weight: 800;
            color: #0f172a;
            background: #f8fafc;
        }

        .dash-table .col-total {
            font-weight: 700;
            color: #2563eb;
        }

        /* ============ HASIL & RANKING — PREVIEW JUARA 1 ============ */

        .juara-jenjang-label {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .04em;
            color: #fff;
            background: #0f8a43;
            padding: .3rem .85rem;
            border-radius: 999px;
            margin: 1rem 0 .75rem;
        }

        .juara-jenjang-label:first-child {
            margin-top: 0;
        }

        .juara-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: .6rem;
        }

        .juara-card {
            border: 1px solid #eef0f2;
            border-radius: 12px;
            padding: .6rem .7rem;
            background: #fff;
        }

        .juara-card.kosong {
            background: #f8fafc;
        }

        .juara-bidang {
            font-size: .66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #94a3b8;
            margin-bottom: .35rem;
        }

        .juara-row {
            display: flex;
            align-items: center;
            gap: .4rem;
            padding: .15rem 0;
        }

        .juara-rank {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .6rem;
            font-weight: 800;
            color: #fff;
            background: #94a3b8;
        }

        .juara-rank-1 {
            background: #eab308;
        }

        .juara-rank-2 {
            background: #94a3b8;
        }

        .juara-rank-3 {
            background: #b45309;
        }

        .juara-nama-compact {
            font-size: .74rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .juara-kosong-text {
            font-size: .8rem;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid pt-3">

            {{-- HEADER --}}
            <div class="dash-header">
                <div>
                    <h2>Dashboard Prestasi Madrasah</h2>
                    <p>Ringkasan prestasi madrasah periode {{ $periode }}</p>
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
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ !$statusFilter ? 'selected' : '' }}>Semua Status</option>
                            @foreach ($opsiFilter['status'] as $item)
                                <option value="{{ $item }}" {{ $statusFilter == $item ? 'selected' : '' }}>
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

                    <a href="{{ route('dashboard', ['periode' => $periode]) }}"
                        class="btn btn-outline-secondary btn-reset-filter">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </form>
            </div>

            {{-- =====================================================================
                 1. RINGKASAN PERIODE BERJALAN
            ====================================================================== --}}
            <div class="section-divider">
                <span class="label">RINGKASAN PERIODE {{ $periode }}</span>
                <span class="line"></span>
            </div>

            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-blue"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Total Prestasi (Diakui)</div>
                            <div class="stat-value">
                                {{ number_format($ringkasanPeriode['total_prestasi'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-green"><i class="bi bi-building-check"></i></div>
                        <div>
                            <div class="stat-label">Madrasah Aktif</div>
                            <div class="stat-value">{{ $ringkasanPeriode['madrasah_aktif'] }}
                                <span class="stat-value-sub">/ {{ $ringkasanPeriode['total_madrasah_terdaftar'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-purple"><i class="bi bi-patch-check"></i></div>
                        <div>
                            <div class="stat-label">Madrasah Selesai Dinilai</div>
                            <div class="stat-value">{{ $ringkasanPeriode['madrasah_finished'] }}
                                <span class="stat-value-sub">/ {{ $ringkasanPeriode['total_madrasah_terdaftar'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =====================================================================
                 2. PERBANDINGAN PRESTASI PER TINGKAT
            ====================================================================== --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-bar-chart text-primary"></i> Distribusi Prestasi berdasarkan Tingkat
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Tingkat</th>
                                <th>Jumlah Prestasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($perbandinganTingkat as $row)
                                <tr>
                                    <td>{{ $row['tingkat'] }}</td>
                                    <td class="col-total">{{ $row['jumlah'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- =====================================================================
                 3. MATRIX TOTAL PRESTASI PER JENJANG x BIDANG
            ====================================================================== --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-grid-3x3 text-primary"></i> Total Prestasi Keseluruhan Per Jenjang
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Bidang</th>
                                @foreach ($matrixJenjangBidang['daftar_jenjang'] as $jenjang)
                                    <th>{{ $jenjang }}</th>
                                @endforeach
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matrixJenjangBidang['matrix'] as $row)
                                <tr>
                                    <td>{{ $row['bidang'] }}</td>
                                    @foreach ($matrixJenjangBidang['daftar_jenjang'] as $jenjang)
                                        <td>{{ $row['per_jenjang'][$jenjang] }}</td>
                                    @endforeach
                                    <td class="col-total">{{ $row['total'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td>Total</td>
                                @foreach ($matrixJenjangBidang['daftar_jenjang'] as $jenjang)
                                    <td>{{ $matrixJenjangBidang['total_per_jenjang'][$jenjang] }}</td>
                                @endforeach
                                <td class="col-total">{{ $matrixJenjangBidang['total_keseluruhan'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- =====================================================================
                 4. HASIL & RANKING — PREVIEW JUARA 1 PER JENJANG x BIDANG
            ====================================================================== --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-trophy text-primary"></i> Pemenang Jakarta Madrasah Awards {{ $periode + 1 }}</div>
                    <a href="{{ route('ranking.index', ['periode' => $periode, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                        class="btn btn-sm btn-outline-success">
                        Lihat Selengkapnya <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @forelse ($juaraPerJenjangBidang->groupBy('jenjang') as $jenjang => $itemsJenjang)
                    <div class="juara-jenjang-label"><i class="bi bi-mortarboard-fill"></i> {{ $jenjang }}</div>
                    <div class="juara-grid">
                        @foreach ($itemsJenjang as $row)
                            <div class="juara-card {{ $row['top3']->isEmpty() ? 'kosong' : '' }}">
                                <div class="juara-bidang">{{ $row['bidang'] }}</div>
                                @forelse ($row['top3'] as $item)
                                    <div class="juara-row">
                                        <span
                                            class="juara-rank juara-rank-{{ $item->peringkat }}">{{ $item->peringkat }}</span>
                                        <span class="juara-nama-compact">{{ $item->nama_madrasah }}</span>
                                    </div>
                                @empty
                                    <div class="juara-kosong-text">Belum ada data</div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada madrasah yang penilaiannya difinalisasi pada
                            periode ini.</div>
                    @endforelse
                </div>

                {{-- =====================================================================
                 5 & 6. KOMPOSISI BIDANG + KOMPOSISI JUARA
            ====================================================================== --}}
                <div class="dash-row">
                    <div class="dash-col-half">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title"><i class="bi bi-pie-chart text-primary"></i> Komposisi Bidang
                                    Prestasi</div>
                            </div>
                            <div class="chart-box-sm">
                                <canvas id="chartBidang"></canvas>
                            </div>
                            <ul class="legend-list">
                                @forelse ($komposisiBidang as $item)
                                    <li>
                                        <span class="legend-label">
                                            <span class="legend-dot" style="background: {{ $item['warna'] }}"></span>
                                            {{ $item['label'] }}
                                        </span>
                                        <span class="legend-value">{{ $item['persen'] }}%</span>
                                    </li>
                                @empty
                                    <li class="justify-content-center text-muted">Belum ada data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="dash-col-half">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title"><i class="bi bi-award text-primary"></i> Komposisi Juara</div>
                            </div>
                            <div class="chart-box-sm">
                                <canvas id="chartJuara"></canvas>
                            </div>
                            <ul class="legend-list">
                                @forelse ($komposisiJuara as $item)
                                    <li>
                                        <span class="legend-label">
                                            <span class="legend-dot" style="background: {{ $item['warna'] }}"></span>
                                            {{ $item['label'] }}
                                        </span>
                                        <span>
                                            {{ $item['jumlah'] }}
                                            <span class="legend-value ms-2">{{ $item['persen'] }}%</span>
                                        </span>
                                    </li>
                                @empty
                                    <li class="justify-content-center text-muted">Belum ada data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- =====================================================================
                 7. SEBARAN PRESTASI (CROSS-TAB BIDANG x TINGKAT)
            ====================================================================== --}}
                <div class="content-card mb-4">
                    <div class="card-title-row">
                        <div class="title"><i class="bi bi-grid-3x3 text-primary"></i> Distribusi Bidang Prestasi</div>
                    </div>
                    <div class="table-responsive">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Bidang</th>
                                    @foreach (['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'] as $tingkat)
                                        <th>{{ $tingkat }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sebaranTingkat as $row)
                                    <tr>
                                        <td>{{ $row['bidang'] }}</td>
                                        @foreach (['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'] as $tingkat)
                                            <td>{{ $row['per_tingkat'][$tingkat] }}</td>
                                        @endforeach
                                        <td class="col-total">{{ $row['total'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- =====================================================================
                 8 & 9. INDIVIDU vs BEREGU + LURING vs DARING
            ====================================================================== --}}
                <div class="dash-row">
                    <div class="dash-col-half">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title"><i class="bi bi-people text-primary"></i> Individu vs Beregu</div>
                            </div>
                            <div class="chart-box-sm">
                                <canvas id="chartKategori"></canvas>
                            </div>
                            <ul class="legend-list">
                                @forelse ($komposisiKategori as $item)
                                    <li>
                                        <span class="legend-label">{{ $item['label'] }}</span>
                                        <span class="legend-value">{{ $item['jumlah'] }} ({{ $item['persen'] }}%)</span>
                                    </li>
                                @empty
                                    <li class="justify-content-center text-muted">Belum ada data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="dash-col-half">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title"><i class="bi bi-wifi text-primary"></i> Luring vs Daring</div>
                            </div>
                            <div class="chart-box-sm">
                                <canvas id="chartMetode"></canvas>
                            </div>
                            <ul class="legend-list">
                                @forelse ($komposisiMetode as $item)
                                    <li>
                                        <span class="legend-label">{{ $item['label'] }}</span>
                                        <span class="legend-value">{{ $item['jumlah'] }} ({{ $item['persen'] }}%)</span>
                                    </li>
                                @empty
                                    <li class="justify-content-center text-muted">Belum ada data.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- =====================================================================
                 10. DISTRIBUSI KEGIATAN PER BULAN
            ====================================================================== --}}
                <div class="content-card mb-4">
                    <div class="card-title-row">
                        <div class="title"><i class="bi bi-calendar3 text-primary"></i> Distribusi Kegiatan per Bulan
                        </div>
                    </div>
                    <div class="chart-box">
                        <canvas id="chartBulan"></canvas>
                    </div>
                </div>

                {{-- =====================================================================
                 11. TOP LEMBAGA PENYELENGGARA
            ====================================================================== --}}
                <div class="content-card">
                    <div class="card-title-row">
                        <div class="title"><i class="bi bi-building text-primary"></i> Top {{ $topLembaga->count() }}
                            Lembaga Penyelenggara</div>
                    </div>
                    <div class="table-responsive">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Lembaga Penyelenggara</th>
                                    <th style="width:100px">Jumlah</th>
                                    <th style="width:100px">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topLembaga as $item)
                                    <tr>
                                        <td>{{ $item['lembaga'] }}</td>
                                        <td>{{ $item['jumlah'] }}</td>
                                        <td>{{ $item['persen'] }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Plugin kecil untuk menulis teks di tengah donut chart
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

                const totalPrestasi = {{ $totalPrestasi }};

                const donutOptionsDasar = (value, label) => ({
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    centerText: {
                        value: String(value),
                        label: label
                    }
                });

                /* ============ KOMPOSISI BIDANG (DONUT) ============ */
                const bidangLabels = @json($komposisiBidang->pluck('label'));
                const bidangJumlah = @json($komposisiBidang->pluck('jumlah'));
                const bidangWarna = @json($komposisiBidang->pluck('warna'));

                new Chart(document.getElementById('chartBidang'), {
                    type: 'doughnut',
                    data: {
                        labels: bidangLabels,
                        datasets: [{
                            data: bidangJumlah,
                            backgroundColor: bidangWarna,
                            borderWidth: 3,
                            borderColor: '#fff',
                        }]
                    },
                    options: donutOptionsDasar(totalPrestasi, 'Total Prestasi')
                });

                /* ============ KOMPOSISI JUARA (DONUT) ============ */
                const juaraLabels = @json($komposisiJuara->pluck('label'));
                const juaraJumlah = @json($komposisiJuara->pluck('jumlah'));
                const juaraWarna = @json($komposisiJuara->pluck('warna'));

                new Chart(document.getElementById('chartJuara'), {
                    type: 'doughnut',
                    data: {
                        labels: juaraLabels,
                        datasets: [{
                            data: juaraJumlah,
                            backgroundColor: juaraWarna,
                            borderWidth: 3,
                            borderColor: '#fff',
                        }]
                    },
                    options: donutOptionsDasar(totalPrestasi, 'Total Prestasi')
                });

                /* ============ INDIVIDU vs BEREGU (DONUT) ============ */
                const kategoriLabels = @json($komposisiKategori->pluck('label'));
                const kategoriJumlah = @json($komposisiKategori->pluck('jumlah'));
                const warnaKategori = ['#2563eb', '#f59e0b', '#94a3b8'];

                new Chart(document.getElementById('chartKategori'), {
                    type: 'doughnut',
                    data: {
                        labels: kategoriLabels,
                        datasets: [{
                            data: kategoriJumlah,
                            backgroundColor: warnaKategori,
                            borderWidth: 3,
                            borderColor: '#fff',
                        }]
                    },
                    options: donutOptionsDasar(totalPrestasi, 'Total Prestasi')
                });

                /* ============ LURING vs DARING (DONUT) ============ */
                const metodeLabels = @json($komposisiMetode->pluck('label'));
                const metodeJumlah = @json($komposisiMetode->pluck('jumlah'));
                const warnaMetode = ['#0f8a43', '#8b5cf6', '#94a3b8'];

                new Chart(document.getElementById('chartMetode'), {
                    type: 'doughnut',
                    data: {
                        labels: metodeLabels,
                        datasets: [{
                            data: metodeJumlah,
                            backgroundColor: warnaMetode,
                            borderWidth: 3,
                            borderColor: '#fff',
                        }]
                    },
                    options: donutOptionsDasar(totalPrestasi, 'Total Prestasi')
                });

                /* ============ DISTRIBUSI BULANAN (BAR) ============ */
                const bulanLabels = @json($distribusiBulan->pluck('label'));
                const bulanJumlah = @json($distribusiBulan->pluck('jumlah'));

                new Chart(document.getElementById('chartBulan'), {
                    type: 'bar',
                    data: {
                        labels: bulanLabels,
                        datasets: [{
                            label: 'Jumlah Kegiatan',
                            data: bulanJumlah,
                            backgroundColor: '#2563eb',
                            borderRadius: 6,
                            maxBarThickness: 32,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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

            });
        </script>
    @endpush
