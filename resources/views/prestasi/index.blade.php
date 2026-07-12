@extends('layouts.base')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/prestasi/index.css') }}">
@endpush
@section('content')
    <main class="content"> {{-- HEADER --}}
        <div class="page-title">
            <div>
                <h2>Prestasi {{ ucfirst($jenis) }}</h2>
                <p>Data lomba & hasil verifikasi assessor</p>
            </div>
            <div class="action-group">
                <a href="{{ route('prestasi.create', $jenis) }}"
                    class="btn btn-brand-fill btn-sm {{ request()->routeIs('prestasi.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-lg"></i>
                    Tambah
                </a>
                <a href="{{ route('prestasi.import', $jenis) }}"
                    class="btn btn-brand-outline btn-sm {{ request()->routeIs('prestasi.import') ? 'active' : '' }}">
                    <i class="bi bi-upload"></i>
                    Import
                </a>
            </div>
        </div>
        {{-- SUMMARY --}}
        <div class="row row-cols-2 row-cols-lg-4 g-3 mb-4">

            {{-- Total Prestasi --}}
            <div class="col">
                <div class="summary-card">
                    <div class="summary-icon icon-neutral">
                        <i class="bi bi-trophy"></i>
                    </div>

                    <div class="summary-content">
                        <h2>{{ number_format($summary->total_prestasi ?? 0) }}</h2>
                        <span>Total Prestasi</span>
                    </div>
                </div>
            </div>

            {{-- Tingkat Tertinggi --}}
            @php
                $tingkat = [
                    'Kabupaten/Kota' => $summary->kabupaten ?? 0,
                    'Provinsi' => $summary->provinsi ?? 0,
                    'Nasional' => $summary->nasional ?? 0,
                    'Internasional' => $summary->internasional ?? 0,
                ];

                $topTingkat = array_keys($tingkat, max($tingkat))[0];
                $topJumlah = max($tingkat);
            @endphp

            <div class="col">
                <div class="summary-card">
                    <div class="summary-icon icon-brand">
                        <i class="bi bi-award"></i>
                    </div>

                    <div class="summary-content">
                        <h2>{{ $topJumlah }}</h2>
                        <span>{{ $topTingkat }}</span>
                    </div>
                </div>
            </div>

            {{-- Luring --}}
            <div class="col">
                <div class="summary-card">
                    <div class="summary-icon icon-brand">
                        <i class="bi bi-building"></i>
                    </div>

                    <div class="summary-content">
                        <h2>{{ number_format($summary->total_skor_luring ?? 0) }}</h2>
                        <span>Total Skor Luring</span>
                    </div>
                </div>
            </div>

            {{-- Daring --}}
            <div class="col">
                <div class="summary-card">
                    <div class="summary-icon icon-neutral">
                        <i class="bi bi-globe2"></i>
                    </div>

                    <div class="summary-content">
                        <h2>{{ number_format($summary->total_skor_daring ?? 0) }}</h2>
                        <span>Total Skor Daring</span>
                    </div>
                </div>
            </div>

        </div>


        {{-- DISTRIBUSI TINGKAT --}}
        <div class="content-card p-4 mb-4">

            <div class="d-flex align-items-center mb-4">
                <div class="summary-icon icon-brand me-3">
                    <i class="bi bi-bar-chart"></i>
                </div>

                <div>
                    <h5 class="mb-0 fw-bold">
                        Distribusi Tingkat Prestasi {{ ucfirst($jenis) }}
                    </h5>

                    <small class="text-muted">
                        Sebaran prestasi {{ ucfirst($jenis) }} berdasarkan tingkat kompetisi
                    </small>
                </div>
            </div>

            @php
                $levels = [
                    'Kabupaten/Kota' => $summary->kabupaten ?? 0,
                    'Provinsi' => $summary->provinsi ?? 0,
                    'Nasional' => $summary->nasional ?? 0,
                    'Internasional' => $summary->internasional ?? 0,
                ];

                $max = max($levels) ?: 1;
            @endphp

            <div class="distribusi-list">
                @foreach ($levels as $label => $value)
                    @php
                        $percent = $value > 0 ? max(($value / $max) * 100, 3) : 0;
                    @endphp

                    <div class="distribusi-row">
                        <div class="distribusi-label">{{ $label }}</div>

                        <div class="distribusi-bar-track">
                            <div class="distribusi-bar-fill" style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="distribusi-value">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

        </div>


        {{-- FILTER --}}
        <div class="content-card" hidden>
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
                                <option>Juara</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Lembaga</option>
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

            <div class="table-header">
                <div class="table-title">
                    <div class="table-icon">
                        <i class="bi bi-trophy"></i>
                    </div>

                    <div>
                        <h6 class="mb-1">
                            Daftar Prestasi
                        </h6>

                        <small>
                            Data seluruh prestasi yang telah berhasil diinput dan
                            terverifikasi sistem.
                        </small>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table id="tablePrestasi" class="table table-hover align-middle mb-0 w-100">

                    <thead>
                        <tr>
                            <th style="width:55px">#</th>

                            <th style="width:26%">
                                Info Kegiatan
                            </th>

                            <th style="width:14%">
                                Detail
                            </th>

                            <th style="width:20%">
                                Penyelenggara
                            </th>

                            <th style="width:10%">
                                Waktu
                            </th>

                            <th style="width:9%">
                                Skor
                            </th>

                            <th style="width:9%">
                                Bukti
                            </th>

                            <th style="width:12%">
                                Keterangan
                            </th>

                            <th style="width:90px" class="text-center">
                                Aksi
                            </th>

                        </tr>
                    </thead>

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
            if ($.fn.DataTable.isDataTable('#tablePrestasi')) {
                return;
            }

            $('#tablePrestasi').DataTable({

                processing: true,
                serverSide: true,

                ajax: "{{ route('prestasi.data', $jenis) }}",

                autoWidth: false,
                deferRender: true,
                ordering: true,
                searching: true,
                lengthChange: true,
                pageLength: 10,

                responsive: false,
                scrollX: true,

                language: {
                    search: "",
                    searchPlaceholder: "Cari prestasi...",
                    lengthMenu: "_MENU_",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                    infoEmpty: "Belum ada data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>'
                    }
                },

                dom: "<'row align-items-center g-3 mb-3'" +
                    "<'col-lg-6 col-md-6'l>" +
                    "<'col-lg-6 col-md-6 d-flex justify-content-md-end'f>" +
                    ">" +

                    "rt" +

                    "<'row align-items-center g-3 mt-3'" +
                    "<'col-lg-6 col-md-6'i>" +
                    "<'col-lg-6 col-md-6 d-flex justify-content-md-end'p>" +
                    ">",

                columnDefs: [

                    {
                        targets: 0,
                        width: "55px",
                        className: "text-center"
                    },

                    {
                        targets: 1,
                        width: "27%"
                    },

                    {
                        targets: 2,
                        width: "14%",
                        className: "text-center"
                    },

                    {
                        targets: 3,
                        width: "20%"
                    },

                    {
                        targets: 4,
                        width: "10%",
                        className: "text-center"
                    },

                    {
                        targets: 5,
                        width: "9%",
                        className: "text-center"
                    },

                    {
                        targets: 6,
                        width: "10%",
                        className: "text-center"
                    },

                    {
                        targets: 7,
                        width: "10%"
                    },

                    {
                        targets: 8,
                        width: "90px",
                        className: "text-center"
                    }

                ],

                columns: [

                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },

                    {
                        data: null,
                        render: function(data) {

                            return `
                                <div class="cell-kegiatan">

                                    <div
                                        class="nama-kegiatan"
                                        title="${data.nama_kegiatan}">
                                        ${data.nama_kegiatan}
                                    </div>

                                    <div class="meta-badge">

                                        <span class="badge-soft badge-${data.bidang_prestasi?.toLowerCase().replace(' ', '-')}">
                                            ${data.bidang_prestasi}
                                        </span>

                                        <span class="dot"></span>

                                        <span class="badge-soft tingkat-${data.tingkat?.toLowerCase()}">
                                            ${data.tingkat}
                                        </span>

                                    </div>

                                </div>
                            `;
                        }
                    },

                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {

                            let juaraClass = "juara-default";

                            if (data.juara?.includes("1"))
                                juaraClass = "juara-gold";

                            else if (data.juara?.includes("2"))
                                juaraClass = "juara-silver";

                            else if (data.juara?.includes("3"))
                                juaraClass = "juara-bronze";

                            return `
                                <div class="cell-detail">

                                    <div class="kategori">
                                        ${data.kategori_kegiatan}
                                    </div>

                                    <span class="badge-soft ${juaraClass}">
                                        ${data.juara}
                                    </span>

                                </div>
                            `;
                        }
                    },

                    {
                        data: null,
                        render: function(data) {

                            return `
                                <div class="cell-penyelenggara">

                                    <div class="kategori-penyelenggara">
                                        ${data.kategori_penyelenggara ?? '-'}
                                    </div>

                                    <div
                                        class="nama-penyelenggara"
                                        title="${data.lembaga_penyelenggara ?? '-'}">

                                        ${data.lembaga_penyelenggara ?? '-'}

                                    </div>

                                </div>
                            `;
                        }
                    },

                    {
                        data: 'waktu_kegiatan'
                    },

                    {
                        data: null,
                        render: function(data) {

                            let html = [];

                            if (data.skor_luring) {

                                html.push(`
                                    <span class="badge bg-success-subtle text-success border">
                                        L ${data.skor_luring}
                                    </span>
                                `);

                            }

                            if (data.skor_daring) {

                                html.push(`
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        D ${data.skor_daring}
                                    </span>
                                `);

                            }

                            return html.join(' ');
                        }
                    },

                    {
                        data: 'link_drive_bukti',
                        searchable: false,
                        orderable: false,

                        render: function(data) {

                            if (!data)
                                return "-";

                            return `
                                <a
                                    href="${data}"
                                    target="_blank"
                                    class="btn btn-drive">

                                    <i class="bi bi-box-arrow-up-right"></i>

                                    Bukti

                                </a>
                            `;
                        }
                    },

                    {
                        data: 'keterangan'
                    },

                    {
                        data: 'id',
                        searchable: false,
                        orderable: false,
                        className: "text-center",

                        render: function(data) {

                            const editUrl =
                                "{{ route('prestasi.edit', ['jenis' => $jenis, 'id' => ':id']) }}"
                                .replace(':id', data);

                            const deleteUrl =
                                "{{ route('prestasi.destroy', ['jenis' => $jenis, 'id' => ':id']) }}"
                                .replace(':id', data);

                            return `
                                <div class="d-flex justify-content-center gap-1">

                                    <a
                                        href="${editUrl}"
                                        class="btn btn-sm btn-warning">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>

                                    <form
                                        action="${deleteUrl}"
                                        method="POST"
                                        class="form-delete">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </div>
                            `;
                        }
                    }

                ]

            });

        });
    </script>
    <script>
        document.addEventListener('submit', function(e) {

            if (!e.target.matches('.form-delete')) {
                return;
            }

            e.preventDefault();

            Swal.fire({
                title: 'Hapus Data?',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });

        });
    </script>
@endpush
