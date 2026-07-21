@extends('layouts.base')

@push('styles')
    <style>
        /* ===========================================================
       RUBRIK PENILAIAN — DESIGN TOKENS
       Dipakai token yang sama dengan assets/css/prestasi/index.css
       (--presma-*) supaya konsisten satu sistem desain PRESMA.
       Kalau nanti token ini sudah dipindah ke base.css secara
       global, blok :root di bawah ini boleh dihapus.
       =========================================================== */
        :root {
            --presma-primary: #0f8a43;
            --presma-primary-soft: #eaf6ef;
            --presma-text: #1e293b;
            --presma-text-light: #64748b;
            --presma-border: #e8edf5;
            --presma-border-dark: #d9e2ec;
            --presma-bg: #ffffff;
            --presma-bg-soft: #f8fafc;
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }


        .container-fluid {
            padding: 0 1rem;
        }

        /* ===== HEADER ===== */
        .rp-header {
            padding: 1.5rem 1.6rem;
            margin: 0 1rem 1.5rem;
            border-radius: 20px;
            background: linear-gradient(135deg, #0f8a43 0%, #0c6f37 100%);
            box-shadow: 0 10px 30px -12px rgba(15, 138, 67, .45);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .rp-header::after {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .rp-header-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: .6rem;
        }

        .rp-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin: 0 0 .2rem;
            letter-spacing: -.01em;
        }

        .rp-header p {
            color: rgba(255, 255, 255, .82);
            margin: 0;
            font-size: .88rem;
            max-width: 560px;
            position: relative;
            z-index: 1;
        }

        .rp-header .btn-tambah {
            background: #fff;
            color: var(--presma-primary);
            border: none;
            font-weight: 700;
            border-radius: 12px;
            padding: .65rem 1.2rem;
            font-size: .88rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: transform .15s ease, box-shadow .15s ease;
            position: relative;
            z-index: 1;
        }

        .rp-header .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, .16);
            color: var(--presma-primary);
        }

        /* ===== CARD ===== */
        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid var(--presma-border);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.25rem;
        }

        /* ===== FILTER ===== */
        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .filter-form .form-label {
            font-size: .74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--presma-text-light);
            margin-bottom: .35rem;
        }

        .filter-form .form-select,
        .filter-form .form-control {
            border-radius: 10px;
            min-width: 170px;
            border-color: #e2e8f0;
            font-size: .87rem;
        }

        .filter-form .form-select:focus,
        .filter-form .form-control:focus {
            border-color: var(--presma-primary);
            box-shadow: 0 0 0 .2rem rgba(15, 138, 67, .12);
        }

        .filter-form .btn-outline-secondary {
            border-radius: 10px;
        }

        /* ===== TABLE ===== */
        .rubrik-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .85rem;
        }

        .rubrik-table thead th {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--presma-text-light);
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
            white-space: nowrap;
        }

        .rubrik-table thead th:first-child {
            border-top-left-radius: 12px;
        }

        .rubrik-table thead th:last-child {
            border-top-right-radius: 12px;
        }

        .rubrik-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .rubrik-table tbody tr {
            transition: background .12s ease;
        }

        .rubrik-table tbody tr:hover {
            background: #f8fbf9;
        }

        .rubrik-table tbody tr:last-child td {
            border-bottom: none;
        }

        .bidang-label {
            font-weight: 700;
            color: var(--presma-text);
        }

        /* ===== BADGES ===== */
        .jenis-badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .jenis-badge.jenis-lomba {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .jenis-badge.jenis-karya {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .jenis-badge.jenis-kelembagaan {
            background: #fef3c7;
            color: #b45309;
        }

        .jenis-badge.jenis-hafalan {
            background: #d1f2de;
            color: #0f8a43;
        }

        .kriteria-main {
            font-weight: 600;
            color: var(--presma-text);
            font-size: .84rem;
        }

        .kriteria-detail {
            font-size: .76rem;
            color: var(--presma-text-light);
            margin-top: 2px;
        }

        .skor-value {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 800;
            color: var(--presma-primary);
            background: var(--presma-primary-soft);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: .83rem;
        }

        .tahun-chip {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: .78rem;
        }

        .action-btns {
            display: flex;
            gap: .35rem;
        }

        .action-btns .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== EMPTY STATE ===== */
        .rp-empty {
            text-align: center;
            padding: 3.5rem 1rem;
        }

        .rp-empty i {
            font-size: 2.4rem;
            color: #cbd5e1;
            margin-bottom: .75rem;
            display: block;
        }

        .rp-empty p {
            color: var(--presma-text-light);
            margin: 0;
            font-size: .9rem;
        }

        /* ===== MODAL / FORM ===== */
        .modal-content {
            border-radius: 18px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--presma-border);
            padding: 1.1rem 1.4rem;
        }

        .modal-header .modal-title {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--presma-text);
        }

        .modal-body {
            padding: 1.4rem;
        }

        .modal-footer {
            border-top: 1px solid var(--presma-border);
            padding: 1rem 1.4rem;
        }

        .modal-footer .btn,
        .rp-header .btn-tambah {
            border-radius: 10px;
        }

        .modal-footer .btn-success {
            background: var(--presma-primary);
            border-color: var(--presma-primary);
            font-weight: 700;
        }

        /* ===== FORM MODAL (Tambah/Edit Rubrik) ===== */
        .rp-form-body .form-label {
            font-size: .76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: var(--presma-text-light);
            margin-bottom: .3rem;
        }

        .rp-form-body .form-control,
        .rp-form-body .form-select {
            border-radius: 10px;
            font-size: .9rem;
        }

        .rp-form-body .form-control:focus,
        .rp-form-body .form-select:focus {
            border-color: var(--presma-primary);
            box-shadow: 0 0 0 .2rem rgba(15, 138, 67, .12);
        }

        .rp-form-body .form-hint {
            font-size: .74rem;
            color: var(--presma-text-light);
            margin-top: .35rem;
            line-height: 1.5;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--presma-primary);
            border-bottom: 1px solid var(--presma-border);
            padding-bottom: .5rem;
            margin-bottom: 1rem;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .9rem 1rem;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: .9rem 1rem;
        }

        .form-grid-3 .full-width,
        .form-grid-2 .full-width {
            grid-column: 1 / -1;
        }

        /* Panel khusus untuk grup kondisional (Lomba / Bebas) supaya jelas
               terpisah secara visual dari section Klasifikasi & Hasil */
        .form-panel {
            border: 1px solid var(--presma-border);
            background: #f8fafc;
            border-radius: 14px;
            padding: 1.1rem 1.2rem;
            margin-bottom: 1.5rem;
        }

        .form-panel-head {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--presma-primary);
            margin-bottom: 1rem;
        }

        .form-panel-head i {
            font-size: .85rem;
        }

        @media (max-width: 576px) {
            .form-grid-3 {
                grid-template-columns: 1fr;
            }

            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .rp-header {
                margin: 0 0 1.25rem;
                border-radius: 16px;
            }

            .rp-header .btn-tambah {
                width: 100%;
                justify-content: center;
            }
        }

        /* ===== PAGINATION (disamakan persis dengan pola Prestasi) ===== */
        .p-3 nav[role="navigation"] p,
        .p-3 nav[role="navigation"] small {
            font-size: .82rem;
            color: var(--presma-text-light);
            margin: 0;
        }

        .p-3 nav[role="navigation"]>div {
            flex-wrap: wrap;
            gap: .75rem;
        }

        .pagination {
            gap: 6px;
            margin: 0;
        }

        .page-item .page-link {
            width: 36px;
            height: 36px;
            border-radius: 10px !important;
            border: 1px solid var(--presma-border);
            background: #fff;
            color: #475569;
            font-size: .82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .18s;
            box-shadow: none;
        }

        .page-item .page-link:hover {
            background: var(--presma-primary-soft);
            border-color: #bfe3cd;
            color: var(--presma-primary);
        }

        .page-item.active .page-link {
            background: var(--presma-primary);
            border-color: var(--presma-primary);
            color: #fff;
        }

        .page-item.disabled .page-link {
            background: #f8fafc;
            color: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- HEADER --}}
        <div class="rp-header">
            <div>
                <div class="rp-header-icon"><i class="bi bi-clipboard2-data"></i></div>
                <h2>Rubrik Penilaian</h2>
                <p>Tabel skor resmi Juknis JMA — dipakai untuk mencocokkan skor yang diinput Madrasah di halaman
                    penilaian Asesor.</p>
            </div>
            <button type="button" class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg"></i> Tambah Rubrik
            </button>
        </div>

        <div class="container-fluid">

            {{-- FILTER --}}
            <div class="content-card">
                <form method="GET" class="filter-form">
                    <div>
                        <label class="form-label">Bidang Prestasi</label>
                        <select name="bidang_prestasi" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Bidang</option>
                            @foreach ($bidangList as $item)
                                <option value="{{ $item }}" {{ $filterBidang == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jenis Rubrik</label>
                        <select name="jenis_rubrik" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenisRubrikList as $item)
                                <option value="{{ $item }}" {{ $filterJenis == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tahun Berlaku</label>
                        <select name="tahun_berlaku" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach ($daftarTahun as $item)
                                <option value="{{ $item }}" {{ $filterTahun == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="flex:1; min-width:200px">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Kriteria, juara, tingkat..."
                            value="{{ $filterSearch }}">
                    </div>
                    <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    @if ($filterBidang || $filterJenis || $filterTahun || $filterSearch)
                        <a href="{{ route('rubrik-penilaian.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- TABEL --}}
            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="rubrik-table">
                        <thead>
                            <tr>
                                <th>Bidang</th>
                                <th>Jenis</th>
                                <th>Kriteria</th>
                                <th class="text-end">Skor</th>
                                <th class="text-center">Tahun</th>
                                <th class="text-center" style="width:90px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarRubrik as $rubrik)
                                <tr>
                                    <td class="bidang-label">{{ $rubrik->bidang_prestasi }}</td>
                                    <td>
                                        <span
                                            class="jenis-badge jenis-{{ strtolower($rubrik->jenis_rubrik) }}">{{ $rubrik->jenis_rubrik }}</span>
                                    </td>
                                    <td>
                                        @if ($rubrik->jenis_rubrik === 'Lomba')
                                            <div class="kriteria-main">{{ $rubrik->tingkat }} &middot;
                                                {{ $rubrik->juara }}</div>
                                            <div class="kriteria-detail">
                                                {{ $rubrik->kategori_kegiatan }}
                                                {{ $rubrik->metode_pelaksanaan ? '· ' . $rubrik->metode_pelaksanaan : '' }}
                                                · {{ $rubrik->kategori_penyelenggara }}
                                            </div>
                                        @else
                                            <div class="kriteria-main">{{ $rubrik->kriteria_khusus ?? '-' }}</div>
                                            @if ($rubrik->nilai_min !== null)
                                                <div class="kriteria-detail">Rentang: {{ $rubrik->nilai_min }} –
                                                    {{ $rubrik->nilai_max }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="skor-value">
                                            <i class="bi bi-star-fill" style="font-size:.65rem"></i>
                                            {{ number_format($rubrik->skor, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center"><span class="tahun-chip">{{ $rubrik->tahun_berlaku }}</span>
                                    </td>
                                    <td>
                                        <div class="action-btns justify-content-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal" data-bs-target="#modalEdit{{ $rubrik->id }}"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('rubrik-penilaian.destroy', $rubrik->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus rubrik ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <div class="modal fade" id="modalEdit{{ $rubrik->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <form action="{{ route('rubrik-penilaian.update', $rubrik->id) }}"
                                                method="POST" class="form-rubrik">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Rubrik</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('rubrik-penilaian.form', [
                                                        'rubrik' => $rubrik,
                                                    ])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="rp-empty">
                                            <i class="bi bi-inbox"></i>
                                            <p>Belum ada rubrik yang cocok dengan filter ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top" style="border-color:var(--presma-border) !important">
                    {{ $daftarRubrik->links() }}
                </div>
            </div>
        </div>

    </main>
@endsection
{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('rubrik-penilaian.store') }}" method="POST" class="form-rubrik">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rubrik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('rubrik-penilaian.form', ['rubrik' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
    <script>
        // Tampilkan/sembunyikan grup field sesuai jenis_rubrik yang dipilih --
        // jalan di SEMUA form (tambah + tiap modal edit) sekaligus lewat class.
        function toggleGrupRubrik(select) {
            const form = select.closest('form');
            const jenis = select.value;
            const grupLomba = form.querySelector('.grup-lomba');
            const grupFleksibel = form.querySelector('.grup-fleksibel');

            if (jenis === 'Lomba') {
                grupLomba.classList.remove('d-none');
                grupFleksibel.classList.add('d-none');
            } else {
                grupLomba.classList.add('d-none');
                grupFleksibel.classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.select-jenis-rubrik').forEach(function(select) {
                toggleGrupRubrik(select);
                select.addEventListener('change', function() {
                    toggleGrupRubrik(this);
                });
            });
        });
    </script>
@endpush
