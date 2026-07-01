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
            <div class="action-group"> <button class="btn btn-success btn-sm">
                    <i class="bi bi-plus"></i>
                    Tambah
                </button> <a href="{{ route('prestasi.import', $jenis) }}"
                    class="btn btn-primary btn-sm {{ request()->routeIs('prestasi.import') ? 'active' : '' }}"">
                    <i class="bi bi-upload"></i>
                    Import
                </a> </div>
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
                        </div> <i class="bi bi-trophy fs-1 text-primary opacity-50"></i>
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
                        </div> <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
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
                        </div> <i class="bi bi-clock fs-1 text-warning opacity-50"></i>
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
                        </div> <i class="bi bi-x-circle fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
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
                            Data seluruh prestasi yang telah berhasil diinput
                            dan terverifikasi sistem
                        </small>
                    </div>
                </div>
            </div>
            <div>
                <table id="tablePrestasi" class="table align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Bidang</th>
                            <th>Nama Kegiatan</th>
                            <th>Tingkat</th>
                            <th>Kategori</th>
                            <th>Juara</th>
                            <th>Lembaga Penyelenggara</th>
                            <th>Penyelenggara</th>
                            <th>Waktu</th>
                            <th>Skor</th>
                            <th>Link Drive</th>
                            <th>Keterangan</th>
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
        $('#tablePrestasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('prestasi.data', $jenis) }}",
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            dom: "<'row align-items-center mb-3'<'col-md-6'l><'col-md-6 d-flex justify-content-md-end'f>>" +
                "rt" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7 d-flex justify-content-md-end'p>>",
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'bidang_prestasi',

                    render: function(data) {

                        let cls = 'badge-secondary';

                        switch (data) {

                            case 'Akademik':
                                cls = 'badge-akademik';
                                break;

                            case 'Non Akademik':
                                cls = 'badge-non';
                                break;

                            case 'Keagamaan':
                                cls = 'badge-keagamaan';
                                break;

                            case 'GTK':
                                cls = 'badge-gtk';
                                break;

                            case 'Lembaga':
                                cls = 'badge-lembaga';
                                break;

                        }

                        return `<span class="badge-soft ${cls}">${data}</span>`;
                    }
                },
                {
                    data: 'nama_kegiatan',

                    render: function(data) {

                        return `
                        <div class="nama-kegiatan"
                            title="${data}">
                            ${data}
                        </div>`;
                    }
                },
                {
                    data: 'tingkat',

                    render: function(data) {

                        let cls = 'tingkat-sekolah';

                        const t = data.toLowerCase();

                        if (t.includes('kecamatan')) cls = 'tingkat-kecamatan';
                        else if (t.includes('kabupaten')) cls = 'tingkat-kabupaten';
                        else if (t.includes('provinsi')) cls = 'tingkat-provinsi';
                        else if (t.includes('nasional')) cls = 'tingkat-nasional';
                        else if (t.includes('internasional')) cls = 'tingkat-internasional';

                        return `<span class="badge-soft ${cls}">${data}</span>`;
                    }
                },
                {
                    data: 'kategori_kegiatan'
                },
                {
                    data: 'juara',

                    render: function(data) {

                        let cls = 'juara-default';

                        if (data.includes('1')) cls = 'juara-gold';
                        else if (data.includes('2')) cls = 'juara-silver';
                        else if (data.includes('3')) cls = 'juara-bronze';

                        return `<span class="badge-soft ${cls}">
                        ${data}
                        </span>`;
                    }
                },
                {
                    data: 'lembaga_penyelenggara',

                    render: function(data) {

                        return `
                        <span
                        title="${data}"
                        style="
                        display:inline-block;
                        max-width:260px;
                        overflow:hidden;
                        text-overflow:ellipsis;
                        white-space:nowrap;">
                        ${data}
                        </span>`;
                    }
                },
                {
                    data: 'kategori_penyelenggara'
                },
                {
                    data: 'waktu_kegiatan'
                },
                {
                    data: null,
                    render: function(data) {

                        let skor = [];

                        if (data.skor_luring) {

                            skor.push(`
                            <span class="badge bg-success-subtle text-success border">
                                Luring ${data.skor_luring}
                            </span>`);
                        }

                        if (data.skor_daring) {

                            skor.push(`
                            <span class="badge bg-primary-subtle text-primary border">
                                Daring ${data.skor_daring}
                            </span>`);
                        }

                        return skor.join('<br>');
                    }
                },
                {
                    data: 'link_drive_bukti',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!data) return "-";
                        return `
                        <a href="${data}" target="_blank"
                        class="btn btn-sm btn-outline-primary btn-drive">
                            <i class="bi bi-box-arrow-up-right me-1"></i>
                            Bukti
                        </a>`;
                    }
                },
                {
                    data: 'keterangan',
                    defaultContent: '-'
                }
            ]
        });
    </script>
@endpush
