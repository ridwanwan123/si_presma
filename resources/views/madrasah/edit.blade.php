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
            Tambah Data
        </div>

    </div>
@endsection

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Tambah Data Madrasah</h2>
            <p>Input data lengkap madrasah baru.</p>
        </div>


        <div class="content-card">


            <div class="table-header">
                <h6>
                    Form Data Madrasah
                </h6>
            </div>


            <div class="content-card-body">


                <form action="{{ route('madrasah.update', $madrasah->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="row g-3">


                        {{-- Jenjang --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Jenjang Madrasah
                            </label>

                            <select name="jenjang_madrasah"
                                class="form-select @error('jenjang_madrasah') is-invalid @enderror">

                                <option value="">
                                    Pilih Jenjang
                                </option>

                                <option value="RA"
                                    {{ old('jenjang_madrasah', $madrasah->jenjang_madrasah) == 'RA' ? 'selected' : '' }}>
                                    RA
                                </option>

                                <option value="MI"
                                    {{ old('jenjang_madrasah', $madrasah->jenjang_madrasah) == 'MI' ? 'selected' : '' }}>
                                    MI
                                </option>

                                <option value="MTs"
                                    {{ old('jenjang_madrasah', $madrasah->jenjang_madrasah) == 'MTs' ? 'selected' : '' }}>
                                    MTs
                                </option>

                                <option value="MA"
                                    {{ old('jenjang_madrasah', $madrasah->jenjang_madrasah) == 'MA' ? 'selected' : '' }}>
                                    MA
                                </option>

                            </select>
                            @error('jenjang_madrasah')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Nama Madrasah --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Madrasah
                            </label>

                            <input type="text" name="nama_madrasah"
                                value="{{ old('nama_madrasah', $madrasah->nama_madrasah) }}"
                                class="form-control @error('nama_madrasah') is-invalid @enderror">

                            @error('nama_madrasah')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- NPSN --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                NPSN
                            </label>

                            <input type="text" name="npsn" maxlength="8" value="{{ old('npsn', $madrasah->npsn) }}"
                                class="form-control @error('npsn') is-invalid @enderror">

                            @error('npsn')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Akreditasi --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Akreditasi
                            </label>

                            <select name="akreditasi" class="form-select @error('akreditasi') is-invalid @enderror">
                                <option value="">
                                    Pilih Akreditasi
                                </option>

                                <option value="A"
                                    {{ old('akreditasi', $madrasah->akreditasi) == 'A' ? 'selected' : '' }}>
                                    A
                                </option>

                                <option value="B"
                                    {{ old('akreditasi', $madrasah->akreditasi) == 'B' ? 'selected' : '' }}>
                                    B
                                </option>

                                <option value="C"
                                    {{ old('akreditasi', $madrasah->akreditasi) == 'C' ? 'selected' : '' }}>
                                    C
                                </option>

                                <option value="belum_diakreditasi"
                                    {{ old('akreditasi', $madrasah->akreditasi) == 'belum_diakreditasi' ? 'selected' : '' }}>
                                    Belum Diakreditasi
                                </option>

                            </select>

                            @error('akreditasi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Kota --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Kota/Kabupaten
                            </label>

                            <select name="kota" class="form-select @error('kota') is-invalid @enderror">

                                <option value="">
                                    Pilih Kota/Kabupaten
                                </option>

                                <option value="Jakarta Timur"
                                    {{ old('kota', $madrasah->kota) == 'Jakarta Timur' ? 'selected' : '' }}>
                                    Jakarta Timur
                                </option>

                                <option value="Jakarta Barat"
                                    {{ old('kota', $madrasah->kota) == 'Jakarta Barat' ? 'selected' : '' }}>
                                    Jakarta Barat
                                </option>

                                <option value="Jakarta Selatan"
                                    {{ old('kota', $madrasah->kota) == 'Jakarta Selatan' ? 'selected' : '' }}>
                                    Jakarta Selatan
                                </option>

                                <option value="Jakarta Utara"
                                    {{ old('kota', $madrasah->kota) == 'Jakarta Utara' ? 'selected' : '' }}>
                                    Jakarta Utara
                                </option>

                                <option value="Jakarta Pusat"
                                    {{ old('kota', $madrasah->kota) == 'Jakarta Pusat' ? 'selected' : '' }}>
                                    Jakarta Pusat
                                </option>

                                <option value="Kepulauan Seribu"
                                    {{ old('kota', $madrasah->kota) == 'Kepulauan Seribu' ? 'selected' : '' }}>
                                    Kepulauan Seribu
                                </option>

                            </select>
                            @error('kota')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Provinsi --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Provinsi
                            </label>

                            <input type="text" name="provinsi" value="{{ old('provinsi', $madrasah->provinsi) }}"
                                readonly class="form-control">

                            @error('provinsi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Alamat --}}
                        <div class="col-12">

                            <label class="form-label">
                                Alamat Sekolah
                            </label>

                            <textarea name="alamat_sekolah" rows="4" class="form-control @error('alamat_sekolah') is-invalid @enderror"
                                placeholder="Alamat lengkap sekolah">{{ old('alamat_sekolah', $madrasah->alamat_sekolah) }}</textarea>

                            @error('alamat_sekolah')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- Kepala Madrasah --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Kepala Madrasah
                            </label>

                            <input type="text" name="nama_kepala_madrasah"
                                value="{{ old('nama_kepala_madrasah', $madrasah->nama_kepala_madrasah) }}"
                                class="form-control">

                            @error('nama_kepala_madrasah')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">
                                NIP Kepala Madrasah
                            </label>

                            <input type="text" name="nip_kepala_madrasah" maxlength="18"
                                value="{{ old('nip_kepala_madrasah', $madrasah->nip_kepala_madrasah) }}"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control">

                            @error('nip_kepala_madrasah')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        {{-- TU --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Nama Kepala Urusan Tata Usaha
                            </label>

                            <input type="text" name="nama_kepala_urusan_tata_usaha"
                                value="{{ old('nama_kepala_urusan_tata_usaha', $madrasah->nama_kepala_urusan_tata_usaha) }}"
                                class="form-control @error('nama_kepala_urusan_tata_usaha') is-invalid @enderror"
                                placeholder="Nama kepala urusan TU">

                            @error('nama_kepala_urusan_tata_usaha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>



                        <div class="col-md-6">

                            <label class="form-label">
                                NIP Kepala Urusan Tata Usaha
                            </label>

                            <input type="text" name="nip_kepala_urusan_tata_usaha"
                                value="{{ old('nip_kepala_urusan_tata_usaha', $madrasah->nip_kepala_urusan_tata_usaha) }}"
                                maxlength="18" pattern="[0-9]{18}" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                class="form-control @error('nip_kepala_urusan_tata_usaha') is-invalid @enderror"
                                placeholder="NIP Kepala Urusan Tata Usaha">

                            @error('nip_kepala_urusan_tata_usaha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('madrasah.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>


            </div>


        </div>


    </main>
@endsection
