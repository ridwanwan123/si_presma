@extends('layouts.base')

@push('styles')
    <style>
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

        .container-fluid {
            padding: 0 1rem;
        }

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-section-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .card-section-title i {
            color: #0f8a43;
        }

        /* ================= RANK CARD ================= */

        .rank-hero {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.75rem 2rem;
            border-radius: 22px;
            background: linear-gradient(135deg, #0f8a43 0%, #0c6e35 100%);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .rank-hero.rank-locked {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .rank-number {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            font-weight: 700;
        }

        .rank-hero h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: .35rem;
        }

        .rank-hero p {
            margin: 0;
            opacity: .9;
        }

        /* ================= SUMMARY ================= */

        .summary-card {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.1rem 1.2rem;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            height: 100%;
        }

        .summary-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: rgba(15, 138, 67, .12);
            color: #0f8a43;
        }

        .summary-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .summary-label {
            font-size: .78rem;
            color: #64748b;
        }

        /* ================= TABLE ================= */

        .hasil-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .hasil-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .hasil-table tbody td {
            padding: 12px 14px;
            font-size: .86rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .badge-nilai {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 700;
        }

        .badge-nilai.badge-sudah {
            background: rgba(15, 138, 67, .1);
            color: #0f8a43;
        }

        .badge-nilai.badge-belum {
            background: #f1f5f9;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Hasil Penilaian</h2>
            <p>Nilai dari asesor untuk seluruh prestasi yang telah Anda ajukan.</p>
        </div>

        <div class="container-fluid">

            {{-- FILTER PERIODE --}}
            @if ($daftarPeriodeTersedia->count() > 1)
                <div class="content-card">
                    <form method="GET" class="d-flex align-items-end gap-3 flex-wrap">
                        <div>
                            <label class="form-label mb-1" style="font-size:.8rem;font-weight:600;color:#475569;">
                                Lihat Hasil Periode
                            </label>
                            <select name="periode" class="form-select" onchange="this.form.submit()">
                                @foreach ($daftarPeriodeTersedia as $item)
                                    <option value="{{ $item }}" {{ $periodeDilihat == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            @endif

            {{-- CARD PERINGKAT --}}
            @if ($peringkat !== null)
                <div class="rank-hero">
                    <div class="rank-number">#{{ $peringkat }}</div>
                    <div>
                        <h2>Peringkat {{ $peringkat }} dari {{ $totalPesertaJenjang }} Madrasah</h2>
                        <p>Dibandingkan dengan madrasah lain di jenjang yang sama, berdasarkan total nilai akhir.</p>
                    </div>
                </div>
            @else
                <div class="rank-hero rank-locked">
                    <div class="rank-number">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <h2>Peringkat Belum Tersedia</h2>
                        <p>
                            @if ($siklus->status === \App\Models\PrestasiSiklus::OPEN)
                                Anda belum mengirim pengajuan prestasi untuk periode ini.
                            @else
                                Peringkat akan muncul setelah seluruh proses penilaian untuk periode ini selesai (status
                                saat ini: {{ $siklus->status }}).
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- SUMMARY --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ $daftarPrestasi->count() }}</div>
                            <div class="summary-label">Total Prestasi</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ $daftarPrestasi->where('sudah_dinilai', true)->count() }}</div>
                            <div class="summary-label">Sudah Dinilai</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ $daftarPrestasi->where('sudah_dinilai', false)->count() }}</div>
                            <div class="summary-label">Belum Dinilai</div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <div class="summary-value">{{ number_format($totalNilaiAkhir, 2, ',', '.') }}</div>
                            <div class="summary-label">Total Nilai Akhir</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL DETAIL --}}
            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="hasil-table">
                        <thead>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th class="text-center">Metode</th>
                                <th class="text-center">Skor Dasar</th>
                                <th class="text-center">Persentase</th>
                                <th class="text-end">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarPrestasi as $prestasi)
                                <tr>
                                    <td>{{ $prestasi->nama_kegiatan }}</td>
                                    <td>{{ $prestasi->bidang_prestasi }}</td>
                                    <td>{{ $prestasi->tingkat }}</td>
                                    <td class="text-center">{{ $prestasi->metode_pelaksanaan }}</td>
                                    <td class="text-center">{{ $prestasi->skor }}</td>
                                    <td class="text-center">
                                        @if ($prestasi->sudah_dinilai)
                                            {{ $prestasi->persentase }}%
                                        @else
                                            <span class="badge-nilai badge-belum">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($prestasi->sudah_dinilai)
                                            <span
                                                class="badge-nilai badge-sudah">{{ number_format($prestasi->nilai_akhir, 2, ',', '.') }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Belum ada data prestasi.
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
