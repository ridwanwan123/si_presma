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
            margin-bottom: 1.5rem;
            padding: 1.5rem 1.75rem;
            border-radius: 16px;
            background: linear-gradient(120deg, #0f766e 0%, #198754 55%, #16a34a 100%);
            box-shadow: 0 10px 26px rgba(25, 135, 84, .18);
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
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: .3rem;
        }

        .tambah-header p {
            color: rgba(255, 255, 255, .9);
            max-width: 580px;
            text-align: left;
            margin-bottom: 0;
            font-size: .92rem;
            line-height: 1.5;
        }

        /* =========================
                   TIPS ALERT
                ========================= */

        .tips-alert {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .75rem 1.15rem;
            background: linear-gradient(90deg, #fff7e6 0%, #fffdf5 100%);
            border: 1px solid #fcd34d;
            border-radius: 14px;
            font-size: .85rem;
            color: #78350f;
            box-shadow: 0 3px 10px rgba(251, 191, 36, .12);
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
            margin-top: .05rem;
            box-shadow: 0 2px 6px rgba(251, 191, 36, .4);
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
            border-radius: 16px;
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
            transform: translateY(-4px);
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

        .choice-card-badge {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: .2rem .6rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .choice-card.card-primary .choice-card-badge {
            background: rgba(13, 110, 253, .1);
            color: #0d6efd;
        }

        .choice-card.card-success .choice-card-badge {
            background: rgba(25, 135, 84, .1);
            color: #198754;
        }

        .choice-icon {
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            margin-bottom: 1.1rem;
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
            font-size: .89rem;
            line-height: 1.6;
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

        /* =========================
                   MODAL IMPORT EXCEL
                ========================= */

        #modalImportExcel .modal-content {
            border: none;
            border-radius: 16px;
            overflow: hidden;
        }

        #modalImportExcel .modal-header {
            border-bottom: 1px solid #f1f3f5;
            padding: 1.35rem 2rem;
        }

        #modalImportExcel .modal-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: #0f172a;
        }

        #modalImportExcel .modal-body {
            padding: 1.75rem 2rem;
        }

        #modalImportExcel .modal-footer {
            padding: 1.1rem 2rem;
        }

        .step-label {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .8rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: .75rem;
        }

        .step-label .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #198754;
            color: #fff;
            font-size: .72rem;
            flex-shrink: 0;
        }

        .option-list .list-group-item {
            border: 1px solid #e9edf2;
            border-radius: 12px !important;
            margin-bottom: .6rem;
            padding: 1.1rem 1.3rem;
            transition: border-color .2s ease, background-color .2s ease;
        }

        .option-list .list-group-item:last-child {
            margin-bottom: 0;
        }

        .option-list .list-group-item:has(input:checked) {
            border-color: #198754;
            background-color: #f0fdf6;
        }

        .option-list .form-check-input {
            margin-top: .2rem;
        }

        .option-list strong {
            color: #0f172a;
        }

        .info-box {
            border-radius: 12px;
            font-size: .85rem;
            line-height: 1.55;
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
                font-size: 1.3rem;
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
                <strong>Anjuran penggunaan:</strong> gunakan <strong>Input Manual</strong> apabila jumlah data yang
                akan ditambahkan tidak banyak, atau <strong>Import Excel</strong> apabila data yang akan
                ditambahkan berjumlah besar.
            </span>
        </div>

        {{-- HEADER --}}
        <div class="tambah-header">
            <div class="header-icon">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <div class="header-text">
                <span class="eyebrow">Formulir Penambahan Data</span>
                <h2>Tambah Prestasi</h2>
                <p>Pilih metode penambahan data prestasi yang sesuai dengan kebutuhan Anda.</p>
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
                                Tambahkan data prestasi satu per satu melalui formulir isian. Metode ini sesuai
                                digunakan untuk penambahan data dalam jumlah terbatas.
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
                    <a href="#" class="choice-card-link" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                        <div class="choice-card card-success">
                            <div class="choice-icon icon-success">
                                <i class="bi bi-file-earmark-excel"></i>
                            </div>

                            <h5>Import Excel</h5>

                            <p>
                                Tambahkan data prestasi dalam jumlah besar sekaligus menggunakan templat Excel
                                yang telah disediakan oleh sistem.
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
{{--
            MODAL IMPORT EXCEL -- 1 MODAL, 2 LANGKAH
            ==========================================================
            Langkah 1: pilih TIPE TEMPLATE (Prestasi Siswa / Non-Prestasi).
            Langkah 2: daftar bidang muncul sesuai tipe yang dipilih --
            di-toggle pakai JS (show/hide div), BUKAN buka modal baru.

            GTK & Keagamaan SENGAJA muncul di KEDUA daftar bidang (baik
            tipe Prestasi Siswa maupun Non-Prestasi) -- itu bukan
            duplikat/salah, tapi 2 SUB-JENIS BEDA dari bidang yang sama
            (lihat RubrikPenilaianSeeder: GTK punya jenis_rubrik 'Lomba'
            DAN 'Karya' sekaligus, begitu juga Keagamaan punya 'Lomba'
            DAN 'Hafalan'). Tipe yang dipilih di Langkah 1 itulah yang
            menentukan sub-jenis mana yang dipakai.

            Semua radio 'bidang_prestasi' SENGAJA pakai name yang SAMA
            persis di kedua daftar (bukan dibedakan nama per tipe) --
            supaya cuma 1 yang bisa "checked" dalam satu waktu di
            seluruh form, radio HTML native yang jamin ini, bukan JS.
        --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('prestasi.import') }}" method="GET" id="formImportExcel">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- LANGKAH 1: TIPE TEMPLATE --}}
                    <div class="step-label">
                        <span class="step-number">1</span>
                        Jenis Prestasi yang Akan Diimpor
                    </div>
                    <div class="list-group option-list mb-4">
                        <label class="list-group-item">
                            <input class="form-check-input me-2" type="radio" name="tipe_template" value="lomba"
                                checked>
                            <strong>Prestasi Siswa</strong>
                            <span class="text-muted d-block small">
                                Kompetisi/lomba berjenjang dengan Juara atau Peringkat, meliputi bidang
                                Akademik, Non Akademik, GTK, dan Keagamaan.
                            </span>
                        </label>
                        <label class="list-group-item">
                            <input class="form-check-input me-2" type="radio" name="tipe_template" value="kriteria">
                            <strong>Non Prestasi</strong>
                            <span class="text-muted d-block small">
                                Bukan kompetisi berjenjang dan tidak memiliki Juara, meliputi GTK (karya
                                tulis/publikasi), Keagamaan (hafalan Al-Qur'an), dan Lembaga.
                            </span>
                        </label>
                    </div>

                    {{--
                                LANGKAH 2: BIDANG -- CUMA MUNCUL untuk tipe
                                Kriteria (Non-Prestasi). Untuk tipe Lomba
                                (Prestasi Siswa) TIDAK ADA langkah ini --
                                bidang_prestasi dibaca otomatis dari isi
                                tiap baris Excel oleh PrestasiImportService::
                                validateFile(), 1 file boleh berisi
                                campuran Akademik/Non Akademik/GTK/Keagamaan
                                sekaligus. Beda dari Template Kriteria yang
                                WAJIB 1 bidang per file (dropdown-nya
                                dikunci per bidang).

                                PENTING soal nama field: radio bidang di
                                bawah ini SENGAJA TIDAK bernama
                                "bidang_prestasi" langsung (dipakai
                                "_pilihan_bidang_kriteria") -- kalau
                                namanya sama, elemen ini TETAP IKUT
                                TERSUBMIT walau disembunyikan lewat class
                                'd-none' (CSS display:none tidak mencegah
                                input ikut ke-submit, beda dari atribut
                                'disabled'). Nilainya baru disalin ke 1
                                hidden field kanonik (#bidangPrestasiHidden)
                                lewat JS -- itu satu-satunya yang benar-
                                benar dikirim ke server.
                            --}}
                    <div id="infoLomba" class="pilihan-bidang">
                        <div class="alert alert-light border info-box mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Bidang prestasi tidak perlu dipilih pada tahap ini. Sistem akan membaca bidang
                            prestasi secara otomatis dari kolom <code>bidang_prestasi</code> pada file
                            Excel. Satu file dapat memuat kombinasi bidang Akademik, Non Akademik, GTK,
                            dan Keagamaan sekaligus.
                        </div>
                    </div>

                    <div id="pilihanBidangKriteria" class="pilihan-bidang d-none">
                        <div class="step-label">
                            <span class="step-number">2</span>
                            Pilih Bidang Penilaian
                        </div>
                        <div class="list-group option-list">
                            <label class="list-group-item">
                                <input class="form-check-input me-2" type="radio" name="_pilihan_bidang_kriteria"
                                    value="gtk" data-jenis="Karya">
                                <strong>GTK</strong>
                                <span class="text-muted"> Karya tulis/publikasi ilmiah</span>
                            </label>
                            <label class="list-group-item">
                                <input class="form-check-input me-2" type="radio" name="_pilihan_bidang_kriteria"
                                    value="keagamaan" data-jenis="Hafalan">
                                <strong>Keagamaan</strong>
                                <span class="text-muted"> Hafalan Al-Qur'an</span>
                            </label>
                            <label class="list-group-item">
                                <input class="form-check-input me-2" type="radio" name="_pilihan_bidang_kriteria"
                                    value="lembaga" data-jenis="Kelembagaan">
                                <strong>Lembaga</strong>
                                <span class="text-muted"> Kelembagaan</span>
                            </label>
                        </div>
                    </div>

                    {{-- SATU-SATUNYA field yang benar-benar dikirim ke server untuk
                                 bidang_prestasi & jenis_rubrik -- diisi/disinkronkan oleh JS. --}}
                    <input type="hidden" name="bidang_prestasi" id="bidangPrestasiHidden" value="">
                    <input type="hidden" name="jenis_rubrik" id="jenisRubrikHidden" value="Lomba">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        Lanjut ke Import <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
    <script>
        var bidangPrestasiHidden = document.getElementById('bidangPrestasiHidden');
        var jenisRubrikHidden = document.getElementById('jenisRubrikHidden');
        var pilihanBidangKriteria = document.getElementById('pilihanBidangKriteria');
        var infoLomba = document.getElementById('infoLomba');

        // Sinkronkan 2 hidden field kanonik berdasarkan state form SAAT
        // INI -- dipanggil di titik-titik penting (ganti tipe, ganti
        // radio bidang, DAN tepat sebelum submit) supaya hidden field
        // selalu benar apapun jalur user sampai ke sana, bukan bergantung
        // 1 event tunggal yang mungkin tidak terpicu di semua browser.
        function sinkronkanHiddenField() {
            var tipeDipilih = document.querySelector('input[name="tipe_template"]:checked').value;

            if (tipeDipilih === 'lomba') {
                bidangPrestasiHidden.value = '';
                jenisRubrikHidden.value = 'Lomba';
                return;
            }

            var bidangDipilih = document.querySelector('input[name="_pilihan_bidang_kriteria"]:checked');

            if (bidangDipilih) {
                bidangPrestasiHidden.value = bidangDipilih.value;
                jenisRubrikHidden.value = bidangDipilih.dataset.jenis;
            }
        }

        // LANGKAH 1 -> LANGKAH 2: toggle tampilan info Lomba vs daftar
        // bidang Kriteria. Begitu pindah ke Kriteria, radio bidang
        // pertama otomatis dicentang (biar user nggak wajib klik lagi
        // kalau memang mau opsi pertama).
        document.querySelectorAll('input[name="tipe_template"]').forEach(function(radioTipe) {
            radioTipe.addEventListener('change', function() {
                if (this.value === 'lomba') {
                    infoLomba.classList.remove('d-none');
                    pilihanBidangKriteria.classList.add('d-none');
                } else {
                    infoLomba.classList.add('d-none');
                    pilihanBidangKriteria.classList.remove('d-none');

                    var sudahAdaYangDicentang = document.querySelector(
                        'input[name="_pilihan_bidang_kriteria"]:checked');
                    if (!sudahAdaYangDicentang) {
                        var radioPertama = pilihanBidangKriteria.querySelector(
                            'input[name="_pilihan_bidang_kriteria"]');
                        if (radioPertama) {
                            radioPertama.checked = true;
                        }
                    }
                }

                sinkronkanHiddenField();
            });
        });

        document.querySelectorAll('input[name="_pilihan_bidang_kriteria"]').forEach(function(radioBidang) {
            radioBidang.addEventListener('change', sinkronkanHiddenField);
        });

        // Jaring pengaman terakhir -- sinkronkan sekali lagi tepat
        // sebelum submit, jaga-jaga ada perubahan yang tidak memicu
        // event 'change' di browser tertentu.
        document.getElementById('formImportExcel').addEventListener('submit', sinkronkanHiddenField);
    </script>
@endpush
