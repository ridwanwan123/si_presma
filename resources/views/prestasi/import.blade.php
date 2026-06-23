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



        /* CARD */
        .content-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .05);
            margin: 0 1rem 1rem;
        }

        .content-card-body {
            padding: 1.5rem;
        }

        /* STEP */
        .import-steps .step {
            position: relative;
        }

        .import-steps .step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 22px;
            left: 60%;
            width: 80%;
            height: 2px;
            background: #e5e7eb;
        }

        .step-number {
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }


        .step.active .step-number {
            background: #198754;
            color: white;
            box-shadow: 0 5px 15px rgba(25, 135, 84, .35);
        }

        .step-title {
            font-weight: 600;
            color: #1f2937;

        }

        @media(max-width:768px) {
            .import-steps .step:not(:last-child)::after {
                display: none;
            }

            .step-number {
                width: 34px;
                height: 34px;
            }

            .import-steps small {
                display: none;
            }
        }

        /* FORM */
        .form-label {
            font-weight: 600;
            color: #374151;
        }

        .form-control {
            border-radius: 10px;
            height: 42px;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow:
                0 0 0 .2rem rgba(25, 135, 84, .15);
        }

        /* UPLOAD */
        .file-input-field {
            position: relative;
            height: 220px;
            border: 2px dashed #198754;
            border-radius: 18px;
            background: #f8fffb;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
            overflow: hidden;
        }

        .file-input-field:hover {
            background: #ecfdf5;
            border-color: #157347;
        }

        .file-input-field input[type=file] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .placeholder-content {
            text-align: center;
        }

        .placeholder-content img {
            width: 70px;
            margin-bottom: 15px;
        }

        .placeholder-content p {
            color: #6b7280;
            margin: 0;
            font-size: .9rem;
        }

        /* BUTTON */
        .btn-success {
            border-radius: 10px;
            padding: .55rem 1.2rem;
        }

        .btn-outline-secondary {
            border-radius: 10px;
        }

        @media(max-width:768px) {
            .content-card-body>.d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 15px;
            }

            .content-card-body>.d-flex a {
                width: 100%;
                text-align: center;
            }
        }

        .import-loading {
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

        .import-loading {
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
            <h2>Import Prestasi</h2>
            <p class="text-muted">
                Upload file Excel untuk menambahkan banyak data prestasi sekaligus
            </p>
        </div>

        <div class="content-card mb-4">
            <div class="content-card-body">
                <div class="row text-center g-0 import-steps">
                    <div class="col step active">
                        <div class="step-number">1</div>
                        <div class="step-title">Upload File</div>
                        <small>Pilih file Excel</small>
                    </div>

                    <div class="col step">
                        <div class="step-number">2</div>
                        <div class="step-title">Preview Data</div>
                        <small>Validasi data</small>
                    </div>

                    <div class="col step">
                        <div class="step-number">3</div>
                        <div class="step-title">Simpan</div>
                        <small>Import ke database</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-body">
                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            Upload File Excel
                        </h5>

                        <small class="text-muted">
                            Gunakan template resmi agar format sesuai sistem
                        </small>
                    </div>

                    <a href="{{ route('prestasi.template', $jenis) }}"
                        class="btn btn-success btn-sm d-inline-flex align-items-center">
                        <span>Download Template</span>
                        <span class="mx-2 opacity-50">|</span>
                        <i class="bi bi-file-earmark-pdf-fill"></i>

                    </a>
                </div>

                <div class="row g-4">
                    {{-- LEFT --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Madrasah
                        </label>
                        <input type="text" class="form-control" id="madrasah_id" name="madrasah_id" readonly
                            value="{{ auth()->user()->madrasah?->nama_madrasah ?? '-' }}">
                        <label class="form-label mt-3">
                            Bidang Prestasi
                        </label>
                        <input type="text" class="form-control" id="bidang_prestasi" name="bidang_prestasi" readonly
                            value="{{ ucfirst($jenis) }}">

                        <label class="form-label mt-3">
                            Submitter
                        </label>
                        <input type="text" class="form-control" id="submitter" name="submitter" readonly
                            value="{{ auth()->user()->nama }}">
                    </div>

                    {{-- RIGHT --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Upload File Excel Prestasi
                            <span class="text-danger">*</span>
                        </label>
                        <div class="file-input-field">
                            <input type="file" name="file_import" id="file_import" accept=".xlsx,.xls">
                            <div class="placeholder-content">
                                <i class="bi bi-cloud-arrow-up fs-1 text-success"></i>
                                <h5 class="mt-2 text-success">
                                    Upload Document
                                </h5>
                                <p>
                                    Excel XLSX / XLS
                                    <br>
                                    Maksimal 1 MB
                                </p>
                                <div id="selectedFile" class="fw-semibold text-success">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="border-top p-3 d-flex justify-content-end">
                <button id="btnPreview" class="btn btn-success d-inline-flex align-items-center">
                    <span class="fw-medium">Preview Data</span>

                    <span class="ms-3 ps-3" style="border-left:1px solid rgba(255,255,255,.35)">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </button>
            </div>

        </div>
    </main>
    <div id="loading" class="import-loading" style="display:none">
        <div class="loading-card">
            <div class="loading-icon">
                <div class="spinner-border text-success"></div>
            </div>
            <h5 class="mt-3 mb-2 fw-bold">
                Memproses Data
            </h5>
            <div class="text-muted small">
                Mohon tunggu,<br /> Sistem sedang melakukan validasi file Excel.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('file_import').addEventListener('change', function() {
            var fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
            this.nextElementSibling.textContent = fileName;
        });

        $(document).ready(function() {
            $('#btnPreview').on('click', function(e) {
                e.preventDefault();

                var formData = new FormData();
                var fileInput = $('#file_import')[0].files[0];

                if (!fileInput) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Required',
                        text: 'Silakan pilih file terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#198754'
                    });
                    return;
                }

                formData.append('file_import', fileInput);

                $('#loading').show();

                $.ajax({
                    url: "{{ route('prestasi.checking_import', $jenis) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#loading').hide();

                        if (response.errors && response.errors.length > 0) {
                            let errorMessages = response.errors.map(e =>
                                `Baris ${e.row}${e.column ? ', Kolom ' + e.column : ''}: ${e.error}`
                            ).join('<br>');

                            Swal.fire({
                                icon: 'warning',
                                title: 'Data yang dimasukkan tidak valid',
                                html: `<p>Beberapa kesalahan ditemukan dalam data:</p><p>${errorMessages}</p>`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#198754'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data valid, menyiapkan preview...',
                                showConfirmButton: false,
                                timer: 1200,
                                timerProgressBar: true,
                            }).then(function() {

                                $('#loading').show();

                                $.ajax({
                                    url: "{{ route('prestasi.save_preview', $jenis) }}",
                                    type: 'POST',
                                    data: {
                                        data: JSON.stringify(response.data),
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function() {
                                        window.location.href =
                                            "{{ route('prestasi.preview', $jenis) }}";

                                    },
                                    error: function(xhr) {
                                        $('#loading').hide();
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan saat menyiapkan preview.'
                                        });
                                        console.error(xhr.responseText);
                                    }
                                });
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loading').hide();
                        handleError(xhr);
                    }
                });

                function handleError(xhr) {
                    if (xhr.status === 400) {
                        xhr.responseJSON.then(data => {
                            if (data.row) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: `Error on row ${data.row}: ${data.error}`,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#198754'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.error,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#198754'
                                });
                            }
                        }).catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memproses data.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#198754'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memproses data.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#198754'
                        });
                    }
                }
            });
        });
    </script>
@endpush
