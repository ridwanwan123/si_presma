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

        .periode-select-wrap {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: .5rem .9rem;
        }

        .periode-select-wrap label {
            font-size: .82rem;
            font-weight: 600;
            color: #64748b;
            margin: 0;
        }

        .periode-select-wrap select {
            border: none;
            font-weight: 700;
            color: #0f172a;
            background: transparent;
            outline: none;
        }

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.1rem 1.25rem;
        }

        /* ============ DASHBOARD ROW/COL (gap sendiri, tidak gantung ke gutter Bootstrap) ============ */

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

        .dash-col-half {
            flex: 1 1 49%;
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

        .stat-icon.bg-cyan {
            background: #0ea5e9;
        }

        .stat-icon.bg-amber {
            background: #f59e0b;
        }

        .stat-icon.bg-purple {
            background: #8b5cf6;
        }

        .stat-icon.bg-gold {
            background: #ca8a04;
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

        /* ============ TABLES ============ */

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
            text-align: center;
        }

        .dash-table thead th:first-child,
        .dash-table thead th:nth-child(2) {
            text-align: left;
        }

        .dash-table tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            color: #334155;
        }

        .dash-table tbody td:first-child,
        .dash-table tbody td:nth-child(2) {
            text-align: left;
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

        /* ============ TOP KEGIATAN BAR ============ */

        .kegiatan-bar-track {
            width: 100%;
            height: 8px;
            border-radius: 6px;
            background: #eef2f7;
            overflow: hidden;
        }

        .kegiatan-bar-fill {
            height: 100%;
            border-radius: 6px;
            background: linear-gradient(90deg, #60a5fa, #2563eb);
        }

        .kegiatan-jumlah {
            font-weight: 700;
            color: #0f172a;
            min-width: 24px;
            display: inline-block;
        }

        /* ============ BENCHMARK ============ */

        .benchmark-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .benchmark-angka {
            display: flex;
            align-items: baseline;
            gap: .5rem;
        }

        .benchmark-angka .saya {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
        }

        .benchmark-angka .vs {
            font-size: .85rem;
            color: #94a3b8;
        }

        .benchmark-angka .rata {
            font-size: 1.1rem;
            font-weight: 700;
            color: #64748b;
        }

        .benchmark-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .85rem;
        }

        .benchmark-badge.naik {
            background: rgba(22, 163, 74, .1);
            color: #16a34a;
        }

        .benchmark-badge.turun {
            background: rgba(220, 38, 38, .1);
            color: #dc2626;
        }

        .benchmark-badge.sama {
            background: #f1f5f9;
            color: #64748b;
        }

        .benchmark-sub {
            font-size: .8rem;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid pt-3">

            {{-- HEADER --}}
            <div class="dash-header">
                <div>
                    <h2>Dashboard Madrasah</h2>
                    <p>Analisis perkembangan prestasi madrasah berdasarkan periode.</p>
                </div>

                <form method="GET" class="periode-select-wrap">
                    <label for="periode"><i class="bi bi-calendar3"></i> Periode</label>
                    <select name="periode" id="periode" onchange="this.form.submit()">
                        @foreach ($daftarPeriode as $item)
                            <option value="{{ $item }}" {{ $periodeDipilih == $item ? 'selected' : '' }}>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- STAT CARDS --}}
            <div class="dash-stat-row">
                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-blue"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Total Prestasi</div>
                            <div class="stat-value">{{ $totalPrestasi }}</div>
                            <div class="stat-underline bg-blue"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-cyan"><i class="bi bi-journal-bookmark"></i></div>
                        <div>
                            <div class="stat-label">Bidang Prestasi</div>
                            <div class="stat-value">{{ $bidangDipakai }}</div>
                            <div class="stat-underline bg-cyan"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-amber"><i class="bi bi-bullseye"></i></div>
                        <div>
                            <div class="stat-label">Tingkat Lomba</div>
                            <div class="stat-value">{{ $tingkatDipakai }}</div>
                            <div class="stat-underline bg-amber"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-purple"><i class="bi bi-award"></i></div>
                        <div>
                            <div class="stat-label">Jenis Kegiatan</div>
                            <div class="stat-value">{{ $jenisKegiatan }}</div>
                            <div class="stat-underline bg-purple"></div>
                        </div>
                    </div>
                </div>

                <div class="dash-stat-col">
                    <div class="content-card stat-card">
                        <div class="stat-icon bg-gold"><i class="bi bi-award-fill"></i></div>
                        <div>
                            <div class="stat-label">Total Juara 1</div>
                            <div class="stat-value">{{ $totalJuara1 }}</div>
                            <div class="stat-underline bg-gold"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BARU: BENCHMARK SESAMA JENJANG --}}
            @if ($benchmarkJenjang)
                <div class="content-card mb-4">
                    <div class="card-title-row mb-2">
                        <div class="title"><i class="bi bi-bar-chart-line text-primary"></i> Benchmark Partisipasi —
                            Jenjang {{ $benchmarkJenjang['jenjang'] }}</div>
                    </div>
                    <div class="benchmark-card">
                        <div class="benchmark-angka">
                            <span class="saya">{{ $benchmarkJenjang['total_saya'] }}</span>
                            <span class="vs">prestasi Anda &nbsp;vs&nbsp;</span>
                            <span class="rata">{{ $benchmarkJenjang['rata_rata_sejenjang'] }}</span>
                            <span class="vs">rata-rata</span>
                        </div>

                        @if ($benchmarkJenjang['selisih'] > 0)
                            <span class="benchmark-badge naik">
                                <i class="bi bi-arrow-up-circle"></i> +{{ $benchmarkJenjang['selisih'] }} di atas rata-rata
                            </span>
                        @elseif ($benchmarkJenjang['selisih'] < 0)
                            <span class="benchmark-badge turun">
                                <i class="bi bi-arrow-down-circle"></i> {{ $benchmarkJenjang['selisih'] }} di bawah
                                rata-rata
                            </span>
                        @else
                            <span class="benchmark-badge sama">
                                <i class="bi bi-dash-circle"></i> Setara rata-rata
                            </span>
                        @endif

                        <span class="benchmark-sub">Dibandingkan dengan
                            {{ $benchmarkJenjang['jumlah_madrasah_sejenjang'] }} madrasah jenjang
                            {{ $benchmarkJenjang['jenjang'] }} lainnya (berdasarkan jumlah prestasi).</span>
                    </div>
                </div>
            @endif

            {{-- TREN + KOMPOSISI BIDANG --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-bar-chart text-primary"></i> Tren Total Prestasi</div>
                        </div>
                        <div class="chart-box">
                            <canvas id="chartTren"></canvas>
                        </div>
                    </div>
                </div>

                <div class="dash-col-5">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-pie-chart text-primary"></i> Komposisi Bidang Prestasi</div>
                        </div>
                        <div class="chart-box-sm">
                            <canvas id="chartBidang"></canvas>
                        </div>
                        <ul class="legend-list">
                            @foreach ($komposisiBidang as $item)
                                <li>
                                    <span class="legend-label">
                                        <span class="legend-dot" style="background: {{ $item['warna'] }}"></span>
                                        {{ $item['label'] }}
                                    </span>
                                    <span class="legend-value">{{ $item['persen'] }}%</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- TINGKAT + KOMPOSISI JUARA --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-bar-chart text-primary"></i> Prestasi Berdasarkan Tingkat
                            </div>
                        </div>
                        <div class="chart-box">
                            <canvas id="chartTingkat"></canvas>
                        </div>
                    </div>
                </div>

                <div class="dash-col-5">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-award text-primary"></i> Komposisi Juara</div>
                        </div>
                        <div class="chart-box-sm">
                            <canvas id="chartJuara"></canvas>
                        </div>
                        <ul class="legend-list">
                            @foreach ($komposisiJuara as $item)
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
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- BARU: CROSS-TAB BIDANG x TINGKAT --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-grid-3x3 text-primary"></i> Sebaran Bidang berdasarkan Tingkat
                        (Periode {{ $periodeDipilih }})</div>
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
                            @forelse ($crosstabBidangTingkat as $row)
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

            {{-- BARU: INDIVIDU/BEREGU + LURING/DARING --}}
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
                            @foreach ($komposisiKategori as $item)
                                <li>
                                    <span class="legend-label">{{ $item['label'] }}</span>
                                    <span class="legend-value">{{ $item['jumlah'] }} ({{ $item['persen'] }}%)</span>
                                </li>
                            @endforeach
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
                            @foreach ($komposisiMetode as $item)
                                <li>
                                    <span class="legend-label">{{ $item['label'] }}</span>
                                    <span class="legend-value">{{ $item['jumlah'] }} ({{ $item['persen'] }}%)</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- BARU: DISTRIBUSI BULANAN --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-calendar3 text-primary"></i> Distribusi Kegiatan per Bulan
                        (Periode {{ $periodeDipilih }})</div>
                </div>
                <div class="chart-box">
                    <canvas id="chartBulan"></canvas>
                </div>
            </div>

            {{-- RINGKASAN TABEL + INSIGHT --}}
            <div class="dash-row">
                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-table text-primary"></i> Ringkasan Prestasi per Bidang
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Bidang</th>
                                        @foreach ($tahunRentang as $tahun)
                                            <th>{{ $tahun }}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ringkasanBidang as $row)
                                        <tr>
                                            <td>{{ $row['bidang'] }}</td>
                                            @foreach ($tahunRentang as $tahun)
                                                <td>{{ $row['per_tahun'][$tahun] }}</td>
                                            @endforeach
                                            <td class="col-total">{{ $row['total'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td>Total</td>
                                        @foreach ($tahunRentang as $tahun)
                                            <td>{{ $totalPerTahun[$tahun] }}</td>
                                        @endforeach
                                        <td class="col-total">{{ $totalKeseluruhan }}</td>
                                    </tr>
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
                                <div>Belum cukup data untuk menampilkan insight periode ini.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- BARU: TOP 5 LEMBAGA PENYELENGGARA --}}
            <div class="content-card mb-4">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-building text-primary"></i> Top 5 Lembaga Penyelenggara</div>
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

            {{-- TOP 10 KEGIATAN --}}
            <div class="content-card">
                <div class="card-title-row">
                    <div class="title"><i class="bi bi-list-ol text-primary"></i> Top 10 Kegiatan Penyumbang Prestasi
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th style="width:40px">No</th>
                                <th>Nama Kegiatan</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th style="width:220px">Jumlah Prestasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topKegiatan as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td style="text-align:left">{{ $item['nama'] }}</td>
                                    <td style="text-align:left">{{ $item['bidang'] }}</td>
                                    <td style="text-align:left">{{ $item['tingkat'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="kegiatan-bar-track flex-grow-1">
                                                <div class="kegiatan-bar-fill"
                                                    style="width: {{ round(($item['jumlah'] / $maxJumlahKegiatan) * 100) }}%">
                                                </div>
                                            </div>
                                            <span class="kegiatan-jumlah">{{ $item['jumlah'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data prestasi pada
                                        periode ini.</td>
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

            const tahunRentang = @json($tahunRentang);
            const trenTotalPrestasi = @json($trenTotalPrestasi->values());

            /* ============ TREN TOTAL PRESTASI ============ */
            new Chart(document.getElementById('chartTren'), {
                type: 'line',
                data: {
                    labels: tahunRentang,
                    datasets: [{
                        label: 'Total Prestasi',
                        data: trenTotalPrestasi,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, .12)',
                        borderWidth: 3,
                        fill: true,
                        tension: .35,
                        pointRadius: 5,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 6,
                            right: 6,
                            bottom: 0,
                            left: 0
                        }
                    },
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
                        value: '{{ $totalPrestasi }}',
                        label: 'Total Prestasi'
                    }
                }
            });

            /* ============ PRESTASI BERDASARKAN TINGKAT (GROUPED BAR) ============ */
            const tingkatLabels = ['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'];
            const tingkatPerTahun = @json($tingkatPerTahun);
            const warnaTahun = ['#1d4ed8', '#38bdf8', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'];

            const datasetsTingkat = tahunRentang.map((tahun, idx) => ({
                label: String(tahun),
                data: tingkatPerTahun[tahun],
                backgroundColor: warnaTahun[idx % warnaTahun.length],
                borderRadius: 4,
                maxBarThickness: 28,
            }));

            new Chart(document.getElementById('chartTingkat'), {
                type: 'bar',
                data: {
                    labels: tingkatLabels,
                    datasets: datasetsTingkat
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
                        value: '{{ $totalPrestasi }}',
                        label: 'Total Prestasi'
                    }
                }
            });

            /* ============ BARU: INDIVIDU vs BEREGU (DONUT) ============ */
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
                        value: '{{ $totalPrestasi }}',
                        label: 'Total Prestasi'
                    }
                }
            });

            /* ============ BARU: LURING vs DARING (DONUT) ============ */
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
                        value: '{{ $totalPrestasi }}',
                        label: 'Total Prestasi'
                    }
                }
            });

            /* ============ BARU: DISTRIBUSI BULANAN (BAR) ============ */
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
