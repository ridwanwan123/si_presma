@extends('layouts.base')

@push('styles')
    <style>
        /* =========================
               HEADER
            ========================= */

        .tambah-header {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1.1rem;
            text-align: left;
            margin-bottom: 1.75rem;
            padding: 1.5rem 1.75rem;
            border-radius: 18px;
            background: linear-gradient(120deg, #0f766e 0%, #198754 55%, #16a34a 100%);
            box-shadow: 0 10px 26px rgba(25, 135, 84, .22);
            overflow: hidden;
        }

        .tambah-header::after {
            content: "";
            position: absolute;
            top: -40%;
            right: -6%;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, .08);
            border-radius: 50%;
        }

        .tambah-header .header-icon {
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .tambah-header .header-text {
            position: relative;
            z-index: 1;
        }

        .tambah-header .eyebrow {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .8);
            margin-bottom: .25rem;
        }

        .tambah-header h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: .25rem;
        }

        .tambah-header p {
            color: rgba(255, 255, 255, .88);
            max-width: 560px;
            text-align: left;
            margin-bottom: 0;
            font-size: .92rem;
        }

        /* =========================
               TIPS ALERT (ringkas, di atas)
            ========================= */

        .tips-alert {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1.1rem;
            background: linear-gradient(90deg, #fff7e6 0%, #fffdf5 100%);
            border: 1px solid #fcd34d;
            border-radius: 999px;
            font-size: .82rem;
            color: #78350f;
            box-shadow: 0 3px 10px rgba(251, 191, 36, .15);
        }

        .tips-alert .tips-icon-badge {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #fbbf24;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            box-shadow: 0 2px 6px rgba(251, 191, 36, .45);
        }

        .tips-alert strong {
            font-weight: 700;
            color: #92400e;
        }

        /* =========================
               CHOICE CARD
            ========================= */

        .choice-card-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
        }

        .choice-card-link:hover {
            color: inherit;
            text-decoration: none;
        }

        .choice-card {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: 100%;
            padding: 2rem 1.85rem;
            background: #fff;
            border: 1px solid #e9edf2;
            border-radius: 18px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
            cursor: pointer;
            overflow: hidden;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .choice-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent, #0d6efd), transparent);
            opacity: 0;
            transition: opacity .25s ease;
        }

        .choice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, .09);
            border-color: transparent;
        }

        .choice-card:hover::before {
            opacity: 1;
        }

        .choice-card.card-primary {
            --accent: #0d6efd;
        }

        .choice-card.card-success {
            --accent: #198754;
        }

        .choice-icon {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.35rem;
            transition: transform .25s ease;
        }

        .choice-card:hover .choice-icon {
            transform: scale(1.06);
        }

        .choice-icon.icon-primary {
            background: rgba(13, 110, 253, .1);
            color: #0d6efd;
        }

        .choice-icon.icon-success {
            background: rgba(25, 135, 84, .1);
            color: #198754;
        }

        .choice-card h5 {
            font-weight: 700;
            font-size: 1.1rem;
            color: #0f172a;
            margin-bottom: .5rem;
        }

        .choice-card p {
            color: #64748b;
            font-size: .9rem;
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }

        .choice-card .btn {
            width: 100%;
            margin-top: auto;
            border-radius: 10px;
            font-weight: 600;
            font-size: .92rem;
            padding: .65rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease;
        }

        .choice-card:hover .btn {
            transform: translateX(2px);
        }

        @media (max-width: 768px) {
            .choice-card {
                padding: 1.6rem 1.4rem;
            }

            .tambah-header {
                padding: 1rem 1.35rem;
                gap: .85rem;
            }

            .tambah-header h2 {
                font-size: 1.35rem;
            }

            .tambah-header .header-icon {
                width: 46px;
                height: 46px;
                font-size: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- TIPS RINGKAS --}}
        <div class="tips-alert mb-3">
            <span class="tips-icon-badge">
                <i class="bi bi-lightbulb-fill"></i>
            </span>
            <span>
                <strong>Tips:</strong> pakai <strong>Input Manual</strong> untuk beberapa data,
                atau <strong>Import Excel</strong> kalau datanya banyak sekaligus.
            </span>
        </div>

        {{-- HEADER --}}
        <div class="tambah-header">
            <div class="header-icon">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <div class="header-text">
                <span class="eyebrow">Form Penambahan Data</span>
                <h2>Tambah Prestasi</h2>
                <p>Pilih metode yang akan digunakan untuk menambahkan data prestasi ke sistem.</p>
            </div>
        </div>

        <div class="container-fluid px-0">

            {{-- PILIHAN METODE --}}
            <div class="row g-4 justify-content-center mb-2">

                {{-- INPUT MANUAL --}}
                <div class="col-12 col-md-6 col-lg-5">
                    <a href="{{ route('prestasi.create') }}" class="choice-card-link">
                        <div class="choice-card card-primary">
                            <div class="choice-icon icon-primary">
                                <i class="bi bi-pencil-square"></i>
                            </div>

                            <h5>Input Manual</h5>

                            <p>
                                Tambahkan data prestasi satu per satu melalui formulir.
                                Cocok digunakan apabila hanya ingin menambahkan beberapa data.
                            </p>

                            <span class="btn btn-primary">
                                Mulai Input
                                <i class="bi bi-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </a>
                </div>

                {{-- IMPORT EXCEL --}}
                <div class="col-12 col-md-6 col-lg-5">
                    <a href="{{ route('prestasi.import') }}" class="choice-card-link">
                        <div class="choice-card card-success">
                            <div class="choice-icon icon-success">
                                <i class="bi bi-file-earmark-excel"></i>
                            </div>

                            <h5>Import Excel</h5>

                            <p>
                                Upload banyak data prestasi sekaligus menggunakan template Excel yang telah disediakan.
                            </p>

                            <span class="btn btn-success">
                                Import Data
                                <i class="bi bi-arrow-right ms-2"></i>
                            </span>
                        </div>
                    </a>
                </div>

            </div>

        </div>

    </main>
@endsection
