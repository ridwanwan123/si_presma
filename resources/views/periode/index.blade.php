@extends('layouts.base')

@push('styles')
    <style>
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

        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
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

        /* ============ PERIODE AKTIF HERO ============ */

        .periode-hero {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.75rem 2rem;
            border-radius: 22px;
            background: linear-gradient(135deg, #0f8a43 0%, #0c6e35 100%);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .periode-number {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .periode-hero h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: .35rem;
        }

        .periode-hero p {
            margin: 0;
            opacity: .9;
        }

        /* ============ FORM AKTIFKAN ============ */

        .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .45rem;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid #dbe2ea;
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-success {
            background: #0f8a43;
            border-color: #0f8a43;
        }

        .btn-success:hover {
            background: #0c7438;
            border-color: #0c7438;
        }

        .alert-warning-soft {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            font-size: .85rem;
            color: #92400e;
            margin-bottom: 1.25rem;
        }

        /* ============ TABLE RIWAYAT ============ */

        .periode-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .periode-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #64748b;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .periode-table tbody td {
            padding: 12px 14px;
            font-size: .86rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .badge-status-periode {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 700;
        }

        .badge-status-periode.badge-aktif {
            background: rgba(15, 138, 67, .1);
            color: #0f8a43;
        }

        .badge-status-periode.badge-nonaktif {
            background: #f1f5f9;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Kelola Periode</h2>
            <p>Menentukan periode (tahun) yang sedang berjalan di seluruh sistem PRESMA.</p>
        </div>

        <div class="container-fluid">

            {{-- PERIODE AKTIF SAAT INI --}}
            <div class="periode-hero">
                <div class="periode-number">{{ $periodeAktif }}</div>
                <div>
                    <h2>Periode Aktif Saat Ini</h2>
                    <p>Seluruh input prestasi, pengajuan, dan penilaian baru akan tercatat di bawah periode
                        {{ $periodeAktif }}.</p>
                </div>
            </div>

            {{-- FORM AKTIFKAN PERIODE --}}
            <div class="content-card">
                <div class="card-section-title">
                    <i class="bi bi-calendar-range"></i>
                    Aktifkan / Buat Periode Baru
                </div>

                <div class="alert-warning-soft">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Mengaktifkan periode baru akan mengubah acuan periode untuk SELURUH sistem
                    (input prestasi, pengajuan, assign asesor, penilaian). Data periode sebelumnya
                    tidak hilang, tetap tersimpan sebagai riwayat.
                </div>

                <form id="formAktifkanPeriode" action="{{ route('periode.aktifkan') }}" method="POST">
                    @csrf

                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Periode (Tahun)</label>
                            <input type="number" name="periode" class="form-control" min="2020" max="2100"
                                value="{{ old('periode', $periodeAktif + 1) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Keterangan (opsional)</label>
                            <input type="text" name="keterangan" class="form-control"
                                placeholder="Mis. Pembukaan siklus penilaian tahun ajaran baru"
                                value="{{ old('keterangan') }}">
                        </div>

                        <div class="col-md-3">
                            <button type="button" id="btnAktifkanPeriode" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i>
                                Aktifkan Periode
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- RIWAYAT PERIODE --}}
            <div class="content-card p-0">
                <div class="p-3 pb-0">
                    <div class="card-section-title mb-0">
                        <i class="bi bi-clock-history"></i>
                        Riwayat Periode
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="periode-table">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Diaktifkan Oleh</th>
                                <th>Diaktifkan Pada</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarPeriode as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->periode }}</td>
                                    <td>
                                        @if ($item->is_active)
                                            <span class="badge-status-periode badge-aktif">
                                                <i class="bi bi-check-circle-fill"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge-status-periode badge-nonaktif">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $item->diaktifkanOleh->nama ?? '-' }}</td>
                                    <td>{{ optional($item->diaktifkan_pada)->format('d M Y H:i') ?? '-' }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Belum ada riwayat periode.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnAktifkan = document.getElementById('btnAktifkanPeriode');
            const formAktifkan = document.getElementById('formAktifkanPeriode');

            btnAktifkan.addEventListener('click', function() {

                const periode = formAktifkan.querySelector('[name="periode"]').value;

                if (!periode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Periode Wajib Diisi',
                        text: 'Silakan isi tahun periode terlebih dahulu.',
                        confirmButtonColor: '#0f8a43'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Aktifkan Periode ' + periode + '?',
                    html: 'Seluruh input, pengajuan, dan penilaian baru akan mengacu ke periode ini.<br><strong>Periode sebelumnya akan dinonaktifkan.</strong>',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Aktifkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0f8a43',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        formAktifkan.submit();
                    }
                });

            });

        });
    </script>
@endpush
