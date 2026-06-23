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

        .preview-table {
            white-space: nowrap;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .preview-table th {
            background: #f8fafc;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: .82rem;
            font-weight: 600;
        }

        .preview-table td {
            font-size: .85rem;
            vertical-align: middle;
        }

        .table-responsive {
            max-height: 550px;
        }

        .col-kegiatan {
            min-width: 350px;
        }

        .col-lembaga {
            min-width: 220px;
        }

        .summary-value {
            font-weight: 600;
            color: #111827;
        }

        @media(max-width:768px) {

            .page-title {
                padding: 0;
            }

            .content-card {
                margin-left: 0;
                margin-right: 0;
            }

        }

        .simpan-loading {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .85);
            z-index: 9999;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-content {
            text-align: center;
        }

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

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .08);

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


        {{-- SUMMARY --}}
        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <div class="content-card">
                    <div class="content-card-body">

                        <div class="small text-muted">
                            Madrasah
                        </div>

                        <div class="summary-value">
                            {{ auth()->user()->madrasah->nama_madrasah }}
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="content-card">
                    <div class="content-card-body">

                        <div class="small text-muted">
                            Bidang Prestasi
                        </div>

                        <div class="summary-value">
                            {{ $data[0]['bidang_prestasi'] }}
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="content-card">
                    <div class="content-card-body">

                        <div class="small text-muted">
                            Submitter
                        </div>

                        <div class="summary-value">
                            {{ $data[0]['submitter'] }}
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="content-card">
                    <div class="content-card-body">

                        <div class="small text-muted">
                            Total Data
                        </div>

                        <div class="summary-value text-success fs-5">
                            {{ count($data) }}
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

                    <table class="table table-hover preview-table align-middle">

                        <thead>

                            <tr>

                                <th width="50">
                                    No
                                </th>

                                <th class="col-kegiatan">
                                    Nama Kegiatan
                                </th>

                                <th>
                                    Tingkat
                                </th>

                                <th>
                                    Kategori
                                </th>

                                <th>
                                    Juara
                                </th>

                                <th class="col-lembaga">
                                    Lembaga
                                </th>

                                <th>
                                    Penyelenggara
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Skor Luring
                                </th>

                                <th>
                                    Skor Daring
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($data as $item)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="col-kegiatan">

                                        {{ $item['nama_kegiatan'] }}

                                    </td>

                                    <td>

                                        {{ $item['tingkat'] }}

                                    </td>

                                    <td>

                                        {{ $item['kategori_kegiatan'] }}

                                    </td>

                                    <td>

                                        <span class="badge bg-success">

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

                                        {{ $item['skor_luring'] }}

                                    </td>

                                    <td class="text-center">

                                        {{ $item['skor_daring'] }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="10" class="text-center py-5 text-muted">

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

                    <a href="{{ route('prestasi.import', $jenis) }}" class="btn btn-outline-secondary">

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
                Mohon tunggu, sistem sedang melakukan simpan data.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#btnImport').click(function() {

            Swal.fire({
                title: 'Simpan Data?',
                text: 'Data akan dimasukkan ke database PRESMA',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Simpan'
            }).then((result) => {

                if (result.isConfirmed) {

                    $('#loading').show();

                    $.ajax({

                        url: "{{ route('prestasi.store_import', $jenis) }}",

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
                                    "{{ route('prestasi.index', $jenis) }}";

                            });

                        },

                        error: function(xhr) {

                            $('#loading').hide();

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
