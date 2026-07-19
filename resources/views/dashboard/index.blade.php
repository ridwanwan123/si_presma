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

        .section-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 2rem 0 1.25rem;
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

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.25rem 1.4rem;
        }

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

        .dash-col-6 {
            flex: 1 1 48%;
            min-width: 300px;
        }

        .dash-col-5 {
            flex: 1 1 34%;
            min-width: 280px;
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

        .stat-icon.bg-blue {
            background: #2563eb;
        }

        .stat-icon.bg-green {
            background: #16a34a;
        }

        .stat-icon.bg-amber {
            background: #f59e0b;
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

        .btn-export-mini {
            font-size: .74rem;
            font-weight: 700;
            padding: .3rem .7rem;
            border-radius: 8px;
        }

        .chart-box {
            position: relative;
            height: 240px;
        }

        .chart-box-sm {
            position: relative;
            height: 200px;
        }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: .8rem 1rem;
            color: #1e40af;
            font-size: .82rem;
            margin-bottom: 1.25rem;
        }

        .empty-note {
            text-align: center;
            color: #94a3b8;
            padding: 2rem 1rem;
            font-size: .88rem;
        }

        .mini-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .84rem;
        }

        .mini-table thead th {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 9px 10px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
        }

        .mini-table thead th:first-child {
            text-align: left;
        }

        .mini-table tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            text-align: center;
        }

        .mini-table tbody td:first-child {
            text-align: left;
            font-weight: 600;
        }

        .mini-table tbody tr.total-row td {
            font-weight: 800;
            color: #0f172a;
            background: #f8fafc;
        }

        .selisih-naik {
            color: #16a34a;
            font-weight: 700;
        }

        .selisih-turun {
            color: #dc2626;
            font-weight: 700;
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
            min-width: 260px;
        }

        .profil-placeholder {
            text-align: center;
            color: #94a3b8;
            padding: 2.5rem 1rem;
        }

        .rank-pill {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            background: #eef2f7;
            color: #475569;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid pt-3">

            <div class="dash-header">
                <div>
                    <h2>Dashboard PRESMA</h2>
                    <p>Ringkasan prestasi madrasah untuk pelaporan, dan analisis perkembangan lintas tahun.</p>
                </div>
            </div>

            {{-- FILTER GLOBAL — berlaku ke SELURUH dashboard (Bagian 1 & 2) --}}
            <div class="content-card mb-4">
                <form method="GET" class="filter-form">
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

                    @if ($jenjangFilter || $statusFilter || $kotaFilter)
                        <a href="{{ route('dashboard', ['jenjang' => '']) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- =========================================================================
             BAGIAN 1: RINGKASAN GLOBAL (untuk laporan ke Kabid/Kakanwil)
        ========================================================================== --}}

            <div class="section-divider">
                <span class="label">RINGKASAN PERIODE {{ $ringkasanPeriode['periode_aktif'] }}</span>
                <span class="line"></span>
            </div>

            <div class="stat-row">
                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-blue"><i class="bi bi-trophy"></i></div>
                        <div>
                            <div class="stat-label">Total Prestasi Periode Ini</div>
                            <div class="stat-value">{{ number_format($ringkasanPeriode['total_prestasi'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-green"><i class="bi bi-building-check"></i></div>
                        <div>
                            <div class="stat-label">Madrasah Aktif</div>
                            <div class="stat-value">{{ $ringkasanPeriode['madrasah_aktif'] }} <span
                                    class="fs-6 text-muted fw-normal">/
                                    {{ $ringkasanPeriode['total_madrasah_terdaftar'] }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-purple"><i class="bi bi-patch-check"></i></div>
                        <div>
                            <div class="stat-label">Madrasah Selesai Dinilai</div>
                            <div class="stat-value">{{ $ringkasanPeriode['madrasah_finished'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="stat-col">
                    <div class="content-card stat-card mb-0">
                        <div class="stat-icon bg-amber"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <div class="stat-label">Peningkatan Prestasi</div>
                            <div class="stat-value">
                                @if ($persenPeningkatan)
                                    {{ $persenPeningkatan['persen_total'] >= 0 ? '+' : '' }}{{ $persenPeningkatan['persen_total'] }}%
                                @else
                                    <span class="text-muted fs-6 fw-normal">Butuh 2 periode</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-row">
                <div class="dash-col-5">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-bar-chart text-primary"></i> Persentase Peningkatan per
                                Tingkat</div>
                        </div>

                        @if ($persenPeningkatan)
                            <div class="mb-2 text-muted" style="font-size:.78rem">
                                Dibanding periode {{ $persenPeningkatan['periode_sebelumnya'] }} →
                                {{ $persenPeningkatan['periode_sekarang'] }}
                            </div>
                            <div class="chart-box-sm">
                                <canvas id="chartPeningkatan"></canvas>
                            </div>
                        @else
                            <div class="empty-note">
                                <i class="bi bi-info-circle d-block mb-2" style="font-size:1.5rem"></i>
                                Perbandingan persentase butuh data minimal 2 periode. Baru ada
                                {{ $matrixTingkat['periodeList']->count() }} periode saat ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="dash-col-7">
                    <div class="content-card h-100">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-table text-primary"></i> Perbandingan Prestasi per Tingkat,
                                Tahun ke Tahun</div>
                            <a href="{{ route('dashboard.export', ['tipe' => 'perbandingan-tingkat', 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                class="btn btn-outline-success btn-export-mini">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th>Jenjang</th>
                                        @foreach ($matrixTingkat['periodeList'] as $periode)
                                            <th>{{ $periode }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matrixTingkat['matrix'] as $row)
                                        <tr>
                                            <td>{{ $row['tingkat'] }}</td>
                                            @foreach ($matrixTingkat['periodeList'] as $periode)
                                                <td>{{ number_format($row['per_tahun'][$periode], 0, ',', '.') }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td>TOTAL</td>
                                        @foreach ($matrixTingkat['periodeList'] as $periode)
                                            <td>{{ number_format($matrixTingkat['totalPerTahun'][$periode], 0, ',', '.') }}
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
             BAGIAN 2: PERKEMBANGAN MADRASAH (LINTAS TAHUN, DATA ARSIP)
        ========================================================================== --}}

            <div class="section-divider">
                <span class="label">PERKEMBANGAN MADRASAH (LINTAS TAHUN — DATA ARSIP)</span>
                <span class="line"></span>
            </div>

            @if ($daftarArsip->isEmpty())
                <div class="content-card empty-note">
                    <i class="bi bi-archive d-block mb-2" style="font-size:1.8rem;color:#cbd5e1"></i>
                    Belum ada data arsip. Bagian ini menampilkan perbandingan lintas tahun, jadi butuh minimal 1 periode
                    yang sudah diarsipkan dulu di halaman Hasil &amp; Ranking.
                    <div class="mt-2">
                        <a href="{{ route('ranking.index') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-trophy"></i> Buka Hasil &amp; Ranking
                        </a>
                    </div>
                </div>
            @else
                <div class="dash-row">
                    <div class="dash-col-6">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title">
                                    <i class="bi bi-graph-up-arrow text-primary"></i>
                                    Tren Total Prestasi Sistem
                                </div>
                                <a href="{{ route('dashboard.export', ['tipe' => 'tren-sistem', 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                    class="btn btn-outline-success btn-export-mini">
                                    <i class="bi bi-file-earmark-excel"></i> Export
                                </a>
                            </div>
                            <div class="chart-box">
                                <canvas id="chartTrenSistem"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="dash-col-6">
                        <div class="content-card h-100">
                            <div class="card-title-row">
                                <div class="title">
                                    <i class="bi bi-bar-chart-steps text-primary"></i>
                                    Rata-rata Nilai per Jenjang
                                </div>
                                <a href="{{ route('dashboard.export', ['tipe' => 'rata-jenjang', 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                    class="btn btn-outline-success btn-export-mini">
                                    <i class="bi bi-file-earmark-excel"></i> Export
                                </a>
                            </div>
                            <div class="chart-box">
                                <canvas id="chartRataJenjang"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                @if (is_null($periodePembanding))
                    <div class="content-card mb-4 empty-note">
                        <i class="bi bi-info-circle"></i>
                        Perbandingan kenaikan/penurunan butuh minimal 2 periode yang sudah diarsipkan. Baru ada
                        {{ $daftarArsip->count() }} arsip saat ini.
                    </div>
                @else
                    <div class="dash-row">
                        <div class="dash-col-7" style="flex-basis:49%">
                            <div class="content-card h-100">
                                <div class="card-title-row">
                                    <div class="title"><i class="bi bi-arrow-up-circle text-success"></i> Kenaikan
                                        Terbesar ({{ $periodePembanding['sebelumnya'] }} →
                                        {{ $periodePembanding['sekarang'] }})</div>
                                    <a href="{{ route('dashboard.export', ['tipe' => 'kenaikan', 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                        class="btn btn-outline-success btn-export-mini">
                                        <i class="bi bi-file-earmark-excel"></i> Export
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left">Madrasah</th>
                                                <th>Selisih</th>
                                                <th>Peringkat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($kenaikanTerbesar as $item)
                                                <tr>
                                                    <td>{{ $item->nama_madrasah }}</td>
                                                    <td class="selisih-naik">
                                                        +{{ number_format($item->selisih, 2, ',', '.') }}</td>
                                                    <td><span class="rank-pill">#{{ $item->peringkat_sebelumnya }} →
                                                            #{{ $item->peringkat_sekarang }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Tidak ada data.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="dash-col-5" style="flex-basis:49%">
                            <div class="content-card h-100">
                                <div class="card-title-row">
                                    <div class="title"><i class="bi bi-arrow-down-circle text-danger"></i> Penurunan
                                        Terbesar</div>
                                    <a href="{{ route('dashboard.export', ['tipe' => 'penurunan', 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                        class="btn btn-outline-success btn-export-mini">
                                        <i class="bi bi-file-earmark-excel"></i> Export
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left">Madrasah</th>
                                                <th>Selisih</th>
                                                <th>Peringkat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($penurunanTerbesar as $item)
                                                <tr>
                                                    <td>{{ $item->nama_madrasah }}</td>
                                                    <td class="selisih-turun">
                                                        {{ number_format($item->selisih, 2, ',', '.') }}</td>
                                                    <td><span class="rank-pill">#{{ $item->peringkat_sebelumnya }} →
                                                            #{{ $item->peringkat_sekarang }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">Tidak ada data.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="content-card mb-4">
                    <div class="card-title-row">
                        <div class="title"><i class="bi bi-building text-primary"></i> Profil Perkembangan Madrasah</div>
                    </div>

                    <form method="GET" class="filter-form mb-2">
                        <input type="hidden" name="jenjang" value="{{ $jenjangFilter }}">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <input type="hidden" name="kota" value="{{ $kotaFilter }}">
                        <div>
                            <label class="form-label">Pilih Madrasah</label>
                            <select name="madrasah_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Pilih Madrasah --</option>
                                @foreach ($daftarMadrasah as $m)
                                    <option value="{{ $m->madrasah_id }}"
                                        {{ $madrasahIdFilter == $m->madrasah_id ? 'selected' : '' }}>
                                        {{ $m->nama_madrasah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                @if ($madrasahIdFilter && $profilMadrasah)
                    <div class="dash-row">
                        <div class="dash-col-7">
                            <div class="content-card h-100">
                                <div class="card-title-row">
                                    <div class="title"><i class="bi bi-graph-up text-primary"></i> Tren Total Nilai Akhir
                                        — {{ $profilMadrasah->nama_madrasah }}</div>
                                </div>
                                <div class="chart-box">
                                    <canvas id="chartTrenMadrasah"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="dash-col-5">
                            <div class="content-card h-100">
                                <div class="card-title-row">
                                    <div class="title"><i class="bi bi-diagram-3 text-primary"></i> Tren per Bidang</div>
                                </div>
                                <div class="chart-box">
                                    <canvas id="chartBidangMadrasah"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-title-row">
                            <div class="title"><i class="bi bi-table text-primary"></i> Histori Lengkap</div>
                            <a href="{{ route('dashboard.export', ['tipe' => 'profil-madrasah', 'madrasah_id' => $madrasahIdFilter, 'jenjang' => $jenjangFilter, 'status' => $statusFilter, 'kota' => $kotaFilter]) }}"
                                class="btn btn-outline-success btn-export-mini">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th style="text-align:left">Periode</th>
                                        <th>Akademik</th>
                                        <th>Non Akademik</th>
                                        <th>Keagamaan</th>
                                        <th>GTK</th>
                                        <th>Lembaga</th>
                                        <th>Total Akhir</th>
                                        <th>Peringkat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($profilMadrasah->histori as $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $row->periode }}</td>
                                            <td>{{ number_format($row->nilai_akademik, 2, ',', '.') }} <span
                                                    class="rank-pill">#{{ $row->peringkat_per_bidang['Akademik'] ?? '-' }}</span>
                                            </td>
                                            <td>{{ number_format($row->nilai_non_akademik, 2, ',', '.') }} <span
                                                    class="rank-pill">#{{ $row->peringkat_per_bidang['Non Akademik'] ?? '-' }}</span>
                                            </td>
                                            <td>{{ number_format($row->nilai_keagamaan, 2, ',', '.') }} <span
                                                    class="rank-pill">#{{ $row->peringkat_per_bidang['Keagamaan'] ?? '-' }}</span>
                                            </td>
                                            <td>{{ number_format($row->nilai_gtk, 2, ',', '.') }} <span
                                                    class="rank-pill">#{{ $row->peringkat_per_bidang['GTK'] ?? '-' }}</span>
                                            </td>
                                            <td>{{ number_format($row->nilai_lembaga, 2, ',', '.') }} <span
                                                    class="rank-pill">#{{ $row->peringkat_per_bidang['Lembaga'] ?? '-' }}</span>
                                            </td>
                                            <td class="fw-bold text-success">
                                                {{ number_format($row->total_nilai_akhir, 2, ',', '.') }}</td>
                                            <td><span class="rank-pill">#{{ $row->peringkat_keseluruhan }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif ($madrasahIdFilter && !$profilMadrasah)
                    <div class="content-card profil-placeholder">
                        Madrasah ini belum pernah tercatat FINISHED di arsip manapun.
                    </div>
                @else
                    <div class="content-card profil-placeholder">
                        <i class="bi bi-building"
                            style="font-size:2rem;color:#cbd5e1;display:block;margin-bottom:.5rem;"></i>
                        Pilih madrasah di atas untuk melihat profil perkembangannya per tahun.
                    </div>
                @endif

            @endif

        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const warnaJenjang = {
                'RA': '#ef4444', // Merah
                'MI': '#eab308', // Kuning
                'MTs': '#16a34a', // Hijau
                'MA': '#2563eb', // Biru
            };
            const warnaJenjangDefault = '#94a3b8'; // fallback abu-abu kalau ada jenjang di luar 4 ini
            const warnaBidang = {
                'Akademik': '#2563eb',
                'Non Akademik': '#38bdf8',
                'Keagamaan': '#f59e0b',
                'GTK': '#8b5cf6',
                'Lembaga': '#94a3b8',
            };

            @if ($persenPeningkatan)
                /* ============ PERSENTASE PENINGKATAN PER TINGKAT ============ */
                const perTingkat = @json($persenPeningkatan['per_tingkat']);

                new Chart(document.getElementById('chartPeningkatan'), {
                    type: 'bar',
                    data: {
                        labels: perTingkat.map(r => r.tingkat),
                        datasets: [{
                            label: '% Perubahan',
                            data: perTingkat.map(r => r.persen),
                            backgroundColor: perTingkat.map(r => r.persen >= 0 ? '#16a34a' :
                                '#dc2626'),
                            borderRadius: 6,
                            maxBarThickness: 40,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    callback: v => v + '%'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            @endif

            @if ($daftarArsip->isNotEmpty())
                const labelPeriode = @json($daftarArsip->pluck('periode'));

                /* ============ TREN TOTAL PRESTASI SISTEM — PER JENJANG ============ */
                const trenPerJenjang = @json($trenSistem['per_jenjang']);

                const datasetsTrenSistem = trenPerJenjang.map((row) => ({
                    label: row.jenjang,
                    data: labelPeriode.map(p => row.per_tahun[p] ?? 0),
                    borderColor: warnaJenjang[row.jenjang] || warnaJenjangDefault,
                    backgroundColor: 'transparent',
                    borderWidth: 3,
                    tension: .35,
                    pointRadius: 4,
                }));

                new Chart(document.getElementById('chartTrenSistem'), {
                    type: 'line',
                    data: {
                        labels: labelPeriode,
                        datasets: datasetsTrenSistem
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
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

                /* ============ RATA-RATA PER JENJANG ============ */
                const rataJenjang = @json($rataJenjang);

                const datasetsJenjang = rataJenjang.map((row) => ({
                    label: row.jenjang,
                    data: labelPeriode.map(p => row.per_tahun[p] ?? 0),
                    borderColor: warnaJenjang[row.jenjang] || warnaJenjangDefault,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: .3,
                    pointRadius: 3,
                }));

                new Chart(document.getElementById('chartRataJenjang'), {
                    type: 'line',
                    data: {
                        labels: labelPeriode,
                        datasets: datasetsJenjang
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
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

                @if ($madrasahIdFilter && $profilMadrasah)
                    const historiMadrasah = @json($profilMadrasah->histori);
                    const labelHistori = historiMadrasah.map(h => h.periode);

                    new Chart(document.getElementById('chartTrenMadrasah'), {
                        type: 'line',
                        data: {
                            labels: labelHistori,
                            datasets: [{
                                label: 'Total Nilai Akhir',
                                data: historiMadrasah.map(h => h.total_nilai_akhir),
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37,99,235,.12)',
                                borderWidth: 3,
                                fill: true,
                                tension: .35,
                                pointRadius: 4,
                                pointBackgroundColor: '#2563eb',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
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

                    const kolomBidang = {
                        'Akademik': 'nilai_akademik',
                        'Non Akademik': 'nilai_non_akademik',
                        'Keagamaan': 'nilai_keagamaan',
                        'GTK': 'nilai_gtk',
                        'Lembaga': 'nilai_lembaga',
                    };

                    const datasetsBidangMadrasah = Object.entries(kolomBidang).map(([label, kolom]) => ({
                        label: label,
                        data: historiMadrasah.map(h => h[kolom]),
                        borderColor: warnaBidang[label],
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: .3,
                        pointRadius: 3,
                    }));

                    new Chart(document.getElementById('chartBidangMadrasah'), {
                        type: 'line',
                        data: {
                            labels: labelHistori,
                            datasets: datasetsBidangMadrasah
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
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
                @endif
            @endif

        });
    </script>
@endpush
