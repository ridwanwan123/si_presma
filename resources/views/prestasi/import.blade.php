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
            height: 255px;
            border: 2px dashed #198754;
            border-radius: 18px;
            background: linear-gradient(180deg,
                    #f8fffb 0%,
                    #f0fdf4 100%);
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

            .btn-template {
                width: 100%;
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

        /* ===========================
                       MODAL ERROR IMPORT (SweetAlert)
                    =========================== */

        .import-error-summary {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            color: #92400e;
            margin-bottom: 14px;
            font-size: .85rem;
        }

        .import-error-summary i {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .import-error-summary-sub {
            font-size: .78rem;
            color: #a16207;
            margin-top: 2px;
            font-weight: 400;
        }

        .import-error-list {
            max-height: 380px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .import-error-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 10px;
            background: #fef2f2;
            margin-bottom: 10px;
        }

        .import-error-item:last-child {
            margin-bottom: 0;
        }

        .import-error-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .import-error-body {
            min-width: 0;
            flex: 1;
        }

        .import-error-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 3px;
        }

        .import-error-label {
            font-weight: 700;
            color: #991b1b;
            font-size: .86rem;
        }

        .import-error-count {
            font-size: .7rem;
            font-weight: 700;
            background: #fecaca;
            color: #991b1b;
            padding: 2px 9px;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .import-error-message {
            font-size: .82rem;
            color: #7f1d1d;
            margin-bottom: 5px;
        }

        .import-error-rows {
            font-size: .76rem;
            color: #b91c1c;
        }

        .import-error-more {
            color: #dc262699;
            font-style: italic;
        }

        /* ============ PANDUAN MENGISI EXCEL ============ */

        .panduan-toggle-btn {
            border-radius: 10px;
            font-size: .84rem;
        }

        .panduan-summary {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .panduan-summary-item {
            flex: 1 1 200px;
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .8rem 1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #eef0f2;
        }

        .panduan-summary-item i {
            font-size: 1.2rem;
            color: #198754;
            flex-shrink: 0;
        }

        .panduan-summary-item .label {
            font-size: .72rem;
            color: #64748b;
            margin-bottom: .1rem;
        }

        .panduan-summary-item .value {
            font-size: .88rem;
            font-weight: 700;
            color: #0f172a;
        }

        .panduan-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .82rem;
        }

        .panduan-table thead th {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 9px 10px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            text-align: left;
        }

        .panduan-table tbody td {
            padding: 10px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            color: #334155;
        }

        .panduan-kolom-nama {
            font-weight: 700;
            color: #0f172a;
            font-family: 'Courier New', monospace;
            font-size: .78rem;
            white-space: nowrap;
        }

        .panduan-wajib {
            display: inline-block;
            font-size: .64rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            margin-top: 4px;
        }

        .panduan-wajib.wajib {
            background: #fee2e2;
            color: #991b1b;
        }

        .panduan-wajib.opsional {
            background: #f1f5f9;
            color: #64748b;
        }

        .panduan-contoh {
            font-size: .76rem;
            color: #64748b;
            font-style: italic;
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

        {{-- ================= PANDUAN MENGISI EXCEL ================= --}}
        <div class="content-card mb-4">
            <div class="content-card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-journal-text text-success me-1"></i>
                        Panduan Mengisi Excel
                    </h5>
                    <button type="button" class="btn btn-success btn-sm panduan-toggle-btn" id="btnTogglePanduan"
                        data-bs-toggle="collapse" data-bs-target="#panduanDetail" aria-expanded="false"
                        aria-controls="panduanDetail">
                        <i class="bi bi-chevron-down" id="iconTogglePanduan"></i>
                        BUKA PANDUAN
                    </button>
                </div>

                {{-- RINGKASAN BATASAN --}}
                <div class="panduan-summary">
                    <div class="panduan-summary-item">
                        <i class="bi bi-table"></i>
                        <div>
                            <div class="label">Jumlah Kolom</div>
                            <div class="value">Harus tepat 12 kolom</div>
                        </div>
                    </div>
                    <div class="panduan-summary-item">
                        <i class="bi bi-list-ol"></i>
                        <div>
                            <div class="label">Maksimal Baris</div>
                            <div class="value">7.000 baris per file</div>
                        </div>
                    </div>
                    <div class="panduan-summary-item">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <div>
                            <div class="label">Ukuran File</div>
                            <div class="value">Maksimal 20 MB</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success border-0 mb-3">
                    <i class="bi bi-lightbulb-fill me-1"></i>
                    Selalu mulai dari <strong>Download Template</strong> di bawah, jangan bikin format sendiri —
                    urutan &amp; nama kolom harus persis sama. Kalau data lebih dari 7.000 baris, pecah jadi
                    beberapa file, upload satu-satu.
                </div>

                {{-- DETAIL PER KOLOM (COLLAPSIBLE) --}}
                <div class="collapse" id="panduanDetail">
                    <div class="table-responsive">
                        <table class="panduan-table">
                            <thead>
                                <tr>
                                    <th style="width:170px">Kolom</th>
                                    <th>Aturan Pengisian</th>
                                    <th style="width:200px">Contoh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">bidang_prestasi</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Isi salah satu: <strong>Akademik</strong>, <strong>Non Akademik</strong>,
                                        <strong>Keagamaan</strong>, <strong>GTK</strong>, atau <strong>Lembaga</strong>.
                                        Boleh beda-beda tiap baris — satu file boleh campuran semua bidang.
                                    </td>
                                    <td class="panduan-contoh">Akademik</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">nama_kegiatan</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Nama kegiatan/lomba, teks bebas. <strong>Maksimal 255 karakter</strong> — jangan
                                        tempel deskripsi panjang di kolom ini.</td>
                                    <td class="panduan-contoh">Olimpiade Sains Nasional</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">tingkat</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Isi salah satu: <strong>Kabupaten/Kota</strong>, <strong>Provinsi</strong>,
                                        <strong>Nasional</strong>, atau <strong>Internasional</strong>.
                                    </td>
                                    <td class="panduan-contoh">Nasional</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">kategori_kegiatan</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Isi salah satu: <strong>Individu</strong> atau <strong>Beregu</strong>.</td>
                                    <td class="panduan-contoh">Individu</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">juara</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Teks bebas, sesuai hasil yang diperoleh — tidak ada format baku.</td>
                                    <td class="panduan-contoh">Juara 1 / Harapan 2</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">lembaga_penyelenggara</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Nama penyelenggara kegiatan, teks bebas. <strong>Maksimal 255 karakter</strong>.
                                    </td>
                                    <td class="panduan-contoh">Kemendikbudristek</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">kategori_penyelenggara</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Teks bebas, mis. jenis instansi penyelenggara. <strong>Maksimal 255
                                            karakter</strong>.</td>
                                    <td class="panduan-contoh">Pemerintah</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">waktu_kegiatan</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>
                                        <strong>Harus berupa sel bertipe Tanggal di Excel</strong> (klik kanan sel →
                                        Format Cells → Date), <strong>bukan diketik sebagai teks bebas</strong>. Ini
                                        penyebab error paling sering — kalau tanggal ditulis manual seperti
                                        "kemarin" atau format aneh, baris itu akan ditolak.
                                    </td>
                                    <td class="panduan-contoh">15/03/2025 (format tanggal Excel)</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">metode_pelaksanaan</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Isi salah satu: <strong>Luring</strong> atau <strong>Daring</strong>.</td>
                                    <td class="panduan-contoh">Luring</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">skor</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Harus <strong>angka</strong> (bukan teks), tanpa titik/koma ribuan. Maksimal
                                        <strong>999.999,99</strong>.
                                    </td>
                                    <td class="panduan-contoh">87.5</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">link_drive_bukti</div>
                                        <span class="panduan-wajib wajib">Wajib</span>
                                    </td>
                                    <td>Link Google Drive/penyimpanan bukti kegiatan. <strong>Maksimal 255
                                            karakter</strong>. Pastikan link bisa diakses (mode "Anyone with the link").
                                    </td>
                                    <td class="panduan-contoh">https://drive.google.com/...</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="panduan-kolom-nama">keterangan</div>
                                        <span class="panduan-wajib opsional">Opsional</span>
                                    </td>
                                    <td>Catatan tambahan, boleh dikosongkan.</td>
                                    <td class="panduan-contoh">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-body">
                {{-- HEADER --}}
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            Upload File Excel
                        </h5>

                        <small class="text-muted">
                            Gunakan template resmi agar format sesuai sistem
                        </small>
                    </div>

                    <a href="{{ route('prestasi.template') }}"
                        class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center btn-template">
                        <span>Download Template</span>
                        <span class="mx-2 opacity-50">|</span>
                        <i class="bi bi-file-earmark-excel-fill"></i>
                    </a>
                </div>

                <div class="row g-4 align-items-stretch">
                    {{-- LEFT --}}
                    <div class="col-md-6">
                        <div class="row g-3">

                            {{-- INFO --}}
                            <div class="col-12">
                                <div class="alert alert-success border-0 mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Data Madrasah dan Submitter diisi otomatis oleh sistem. Bidang Prestasi dibaca
                                    otomatis dari isi Excel pada setiap baris, sehingga file boleh berisi campuran
                                    Akademik, Non Akademik, Keagamaan, GTK, dan Lembaga sekaligus.
                                </div>
                            </div>

                            {{-- ROW 1: 2 KOLOM --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Madrasah
                                </label>
                                <input type="text" class="form-control" readonly
                                    value="{{ auth()->user()->madrasah?->nama_madrasah ?? '-' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tahun Periode
                                </label>
                                <input type="text" class="form-control" readonly value="{{ date('Y') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label mt-3">
                                    Submitter
                                </label>
                                <input type="text" class="form-control" readonly value="{{ auth()->user()->nama }}">
                            </div>

                        </div>
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

                                <div class="mb-3">
                                    <i class="bi bi-file-earmark-excel-fill text-success fs-1"></i>
                                </div>

                                <h5 class="fw-bold text-success">
                                    Upload Template Prestasi
                                </h5>

                                <p class="text-muted mb-2">
                                    Drag & Drop atau klik untuk memilih file
                                </p>

                                <small class="text-muted">
                                    Format XLSX / XLS • Maks. 20 MB • Maks. 7.000 baris
                                </small>

                                <div id="selectedFile" class="mt-3 fw-semibold text-success">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="border-top p-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('prestasi.tambah') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>

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

        // Ikon & teks tombol panduan ikut berubah sesuai status buka/tutup
        (function() {
            const panduanDetail = document.getElementById('panduanDetail');
            const btnToggle = document.getElementById('btnTogglePanduan');
            const iconToggle = document.getElementById('iconTogglePanduan');

            if (!panduanDetail || !btnToggle || !iconToggle) return;

            panduanDetail.addEventListener('show.bs.collapse', function() {
                iconToggle.classList.replace('bi-chevron-down', 'bi-chevron-up');
                btnToggle.lastChild.textContent = ' TUTUP PANDUAN';
            });

            panduanDetail.addEventListener('hide.bs.collapse', function() {
                iconToggle.classList.replace('bi-chevron-up', 'bi-chevron-down');
                btnToggle.lastChild.textContent = ' BUKA PANDUAN';
            });
        })();

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
                    url: "{{ route('prestasi.checking_import') }}",
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

                            // Label & ikon ramah-user untuk tiap kolom teknis,
                            // supaya staf madrasah nggak perlu baca snake_case.
                            const kolomInfo = {
                                bidang_prestasi: {
                                    label: 'Bidang Prestasi',
                                    icon: 'bi-bookmark-star'
                                },
                                nama_kegiatan: {
                                    label: 'Nama Kegiatan',
                                    icon: 'bi-card-text'
                                },
                                tingkat: {
                                    label: 'Tingkat',
                                    icon: 'bi-bar-chart-steps'
                                },
                                kategori_kegiatan: {
                                    label: 'Kategori Kegiatan',
                                    icon: 'bi-people'
                                },
                                juara: {
                                    label: 'Juara',
                                    icon: 'bi-trophy'
                                },
                                lembaga_penyelenggara: {
                                    label: 'Lembaga Penyelenggara',
                                    icon: 'bi-building'
                                },
                                kategori_penyelenggara: {
                                    label: 'Kategori Penyelenggara',
                                    icon: 'bi-diagram-3'
                                },
                                waktu_kegiatan: {
                                    label: 'Waktu Kegiatan',
                                    icon: 'bi-calendar-event'
                                },
                                metode_pelaksanaan: {
                                    label: 'Metode Pelaksanaan',
                                    icon: 'bi-toggle-on'
                                },
                                skor: {
                                    label: 'Skor',
                                    icon: 'bi-123'
                                },
                                link_drive_bukti: {
                                    label: 'Link Drive Bukti',
                                    icon: 'bi-link-45deg'
                                },
                                keterangan: {
                                    label: 'Keterangan',
                                    icon: 'bi-chat-left-text'
                                },
                                general: {
                                    label: 'Umum',
                                    icon: 'bi-exclamation-circle'
                                },
                            };

                            // Total baris unik yang bermasalah, buat ringkasan cepat
                            const semuaBarisBermasalah = new Set();
                            response.errors.forEach(err => (err.rows || [])
                                .forEach(r => semuaBarisBermasalah.add(r)));

                            const MAKS_BARIS_TAMPIL = 8;

                            let errorHtml = response.errors.map(err => {

                                const kolom = kolomInfo[err.column] || kolomInfo
                                    .general;
                                const rows = err.rows || [];
                                const barisTampil = rows.slice(0, MAKS_BARIS_TAMPIL)
                                    .join(', ');
                                const sisaBaris = rows.length - MAKS_BARIS_TAMPIL;

                                return `
                                <div class="import-error-item">
                                    <div class="import-error-icon">
                                        <i class="bi ${kolom.icon}"></i>
                                    </div>
                                    <div class="import-error-body">
                                        <div class="import-error-header">
                                            <span class="import-error-label">${kolom.label}</span>
                                            <span class="import-error-count">${rows.length} baris</span>
                                        </div>
                                        <div class="import-error-message">${err.message}</div>
                                        <div class="import-error-rows">
                                            <i class="bi bi-list-ol"></i>
                                            Baris: ${barisTampil}${sisaBaris > 0 ? ` <span class="import-error-more">dan ${sisaBaris} baris lainnya</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                                `;
                            }).join('');

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Import Gagal',
                                html: `
                                    <div class="text-start">
                                        <div class="import-error-summary">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <div>
                                                <div class="fw-semibold">
                                                    Ditemukan ${response.errors.length} jenis masalah pada ${semuaBarisBermasalah.size} baris
                                                </div>
                                                <div class="import-error-summary-sub">
                                                    Silakan perbaiki data berikut di file Excel, lalu upload ulang.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="import-error-list">
                                            ${errorHtml}
                                        </div>
                                    </div>
                                `,
                                width: 720,
                                confirmButtonText: 'Perbaiki Data',
                                confirmButtonColor: '#198754'
                            }).then(() => {
                                location.reload();
                            });
                            return;
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data valid, menyiapkan preview...',
                                showConfirmButton: false,
                                timer: 1200,
                                timerProgressBar: true,
                            }).then(function() {

                                // Data hasil validasi sudah disimpan di sisi
                                // server sejak checking_import (token di
                                // session) -- tidak perlu lagi kirim balik
                                // seluruh data ke save_preview sebelum
                                // redirect, jadi langsung arahkan browser.
                                window.location.href =
                                    response.redirect ||
                                    "{{ route('prestasi.preview') }}";

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
