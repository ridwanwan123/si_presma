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
            padding: 1.4rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-section-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .3rem;
        }

        .card-section-sub {
            color: #64748b;
            font-size: .85rem;
            margin-bottom: 1.25rem;
        }

        .pengaturan-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: .9rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .pengaturan-row:last-child {
            border-bottom: none;
        }

        .pengaturan-label {
            flex: 1;
            font-size: .88rem;
            color: #334155;
        }

        .pengaturan-input-wrap {
            display: flex;
            align-items: center;
            gap: .5rem;
            width: 160px;
        }

        .pengaturan-input-wrap input {
            border-radius: 10px;
            text-align: right;
        }

        .pengaturan-suffix {
            font-weight: 700;
            color: #64748b;
            font-size: .85rem;
            width: 24px;
        }

        .btn-simpan {
            background: #0f8a43;
            border-color: #0f8a43;
            border-radius: 12px;
            font-weight: 700;
            padding: .65rem 1.5rem;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <h2>Pengaturan Pengurangan Poin</h2>
            <p>Nilai persen/poin di bawah ini dipakai untuk menghitung otomatis potongan nilai akhir madrasah pada halaman
                Hasil &amp; Ranking.</p>
        </div>

        <div class="container-fluid">

            <form method="POST" action="{{ route('pengurangan-poin.pengaturan.update') }}">
                @csrf

                <div class="content-card">
                    <div class="card-section-title">
                        <i class="bi bi-megaphone text-danger"></i>
                        Aduan Masyarakat
                    </div>
                    <div class="card-section-sub">
                        Potongan dihitung dari total nilai bidang <strong>Lembaga</strong> saja. Kalau ada lebih dari satu
                        permasalahan dalam satu periode, persentasenya dijumlah.
                    </div>

                    @foreach ($pengaturanAduan as $item)
                        <div class="pengaturan-row">
                            <div class="pengaturan-label">{{ $item->label }}</div>
                            <div class="pengaturan-input-wrap">
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                    name="nilai[{{ $item->id }}]" value="{{ old('nilai.' . $item->id, $item->nilai) }}">
                                <span class="pengaturan-suffix">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="content-card">
                    <div class="card-section-title">
                        <i class="bi bi-hourglass-split text-warning"></i>
                        Keterlambatan Mengumpulkan Berkas
                    </div>
                    <div class="card-section-sub">
                        Potongan berupa poin flat, dihitung dari total nilai <strong>seluruh bidang</strong> (Akademik, Non
                        Akademik, Keagamaan, GTK, Lembaga digabung).
                    </div>

                    @foreach ($pengaturanKeterlambatan as $item)
                        <div class="pengaturan-row">
                            <div class="pengaturan-label">{{ $item->label }}</div>
                            <div class="pengaturan-input-wrap">
                                <input type="number" step="0.01" min="0" class="form-control form-control-sm"
                                    name="nilai[{{ $item->id }}]" value="{{ old('nilai.' . $item->id, $item->nilai) }}">
                                <span class="pengaturan-suffix">poin</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('aduan-masyarakat.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-megaphone"></i> Kelola Data Aduan
                        </a>
                        <a href="{{ route('keterlambatan-berkas.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-hourglass-split"></i> Kelola Data Keterlambatan
                        </a>
                    </div>

                    <button type="submit" class="btn btn-simpan text-white">
                        <i class="bi bi-check-circle"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>

        </div>
    </main>
@endsection
