@extends('layouts.base')

@push('styles')
    <style>
        /* =========================
               HEADER
            ========================= */

        .tambah-header {
            padding: 2rem 1rem 1.5rem;
            text-align: center;
        }

        .tambah-header h2 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .5rem;
        }

        .tambah-header p {
            color: #64748b;
            max-width: 560px;
            margin: 0 auto;
        }

        /* =========================
               CHOICE CARD
            ========================= */

        .choice-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: 100%;
            padding: 2.25rem 2rem;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .choice-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 32px rgba(0, 0, 0, .1);
        }

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

        .choice-icon {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
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
            color: #0f172a;
            margin-bottom: .6rem;
        }

        .choice-card p {
            color: #64748b;
            margin-bottom: 1.75rem;
        }

        .choice-card .btn {
            width: 100%;
            margin-top: auto;
            border-radius: 12px;
            font-weight: 600;
            padding: .65rem 1rem;
        }

        /* =========================
               TIPS CARD
            ========================= */

        .tips-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 18px;
        }

        .tips-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #fef3c7;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .tips-card h6 {
            font-weight: 700;
            color: #92400e;
            margin-bottom: .5rem;
        }

        .tips-card ul {
            color: #78716c;
            margin: 0;
        }

        .tips-card ul li:not(:last-child) {
            margin-bottom: .35rem;
        }

        .tips-card strong {
            color: #57534e;
        }

        @media (max-width: 768px) {
            .choice-card {
                padding: 1.75rem 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- HEADER --}}
        <div class="tambah-header">
            <h2>Tambah Prestasi</h2>
            <p>Pilih metode yang akan digunakan untuk menambahkan data prestasi ke dalam sistem PRESMA.</p>
        </div>

        <div class="container-fluid px-3 px-md-4">

            {{-- PILIHAN METODE --}}
            <div class="row g-4 justify-content-center mb-4">

                {{-- INPUT MANUAL --}}
                <div class="col-12 col-md-6 col-lg-5">
                    <a href="{{ route('prestasi.create') }}" class="choice-card-link">
                        <div class="choice-card">
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
                        <div class="choice-card">
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

            {{-- TIPS --}}
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="tips-card">
                        <div class="tips-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>

                        <div>
                            <h6>Tips</h6>
                            <ul>
                                <li>Gunakan <strong>Input Manual</strong> apabila hanya ingin menambahkan sedikit data.</li>
                                <li>Gunakan <strong>Import Excel</strong> apabila ingin mengunggah banyak data sekaligus.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>
@endsection
