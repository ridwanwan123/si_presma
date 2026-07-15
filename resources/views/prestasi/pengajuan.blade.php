@extends('layouts.base')

@push('styles')
    <style>
        /* =========================
           HEADER
        ========================= */

        .page-title {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
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

        .container-fluid {
            padding: 0 1rem;
        }

        /* =========================
           HERO CARD
        ========================= */

        .hero-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 2rem;
            border-radius: 22px;
            background: linear-gradient(135deg, #0f8a43 0%, #0c6e35 100%);
            box-shadow: 0 12px 30px rgba(15, 138, 67, .25);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .hero-icon {
            flex-shrink: 0;
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.1rem;
        }

        .hero-card h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: .4rem;
        }

        .hero-card p {
            margin: 0;
            opacity: .9;
            max-width: 620px;
            line-height: 1.6;
        }

        /* =========================
           GENERIC CARD
        ========================= */

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: box-shadow .25s ease, transform .25s ease;
        }

        .content-card:hover {
            box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .card-section-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .card-section-title i {
            color: #0f8a43;
        }

        /* =========================
           STATUS SIKLUS — HERO
        ========================= */

        .siklus-hero {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            color: #fff;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 26px rgba(0, 0, 0, .12);
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

        .siklus-periode-badge {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
        }

        .siklus-periode-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            opacity: .85;
        }

        .siklus-periode-number {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
        }

        .siklus-hero-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: .02em;
            margin-bottom: .3rem;
        }

        .siklus-hero-desc {
            margin: 0;
            opacity: .92;
            font-size: .95rem;
        }

        /* =========================
           SUMMARY CARDS
        ========================= */

        .summary-card {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: 1.1rem 1.2rem;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            height: 100%;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, .07);
        }

        .summary-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .summary-icon.icon-total {
            background: rgba(15, 138, 67, .12);
            color: #0f8a43;
        }

        .summary-icon.icon-akademik {
            background: rgba(13, 110, 253, .1);
            color: #0d6efd;
        }

        .summary-icon.icon-non-akademik {
            background: rgba(245, 158, 11, .12);
            color: #b45309;
        }

        .summary-icon.icon-keagamaan {
            background: rgba(109, 40, 217, .1);
            color: #6d28d9;
        }

        .summary-icon.icon-gtk {
            background: rgba(14, 165, 233, .1);
            color: #0369a1;
        }

        .summary-icon.icon-lembaga {
            background: rgba(100, 116, 139, .12);
            color: #475569;
        }

        .summary-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .summary-label {
            font-size: .78rem;
            color: #64748b;
        }

        /* =========================
           INFO PENTING
        ========================= */

        .info-card {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 20px;
            padding: 1.5rem;
        }

        .info-card .card-section-title {
            color: #92400e;
        }

        .info-card .card-section-title i {
            color: #b45309;
        }

        .info-list {
            margin: 0;
            padding-left: 1.25rem;
            color: #78716c;
        }

        .info-list li {
            margin-bottom: .5rem;
            line-height: 1.55;
        }

        .info-list li:last-child {
            margin-bottom: 0;
        }

        .info-list strong {
            color: #57534e;
        }

        /* =========================
           PERNYATAAN
        ========================= */

        .pernyataan-card {
            display: flex;
            align-items: flex-start;
            gap: .9rem;
            padding: 1.25rem 1.5rem;
            border: 1.5px solid #dbe2ea;
            border-radius: 18px;
            background: #f8fafc;
            transition: border-color .2s ease, background .2s ease;
        }

        .pernyataan-card:has(.form-check-input:checked) {
            border-color: #0f8a43;
            background: #f0fdf4;
        }

        .pernyataan-card .form-check-input {
            width: 1.35em;
            height: 1.35em;
            margin-top: .1rem;
            flex-shrink: 0;
        }

        .pernyataan-card .form-check-label {
            font-weight: 600;
            color: #334155;
            cursor: pointer;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: .65rem 1.4rem;
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

        @media (max-width: 768px) {
            .hero-card {
                flex-direction: column;
                text-align: center;
                padding: 1.75rem 1.5rem;
            }

            .siklus-hero {
                flex-direction: column;
                text-align: center;
                padding: 1.75rem 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- HEADER --}}
        <div class="page-title d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h2>Pengajuan Prestasi</h2>
                <p>Kirim seluruh data prestasi madrasah Anda untuk diproses ke tahap penilaian.</p>
            </div>

            <a href="{{ route('prestasi.export') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i>
                Export Excel
            </a>
        </div>

        <div class="container-fluid">

            {{-- 1. HERO CARD --}}
            <div class="hero-card">
                <div class="hero-icon">
                    <i class="bi bi-send-check"></i>
                </div>
                <div>
                    <h2>Pengajuan Prestasi</h2>
                    <p>
                        Pastikan seluruh data prestasi sudah lengkap dan benar. Setelah pengajuan dikirim,
                        data tidak dapat diubah kembali sampai proses penilaian selesai.
                    </p>
                </div>
            </div>

            {{-- 2. STATUS SIKLUS --}}
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

            <div class="siklus-hero {{ $statusNow['class'] }}">
                <div class="siklus-periode-badge">
                    <span class="siklus-periode-label">Periode</span>
                    <span class="siklus-periode-number">{{ $siklus->periode }}</span>
                </div>
                <div>
                    <div class="siklus-hero-title">
                        <i class="bi {{ $statusNow['icon'] }}"></i>
                        {{ $siklus->status }}
                    </div>
                    <p class="siklus-hero-desc">{{ $statusNow['label'] }}</p>
                </div>
            </div>

            {{-- 3. CARD RINGKASAN PRESTASI --}}
            <div class="content-card">
                <div class="card-section-title">
                    <i class="bi bi-bar-chart"></i>
                    Ringkasan Prestasi
                </div>

                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-total">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->total ?? 0) }}</div>
                                <div class="summary-label">Total Prestasi</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-akademik">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->akademik ?? 0) }}</div>
                                <div class="summary-label">Akademik</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-non-akademik">
                                <i class="bi bi-award"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->non_akademik ?? 0) }}</div>
                                <div class="summary-label">Non Akademik</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-keagamaan">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->keagamaan ?? 0) }}</div>
                                <div class="summary-label">Keagamaan</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-gtk">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->gtk ?? 0) }}</div>
                                <div class="summary-label">GTK</div>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="summary-card">
                            <div class="summary-icon icon-lembaga">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <div class="summary-value">{{ number_format($summary->lembaga ?? 0) }}</div>
                                <div class="summary-label">Lembaga</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 4. CARD INFORMASI PENTING --}}
            <div class="info-card mb-4">
                <div class="card-section-title">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Informasi Penting
                </div>

                <ul class="info-list">
                    <li>Setelah pengajuan dikirim, <strong>seluruh data prestasi tidak dapat diedit atau dihapus</strong> sampai proses penilaian selesai.</li>
                    <li>Fitur <strong>Tambah Prestasi</strong> dan <strong>Import Excel</strong> akan otomatis dinonaktifkan setelah pengajuan dikirim.</li>
                    <li>Asesor akan mulai melakukan penilaian terhadap seluruh data yang telah diajukan.</li>
                    <li>Pastikan seluruh data pada setiap bidang prestasi sudah benar sebelum melanjutkan.</li>
                </ul>
            </div>

            {{-- FORM PENGAJUAN --}}
            @if ($siklus->canSubmit())
                <form id="formPengajuan" action="{{ route('pengajuan.submit') }}" method="POST">
                    @csrf

                    {{-- 5. CHECKBOX PERNYATAAN --}}
                    <div class="pernyataan-card mb-4">
                        <input class="form-check-input" type="checkbox" id="checkPernyataan">
                        <label class="form-check-label" for="checkPernyataan">
                            Saya menyatakan seluruh data prestasi telah benar dan siap dikirim.
                        </label>
                    </div>

                    {{-- 6. TOMBOL --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between gap-2 mb-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="button" id="btnKirimPrestasi" class="btn btn-kirim" disabled>
                            <i class="bi bi-send-check"></i>
                            Kirim Prestasi
                        </button>
                    </div>
                </form>
            @else
                <div class="content-card mb-4">
                    <div class="card-section-title mb-2">
                        <i class="bi bi-info-circle"></i>
                        Pengajuan Sudah Tidak Dapat Dikirim
                    </div>
                    <p class="mb-0" style="color:#64748b;">
                        Prestasi untuk periode <strong>{{ $siklus->periode }}</strong> sudah dikirim dan status saat ini
                        adalah <strong>{{ $siklus->status }}</strong>. Tidak ada tindakan lain yang perlu dilakukan di
                        halaman ini.
                    </p>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            @endif

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