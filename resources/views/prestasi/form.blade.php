@extends('layouts.base')


@push('styles')
    <style>
        /* =========================
                                                                                                                                                                                                                                                                                                                                   FORM MADRASAH
                                                                                                                                                                                                                                                                                                                                ========================= */

        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
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


        /* CARD */

        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        }


        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: center;
        }


        .table-header h6 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }


        .content-card-body {
            padding: 1.5rem;
        }


        /* FORM */

        .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .45rem;
        }


        .form-control,
        .form-select {

            height: 45px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
            color: #334155;

        }


        textarea.form-control {

            height: auto;
            resize: vertical;

        }


        .form-control:focus,
        .form-select:focus {

            border-color: #0f8a43;

            box-shadow:
                0 0 0 .15rem rgba(15, 138, 67, .15);

        }



        /* BUTTON */

        .btn-success {

            background: #0f8a43;
            border-color: #0f8a43;

        }


        .btn-success:hover {

            background: #0c7438;
            border-color: #0c7438;

        }



        .btn {

            border-radius: 10px;
            font-weight: 600;

        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            font-size: .8rem;
            margin-top: .35rem;
        }

        .required {
            color: #dc3545;
            font-weight: 600;
        }

        .drive-note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            margin-top: .75rem;
            padding: .85rem 1rem;
            border: 1px solid #d8efe2;
            background: #f7fcf9;
            border-radius: 12px;
        }

        .drive-note-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #19875415;
            color: #198754;
            font-size: 1rem;
        }

        .drive-note-content {
            font-size: .88rem;
            color: #556070;
            line-height: 1.6;
        }

        .drive-note-content strong {
            color: #1b4332;
        }

        .drive-note-content a {
            margin-left: .35rem;
            color: #198754;
            font-weight: 600;
            text-decoration: none;
        }

        .drive-note-content a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE */

        @media(max-width:768px) {

            .content-card {

                margin: 0 .5rem 1rem;

            }


            .content-card-body {

                padding: 1rem;

            }

        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>
                {{ $mode === 'edit' ? 'Edit Data Prestasi' : 'Tambah Data Prestasi' }}
            </h2>

            <p>
                {{ $mode === 'edit' ? 'Perbarui data prestasi.' : 'Input data lengkap prestasi baru.' }}
            </p>
        </div>


        <div class="content-card">


            <div class="table-header">
                <h6>
                    Form Data Prestasi
                </h6>
            </div>


            <div class="content-card-body">


                <form
                    action="{{ $mode === 'edit' ? route('prestasi.update', [$jenis, $prestasi->id]) : route('prestasi.store') }}"
                    method="POST">

                    @csrf

                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif


                    <div class="row g-3">


                        {{-- BIDANG --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Bidang Prestasi <span class="required">*</span>
                            </label>
                            <select name="bidang_prestasi" class="form-select" required>
                                <option value="">Pilih Bidang Prestasi</option>
                                @php
                                    $bidang = ['Akademik', 'Non Akademik', 'Keagamaan', 'GTK', 'Lembaga'];
                                @endphp
                                @foreach ($bidang as $item)
                                    <option value="{{ $item }}"
                                        {{ old('bidang_prestasi', $prestasi->bidang_prestasi ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- NAMA KEGIATAN --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Nama Kegiatan <span class="required">*</span>
                            </label>
                            <input type="text" name="nama_kegiatan" class="form-control"
                                value="{{ old('nama_kegiatan', $prestasi->nama_kegiatan ?? '') }}" required>
                        </div>

                        {{-- TINGKAT --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Tingkat <span class="required">*</span>
                            </label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">Pilih Tingkat</option>

                                @foreach (['Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'] as $item)
                                    <option value="{{ $item }}"
                                        {{ old('tingkat', $prestasi->tingkat ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- KATEGORI --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Kategori Kegiatan <span class="required">*</span>
                            </label>
                            <select name="kategori_kegiatan" class="form-select" required>
                                <option value="">Pilih Kategori</option>

                                @foreach (['Individu', 'Beregu'] as $item)
                                    <option value="{{ $item }}"
                                        {{ old('kategori_kegiatan', $prestasi->kategori_kegiatan ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- JUARA --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Juara <span class="required">*</span>
                            </label>

                            @php
                                $juaraOptions = [
                                    'Juara 1',
                                    'Juara 2',
                                    'Juara 3',
                                    'Harapan 1',
                                    'Harapan 2',
                                    'Harapan 3',
                                ];

                                $selected = old('juara', $prestasi->juara ?? '');
                            @endphp

                            {{-- Sengaja TIDAK ada opsi "Lainnya"/input bebas lagi --
                                 juara itu tingkatan yang jumlahnya tetap (cuma 6
                                 kemungkinan), tidak ada alasan bisnis butuh nilai
                                 di luar ini. Dropdown murni mencegah variasi
                                 penulisan (mis. "Juara I" romawi) yang bikin
                                 pencocokan ke rubrik Juknis gagal padahal
                                 datanya sebenarnya sama. --}}
                            <select name="juara" id="juara_select" class="form-control" required>
                                <option value="" disabled {{ empty($selected) ? 'selected' : '' }}>Pilih Juara
                                </option>

                                @foreach ($juaraOptions as $option)
                                    <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- LEMBAGA --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Lembaga Penyelenggara <span class="required">*</span>
                            </label>
                            <input type="text" name="lembaga_penyelenggara" class="form-control"
                                value="{{ old('lembaga_penyelenggara', $prestasi->lembaga_penyelenggara ?? '') }}">
                        </div>

                        {{-- KATEGORI PENYELENGGARA --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Kategori Penyelenggara <span class="required">*</span>
                            </label>

                            <select name="kategori_penyelenggara" class="form-select">
                                <option value="">Pilih Kategori Penyelenggara</option>

                                @foreach (['Pemerintah', 'Non Pemerintah'] as $item)
                                    <option value="{{ $item }}"
                                        {{ old('kategori_penyelenggara', $prestasi->kategori_penyelenggara ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- WAKTU --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Waktu Kegiatan <span class="required">*</span>
                            </label>
                            <input type="date" name="waktu_kegiatan" class="form-control"
                                value="{{ old('waktu_kegiatan', optional(optional($prestasi)->waktu_kegiatan)->format('Y-m-d')) }}"
                                required>
                        </div>

                        {{-- METODE PELAKSANAAN --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Metode Pelaksanaan <span class="required">*</span>
                            </label>
                            <select name="metode_pelaksanaan" class="form-select" required>
                                <option value="">Pilih Metode</option>

                                @foreach (['Luring', 'Daring'] as $item)
                                    <option value="{{ $item }}"
                                        {{ old('metode_pelaksanaan', $prestasi->metode_pelaksanaan ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SKOR --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Skor
                            </label>
                            <input type="number" step="0.01" name="skor" class="form-control skor-input"
                                value="{{ old('skor', isset($prestasi) ? +$prestasi->skor : 0) }}">
                        </div>

                        {{-- LINK DRIVE --}}
                        <div class="col-md-12">
                            <label class="form-label">
                                Link Drive Bukti <span class="required">*</span>
                            </label>

                            <input type="url" name="link_drive_bukti" class="form-control"
                                value="{{ old('link_drive_bukti', $prestasi->link_drive_bukti ?? '') }}"
                                placeholder="https://drive.google.com/file/d/xxxxxxxx/view" required>

                            <div class="drive-note mt-2" data-bs-toggle="modal" data-bs-target="#modalDriveExample"
                                style="cursor: pointer;">
                                <div class="drive-note-icon">
                                    <i class="bi bi-google"></i>
                                </div>

                                <div class="drive-note-content">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalDriveExample"
                                        class="fw-semibold">
                                        <i class="bi bi-play-circle me-1"></i>
                                        Lihat Panduan Pengambilan Link Google Drive
                                    </a>

                                    <div class="mt-1">
                                        Tempelkan <strong>link file Google Drive</strong>, bukan link folder.
                                        Pastikan akses file adalah <strong>"Siapa saja yang memiliki link dapat
                                            melihat"</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="col-md-12">
                            <label class="form-label">
                                Keterangan
                            </label>
                            <textarea name="keterangan" rows="4" class="form-control">{{ old('keterangan', $prestasi->keterangan ?? '') }}</textarea>
                        </div>

                        {{-- BUTTON --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ $mode === 'edit' ? route('prestasi.index', $jenis) : route('prestasi.tambah') }}"
                                class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i>

                                {{ $mode === 'edit' ? 'Update Prestasi' : 'Simpan Prestasi' }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </main>
@endsection

<div class="modal fade" id="modalDriveExample" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Panduan Mengambil Link Google Drive
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <div class="alert alert-info d-flex align-items-start">
                    <i class="bi bi-info-circle-fill me-2"></i>

                    <div>
                        <strong>Perhatian!</strong><br>

                        Masukkan <strong>link langsung menuju file bukti</strong> di Google Drive
                        (misalnya file PDF, JPG, atau PNG), <strong>bukan link folder</strong>.
                        Hal ini bertujuan agar admin dapat langsung membuka dokumen bukti tanpa
                        harus mencari file di dalam folder Google Drive.

                        <br><br>

                        Pastikan akses file diatur menjadi
                        <strong>"Siapa saja yang memiliki link dapat melihat"</strong>.
                    </div>
                </div>

                <div class="ratio ratio-16x9 border rounded overflow-hidden">
                    <iframe src="https://www.youtube.com/embed/VIDEO_ID" title="Panduan Link Google Drive"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.skor-input').forEach(function(input) {

                // Saat diklik/focus, jika nilainya 0 maka dikosongkan
                input.addEventListener('focus', function() {
                    if (this.value === '0' || this.value === '0.00') {
                        this.value = '';
                    }
                });

                // Saat selesai input, jika kosong kembalikan ke 0
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.value = '0';
                    }
                });

            });
        });
    </script>
@endpush
