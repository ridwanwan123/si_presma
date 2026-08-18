<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Penugasan Asesor</title>
    <style>
        /* ==========================================================
           RESET & BASE
           - Bebas pakai flexbox/grid di sini karena ini dirender oleh
             browser user sendiri (bukan DomPDF), lalu di-print/save
             jadi PDF lewat window.print().
           ========================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            color: #1c1c1c;
            background: #eef1f0;
        }

        .page-wrap {
            max-width: 900px;
            margin: 24px auto;
            padding: 40px 44px;
            background: #ffffff;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
        }

        /* ==========================================================
           TOOLBAR (hanya muncul di layar, hilang saat print)
           ========================================================== */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            background: #14532d;
        }

        .btn-cetak {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: #14532d;
            border: none;
            border-radius: 5px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cetak:hover {
            background: #f0f3f1;
        }

        /* ==========================================================
           KOP SURAT
           ========================================================== */
        .kop {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .kop img {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-instansi-utama {
            font-size: 19px;
            font-weight: 700;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .kop-instansi-sub {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            margin-top: 2px;
        }

        .kop-instansi-provinsi {
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            margin-top: 1px;
        }

        .kop-alamat {
            font-size: 11.5px;
            color: #000;
            margin-top: 6px;
            line-height: 1.5;
        }

        .kop-alamat a {
            color: #1a56db;
        }

        .kop-spacer {
            width: 100px;
            flex-shrink: 0;
        }

        .kop-divider {
            border-bottom: 2.5px solid #14532d;
            margin-top: 12px;
        }

        .kop-divider-thin {
            border-bottom: 1px solid #14532d;
            margin-top: 2px;
        }

        /* ==========================================================
           JUDUL
           ========================================================== */
        .report-title {
            text-align: center;
            margin: 22px 0 20px;
        }

        .report-title h1 {
            font-size: 22px;
            font-weight: 700;
            color: #14532d;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 12px;
            color: #374151;
            margin-top: 4px;
        }

        .report-title .underline {
            width: 130px;
            border-bottom: 2px solid #14532d;
            margin: 8px auto 0;
        }

        /* ==========================================================
           INFO CETAK
           ========================================================== */
        .identity-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }

        .identity-box {
            border: 1px solid #dde2e6;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .label {
            font-size: 10px;
            color: #6b7785;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .value {
            font-size: 14px;
            font-weight: 700;
            margin-top: 3px;
        }

        /* ==========================================================
           SECTION TITLE
           ========================================================== */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #14532d;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 2px solid #14532d;
            padding-bottom: 5px;
            margin: 24px 0 10px;
        }

        /* ==========================================================
           RINGKASAN DATA
           ========================================================== */
        .ringkasan-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .ringkasan-box {
            border: 1px solid #dde2e6;
            border-radius: 6px;
            padding: 12px;
        }

        .ringkasan-value {
            font-size: 19px;
            font-weight: 700;
            margin-top: 4px;
        }

        .ringkasan-value.success {
            color: #14532d;
        }

        .ringkasan-value.warning {
            color: #b45309;
        }

        /* ==========================================================
           TABEL (dipakai untuk beban asesor & detail penugasan)
           ========================================================== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        thead {
            display: table-header-group;
            /* thead berulang tiap halaman print */
        }

        th {
            background: #14532d;
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 8px;
            text-align: left;
            border: 1px solid #000;
        }

        td {
            font-size: 12.5px;
            padding: 7px 8px;
            border: 1px solid #000;
            vertical-align: top;
            color: #000;
        }

        tbody tr:nth-child(even) td {
            background: #f7f8f9;
        }

        /* Setiap grup asesor dibungkus <tbody> sendiri -- browser akan
           berusaha menjaga satu grup tetap utuh dalam satu halaman kalau
           muat. Kalau satu asesor pegang sangat banyak madrasah (lebih dari
           kapasitas satu halaman), browser tetap akan memotongnya secara
           wajar sebagai fallback -- tidak error, cuma tidak sepenuhnya utuh. */
        tbody.asesor-group {
            break-inside: avoid-page;
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            /* satu baris jangan kepotong 2 halaman */
        }

        .col-no {
            width: 6%;
            text-align: center;
        }

        .col-npsn {
            width: 14%;
        }

        .col-jenjang {
            width: 12%;
            text-align: center;
        }

        .col-wilayah {
            width: 20%;
            text-align: left;
        }

        .col-prestasi {
            width: 16%;
            text-align: center;
        }

        .col-madrasah {
            width: 32%;
        }

        .beban-col-no {
            width: 6%;
            text-align: center;
        }

        .beban-col-jumlah {
            width: 16%;
            text-align: center;
            font-weight: 700;
        }

        .beban-col-proporsi {
            width: 16%;
            text-align: right;
            font-weight: 700;
        }

        td.beban-col-jumlah,
        td.beban-col-proporsi {
            color: #000;
        }

        .madrasah-nama {
            font-weight: 600;
            color: #000;
        }

        .text-muted {
            color: #000;
        }

        /* Baris header per grup asesor -- tidak pakai rowspan, jadi baris
           bebas pindah halaman tanpa risiko sel merge yang pecah. */
        tr.group-header td {
            background: #eef2ef;
            border: 1px solid #000;
            font-weight: 700;
            color: #000;
            page-break-after: avoid;
            /* judul grup jangan sendirian di akhir halaman */
        }

        tr.group-header .badge {
            float: right;
            font-weight: 400;
            color: #000;
        }

        .summary-footer {
            margin-top: 16px;
            font-size: 10px;
            color: #6b7785;
            border-top: 1px solid #d8dbe0;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
        }

        /* ==========================================================
           PRINT STYLESHEET
           - Ini yang menggantikan peran DomPDF: dirender oleh mesin
             browser asli, jadi seluruh CSS di atas (flex/grid) ikut
             kepakai persis seperti tampilan di layar.
           ========================================================== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 16mm 14mm;
            }

            body {
                background: #ffffff;
            }

            .toolbar {
                display: none;
            }

            .page-wrap {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                max-width: 100%;
                padding: 0;
            }

            .section-title {
                page-break-after: avoid;
            }
        }
    </style>
</head>

<body>

    {{-- Toolbar: hilang otomatis saat print/save-as-PDF --}}
    <div class="toolbar">
        <button class="btn-cetak" onclick="window.print()">
            🖨️ Cetak / Simpan sebagai PDF
        </button>
    </div>

    <div class="page-wrap">

        {{-- KOP SURAT --}}
        <div class="kop">
            <img src="{{ asset('assets/images/kemenag.png') }}" alt="Logo Kementerian Agama">
            <div class="kop-text">
                <div class="kop-instansi-utama">Kementerian Agama Republik Indonesia</div>
                <div class="kop-instansi-sub">Kantor Wilayah Kementerian Agama</div>
                <div class="kop-instansi-provinsi">Provinsi DKI Jakarta</div>
                <div class="kop-alamat">
                    Jalan D.I. Panjaitan No.10 Jakarta Timur 13340<br>
                    Telepon (021) 8197479, 8512403, 856530; Faksimili (021) 8512402<br>
                    Website : <a href="http://www.dki.kemenag.go.id">www.dki.kemenag.go.id</a>
                </div>
            </div>
            <div class="kop-spacer"></div>
        </div>
        <div class="kop-divider"></div>
        <div class="kop-divider-thin"></div>

        {{-- JUDUL --}}
        <div class="report-title">
            <h1>Laporan Penugasan Asesor</h1>
            <div class="report-subtitle">Bidang Pendidikan Madrasah &mdash; Program Jakarta Madrasah Awards (JMA)</div>
            <div class="underline"></div>
        </div>

        {{-- INFO CETAK --}}
        <div class="identity-grid">
            <div class="identity-box">
                <div class="label">Tanggal Cetak</div>
                <div class="value">{{ $tanggalCetak }}</div>
            </div>
            <div class="identity-box">
                <div class="label">Dicetak Oleh</div>
                <div class="value">{{ $dicetakOleh }}</div>
            </div>
            <div class="identity-box">
                <div class="label">Total Data</div>
                <div class="value">{{ $totalMadrasah }} Madrasah</div>
            </div>
        </div>

        {{-- RINGKASAN DATA --}}
        <div class="section-title">Ringkasan Data</div>
        <div class="ringkasan-grid">
            <div class="ringkasan-box">
                <div class="label">Total Madrasah</div>
                <div class="ringkasan-value">{{ $totalMadrasah }}</div>
            </div>
            <div class="ringkasan-box">
                <div class="label">Total Asesor</div>
                <div class="ringkasan-value">{{ $totalAsesor }}</div>
            </div>
            <div class="ringkasan-box">
                <div class="label">Sudah Assigned</div>
                <div class="ringkasan-value success">{{ $sudahAssigned }}</div>
            </div>
            <div class="ringkasan-box">
                <div class="label">Belum Assigned</div>
                <div class="ringkasan-value warning">{{ $belumAssigned }}</div>
            </div>
        </div>

        {{-- RINGKASAN BEBAN ASESOR --}}
        <div class="section-title">Ringkasan Beban Asesor</div>
        <table>
            <thead>
                <tr>
                    <th class="beban-col-no">No</th>
                    <th>Nama Asesor</th>
                    <th class="beban-col-jumlah">Jumlah Madrasah</th>
                    <th class="beban-col-proporsi" style="text-align:center;">Proporsi</th>
                </tr>
            </thead>
            <tbody>
                @php $maxBeban = $bebanAsesor->max('jumlah') ?: 1; @endphp
                @forelse ($bebanAsesor as $i => $beban)
                    <tr>
                        <td class="beban-col-no">{{ $i + 1 }}</td>
                        <td>{{ $beban['nama'] }}</td>
                        <td style="text-align:center;">{{ $beban['jumlah'] }}</td>
                        <td style="text-align:center;">{{ intval(($beban['jumlah'] / $maxBeban) * 100) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:12px">
                            Belum ada asesor yang mendapat penugasan.
                        </td>
                    </tr>
                @endforelse
                @if ($belumAssigned > 0)
                    <tr>
                        <td class="beban-col-no">&mdash;</td>
                        <td class="text-muted">Belum Ditugaskan</td>
                        <td class="beban-col-jumlah" style="color:#4b5563">{{ $belumAssigned }}</td>
                        <td class="beban-col-proporsi" style="color:#4b5563">
                            {{ intval(($belumAssigned / $maxBeban) * 100) }}%
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- DETAIL PENUGASAN PER ASESOR --}}
        <div class="section-title">Detail Penugasan per Asesor</div>
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-madrasah">Nama Madrasah</th>
                    <th class="col-npsn">NPSN</th>
                    <th class="col-jenjang">Jenjang</th>
                    <th class="col-wilayah">Wilayah</th>
                    <th class="col-prestasi">Prestasi</th>
                </tr>
            </thead>
            @php $no = 1; @endphp
            @forelse ($grouped as $namaAsesor => $items)
                <tbody class="asesor-group">
                    <tr class="group-header">
                        <td colspan="6">
                            @if ($namaAsesor === 'Belum Ditugaskan')
                                <span class="text-muted">Belum Ditugaskan</span>
                            @else
                                {{ $namaAsesor }}
                            @endif
                            <span class="badge" style="font-weight: bold;">{{ $items->count() }} Madrasah</span>
                        </td>
                    </tr>
                    @foreach ($items as $madrasah)
                        <tr>
                            <td class="col-no">{{ $no++ }}</td>
                            <td class="madrasah-nama">{{ $madrasah->nama_madrasah }}</td>
                            <td class="col-npsn">{{ $madrasah->npsn }}</td>
                            <td class="col-jenjang">{{ $madrasah->jenjang_madrasah }}</td>
                            <td class="col-wilayah">{{ $madrasah->kota }}</td>
                            <td class="col-prestasi">{{ $madrasah->prestasis_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:16px">
                            Tidak ada data madrasah untuk ditampilkan.
                        </td>
                    </tr>
                </tbody>
            @endforelse
        </table>

        <div class="summary-footer">
            <span>Jumlah Total Data: {{ $totalMadrasah }} madrasah</span>
            <span>Tanggal Cetak: {{ $tanggalCetak }}</span>
        </div>

    </div>

</body>

</html>