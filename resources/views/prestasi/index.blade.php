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
        </div>

        {{-- STATUS SIKLUS --}}
        @php
            $siklusMap = [
                'OPEN' => [
                    'label' => 'Terbuka untuk Pengisian',
                    'icon' => 'bi-unlock-fill',
                    'desc' =>
                        'Operator dapat menambah, mengedit, mengimpor, dan menghapus data prestasi periode ' .
                        $siklus->periode .
                        '.',
                    'border' => '#bbf7d0',
                    'bg' => '#f0fdf4',
                    'icon_bg' => '#dcfce7',
                    'icon_color' => '#15803d',
                ],
                'SUBMITTED' => [
                    'label' => 'Menunggu Penugasan Asesor',
                    'icon' => 'bi-send-check-fill',
                    'desc' =>
                        'Data periode ' .
                        $siklus->periode .
                        ' telah diserahkan dan menunggu penugasan asesor. Data tidak dapat diubah.',
                    'border' => '#bfdbfe',
                    'bg' => '#eff6ff',
                    'icon_bg' => '#dbeafe',
                    'icon_color' => '#1d4ed8',
                ],
                'LOCKED' => [
                    'label' => 'Dikunci untuk Penilaian',
                    'icon' => 'bi-lock-fill',
                    'desc' =>
                        'Data periode ' .
                        $siklus->periode .
                        ' sedang dikunci untuk persiapan penilaian. Data tidak dapat diubah.',
                    'border' => '#fde68a',
                    'bg' => '#fffbeb',
                    'icon_bg' => '#fef3c7',
                    'icon_color' => '#b45309',
                ],
                'ASSESSMENT' => [
                    'label' => 'Sedang Dinilai Asesor',
                    'icon' => 'bi-clipboard-data-fill',
                    'desc' =>
                        'Asesor sedang menilai data periode ' .
                        $siklus->periode .
                        '. Data tidak dapat diubah sampai penilaian selesai.',
                    'border' => '#ddd6fe',
                    'bg' => '#f5f3ff',
                    'icon_bg' => '#ede9fe',
                    'icon_color' => '#6d28d9',
                ],
                'FINISHED' => [
                    'label' => 'Penilaian Selesai',
                    'icon' => 'bi-check-circle-fill',
                    'desc' => 'Penilaian periode ' . $siklus->periode . ' telah selesai. Data periode ini sudah final.',
                    'border' => '#e2e8f0',
                    'bg' => '#f8fafc',
                    'icon_bg' => '#e2e8f0',
                    'icon_color' => '#334155',
                ],
            ];

            $steps = [
                'OPEN' => 'Pengisian',
                'SUBMITTED' => 'Diserahkan',
                'LOCKED' => 'Dikunci',
                'ASSESSMENT' => 'Penilaian',
                'FINISHED' => 'Selesai',
            ];

            $stepKeys = array_keys($steps);
            $currentIndex = array_search($siklus->status, $stepKeys);
            $current = $siklusMap[$siklus->status] ?? $siklusMap['OPEN'];
        @endphp

        <div class="siklus-banner"
            style="--siklus-border: {{ $current['border'] }}; --siklus-bg: {{ $current['bg'] }}; --siklus-icon-bg: {{ $current['icon_bg'] }}; --siklus-icon-color: {{ $current['icon_color'] }};">

            <div class="siklus-banner-icon">
                <i class="bi {{ $current['icon'] }}"></i>
            </div>

            <div class="siklus-banner-body">

                <div class="siklus-banner-title">
                    Periode {{ $siklus->periode }}
                    <span class="badge-status">{{ $current['label'] }}</span>
                </div>

                <div class="siklus-banner-desc">
                    {{ $current['desc'] }}
                </div>

                <div class="siklus-steps">
                    @foreach ($steps as $key => $label)
                        @php
                            $index = array_search($key, $stepKeys);
                            $state =
                                $index < $currentIndex ? 'is-done' : ($index === $currentIndex ? 'is-current' : '');
                        @endphp

                        <div class="siklus-step {{ $state }}">
                            <span class="dot"></span>
                            {{ $label }}
                        </div>

                        @if (!$loop->last)
                            <div class="siklus-step-line"></div>
                        @endif
                    @endforeach
                </div>

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

            @if (($summary->total_prestasi ?? 0) > 0)
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
            @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-bar-chart-line" style="font-size:1.8rem;"></i>
                    <div class="small mt-2">Belum ada data untuk ditampilkan pada periode ini.</div>
                </div>
            @endif

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

            // Aktifkan tooltip Bootstrap untuk tombol yang terkunci status siklus
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });

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

                            if (data.skor === null || data.skor === undefined) {
                                return '-';
                            }

                            const isLuring = data.metode_pelaksanaan === 'Luring';

                            return `
                                <span class="badge ${isLuring ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'} border">
                                    ${isLuring ? 'L' : 'D'} ${data.skor}
                                </span>
                            `;
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
