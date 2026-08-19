@extends('layouts.base')

@push('styles')
    <style>
        :root {
            --ec-green: #1B7A43;
            --ec-green-dark: #14602F;
            --ec-green-soft: #EAF6EE;
            --ec-border: #EAEDF1;
            --ec-text: #2A2E33;
            --ec-muted: #8A919B;
        }

        .ec-header {
            margin-bottom: 24px;
        }

        .ec-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--ec-text);
            margin: 0 0 4px;
        }

        .ec-header p {
            font-size: 14px;
            color: var(--ec-muted);
            margin: 0;
        }

        .ec-card {
            background: #fff;
            border: 1px solid var(--ec-border);
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(20, 30, 40, 0.04);
            opacity: 0;
            transform: translateY(8px);
            animation: ecRise .45s ease-out forwards;
        }

        .ec-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--ec-border);
        }

        .ec-card-icon {
            flex: none;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--ec-green-soft);
            color: var(--ec-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .ec-card-header h2 {
            font-size: 15px;
            font-weight: 700;
            color: var(--ec-text);
            margin: 0;
        }

        .ec-card-body {
            padding: 22px;
        }

        .ec-desc {
            font-size: 13.5px;
            line-height: 1.65;
            color: #5B6169;
            margin: 0 0 22px;
        }

        .ec-desc strong {
            color: var(--ec-text);
            font-weight: 600;
        }

        .ec-fields {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            align-items: end;
        }

        .ec-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ec-text);
            margin-bottom: 6px;
        }

        .ec-field .ec-optional {
            font-weight: 400;
            color: var(--ec-muted);
        }

        .ec-select {
            width: 100%;
            appearance: none;
            font-size: 14px;
            padding: 10px 34px 10px 12px;
            border: 1px solid #DDE2E8;
            border-radius: 10px;
            background-color: #fff;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8'><path d='M1 1l5 5 5-5' stroke='%238A919B' stroke-width='1.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            color: var(--ec-text);
            transition: border-color .15s, box-shadow .15s;
        }

        .ec-select:disabled {
            background-color: #F7F8FA;
            color: #B4BAC2;
        }

        .ec-select:focus {
            outline: none;
            border-color: var(--ec-green);
            box-shadow: 0 0 0 3px rgba(27, 122, 67, .12);
        }

        .ec-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .ec-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: var(--ec-green);
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            cursor: pointer;
            transition: background .15s;
        }

        .ec-submit:hover {
            background: var(--ec-green-dark);
        }

        .ec-submit:focus-visible {
            outline: 2px solid var(--ec-green-dark);
            outline-offset: 2px;
        }

        @keyframes ecRise {
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 767px) {
            .ec-fields { grid-template-columns: 1fr; }
            .ec-actions { justify-content: stretch; }
            .ec-submit { width: 100%; justify-content: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            .ec-card { animation: none !important; opacity: 1 !important; transform: none !important; }
            .ec-submit { transition: none; }
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="ec-header">
            <h1>Export Center</h1>
            <p>Kumpulan export laporan PRESMA.</p>
        </div>

        {{-- ================================================================
             LAPORAN HASIL PENILAIAN ASESOR
             ================================================================ --}}
        <div class="ec-card mb-4">
            <div class="ec-card-header">
                <div class="ec-card-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                <h2>Laporan Hasil Penilaian Asesor</h2>
            </div>
            <div class="ec-card-body">
                <p class="ec-desc">
                    Rekap hasil penilaian asesor lintas madrasah. File Excel berisi 2 sheet:
                    <strong>Ringkasan</strong> (total &amp; rata-rata per madrasah) dan
                    <strong>Detail Per Prestasi</strong> (breakdown persentase, nilai akhir, dan
                    catatan asesor tiap prestasi).
                </p>

                <form method="GET" action="{{ route('export-center.laporan-penilaian') }}" id="formLaporanPenilaian">
                    <div class="ec-fields">
                        <div class="ec-field">
                            <label>Periode <span class="text-danger">*</span></label>
                            <select class="ec-select" name="periode" required>
                                @foreach ($daftarPeriode as $item)
                                    <option value="{{ $item }}"
                                        {{ (string) $item === (string) $periodeAktif ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ec-field">
                            <label>Jenjang <span class="text-danger">*</span></label>
                            <select class="ec-select" name="jenjang" id="filterJenjang" required>
                                <option value="" disabled selected>-- Pilih Jenjang --</option>
                                @foreach ($daftarJenjang as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ec-field">
                            <label>Madrasah <span class="ec-optional">(opsional)</span></label>
                            <select class="ec-select" name="madrasah_id" id="filterMadrasah" disabled>
                                <option value="">-- Pilih Jenjang dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="ec-actions">
                        <button type="submit" class="ec-submit">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const semuaMadrasah = @json($daftarMadrasah);

            const jenjangSelect = document.getElementById('filterJenjang');
            const madrasahSelect = document.getElementById('filterMadrasah');
            const form = document.getElementById('formLaporanPenilaian');

            jenjangSelect.addEventListener('change', function () {
                const jenjangDipilih = this.value;

                madrasahSelect.innerHTML = '';
                madrasahSelect.disabled = false;
                madrasahSelect.appendChild(new Option('Semua Madrasah', ''));

                semuaMadrasah
                    .filter((m) => m.jenjang_madrasah === jenjangDipilih)
                    .forEach((m) => madrasahSelect.appendChild(new Option(m.nama_madrasah, m.id)));
            });

            // Submit lewat fetch supaya loading SweetAlert benar-benar
            // mengikuti proses generate Excel di server (buka saat request
            // dikirim, tutup saat berkas selesai diterima).
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!form.reportValidity()) {
                    return;
                }

                const params = new URLSearchParams(new FormData(form));
                const url = form.action + '?' + params.toString();

                Swal.fire({
                    title: 'Menyiapkan laporan…',
                    html: 'Mengambil data penilaian dan menyusun berkas Excel.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading(),
                });

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (!response.ok) {
                        throw new Error('Server merespons dengan status ' + response.status);
                    }

                    const blob = await response.blob();
                    const disposition = response.headers.get('Content-Disposition') || '';
                    const match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
                    const namaFile = match ? decodeURIComponent(match[1]) : 'laporan-penilaian.xlsx';

                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = namaFile;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'Laporan siap diunduh',
                        text: namaFile,
                        confirmButtonColor: '#1B7A43',
                    });
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengunduh laporan',
                        text: err.message,
                        confirmButtonColor: '#1B7A43',
                    });
                }
            });
        });
    </script>
@endpush