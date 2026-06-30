@extends('layouts.base')

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-title h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }

        .page-title p {
            font-size: .9rem;
            color: #64748b;
            margin: 0;
        }

        .action-group {
            display: flex;
            gap: .5rem;
        }

        /* CARD */
        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        /* FILTER */
        .filter-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: .75rem;
            border-radius: 12px;
        }

        /* ITEM ROW */
        .prestasi-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem;
            transition: .2s;
        }

        .prestasi-item:hover {
            background: #f8fafc;
        }

        .title {
            font-weight: 700;
            font-size: .95rem;
            color: #0f172a;
        }

        .subtext {
            font-size: .8rem;
            color: #64748b;
            margin-top: .2rem;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            margin-top: .4rem;
        }

        .badge-soft {
            font-size: .7rem;
            padding: .25rem .5rem;
            border-radius: 6px;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: .8rem;
            color: #475569;
            margin-top: .5rem;
        }

        .assessor-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: .75rem;
            padding-top: .75rem;
            border-top: 1px dashed #e2e8f0;
        }

        .table-responsive {
            overflow-x: auto;
        }

        #tablePrestasi {
            white-space: nowrap;
        }

        #tablePrestasi thead th {
            background: #f8fafc;
            font-size: .78rem;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
        }

        #tablePrestasi tbody td {
            font-size: .82rem;
            vertical-align: middle;
        }

        .badge-status {
            font-size: .72rem;
        }
    </style>
@endpush

@section('content')
    <main class="content">

        {{-- HEADER --}}
        <div class="page-title">

            <div>
                <h2>Prestasi {{ ucfirst($jenis) }}</h2>
                <p>Data lomba & hasil verifikasi assessor</p>
            </div>

            <div class="action-group">

                <button class="btn btn-success btn-sm">
                    <i class="bi bi-plus"></i>
                    Tambah
                </button>

                <a href="{{ route('prestasi.import', $jenis) }}"
                    class="btn btn-primary btn-sm {{ request()->routeIs('prestasi.import') ? 'active' : '' }}"">
                    <i class="bi bi-upload"></i>
                    Import
                </a>

            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Total Prestasi
                            </div>
                            <div class="fw-bold fs-2">
                                {{ $summary->total ?? '0' }}
                            </div>
                        </div>

                        <i class="bi bi-trophy fs-1 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Sudah Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-success">
                                {{ $summary->verified ?? '0' }}
                            </div>
                        </div>

                        <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Belum Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-warning">
                                {{ $summary->pending ?? '0' }}
                            </div>
                        </div>

                        <i class="bi bi-clock fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="content-card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">
                                Tidak Dinilai
                            </div>
                            <div class="fw-bold fs-2 text-danger">
                                {{ $summary->rejected ?? '0' }}
                            </div>
                        </div>

                        <i class="bi bi-x-circle fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- FILTER --}}
        <div class="content-card">
            <div style="padding:1rem">
                <div class="filter-box">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Cari kegiatan / lembaga">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Tingkat</option>

                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Kategori</option>

                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Penyelenggara</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>

                        <div class="col-md-1">
                            <a href="" class="btn btn-light border btn-sm w-100">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">

                <table id="tablePrestasi" class="table table-hover table-sm align-middle mb-0">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama Kegiatan</th>

                            <th>Tingkat</th>

                            <th>Kategori</th>

                            <th>Juara</th>

                            <th>Lembaga</th>

                            <th>Penyelenggara</th>

                            <th>Tanggal</th>

                            <th>Status</th>

                            <th width="80">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    </tbody>

                </table>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(function() {

            $.get(
                "{{ route('prestasi.data', $jenis) }}",
                function(rows) {

                    let html = '';

                    rows.forEach((item, index) => {

                        let status = '';

                        if (item.status_verifikasi == 'verified') {

                            status =
                                `<span class="badge bg-success">
                        Dinilai
                    </span>`;

                        } else if (item.status_verifikasi == 'pending') {

                            status =
                                `<span class="badge bg-warning text-dark">
                        Pending
                    </span>`;

                        } else {

                            status =
                                `<span class="badge bg-danger">
                        Ditolak
                    </span>`;
                        }

                        html += `
                <tr>

                    <td>${index+1}</td>

                    <td>
                        <div class="fw-semibold">
                            ${item.nama_kegiatan}
                        </div>
                    </td>

                    <td>${item.tingkat}</td>

                    <td>${item.kategori_kegiatan}</td>

                    <td>
                        <span class="badge bg-success">
                            ${item.juara}
                        </span>
                    </td>

                    <td>${item.lembaga_penyelenggara}</td>

                    <td>${item.kategori_penyelenggara}</td>

                    <td>${item.waktu_kegiatan}</td>

                    <td>${status}</td>

                    <td>

                        <button
                            class="btn btn-sm btn-light border">
                                0
                            <i class="bi bi-eye"></i>

                        </button>

                    </td>

                </tr>
                `;
                    });

                    $('#tablePrestasi tbody').html(html);

                }
            );

        });
    </script>
@endpush
