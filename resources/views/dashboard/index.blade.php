@extends('layouts.base')

@push('styles')
    <style>
        .dashboard-header {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
        }

        .dashboard-header h2 {
            font-weight: 700;
            color: #0f172a;
        }

        .dashboard-header p {
            color: #64748b;
        }

        /* CARD */

        .dashboard-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            height: 100%;
        }

        .dashboard-card h6 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        /* FILTER */

        .filter-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1.25rem;
            margin: 0 1rem 1rem;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 12px;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #0f8a43;
            box-shadow: 0 0 0 .15rem rgba(15, 138, 67, .15);
        }

        /* KPI */

        .kpi-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            height: 100%;
        }

        .kpi-title {
            font-size: .9rem;
            color: #64748b;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .kpi-growth {
            color: #0f8a43;
            font-size: .85rem;
        }

        /* TABLE */

        .table-modern thead th {
            background: #f8fafc;
            color: #475569;
        }

        /* INSIGHT */

        .insight-card {
            background: linear-gradient(135deg, #0f8a43, #16a34a);
            color: white;
            border-radius: 18px;
            padding: 1.5rem;
        }

        .insight-item {
            margin-bottom: 1rem;
        }

        #map {
            height: 350px;
            border-radius: 12px;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="dashboard-header">
            <h2>Dashboard Prestasi Madrasah DKI Jakarta</h2>
            <p>Executive Dashboard Jakarta Madrasah Award (JMA)</p>
        </div>

        {{-- FILTER --}}
        <div class="filter-card">

            <div class="row g-3">

                <div class="col-lg-3">
                    <select class="form-select">
                        <option>Semua Jenjang</option>
                        <option>MI</option>
                        <option>MTs</option>
                        <option>MA</option>
                    </select>
                </div>

                <div class="col-lg-3">
                    <select class="form-select">
                        <option>Semua Madrasah</option>
                    </select>
                </div>

                <div class="col-lg-3">
                    <select class="form-select">
                        <option>2026</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>

                <div class="col-lg-3">
                    <select class="form-select">
                        <option>Semua Kota</option>
                        <option>Jakarta Selatan</option>
                        <option>Jakarta Timur</option>
                        <option>Jakarta Barat</option>
                    </select>
                </div>

            </div>

        </div>


        {{-- KPI --}}
        <div class="row g-3 px-3 mb-4">

            <div class="col-lg-3">
                <div class="kpi-card">
                    <div class="kpi-title">Total Prestasi</div>
                    <div class="kpi-value">3.282</div>
                    <div class="kpi-growth">▲ 12%</div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="kpi-card">
                    <div class="kpi-title">Madrasah Aktif</div>
                    <div class="kpi-value">742</div>
                    <div class="kpi-growth">Aktif</div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="kpi-card">
                    <div class="kpi-title">Prestasi Nasional+</div>
                    <div class="kpi-value">437</div>
                    <div class="kpi-growth">Nasional & Internasional</div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="kpi-card">
                    <div class="kpi-title">Growth</div>
                    <div class="kpi-value">+12%</div>
                    <div class="kpi-growth">vs Tahun Lalu</div>
                </div>
            </div>

        </div>


        {{-- TREND + TOP PENYELENGGARA --}}
        <div class="row g-3 px-3 mb-4">

            <div class="col-lg-8">
                <div class="dashboard-card">
                    <h6>Trend Prestasi 3 Tahun</h6>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="col-lg-4">

                <div class="dashboard-card">

                    <h6>Top Penyelenggara</h6>

                    <table class="table table-modern">

                        <tbody>

                            <tr>
                                <td>Kemenag RI</td>
                                <td>145</td>
                            </tr>

                            <tr>
                                <td>Puspresnas</td>
                                <td>124</td>
                            </tr>

                            <tr>
                                <td>Kemdikbud</td>
                                <td>98</td>
                            </tr>

                            <tr>
                                <td>BRIN</td>
                                <td>73</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- COMPARISON --}}
        <div class="row px-3 mb-4">

            <div class="col-12">

                <div class="dashboard-card">

                    <h6>Comparison Bidang Prestasi</h6>

                    <canvas id="kategoriChart"></canvas>

                </div>

            </div>

        </div>


        {{-- MAP --}}
        <div class="row g-3 px-3 mb-4">

            <div class="col-lg-7">

                <div class="dashboard-card">

                    <h6>Sebaran Madrasah</h6>

                    <div id="map"></div>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="dashboard-card">

                    <h6>Distribusi Wilayah</h6>

                    <canvas id="wilayahChart"></canvas>

                </div>

            </div>

        </div>


        {{-- INSIGHT --}}
        <div class="row px-3">

            <div class="col-12">

                <div class="insight-card">

                    <h5 class="mb-4">Insight Dashboard</h5>

                    <div class="insight-item">
                        📈 Jumlah prestasi meningkat 12% dibanding tahun 2025.
                    </div>

                    <div class="insight-item">
                        🏆 MAN 4 Jakarta menjadi madrasah dengan prestasi terbanyak.
                    </div>

                    <div class="insight-item">
                        ⭐ Prestasi Akademik mendominasi dengan kontribusi 38%.
                    </div>

                    <div class="insight-item">
                        🌍 Prestasi tingkat nasional dan internasional terus meningkat.
                    </div>

                </div>

            </div>

        </div>

    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: ['2024', '2025', '2026'],
                datasets: [{
                    label: 'Prestasi',
                    data: [1730, 2110, 3282],
                    borderColor: '#0f8a43',
                    tension: .4
                }]
            }
        });


        new Chart(document.getElementById('kategoriChart'), {

            type: 'line',

            data: {

                labels: ['2024', '2025', '2026'],

                datasets: [{
                        label: 'Akademik',
                        data: [550, 730, 950],
                        borderColor: '#3b82f6'
                    },
                    {
                        label: 'Non Akademik',
                        data: [430, 620, 810],
                        borderColor: '#f59e0b'
                    },
                    {
                        label: 'Keagamaan',
                        data: [300, 380, 510],
                        borderColor: '#22c55e'
                    },
                    {
                        label: 'GTK',
                        data: [170, 230, 320],
                        borderColor: '#ef4444'
                    },
                    {
                        label: 'Lembaga',
                        data: [80, 110, 160],
                        borderColor: '#8b5cf6'
                    }
                ]
            }

        });


        new Chart(document.getElementById('wilayahChart'), {

            type: 'bar',

            data: {

                labels: [
                    'Jaksel',
                    'Jaktim',
                    'Jakbar',
                    'Jakpus',
                    'Jakut'
                ],

                datasets: [{
                    data: [742, 635, 510, 401, 367],
                    backgroundColor: '#0f8a43'
                }]
            }

        });
    </script>
@endpush
