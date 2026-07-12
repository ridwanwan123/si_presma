<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Penugasan Asesor</title>
    <style>
        /* ==========================================================
           DOMPDF-SAFE RESET
           - Tidak pakai flexbox/grid, seluruh layout memakai <table>.
           - Font DejaVu Sans (default DomPDF, aman untuk karakter id).
           ========================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1c1c1c;
        }

        /* ==========================================================
           PENGATURAN HALAMAN A4 - margin cukup di semua sisi
           Catatan: sesuaikan nilai margin-top jika tinggi kop surat
           berubah setelah logo/alamat instansi diganti dengan data asli.
           ========================================================== */
        @page {
            margin: 120px 34px 56px 34px;

            header: page-header;

            @bottom-left {
                content: "Dicetak: {{ $tanggalCetak }}";
                font-size: 8px;
                color: #6b7785;
            }

            @bottom-right {
                content: "Halaman " counter(page) " dari " counter(pages);
                font-size: 8px;
                color: #6b7785;
            }
        }

        /* ==========================================================
           KOP SURAT (muncul berulang di setiap halaman)
           ========================================================== */
        #page-header {
            position: running(page-header);
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo-cell {
            width: 66px;
            vertical-align: top;
            padding-top: 2px;
        }

        .kop-logo-cell img {
            width: 58px;
            height: 58px;
        }

        .kop-text-cell {
            vertical-align: top;
            text-align: center;
        }

        .kop-spacer-cell {
            width: 66px;
        }

        .kop-instansi-utama {
            font-size: 14px;
            font-weight: bold;
            color: #14532d;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .kop-instansi-sub {
            font-size: 11px;
            font-weight: bold;
            color: #1c1c1c;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .kop-instansi-unit {
            font-size: 9.5px;
            font-weight: bold;
            color: #1c1c1c;
            margin-top: 1px;
        }

        .kop-alamat {
            font-size: 8px;
            color: #6b7785;
            margin-top: 3px;
            line-height: 1.4;
        }

        .kop-divider-thick {
            border-bottom: 2.2px solid #14532d;
            margin-top: 9px;
            margin-bottom: 2px;
        }

        .kop-divider-thin {
            border-bottom: 1px solid #14532d;
        }

        /* ==========================================================
           JUDUL LAPORAN
           ========================================================== */
        .report-title-wrap {
            text-align: center;
            margin-top: 18px;
            margin-bottom: 16px;
        }

        .report-title {
            font-size: 19px;
            font-weight: bold;
            color: #14532d;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .report-title-underline {
            width: 130px;
            border-bottom: 1.5px solid #14532d;
            margin: 6px auto 0 auto;
        }

        /* ==========================================================
           TABEL INFORMASI CETAK (identitas dokumen)
           ========================================================== */
        table.identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table.identity-table td {
            width: 33.33%;
            border: 1px solid #cfd4da;
            padding: 8px 10px;
            vertical-align: top;
            background-color: #ffffff;
        }

        .identity-label {
            font-size: 8px;
            color: #6b7785;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .identity-value {
            font-size: 11px;
            font-weight: bold;
            color: #1c1c1c;
            margin-top: 3px;
        }

        /* ==========================================================
           SECTION TITLE (label sebelum tiap blok tabel)
           ========================================================== */
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #14532d;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid #14532d;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        /* ==========================================================
           TABEL RINGKASAN DATA (pengganti summary card dashboard)
           ========================================================== */
        table.ringkasan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.ringkasan-table td {
            width: 25%;
            border: 1px solid #cfd4da;
            padding: 9px 10px;
            vertical-align: top;
            background-color: #ffffff;
        }

        .ringkasan-label {
            font-size: 8px;
            color: #6b7785;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .ringkasan-value {
            font-size: 13px;
            font-weight: bold;
            color: #1c1c1c;
            margin-top: 3px;
        }

        .ringkasan-value.text-success {
            color: #14532d;
        }

        .ringkasan-value.text-warning {
            color: #4b5563;
        }

        /* ==========================================================
           TABEL DATA UTAMA
           ========================================================== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data-table thead {
            display: table-header-group;
        }

        table.data-table tr {
            page-break-inside: avoid;
        }

        table.data-table th {
            background-color: #14532d;
            color: #ffffff;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #0d3d21;
        }

        table.data-table td {
            font-size: 9px;
            padding: 6px 6px;
            border: 1px solid #d8dbe0;
            vertical-align: top;
        }

        table.data-table tbody tr:nth-child(even) td {
            background-color: #f7f8f9;
        }

        .col-no {
            width: 4%;
            text-align: center;
        }

        .col-npsn {
            width: 10%;
        }

        .col-jenjang {
            width: 7%;
            text-align: center;
        }

        .col-wilayah {
            width: 13%;
        }

        .col-prestasi {
            width: 7%;
            text-align: center;
        }

        .col-asesor {
            width: 15%;
            font-weight: bold;
            /* background-color: #f0f3f1 !important; */
        }

        .col-jumlah {
            width: 6%;
            text-align: center;
            font-weight: bold;
            /* background-color: #f0f3f1 !important; */
            color: #14532d;
        }

        .col-madrasah {
            width: 28%;
        }

        /* Pemisah antar blok asesor pada tabel yang di-merge (rowspan) */
        table.data-table tbody tr.group-start td {
            border-top: 1.5px solid #9fb3a6;
        }

        /* ==========================================================
           RINGKASAN BEBAN ASESOR (tabel, tanpa progress bar)
           ========================================================== */
        table.beban-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.beban-table th {
            background-color: #14532d;
            color: #ffffff;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px;
            text-align: left;
            border: 1px solid #0d3d21;
        }

        table.beban-table td {
            font-size: 9px;
            padding: 5px 6px;
            border: 1px solid #d8dbe0;
            vertical-align: middle;
        }

        table.beban-table tbody tr:nth-child(even) td {
            background-color: #f7f8f9;
        }

        .beban-col-no {
            width: 6%;
            text-align: center;
        }

        .beban-col-jumlah {
            width: 14%;
            text-align: center;
            font-weight: bold;
            color: #14532d;
        }

        .beban-col-proporsi {
            width: 16%;
            text-align: right;
            font-weight: bold;
            color: #14532d;
        }

        .col-status {
            width: 13%;
            text-align: center;
        }

        .madrasah-nama {
            font-weight: bold;
        }

        .text-muted {
            color: #8b93a1;
        }

        /* ==========================================================
           RINGKASAN AKHIR / PENUTUP
           ========================================================== */
        .summary-footer {
            margin-top: 14px;
            font-size: 9px;
            color: #6b7785;
            border-top: 1px solid #d8dbe0;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    {{-- ==========================================================
         KOP SURAT (running header - berulang di setiap halaman)
    =========================================================== --}}
    <div id="page-header">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    <img src="{{ public_path('images/logo-kemenag.png') }}" alt="Logo Kementerian Agama">
                </td>
                <td class="kop-text-cell">
                    <div class="kop-instansi-utama">Kementerian Agama Republik Indonesia</div>
                    <div class="kop-instansi-sub">Kantor Wilayah Kementerian Agama Provinsi DKI Jakarta</div>
                    <div class="kop-instansi-unit">Bidang Pendidikan Madrasah</div>
                    <div class="kop-instansi-unit">Jakarta Madrasah Awards (JMA)</div>
                    <div class="kop-alamat">
                        Jl. IPN No. 6, Cempaka Putih Timur, Jakarta Pusat 10510 &mdash; Telp. (021) 4211050
                    </div>
                </td>
                <td class="kop-spacer-cell"></td>
            </tr>
        </table>
        <div class="kop-divider-thick"></div>
        <div class="kop-divider-thin"></div>
    </div>

    {{-- ==========================================================
         JUDUL LAPORAN
    =========================================================== --}}
    <div class="report-title-wrap">
        <div class="report-title">Laporan Penugasan Asesor</div>
        <div class="report-title-underline"></div>
    </div>

    {{-- ==========================================================
         INFORMASI CETAK
    =========================================================== --}}
    <table class="identity-table">
        <tr>
            <td>
                <div class="identity-label">Tanggal Cetak</div>
                <div class="identity-value">{{ $tanggalCetak }}</div>
            </td>
            <td>
                <div class="identity-label">Dicetak Oleh</div>
                <div class="identity-value">{{ $dicetakOleh }}</div>
            </td>
            <td>
                <div class="identity-label">Total Data</div>
                <div class="identity-value">{{ $totalMadrasah }} Madrasah</div>
            </td>
        </tr>
    </table>

    {{-- ==========================================================
         RINGKASAN DATA (pengganti summary card dashboard)
    =========================================================== --}}
    <div class="section-title"> Ringkasan Data</div>
    <table class="ringkasan-table">
        <tr>
            <td>
                <div class="ringkasan-label">Total Madrasah</div>
                <div class="ringkasan-value">{{ $totalMadrasah }}</div>
            </td>
            <td>
                <div class="ringkasan-label">Total Asesor</div>
                <div class="ringkasan-value">{{ $totalAsesor }}</div>
            </td>
            <td>
                <div class="ringkasan-label">Sudah Assigned</div>
                <div class="ringkasan-value text-success">{{ $sudahAssigned }}</div>
            </td>
            <td>
                <div class="ringkasan-label">Belum Assigned</div>
                <div class="ringkasan-value text-warning">{{ $belumAssigned }}</div>
            </td>
        </tr>
    </table>

    {{-- ==========================================================
         RINGKASAN BEBAN ASESOR
    =========================================================== --}}
    <div class="section-title">Ringkasan Beban Asesor</div>
    <table class="beban-table">
        <thead>
            <tr>
                <th class="beban-col-no">No</th>
                <th>Nama Asesor</th>
                <th class="beban-col-jumlah">Jumlah Madrasah</th>
                <th class="beban-col-proporsi">Proporsi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxBeban = $bebanAsesor->max('jumlah') ?: 1;
            @endphp
            @forelse ($bebanAsesor as $i => $beban)
                <tr>
                    <td class="beban-col-no">{{ $i + 1 }}</td>
                    <td>{{ $beban['nama'] }}</td>
                    <td class="beban-col-jumlah">{{ $beban['jumlah'] }}</td>
                    <td class="beban-col-proporsi">{{ intval(($beban['jumlah'] / $maxBeban) * 100) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 12px;">
                        Belum ada asesor yang mendapat penugasan.
                    </td>
                </tr>
            @endforelse
            @if ($belumAssigned > 0)
                <tr>
                    <td class="beban-col-no">&mdash;</td>
                    <td class="text-muted">Belum Ditugaskan</td>
                    <td class="beban-col-jumlah" style="color: #4b5563;">{{ $belumAssigned }}</td>
                    <td class="beban-col-proporsi" style="color: #4b5563;">
                        {{ intval(($belumAssigned / $maxBeban) * 100) }}%
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ==========================================================
         DETAIL PENUGASAN PER ASESOR
    =========================================================== --}}
    <div class="section-title"> Detail Penugasan per Asesor</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-asesor">Nama Asesor</th>
                <th class="col-jumlah">Jml</th>
                <th class="col-madrasah">Nama Madrasah</th>
                <th class="col-npsn">NPSN</th>
                <th class="col-jenjang">Jenjang</th>
                <th class="col-wilayah">Wilayah</th>
                <th class="col-prestasi">Prestasi</th>
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1; @endphp
            @forelse ($grouped as $namaAsesor => $items)
                @php $jumlah = $items->count(); @endphp
                @foreach ($items as $madrasah)
                    <tr @if ($loop->first) class="group-start" @endif>
                        <td class="col-no">{{ $no++ }}</td>
                        @if ($loop->first)
                            <td class="col-asesor" rowspan="{{ $jumlah }}">
                                @if ($namaAsesor === 'Belum Ditugaskan')
                                    <span class="text-muted">Belum Ditugaskan</span>
                                @else
                                    {{ $namaAsesor }}
                                @endif
                            </td>
                            <td class="col-jumlah" rowspan="{{ $jumlah }}">{{ $jumlah }}</td>
                        @endif
                        <td class="madrasah-nama">{{ $madrasah->nama_madrasah }}</td>
                        <td class="col-npsn">{{ $madrasah->npsn }}</td>
                        <td class="col-jenjang">{{ $madrasah->jenjang_madrasah }}</td>
                        <td class="col-wilayah">{{ $madrasah->kota }}</td>
                        <td class="col-prestasi">{{ $madrasah->prestasis_count }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 16px;">
                        Tidak ada data madrasah untuk ditampilkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ==========================================================
         RINGKASAN AKHIR
    =========================================================== --}}
    <div class="section-title">Ringkasan</div>
    <div class="summary-footer">
        Jumlah Total Data: {{ $totalMadrasah }} madrasah &nbsp;|&nbsp;
        Tanggal Cetak: {{ $tanggalCetak }}
    </div>

</body>

</html>
