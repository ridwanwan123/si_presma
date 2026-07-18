@extends('layouts.base')

{{-- @section('title', $mode === 'edit' ? 'Edit Data Madrasah' : 'Tambah Data Madrasah') --}}

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

        .form-card {
            border: 1px solid #eef2f7;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .form-card-header {
            padding: 12px 16px;
            font-weight: 700;
            font-size: .9rem;
            border-bottom: 1px solid #eef2f7;
            background: #f8fafc;
            color: #0f172a;
        }

        .form-card-body {
            padding: 16px;
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

        /* RESPONSIVE */

        @media(max-width:768px) {

            .content-card {

                margin: 0 .5rem 1rem;

            }


            .content-card-body {

                padding: 1rem;

            }

        }

        #map {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        }

        #mapSearch {
            border-radius: 10px 0 0 10px;
        }

        #btnSearchMap {
            border-radius: 0 10px 10px 0;
            font-weight: 600;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1055 !important;
        }

        .cropper-container {
            z-index: 1060 !important;
        }

        .cropper-canvas,
        .cropper-crop-box,
        .cropper-drag-box,
        .cropper-face {
            z-index: 1065 !important;
        }

        .cropper-modal {
            background-color: rgba(0, 0, 0, 0.35) !important;
        }

        /* =========================
               PHOTO UPLOAD CARD
               (Logo / Foto Kamad / Foto KTU)
            ========================= */

        .mp-upload-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .mp-upload-frame {
            position: relative;
            width: 96px;
            height: 96px;
            flex-shrink: 0;
            overflow: hidden;

            border-radius: 14px;
            border: 1.5px dashed #cbd5e1;
            background: #f8fafc;

            display: flex;
            align-items: center;
            justify-content: center;

            transition: border-color .25s, box-shadow .25s;
        }

        .mp-upload-frame--round {
            border-radius: 50%;
        }

        .mp-upload-frame:hover {
            border-color: #38bdf8;
            border-style: solid;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, .12);
        }

        .mp-upload-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mp-upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .3rem;
            padding: .4rem;
            text-align: center;
            color: #94a3b8;
        }

        .mp-upload-placeholder span {
            font-size: .62rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .mp-upload-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            cursor: pointer;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .25rem;

            background: rgba(15, 23, 42, .74);
            backdrop-filter: blur(1px);
            color: #fff;

            opacity: 0;
            transition: opacity .25s;
        }

        .mp-upload-frame:hover .mp-upload-overlay,
        .mp-upload-frame:focus-within .mp-upload-overlay {
            opacity: 1;
        }

        .mp-upload-overlay span {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .mp-upload-overlay input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .mp-upload-meta {
            padding-top: .25rem;
        }

        .mp-upload-meta .form-label {
            margin-bottom: .3rem;
        }

        .mp-upload-hint {
            margin: 0;
            font-size: .76rem;
            color: #94a3b8;
        }
    </style>
@endpush

@section('breadcrumb')
    <div class="breadcrumb-modern">

        <div class="crumb">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-house-door-fill home-icon"></i>
                Home
            </a>
        </div>

        <span class="separator">
            <i class="bi bi-chevron-right"></i>
        </span>

        <div class="crumb">
            <a href="{{ route('madrasah.index') }}">
                Data Madrasah
            </a>
        </div>

        <span class="separator">
            <i class="bi bi-chevron-right"></i>
        </span>

        <div class="active">
            {{ $mode === 'edit' ? 'Edit Data' : 'Tambah Data' }}
        </div>

    </div>
@endsection

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>
                {{ $mode === 'edit' ? 'Edit Data Madrasah' : 'Tambah Data Madrasah' }}
            </h2>

            <p>
                {{ $mode === 'edit' ? 'Perbarui data madrasah.' : 'Input data lengkap madrasah baru.' }}
            </p>
        </div>


        <div class="content-card">

            {{-- HEADER --}}
            <div class="table-header">
                <div>
                    <h6 class="mb-0">Form Data Madrasah</h6>
                    <small class="text-muted">
                        Lengkapi data madrasah dengan benar
                    </small>
                </div>
            </div>


            <div class="content-card-body">

                <form action="{{ $mode === 'edit' ? route('madrasah.update', $madrasah->id) : route('madrasah.store') }}"
                    method="POST" enctype="multipart/form-data">

                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif


                    {{-- =========================
                        PROFILE MADRASAH
                    ========================= --}}
                    <div class="form-card mb-4">

                        <div class="form-card-header">
                            Profile Madrasah
                        </div>

                        <div class="form-card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Jenjang Madrasah <span class="text-danger">*</span>
                                    </label>
                                    <select name="jenjang_madrasah" class="form-select" required>
                                        <option value="">Pilih Jenjang</option>

                                        @foreach (['RA', 'MI', 'MTs', 'MA'] as $jenjang)
                                            <option value="{{ $jenjang }}"
                                                {{ old('jenjang_madrasah', $madrasah->jenjang_madrasah ?? '') == $jenjang ? 'selected' : '' }}>
                                                {{ $jenjang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Status Madrasah <span class="text-danger">*</span>
                                    </label>
                                    <select name="status_madrasah" class="form-select" required>
                                        <option value="">Pilih Status</option>

                                        @foreach (['Negeri', 'Swasta'] as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status_madrasah', $madrasah->status_madrasah ?? '') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nama Madrasah <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nama_madrasah" class="form-control" required
                                        value="{{ old('nama_madrasah', $madrasah->nama_madrasah ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        NPSN <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="npsn" class="form-control" maxlength="8" required
                                        value="{{ old('npsn', $madrasah->npsn ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Akreditasi
                                    </label>
                                    <select name="akreditasi" class="form-select">
                                        <option value="">Pilih</option>

                                        @foreach (['A', 'B', 'C'] as $ak)
                                            <option value="{{ $ak }}"
                                                {{ old('akreditasi', $madrasah->akreditasi ?? '') == $ak ? 'selected' : '' }}>
                                                {{ $ak }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Logo Madrasah</label>

                                    <div class="mp-upload-card">
                                        <div class="mp-upload-frame">
                                            <img id="previewLogo" class="mp-upload-img"
                                                style="display:{{ $mode == 'edit' && $madrasah->logo ? 'block' : 'none' }};"
                                                src="{{ $mode == 'edit' && $madrasah->logo ? asset('storage/' . $madrasah->logo) : '' }}">

                                            <div id="previewLogo_placeholder" class="mp-upload-placeholder"
                                                style="display:{{ $mode == 'edit' && $madrasah->logo ? 'none' : 'flex' }};">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                    <path d="M21 15l-5-5L5 21"></path>
                                                </svg>
                                                <span>Belum ada logo</span>
                                            </div>

                                            <label class="mp-upload-overlay">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z">
                                                    </path>
                                                </svg>
                                                <span>Ganti</span>
                                                <input type="file" class="crop-image-input" data-target="logo_cropped"
                                                    data-preview="previewLogo" data-title="Crop Logo Madrasah"
                                                    accept="image/*">
                                            </label>
                                        </div>

                                        <div class="mp-upload-meta">
                                            <p class="mp-upload-hint">Format PNG/JPG, disarankan rasio 1:1.</p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="logo_cropped" id="logo_cropped">
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- =========================
                        LOKASI
                    ========================= --}}
                    <div class="form-card mb-4">

                        <div class="form-card-header">
                            Lokasi Madrasah
                        </div>

                        <div class="form-card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" name="provinsi" class="form-control"
                                        value="{{ old('provinsi', $madrasah->provinsi ?? '') }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kota</label>
                                    <input type="text" name="kota" class="form-control"
                                        value="{{ old('kota', $madrasah->kota ?? '') }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" name="kecamatan" class="form-control"
                                        value="{{ old('kecamatan', $madrasah->kecamatan ?? '') }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" name="kelurahan" class="form-control"
                                        value="{{ old('kelurahan', $madrasah->kelurahan ?? '') }}" readonly>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-card mb-4">

                        <div class="form-card-header d-flex justify-content-between align-items-center">
                            <span>Lokasi Madrasah</span>

                            <button type="button" class="btn btn-sm btn-warning" id="btnEditLokasi">
                                Edit Lokasi
                            </button>
                        </div>

                        <div class="form-card-body">

                            <!-- LOCK NOTICE -->
                            <div id="lockNotice" class="alert alert-info">
                                Lokasi terkunci. Klik <b>Edit Lokasi</b> untuk mengubah.
                            </div>

                            <div class="row g-3" id="lokasiWrapper">

                                <div class="col-12">
                                    <label class="form-label">Alamat Sekolah</label>
                                    <textarea name="alamat_sekolah" rows="3" class="form-control lokasi-input" readonly>{{ old('alamat_sekolah', $madrasah->alamat_sekolah ?? '') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="latitude" class="form-control lokasi-input" readonly
                                        value="{{ old('latitude', $madrasah->latitude ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="longitude" class="form-control lokasi-input" readonly
                                        value="{{ old('longitude', $madrasah->longitude ?? '') }}">
                                </div>

                                <div class="col-12 mt-2">

                                    <label class="form-label">Lokasi di Peta</label>

                                    <div class="position-relative mb-2">
                                        <input type="text" id="mapSearch" class="form-control lokasi-input" disabled
                                            placeholder="Cari alamat madrasah...">

                                        <div id="mapSuggestions" class="list-group position-absolute w-100"
                                            style="z-index: 999; max-height: 250px; overflow-y: auto;">
                                        </div>
                                    </div>

                                    <div id="map"
                                        style="height: 350px; border-radius: 12px; border: 1px solid #ddd; opacity: 0.5; pointer-events: none;">
                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        Lokasi terkunci. Klik "Edit Lokasi" untuk mengubah.
                                    </small>

                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- =========================
                        KAMAD
                    ========================= --}}
                    <div class="form-card mb-4">

                        <div class="form-card-header">
                            Kepala Madrasah (Kamad)
                        </div>

                        <div class="form-card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Nama Kamad <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_kepala_madrasah" class="form-control" required
                                        value="{{ old('nama_kepala_madrasah', $madrasah->nama_kepala_madrasah ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">NIP Kamad</label>
                                    <input type="text" name="nip_kepala_madrasah" class="form-control"
                                        value="{{ old('nip_kepala_madrasah', $madrasah->nip_kepala_madrasah ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No Telepon Kamad</label>
                                    <input type="text" name="no_telepon_kamad" class="form-control"
                                        value="{{ old('no_telepon_kamad', $madrasah->no_telepon_kamad ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Foto Kamad</label>

                                    <div class="mp-upload-card">
                                        <div class="mp-upload-frame mp-upload-frame--round">
                                            <img id="previewKamad" class="mp-upload-img"
                                                style="display:{{ $mode == 'edit' && $madrasah->foto_kamad ? 'block' : 'none' }};"
                                                src="{{ $mode == 'edit' && $madrasah->foto_kamad ? asset('storage/' . $madrasah->foto_kamad) : '' }}">

                                            <div id="previewKamad_placeholder" class="mp-upload-placeholder"
                                                style="display:{{ $mode == 'edit' && $madrasah->foto_kamad ? 'none' : 'flex' }};">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <span>Belum ada foto</span>
                                            </div>

                                            <label class="mp-upload-overlay">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z">
                                                    </path>
                                                </svg>
                                                <span>Ganti</span>
                                                <input type="file" class="crop-image-input"
                                                    data-target="foto_kamad_cropped" data-preview="previewKamad"
                                                    data-title="Crop Foto Kamad" accept="image/*">
                                            </label>
                                        </div>

                                        <div class="mp-upload-meta">
                                            <p class="mp-upload-hint">Format PNG/JPG, foto wajah jelas.</p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="foto_kamad_cropped" id="foto_kamad_cropped">
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- =========================
                        KTU
                    ========================= --}}
                    <div class="form-card mb-4">

                        <div class="form-card-header">
                            Kepala TU (KTU)
                        </div>

                        <div class="form-card-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Nama KTU</label>
                                    <input type="text" name="nama_kepala_urusan_tata_usaha" class="form-control"
                                        value="{{ old('nama_kepala_urusan_tata_usaha', $madrasah->nama_kepala_urusan_tata_usaha ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">NIP KTU</label>
                                    <input type="text" name="nip_kepala_urusan_tata_usaha" class="form-control"
                                        value="{{ old('nip_kepala_urusan_tata_usaha', $madrasah->nip_kepala_urusan_tata_usaha ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No Telepon KTU</label>
                                    <input type="text" name="no_telepon_katu" class="form-control"
                                        value="{{ old('no_telepon_katu', $madrasah->no_telepon_katu ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Foto KTU</label>

                                    <div class="mp-upload-card">
                                        <div class="mp-upload-frame mp-upload-frame--round">
                                            <img id="previewKatu" class="mp-upload-img"
                                                style="display:{{ $mode == 'edit' && $madrasah->foto_katu ? 'block' : 'none' }};"
                                                src="{{ $mode == 'edit' && $madrasah->foto_katu ? asset('storage/' . $madrasah->foto_katu) : '' }}">

                                            <div id="previewKatu_placeholder" class="mp-upload-placeholder"
                                                style="display:{{ $mode == 'edit' && $madrasah->foto_katu ? 'none' : 'flex' }};">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                                <span>Belum ada foto</span>
                                            </div>

                                            <label class="mp-upload-overlay">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z">
                                                    </path>
                                                </svg>
                                                <span>Ganti</span>
                                                <input type="file" class="crop-image-input"
                                                    data-target="foto_katu_cropped" data-preview="previewKatu"
                                                    data-title="Crop Foto KTU" accept="image/*">
                                            </label>
                                        </div>

                                        <div class="mp-upload-meta">
                                            <p class="mp-upload-hint">Format PNG/JPG, foto wajah jelas.</p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="foto_katu_cropped" id="foto_katu_cropped">
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- BUTTON --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('madrasah.index') }}" class="btn btn-light">
                            Kembali
                        </a>

                        <button class="btn btn-success">
                            {{ $mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Data' }}
                        </button>
                    </div>

                </form>


            </div>
        </div>



    </main>
@endsection
<div class="modal fade" id="confirmLokasiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Edit Lokasi</h5>
            </div>

            <div class="modal-body">
                Yakin ingin mengubah lokasi madrasah?
                Setelah diubah, koordinat harus diverifikasi ulang.
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger" id="confirmEditLokasi">Ya, Edit</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalTitle">
                    Crop Gambar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                <div style="max-height:450px">
                    <img id="cropImage" style="max-width:100%;display:block">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button id="cropBtn" class="btn btn-success">
                    Simpan Crop
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        // kONFIRM update lokasi
        let lokasiUnlocked = false;

        document.getElementById('btnEditLokasi').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('confirmLokasiModal'));
            modal.show();
        });

        document.getElementById('confirmEditLokasi').addEventListener('click', function() {

            lokasiUnlocked = true;

            // enable input
            document.querySelectorAll('.lokasi-input').forEach(el => {
                el.removeAttribute('readonly');
                el.removeAttribute('disabled');
            });

            // enable map
            const mapEl = document.getElementById('map');
            mapEl.style.opacity = "1";
            mapEl.style.pointerEvents = "auto";

            // hide notice
            document.getElementById('lockNotice').style.display = 'none';

            bootstrap.Modal.getInstance(document.getElementById('confirmLokasiModal')).hide();
        });

        // ==========================================
        // GENERIC IMAGE CROPPER
        // ==========================================

        let cropper = null;
        let imageURL = null;
        let currentTarget = null;

        const cropModalEl = document.getElementById('cropModal');
        const cropModal = new bootstrap.Modal(cropModalEl, {
            backdrop: 'static',
            keyboard: false
        });

        const cropImage = document.getElementById('cropImage');
        const cropTitle = document.getElementById('cropModalTitle');
        const cropBtn = document.getElementById('cropBtn');

        // ==========================================
        // OPEN CROPPER
        // ==========================================

        document.querySelectorAll('.crop-image-input').forEach(input => {

            input.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (!file) return;

                // target hidden input
                currentTarget = document.getElementById(
                    this.dataset.target
                );

                // judul modal
                cropTitle.innerText = this.dataset.title ?? 'Crop Gambar';

                if (imageURL) {
                    URL.revokeObjectURL(imageURL);
                }

                imageURL = URL.createObjectURL(file);

                cropImage.src = imageURL;

                cropModal.show();

            });

        });


        // ==========================================
        // INIT CROPPER
        // ==========================================

        cropModalEl.addEventListener('shown.bs.modal', function() {

            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                responsive: true,
                background: false,
                restore: false
            });

        });


        // ==========================================
        // SAVE RESULT
        // ==========================================

        cropBtn.addEventListener('click', function() {

            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            canvas.toBlob(function(blob) {

                const reader = new FileReader();

                reader.onloadend = function() {

                    // SET VALUE KE HIDDEN INPUT
                    if (currentTarget) {
                        currentTarget.value = reader.result;
                    }

                    // SET PREVIEW IMAGE
                    const previewId = document
                        .querySelector('.crop-image-input[data-target="' + currentTarget.id + '"]')
                        ?.dataset.preview;

                    if (previewId) {
                        const previewEl = document.getElementById(previewId);

                        if (previewEl) {
                            previewEl.src = reader.result;
                            previewEl.style.display = 'block';

                            const placeholderEl = document.getElementById(previewId + '_placeholder');
                            if (placeholderEl) {
                                placeholderEl.style.display = 'none';
                            }
                        }
                    }
                };

                reader.readAsDataURL(blob);

                cropModal.hide();

            }, 'image/png');

        });


        // ==========================================
        // CLEANUP
        // ==========================================

        cropModalEl.addEventListener('hidden.bs.modal', function() {

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            if (imageURL) {
                URL.revokeObjectURL(imageURL);
                imageURL = null;
            }

            cropImage.src = '';

            currentTarget = null;

        });

        // ==========================================

        // ==========================================
        // INIT MAP
        // ==========================================
        let map;
        let marker;
        let searchTimeout;

        // default koordinat
        let defaultLat = Number({{ $madrasah->latitude ?? -6.2 }});
        let defaultLng = Number({{ $madrasah->longitude ?? 106.816666 }});

        document.addEventListener("DOMContentLoaded", function() {

            // ======================
            // INIT MAP
            // ======================
            map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // marker
            marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            setLatLng(defaultLat, defaultLng);

            // ======================
            // CLICK MAP
            // ======================
            map.on('click', function(e) {
                moveMarker(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            // ======================
            // DRAG MARKER
            // ======================
            marker.on('dragend', function() {
                let pos = marker.getLatLng();
                setLatLng(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });

            // ======================
            // AUTOCOMPLETE INPUT
            // ======================
            const searchInput = document.getElementById('mapSearch');

            searchInput.addEventListener('input', function() {

                let query = this.value;

                clearTimeout(searchTimeout);

                if (query.length < 3) {
                    document.getElementById('mapSuggestions').innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 300);

            });

            // klik luar untuk close dropdown
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#mapSearch')) {
                    document.getElementById('mapSuggestions').innerHTML = '';
                }
            });

        });


        // ======================
        // MOVE MARKER
        // ======================
        function moveMarker(lat, lng) {

            lat = Number(lat);
            lng = Number(lng);

            if (isNaN(lat) || isNaN(lng)) return;

            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 15);

            setLatLng(lat, lng);
        }


        // ======================
        // SET LAT LNG INPUT
        // ======================
        function setLatLng(lat, lng) {

            lat = Number(lat);
            lng = Number(lng);

            if (isNaN(lat) || isNaN(lng)) return;

            const latInput = document.querySelector('[name="latitude"]');
            const lngInput = document.querySelector('[name="longitude"]');

            if (latInput && lngInput) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }
        }


        // ======================
        // AUTOCOMPLETE SEARCH
        // ======================
        function fetchSuggestions(query) {

            fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {

                    let box = document.getElementById('mapSuggestions');
                    box.innerHTML = '';

                    if (!data.length) return;

                    data.forEach(item => {

                        let div = document.createElement('div');

                        div.className = "list-group-item list-group-item-action";
                        div.style.cursor = "pointer";
                        div.style.fontSize = "0.85rem";

                        div.innerHTML = `
                            <div class="fw-semibold">${item.display_name}</div>
                        `;

                        div.addEventListener('click', function() {

                            let lat = Number(item.lat);
                            let lon = Number(item.lon);

                            moveMarker(lat, lon);
                            reverseGeocode(lat, lon);

                            document.getElementById('mapSearch').value = item.display_name;
                            box.innerHTML = '';

                        });

                        box.appendChild(div);

                    });

                })
                .catch(err => console.log(err));

        }


        // ======================
        // REVERSE GEOCODING
        // ======================
        function reverseGeocode(lat, lng) {

            lat = Number(lat);
            lng = Number(lng);

            if (isNaN(lat) || isNaN(lng)) return;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {

                    if (data?.display_name) {
                        let alamat = document.querySelector('[name="alamat_sekolah"]');
                        if (alamat) {
                            alamat.value = data.display_name;
                        }
                    }

                })
                .catch(err => console.log("Reverse error:", err));

        }
    </script>
@endpush
