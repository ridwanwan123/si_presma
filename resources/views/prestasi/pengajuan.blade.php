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
                   HERO
                   ========================= */

        .pg-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.4rem 1.75rem;
            border-radius: 20px;
            color: #fff;
            margin-bottom: 1.25rem;
            box-shadow: 0 14px 30px rgba(0, 0, 0, .12);
        }

        .siklus-open {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        }

        .siklus-submitted {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .siklus-assessment {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        }

        .siklus-finished {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
        }

        .pg-hero-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .pg-hero-icon {
            flex-shrink: 0;
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .pg-hero-title {
            font-size: 1.3rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .pg-hero-desc {
            font-size: .84rem;
            opacity: .88;
            margin: 0;
        }

        .pg-hero-right {
            display: flex;
            align-items: center;
            gap: .55rem;
            flex-wrap: wrap;
        }

        .pg-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .4rem .9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .16);
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .btn-export {
            background: rgba(255, 255, 255, .95);
            color: #0f172a;
            border: none;
            font-weight: 700;
            font-size: .8rem;
            border-radius: 10px;
            padding: .5rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            white-space: nowrap;
            transition: transform .15s ease;
        }

        .btn-export:hover {
            transform: translateY(-1px);
            color: #0f172a;
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

            .pg-hero {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .pg-hero-left {
                justify-content: center;
            }

            .pg-hero-right {
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
                            'class' => 'siklus-open',
                        ],
                        'SUBMITTED' => [
                            'label' => 'Menunggu Penugasan Asesor',
                            'icon' => 'bi-send-check-fill',
                            'class' => 'siklus-submitted',
                        ],
                        'ASSESSMENT' => [
                            'label' => 'Sedang Dinilai Asesor',
                            'icon' => 'bi-clipboard-data-fill',
                            'class' => 'siklus-assessment',
                        ],
                        'FINISHED' => [
                            'label' => 'Penilaian Selesai',
                            'icon' => 'bi-check-circle-fill',
                            'class' => 'siklus-finished',
                        ],
                    ];

                    $statusNow = $statusMap[$siklus->status] ?? $statusMap['OPEN'];
                @endphp

                {{-- HERO --}}
                <div class="pg-hero {{ $statusNow['class'] }}">
                    <div class="pg-hero-left">
                        <div class="pg-hero-icon">
                            <i class="bi bi-send-check"></i>
                        </div>
                        <div>
                            <div class="pg-hero-title">Pengajuan Prestasi</div>
                            <p class="pg-hero-desc">Kirim seluruh data prestasi madrasah untuk diproses ke tahap
                                penilaian.</p>
                        </div>
                    </div>

                    <div class="pg-hero-right">
                        <span class="pg-chip"><i class="bi bi-calendar-event"></i> Periode {{ $siklus->periode }}</span>
                        <span class="pg-chip"><i class="bi {{ $statusNow['icon'] }}"></i>
                            {{ $statusNow['label'] }}</span>
                        <a href="{{ route('prestasi.export') }}" class="btn-export">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </a>
                    </div>
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
