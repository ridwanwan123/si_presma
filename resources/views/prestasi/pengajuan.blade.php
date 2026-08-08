@extends('layouts.base')

@push('styles')
    <style>
        :root {
            --presma-primary: #0f8a43;
            --presma-primary-soft: #eaf6ef;
            --presma-text: #1e293b;
            --presma-text-light: #64748b;
            --presma-border: #e8edf5;
            --presma-bg-soft: #f8fafc;
        }

        .container-fluid {
            padding: 0 1rem;
        }

        /* Halaman ini cuma satu "tugas" (kirim pengajuan), bukan dashboard
                                   padat data -- jadi kontennya di-center vertikal di ruang yang
                                   tersedia daripada dipaksa mepet ke atas & nyisa kosong di bawah. */
        .pg-wrap {
            min-height: calc(100vh - 175px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* =========================
                                   TOPBAR (breadcrumb)
                                   ========================= */

        .pg-topbar {
            background: #fff;
            border-radius: 14px;
            border: 1px solid var(--presma-border);
            padding: .7rem 1.1rem;
            margin-bottom: 1rem;
        }

        .pg-breadcrumb {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .86rem;
            color: var(--presma-text-light);
        }

        .pg-breadcrumb a {
            color: var(--presma-text-light);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .pg-breadcrumb a:hover {
            color: var(--presma-primary);
        }

        .pg-breadcrumb .current {
            color: var(--presma-primary);
            font-weight: 700;
        }

        /* =========================
                                   HEADER (judul + tombol)
                                   ========================= */

        .pg-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.1rem;
        }

        .pg-header-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--presma-text);
            margin: 0 0 .25rem;
        }

        .pg-header-desc {
            font-size: .88rem;
            color: var(--presma-text-light);
            margin: 0;
        }

        .pg-header-right {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .btn-export {
            background: var(--presma-primary);
            color: #fff;
            border: 1.5px solid var(--presma-primary);
            font-weight: 700;
            font-size: .84rem;
            border-radius: 10px;
            padding: .5rem 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            white-space: nowrap;
            text-decoration: none;
            transition: background .15s ease;
        }

        .btn-export:hover {
            background: #0d7a3a;
            border-color: #0d7a3a;
            color: #fff;
            text-decoration: none;
        }

        /* =========================
                                   ALERT STATUS SIKLUS
                                   ========================= */

        .pg-status-alert {
            display: flex;
            align-items: center;
            gap: .7rem;
            font-size: .88rem;
            margin-bottom: 1.25rem;
        }

        .pg-status-alert i {
            font-size: 1.1rem;
        }

        /* =========================
                                   CARD generik (dua kolom)
                                   ========================= */

        .pg-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--presma-border);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .05);
            padding: 1.75rem;
        }

        .pg-card-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            font-size: 1rem;
            color: var(--presma-text);
            margin-bottom: 1.25rem;
        }

        .pg-card-title i {
            color: var(--presma-primary);
        }

        /* =========================
                                   RINGKASAN (kiri)
                                   ========================= */

        .pg-stat-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: .8rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .pg-stat-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .pg-stat-row:first-child {
            padding-top: 0;
        }

        .pg-stat-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
        }

        .icon-total {
            background: rgba(15, 138, 67, .12);
            color: #0f8a43;
        }

        .icon-akademik {
            background: rgba(13, 110, 253, .1);
            color: #0d6efd;
        }

        .icon-non-akademik {
            background: rgba(245, 158, 11, .12);
            color: #b45309;
        }

        .icon-keagamaan {
            background: rgba(109, 40, 217, .1);
            color: #6d28d9;
        }

        .icon-gtk {
            background: rgba(14, 165, 233, .1);
            color: #0369a1;
        }

        .icon-lembaga {
            background: rgba(100, 116, 139, .12);
            color: #475569;
        }

        .pg-stat-label {
            flex: 1;
            font-size: .9rem;
            color: var(--presma-text-light);
            font-weight: 500;
        }

        .pg-stat-value {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--presma-text);
        }

        .pg-stat-row.pg-stat-total {
            background: var(--presma-primary-soft);
            border-radius: 14px;
            padding: .9rem 1rem;
            margin-bottom: .4rem;
            border-bottom: none;
        }

        .pg-stat-row.pg-stat-total .pg-stat-label {
            color: var(--presma-text);
            font-weight: 700;
        }

        .pg-stat-row.pg-stat-total .pg-stat-value {
            color: var(--presma-primary);
            font-size: 1.4rem;
        }

        /* =========================
                                   INFORMASI PENTING -> gaya checklist
                                   (ikon & teks dibungkus terpisah supaya
                                   <strong> di dalam teks TIDAK ikut jadi
                                   flex-item sendiri-sendiri)
                                   ========================= */

        .pg-checklist {
            margin: 0 0 1.5rem;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .85rem;
        }

        .pg-checklist li {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
        }

        .pg-checklist .pg-check-icon {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #fef3c7;
            color: #b45309;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .68rem;
            margin-top: .1rem;
        }

        .pg-checklist span {
            font-size: .86rem;
            line-height: 1.55;
            color: #57534e;
        }

        .pg-checklist strong {
            color: #292524;
        }

        /* =========================
                                   PERNYATAAN + TOMBOL
                                   ========================= */

        .pernyataan-card {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: 1rem 1.1rem;
            border: 1.5px solid #dbe2ea;
            border-radius: 14px;
            background: var(--presma-bg-soft);
            transition: border-color .2s ease, background .2s ease;
            margin-bottom: 1.1rem;
        }

        .pernyataan-card:has(.form-check-input:checked) {
            border-color: var(--presma-primary);
            background: #f0fdf4;
        }

        .pernyataan-card .form-check-input {
            width: 1.3em;
            height: 1.3em;
            margin-top: .1rem;
            flex-shrink: 0;
        }

        .pernyataan-card .form-check-label {
            font-size: .88rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
        }

        .pg-actions {
            display: flex;
            justify-content: space-between;
            gap: .6rem;
        }

        .pg-actions .btn {
            border-radius: 10px;
            font-weight: 600;
            font-size: .88rem;
            padding: .6rem 1.3rem;
        }

        .btn-kirim {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-kirim:hover:not(:disabled) {
            background: #bb2d3b;
            border-color: #bb2d3b;
            color: #fff;
        }

        .btn-kirim:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .pg-closed-msg {
            font-size: .88rem;
            color: var(--presma-text-light);
            line-height: 1.65;
            margin-bottom: 1.1rem;
        }

        @media (max-width: 992px) {
            .pg-wrap {
                min-height: auto;
            }

            .pg-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .pg-header-right {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="pg-wrap">

                @php
                    $statusMap = [
                        'OPEN' => [
                            'label' => 'Terbuka untuk Pengisian',
                            'icon' => 'bi-unlock-fill',
                            'alert' => 'alert-success',
                        ],
                        'SUBMITTED' => [
                            'label' => 'Menunggu Penugasan Asesor',
                            'icon' => 'bi-send-check-fill',
                            'alert' => 'alert-primary',
                        ],
                        'ASSESSMENT' => [
                            'label' => 'Sedang Dinilai Asesor',
                            'icon' => 'bi-clipboard-data-fill',
                            'alert' => 'alert-info',
                        ],
                        'FINISHED' => [
                            'label' => 'Penilaian Selesai',
                            'icon' => 'bi-check-circle-fill',
                            'alert' => 'alert-secondary',
                        ],
                    ];

                    $statusNow = $statusMap[$siklus->status] ?? $statusMap['OPEN'];
                @endphp

                {{-- BREADCRUMB --}}
                <div class="pg-topbar">
                    <nav class="pg-breadcrumb">
                        <a href="{{ dashboardRoute() }}"><i class="bi bi-house-door-fill"></i> Home</a>
                        <i class="bi bi-chevron-right"></i>
                        <span class="current">Pengajuan Prestasi</span>
                    </nav>
                </div>

                {{-- HEADER --}}
                <div class="pg-header">
                    <div>
                        <h1 class="pg-header-title">Pengajuan Prestasi</h1>
                        <p class="pg-header-desc">Kirim seluruh data prestasi madrasah untuk diproses ke tahap
                            penilaian, periode {{ $siklus->periode }}.</p>
                    </div>

                    <div class="pg-header-right">
                        <a href="{{ route('prestasi.export') }}" class="btn-export">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </div>
                </div>

                {{-- ALERT STATUS SIKLUS --}}
                <div class="alert {{ $statusNow['alert'] }} pg-status-alert" role="alert">
                    <i class="bi {{ $statusNow['icon'] }}"></i>
                    <div>{{ $statusNow['label'] }}</div>
                </div>

                {{-- BODY — dua kolom --}}
                <div class="row g-3">

                    {{-- KIRI: RINGKASAN PRESTASI --}}
                    <div class="col-lg-5">
                        <div class="pg-card h-100">
                            <div class="pg-card-title">
                                <i class="bi bi-bar-chart"></i> Ringkasan Prestasi
                            </div>

                            <div class="pg-stat-row pg-stat-total">
                                <div class="pg-stat-icon icon-total"><i class="bi bi-trophy"></i></div>
                                <div class="pg-stat-label">Total Prestasi</div>
                                <div class="pg-stat-value">{{ number_format($summary->total ?? 0) }}</div>
                            </div>

                            <div class="pg-stat-row">
                                <div class="pg-stat-icon icon-akademik"><i class="bi bi-mortarboard"></i></div>
                                <div class="pg-stat-label">Akademik</div>
                                <div class="pg-stat-value">{{ number_format($summary->akademik ?? 0) }}</div>
                            </div>

                            <div class="pg-stat-row">
                                <div class="pg-stat-icon icon-non-akademik"><i class="bi bi-award"></i></div>
                                <div class="pg-stat-label">Non Akademik</div>
                                <div class="pg-stat-value">{{ number_format($summary->non_akademik ?? 0) }}</div>
                            </div>

                            <div class="pg-stat-row">
                                <div class="pg-stat-icon icon-keagamaan"><i class="bi bi-book"></i></div>
                                <div class="pg-stat-label">Keagamaan</div>
                                <div class="pg-stat-value">{{ number_format($summary->keagamaan ?? 0) }}</div>
                            </div>

                            <div class="pg-stat-row">
                                <div class="pg-stat-icon icon-gtk"><i class="bi bi-people"></i></div>
                                <div class="pg-stat-label">GTK</div>
                                <div class="pg-stat-value">{{ number_format($summary->gtk ?? 0) }}</div>
                            </div>

                            <div class="pg-stat-row">
                                <div class="pg-stat-icon icon-lembaga"><i class="bi bi-building"></i></div>
                                <div class="pg-stat-label">Lembaga</div>
                                <div class="pg-stat-value">{{ number_format($summary->lembaga ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- KANAN: INFORMASI PENTING (checklist) + PERNYATAAN + TOMBOL --}}
                    <div class="col-lg-7">
                        <div class="pg-card h-100 d-flex flex-column">
                            <div class="pg-card-title">
                                <i class="bi bi-exclamation-triangle-fill"></i> Informasi Penting
                            </div>

                            <ul class="pg-checklist">
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Setelah pengajuan dikirim, <strong>seluruh data prestasi tidak dapat
                                            diedit atau dihapus</strong> sampai proses penilaian selesai.</span>
                                </li>
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Fitur <strong>Tambah Prestasi</strong> dan <strong>Import Excel</strong>
                                        akan otomatis dinonaktifkan setelah pengajuan dikirim.</span>
                                </li>
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Asesor akan mulai melakukan penilaian terhadap seluruh data yang telah
                                        diajukan.</span>
                                </li>
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Pastikan seluruh data pada setiap bidang prestasi sudah benar sebelum
                                        melanjutkan.</span>
                                </li>
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Pastikan setiap <strong>link bukti/dokumen</strong> sudah diatur agar dapat
                                        diakses publik
                                        (<em>anyone with the link</em>), karena akan dibuka langsung oleh asesor saat
                                        menilai.</span>
                                </li>
                                <li>
                                    <span class="pg-check-icon"><i class="bi bi-check-lg"></i></span>
                                    <span>Jika ditemukan kesalahan data <strong>setelah</strong> pengajuan dikirim, silakan
                                        hubungi
                                        Bidang Pendidikan Madrasah untuk pengajuan revisi.</span>
                                </li>
                            </ul>

                            <div class="mt-auto">
                                @if ($siklus->canSubmit())
                                    <form id="formPengajuan" action="{{ route('pengajuan.submit') }}" method="POST">
                                        @csrf

                                        <div class="pernyataan-card">
                                            <input class="form-check-input" type="checkbox" id="checkPernyataan">
                                            <label class="form-check-label" for="checkPernyataan">
                                                Saya menyatakan seluruh data prestasi telah benar dan siap dikirim.
                                            </label>
                                        </div>

                                        <div class="pg-actions">
                                            <a href="{{ dashboardRoute() }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left"></i> Kembali
                                            </a>

                                            <button type="button" id="btnKirimPrestasi" class="btn btn-kirim" disabled>
                                                <i class="bi bi-send-check"></i> Kirim Prestasi
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <p class="pg-closed-msg">
                                        Prestasi untuk periode <strong>{{ $siklus->periode }}</strong> sudah dikirim
                                        dan status saat ini adalah <strong>{{ $siklus->status }}</strong>. Tidak ada
                                        tindakan lain yang perlu dilakukan di halaman ini.
                                    </p>

                                    <div class="pg-actions justify-content-end">
                                        <a href="{{ dashboardRoute() }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const checkPernyataan = document.getElementById('checkPernyataan');
            const btnKirim = document.getElementById('btnKirimPrestasi');
            const formPengajuan = document.getElementById('formPengajuan');

            // Elemen-elemen di atas tidak ada di DOM saat status siklus bukan
            // OPEN (formnya tidak dirender sama sekali), jadi guard dulu di sini.
            if (!checkPernyataan || !btnKirim || !formPengajuan) {
                return;
            }

            checkPernyataan.addEventListener('change', function() {
                btnKirim.disabled = !this.checked;
            });

            btnKirim.addEventListener('click', function() {

                Swal.fire({
                    icon: 'warning',
                    title: 'Kirim Prestasi?',
                    html: 'Apakah Anda yakin ingin mengirim prestasi?<br><strong>Setelah dikirim, data tidak dapat diubah kembali.</strong>',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        formPengajuan.submit();
                    }
                });

            });

        });
    </script>
@endpush
