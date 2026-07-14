@extends('layouts.base')

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
        }

        .page-title h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }

        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .content-card-body {
            padding: 1rem;
        }

        /* ===========================
               WARNING CALLOUT
            =========================== */

        .preview-warning {
            display: flex;
            align-items: flex-start;
            gap: 12px;

            margin: 0 1rem 1rem;
            padding: 14px 16px;

            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 14px;
        }

        .preview-warning-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #fef3c7;
            color: #b45309;
            font-size: 1rem;
        }

        .preview-warning-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: #92400e;
            margin-bottom: 2px;
        }

        .preview-warning-text {
            font-size: 0.82rem;
            color: #a16207;
            line-height: 1.5;
            margin: 0;
        }

        /* ===========================
               SUMMARY CARD (berikon)
            =========================== */

        .preview-summary-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
        }

        .preview-summary-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 1.05rem;

            background: #f3f4f6;
            color: #6b7280;
        }

        .preview-summary-icon.accent {
            background: rgba(15, 138, 67, 0.1);
            color: #0f8a43;
        }

        .preview-summary-body {
            min-width: 0;
        }

        .preview-summary-label {
            font-size: 0.76rem;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .summary-value {
            font-weight: 700;
            font-size: 0.95rem;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===========================
               TABLE — COMPACT
            =========================== */

        .preview-table {
            margin-bottom: 0;
            white-space: nowrap;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-responsive {
            max-height: 550px;
            overflow: auto;
        }

        .preview-table thead th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 20;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .preview-table tbody td {
            font-size: .82rem;
            vertical-align: middle;
            padding: 8px 12px;
            color: #334155;
        }

        /* Zebra */
        .preview-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Hover */
        .preview-table tbody tr:hover {
            background: #eef7ff;
        }

        /* ===========================
               STICKY COLUMN
            =========================== */

        .preview-table tbody td.sticky-col {
            position: sticky;
            z-index: 10;
        }

        .preview-table thead th.sticky-col {
            z-index: 30;
            background: #f8fafc;
        }

        .preview-table .sticky-no {
            left: 0;
            width: 56px;
            min-width: 56px;
            max-width: 56px;
        }

        .preview-table .sticky-nama {
            left: 56px;
            min-width: 300px;
        }

        .preview-table tbody tr:nth-child(odd) td.sticky-col {
            background: #fff;
        }

        .preview-table tbody tr:nth-child(even) td.sticky-col {
            background: #fafafa;
        }

        .preview-table tbody tr:hover td.sticky-col {
            background: #eef7ff;
        }

        .preview-table .sticky-no,
        .preview-table .sticky-nama {
            border-right: 1px solid #dee2e6;
            box-shadow: 3px 0 8px rgba(0, 0, 0, .05);
        }

        .col-kegiatan {
            min-width: 300px;
        }

        .col-lembaga {
            min-width: 190px;
        }

        /* ===========================
               BADGE JUARA (selaras dengan halaman Daftar Prestasi)
            =========================== */

        .badge-juara {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-juara-gold {
            background: #fff8db;
            color: #a16207;
            border: 1px solid #fde68a;
        }

        .badge-juara-silver {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #dbe4ee;
        }

        .badge-juara-bronze {
            background: #fef2e8;
            color: #b45309;
            border: 1px solid #fed7aa;
        }

        .badge-juara-default {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge-tingkat-tinggi {
            background: rgba(15, 138, 67, 0.1);
            color: #0f8a43;
            border: 1px solid rgba(15, 138, 67, 0.18);
            font-weight: 700;
        }

        .badge-tingkat-biasa {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        @media(max-width:768px) {

            .page-title {
                padding: 0;
            }

            .content-card,
            .preview-warning {
                margin-left: 0;
                margin-right: 0;
            }

        }

        /* ===========================
               LOADING OVERLAY
            =========================== */

        .simpan-loading {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .75);
            backdrop-filter: blur(4px);

            display: flex;
            align-items: center;
            justify-content: center;

            z-index: 9999;
        }

        .loading-card {
            width: 320px;
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            border: 1px solid #e5e7eb;
        }

        .loading-icon {
            width: 70px;
            height: 70px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #ecfdf5;
            border-radius: 50%;
        }

        .loading-icon .spinner-border {
            width: 2.8rem;
            height: 2.8rem;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- TITLE --}}
        <div class="page-title">

            <h2>Preview Prestasi</h2>

            <p class="text-muted">
                Hasil pemeriksaan data sebelum disimpan ke database
            </p>

        </div>


        {{-- WARNING --}}
        <div class="preview-warning">

            <div class="preview-warning-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>

            <div>
                <div class="preview-warning-title">
                    Periksa kembali sebelum menyimpan
                </div>

                <p class="preview-warning-text">
                    Pastikan nama kegiatan, tingkat, juara, dan skor pada tabel di bawah sudah benar.
                    Setelah <strong>Simpan Data</strong> diklik, seluruh data akan langsung masuk ke database.
                    Anda tetap bisa mengedit atau menghapus data satu per satu setelah tersimpan melalui halaman Daftar
                    Prestasi.
                </p>
            </div>

        </div>


        {{-- SUMMARY --}}
        <div class="row g-3 mb-3">

            <div class="col-6 col-md-3">
                <div class="content-card mb-0 h-100">
                    <div class="preview-summary-card">

                        <div class="preview-summary-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div class="preview-summary-body">
                            <div class="preview-summary-label">Madrasah</div>
                            <div class="summary-value" title="{{ auth()->user()->madrasah->nama_madrasah }}">
                                {{ auth()->user()->madrasah->nama_madrasah }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @php
                $bidangList = collect($data)->pluck('bidang_prestasi')->filter()->unique()->values();
            @endphp

            <div class="col-6 col-md-3">
                <div class="content-card mb-0 h-100">
                    <div class="preview-summary-card">

                        <div class="preview-summary-icon">
                            <i class="bi bi-trophy"></i>
                        </div>

                        <div class="preview-summary-body">
                            <div class="preview-summary-label">Bidang Prestasi</div>
                            <div class="summary-value" title="{{ $bidangList->implode(', ') }}">
                                {{ $bidangList->count() > 1 ? $bidangList->count() . ' Bidang' : $bidangList->first() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="content-card mb-0 h-100">
                    <div class="preview-summary-card">

                        <div class="preview-summary-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <div class="preview-summary-body">
                            <div class="preview-summary-label">Submitter</div>
                            <div class="summary-value" title="{{ $data[0]['submitter'] }}">
                                {{ $data[0]['submitter'] }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="content-card mb-0 h-100">
                    <div class="preview-summary-card">

                        <div class="preview-summary-icon accent">
                            <i class="bi bi-database-fill-check"></i>
                        </div>

                        <div class="preview-summary-body">
                            <div class="preview-summary-label">Total Data</div>
                            <div class="summary-value" style="color:#0f8a43">
                                {{ count($data) }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>



        {{-- TABLE --}}
        <div class="content-card">

            <div class="content-card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h6 class="mb-1">
                            Preview Data
                        </h6>

                        <small class="text-muted">
                            Data berikut akan disimpan ke database PRESMA
                        </small>

                    </div>

                    <span class="badge bg-success rounded-pill">

                        {{ count($data) }} Data

                    </span>

                </div>


                <div class="table-responsive">

                    <table class="table table-hover table-sm preview-table align-middle">

                        <thead>

                            <tr>
                                <th class="sticky-col sticky-no text-center">No</th>
                                <th class="sticky-col sticky-nama">Nama Kegiatan</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th>Kategori</th>
                                <th>Juara</th>
                                <th>Lembaga</th>
                                <th>Penyelenggara</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Skor</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($data as $item)
                                @php
                                    $juara = $item['juara'] ?? '';

                                    $juaraClass = 'badge-juara-default';

                                    if (str_contains($juara, '1')) {
                                        $juaraClass = 'badge-juara-gold';
                                    } elseif (str_contains($juara, '2')) {
                                        $juaraClass = 'badge-juara-silver';
                                    } elseif (str_contains($juara, '3')) {
                                        $juaraClass = 'badge-juara-bronze';
                                    }

                                    $tingkatTinggi = in_array($item['tingkat'] ?? '', ['Nasional', 'Internasional']);
                                @endphp

                                <tr>

                                    <td class="sticky-col sticky-no text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="sticky-col sticky-nama col-kegiatan">
                                        {{ $item['nama_kegiatan'] }}
                                    </td>

                                    <td>
                                        {{ $item['bidang_prestasi'] }}
                                    </td>

                                    <td>
                                        <span
                                            class="badge-juara {{ $tingkatTinggi ? 'badge-tingkat-tinggi' : 'badge-tingkat-biasa' }}">
                                            {{ $item['tingkat'] }}
                                        </span>
                                    </td>

                                    <td>

                                        {{ $item['kategori_kegiatan'] }}

                                    </td>

                                    <td>

                                        <span class="badge-juara {{ $juaraClass }}">

                                            {{ $item['juara'] }}

                                        </span>

                                    </td>

                                    <td class="col-lembaga">

                                        {{ $item['lembaga_penyelenggara'] }}

                                    </td>

                                    <td>

                                        {{ $item['kategori_penyelenggara'] }}

                                    </td>

                                    <td>

                                        {{ $item['waktu_kegiatan'] }}

                                    </td>

                                    <td class="text-center">

                                        {{ $item['metode_pelaksanaan'] }}

                                    </td>

                                    <td class="text-center">

                                        {{ $item['skor'] }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="11" class="text-center py-5 text-muted">

                                        Belum ada data.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>



        {{-- ACTION --}}
        <div class="content-card">

            <div class="content-card-body">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('prestasi.import') }}" class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-left"></i>

                        Kembali

                    </a>


                    <button class="btn btn-success" id="btnImport">

                        <i class="bi bi-database-fill-check"></i>
                        Simpan Data

                    </button>

                </div>

            </div>

        </div>
    </main>

    <div id="loading" class="simpan-loading" style="display:none">
        <div class="loading-card">
            <div class="loading-icon">
                <div class="spinner-border text-success"></div>
            </div>
            <h5 class="mt-3 mb-2 fw-bold">
                Memproses Data
            </h5>
            <div class="text-muted small">
                Mohon tunggu, <br /> Sistem sedang melakukan simpan data.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#btnImport').click(function() {

            const $btn = $(this);

            Swal.fire({
                title: 'Simpan Data?',
                text: 'Data akan dimasukkan ke database PRESMA',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Simpan'
            }).then((result) => {

                if (result.isConfirmed) {

                    $btn.prop('disabled', true);

                    $('#loading').show();

                    $.ajax({

                        url: "{{ route('prestasi.store_import') }}",

                        type: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function(response) {

                            $('#loading').hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data berhasil disimpan.'
                            }).then(() => {

                                window.location.href =
                                    response.redirect ||
                                    "{{ route('prestasi.tambah') }}";

                            });

                        },

                        error: function(xhr) {

                            $('#loading').hide();

                            $btn.prop('disabled', false);

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data.'
                            });

                        }

                    });

                }

            });

        });
    </script>
@endpush
