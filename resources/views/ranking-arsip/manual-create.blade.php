@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
            display: flex;
            justify-content: center;
        }

        .page-title {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
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
            padding: 1.75rem 1.8rem;
            max-width: 520px;
            width: 100%;
        }

        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }

        .step-dot {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: 700;
            font-size: .82rem;
        }

        .step-dot.active {
            background: #0f8a43;
            color: #fff;
        }

        .step-dot.pending {
            background: #f1f5f9;
            color: #94a3b8;
        }

        .step-line {
            width: 36px;
            height: 2px;
            background: #e2e8f0;
        }

        .step-label {
            text-align: center;
            font-size: .78rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .info-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: .8rem 1rem;
            color: #1e40af;
            font-size: .82rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .6rem;
        }

        .info-note i {
            margin-top: 1px;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <h2>Input Manual Arsip Ranking</h2>
            <p>Untuk memasukkan data JMA tahun-tahun sebelumnya dari dokumen arsip (PDF/cetak).</p>
        </div>

        <div class="container-fluid">
            <div class="content-card">

                <div class="step-indicator">
                    <div class="step-dot active">1</div>
                    <div class="step-line"></div>
                    <div class="step-dot pending">2</div>
                </div>
                <div class="step-label">Langkah 1: Buat Periode &nbsp;→&nbsp; Langkah 2: Input Data Madrasah</div>

                <div class="info-note">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Buat dulu "wadah" arsip untuk satu periode. Setelah ini Anda akan diarahkan ke halaman input data
                        madrasah satu per satu.</span>
                </div>

                <form method="POST" action="{{ route('ranking-arsip.manual.store') }}">
                    @csrf

                    <label class="form-label">Periode (Tahun)</label>
                    <input type="number" name="periode" class="form-control mb-1 @error('periode') is-invalid @enderror"
                        min="2000" max="2100" placeholder="Mis. 2022" value="{{ old('periode') }}" required
                        autofocus>
                    @error('periode')
                        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                    @enderror

                    <label class="form-label mt-3">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control mb-4" rows="2"
                        placeholder="Mis. Diinput ulang dari dokumen SK hasil JMA 2022">{{ old('catatan') }}</textarea>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('ranking-arsip.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            Lanjut Input Data <i class="bi bi-arrow-right-circle"></i>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>
@endsection
