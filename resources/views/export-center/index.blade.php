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
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.5rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .filter-grid .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-grid .form-select {
            border-radius: 10px;
        }

        .export-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
        }

        .export-card {
            border: 1px solid #eef0f2;
            border-radius: 16px;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            transition: box-shadow .15s, border-color .15s;
        }

        .export-card:hover {
            border-color: #bbf7d0;
            box-shadow: 0 6px 20px rgba(15, 138, 67, .08);
        }

        .export-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
        }

        .export-icon.bg-blue {
            background: #2563eb;
        }

        .export-icon.bg-green {
            background: #0f8a43;
        }

        .export-icon.bg-gold {
            background: #ca8a04;
        }

        .export-icon.bg-purple {
            background: #8b5cf6;
        }

        .export-card h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .export-card p {
            font-size: .82rem;
            color: #64748b;
            margin: 0;
            flex-grow: 1;
        }

        .export-card .btn-download {
            background: #0f8a43;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: .85rem;
            width: 100%;
        }

        .export-card .btn-download:hover {
            background: #0b6b34;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <h2>Pusat Unduh Data</h2>
            <p>Download data prestasi, hasil penilaian, peringkat, dan tren dalam satu tempat — semuanya bisa disaring
                dengan filter yang sama.</p>
        </div>

        <div class="container-fluid">

            <form method="GET" action="{{ route('export-center.export') }}" id="formExportCenter">

                {{-- FILTER --}}
                <div class="content-card">
                    <div class="filter-grid">
                        <div>
                            <label class="form-label">Periode</label>
                            <select name="periode" class="form-select">
                                <option value="">Semua Periode</option>
                                @foreach ($daftarPeriode as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Jenjang</label>
                            <select name="jenjang" class="form-select">
                                <option value="">Semua Jenjang</option>
                                @foreach ($daftarJenjang as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Bidang Prestasi</label>
                            <select name="bidang" class="form-select">
                                <option value="">Semua Bidang</option>
                                @foreach ($daftarBidang as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Kota</label>
                            <select name="kota" class="form-select">
                                <option value="">Semua Kota</option>
                                @foreach ($daftarKota as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status Madrasah</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($daftarStatus as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- PILIHAN DATA --}}
                <div class="export-grid">

                    <div class="export-card">
                        <div class="export-icon bg-blue"><i class="bi bi-file-earmark-text"></i></div>
                        <h5>Data Prestasi Mentah</h5>
                        <p>Seluruh data prestasi apa adanya — belum ada campur tangan penilaian Asesor sama sekali. Cocok
                            untuk audit data mentah.</p>
                        <button type="submit" name="tipe" value="prestasi-mentah" class="btn btn-download">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </div>

                    <div class="export-card">
                        <div class="export-icon bg-green"><i class="bi bi-clipboard-data"></i></div>
                        <h5>Data Hasil Penilaian</h5>
                        <p>Skor mentah Madrasah berdampingan dengan persentase & nilai akhir dari Asesor, plus status
                            sudah/belum dinilai.</p>
                        <button type="submit" name="tipe" value="hasil-penilaian" class="btn btn-download">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </div>

                    <div class="export-card">
                        <div class="export-icon bg-gold"><i class="bi bi-trophy"></i></div>
                        <h5>Peringkat</h5>
                        <p>Ranking madrasah berdasarkan total nilai akhir per bidang (Akademik/Non
                            Akademik/Keagamaan/GTK/Lembaga), sesuai filter di atas.</p>
                        <button type="submit" name="tipe" value="peringkat" class="btn btn-download">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </div>

                    <div class="export-card">
                        <div class="export-icon bg-purple"><i class="bi bi-graph-up"></i></div>
                        <h5>Tren</h5>
                        <p>Total & rata-rata nilai akhir dari periode ke periode — untuk melihat perkembangan lintas tahun
                            sesuai filter (kecuali Periode, karena ini menampilkan semua periode).</p>
                        <button type="submit" name="tipe" value="tren" class="btn btn-download">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </main>
@endsection
