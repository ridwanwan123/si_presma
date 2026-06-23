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

        .content-card {
            border: 1px solid #ebeef0;
            border-radius: 18px;
            transition: .2s;
        }

        .content-card:hover {
            box-shadow: 0 .25rem .8rem rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .content-card .badge {
            font-size: .75rem;
        }

        /* PAGINATION */
        .pagination {
            gap: .35rem;
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            border-radius: 8px;
            min-width: 38px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 .75rem;
            font-size: .875rem;
            color: #475569;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Active */
        .pagination .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        /* Hover */
        .pagination .page-link:hover {
            background: #eff6ff;
            color: #2563eb;

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
                                <option>NASIONAL</option>
                                <option>PROVINSI</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Kategori</option>
                                <option>INDIVIDU</option>
                                <option>BERGERE</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select form-select-sm">
                                <option>Penyelenggara</option>
                                <option>PEMERINTAH</option>
                                <option>NON PEMERINTAH</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>

                        <div class="col-md-1">
                            <a href="#" class="btn btn-light border btn-sm w-100">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- LIST DATA --}}
        @if ($prestasi->count())
            @foreach ($prestasi as $item)
                <div class="content-card p-3 mb-3">
                    <div class="row align-items-center">
                        {{-- INFORMASI --}}
                        <div class="col-lg-9">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="fw-bold fs-5">
                                        {{ $item->nama_kegiatan }}
                                    </div>
                                    <div class="text-muted">
                                        {{ $item->lembaga_penyelenggara }}
                                    </div>
                                </div>
                                <span class="badge bg-primary">
                                    {{ $item->bidang_prestasi }}
                                </span>
                            </div>

                            <div class="mt-2 d-flex flex-wrap gap-2">
                                <span class="badge bg-info text-dark">
                                    {{ $item->tingkat }}
                                </span>
                                <span class="badge bg-dark">
                                    {{ $item->kategori_kegiatan }}
                                </span>
                                <span class="badge bg-success">
                                    🏆 {{ $item->juara }}
                                </span>
                            </div>

                            <div class="small text-muted mt-3">
                                <i class="bi bi-bank"></i>
                                {{ $item->penyelenggara }}
                                <span class="mx-2">•</span>
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($item->waktu_kegiatan)->translatedFormat('d F Y') }}
                                @if ($item->link_drive_bukti)
                                    <span class="mx-2">•</span>
                                    <a href="{{ $item->link_drive_bukti }}" target="_blank">
                                        Bukti Prestasi
                                    </a>
                                @endif
                            </div>

                            @if ($item->keterangan)
                                <div class="small text-secondary mt-2 mb-2">
                                    <i class="bi bi-chat-left-text"></i>
                                    {{ $item->keterangan }}
                                </div>
                            @endif
                        </div>

                        {{-- NILAI --}}
                        <div class="col-lg-3">
                            <div class="border-start ps-3 h-100">
                                {{-- STATUS --}}
                                @if ($item->status_verifikasi == 'verified')
                                    <span class="badge bg-success mb-3">
                                        <i class="bi bi-check-circle"></i>
                                        Sudah Dinilai
                                    </span>
                                @elseif($item->status_verifikasi == 'rejected')
                                    <span class="badge bg-danger mb-3">
                                        <i class="bi bi-x-circle"></i>
                                        Tidak Dinilai
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark mb-3">
                                        <i class="bi bi-clock"></i>
                                        Belum Dinilai
                                    </span>
                                @endif

                                <div class="small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">
                                            Luring
                                        </span>
                                        <b>
                                            {{ $item->skor_luring }}
                                        </b>
                                    </div>

                                    <div class="d-flex justify-content-between mt-1">
                                        <span class="text-muted">
                                            Daring
                                        </span>
                                        <b>
                                            {{ $item->skor_daring }}
                                        </b>
                                    </div>

                                    @if ($item->status_verifikasi == 'verified')
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">
                                                Presentase
                                            </span>
                                            <b>
                                                {{ $item->presentase }}%
                                            </b>
                                        </div>

                                        <div class="d-flex justify-content-between mt-1">
                                            <span class="text-muted">
                                                Nilai Akhir
                                            </span>
                                            <b class="text-success">
                                                {{ $item->nilai_akhir }}
                                            </b>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tombol --}}
                                <div class="mt-3">
                                    @if ($item->status_verifikasi == 'pending')
                                        <a href="" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-clipboard-check"></i>
                                            Review
                                        </a>
                                    @elseif($item->status_verifikasi == 'verified')
                                        <a href="" class="btn btn-outline-success btn-sm w-100">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </a>
                                    @else
                                        <a href="" class="btn btn-outline-danger btn-sm w-100">
                                            <i class="bi bi-exclamation-circle"></i>
                                            Alasan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($item->status_verifikasi == 'verified' && $item->catatan_verifikasi)
                        <div class="mt-3 pt-3 border-top">
                            <div class="alert alert-success mb-0 py-2 px-3">
                                <div class="small fw-semibold">
                                    <i class="bi bi-chat-square-text"></i>
                                    Catatan Assessor
                                </div>
                                <div class="small mt-1">
                                    {{ $item->catatan_verifikasi }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="content-card p-5 text-center">
                <i class="bi bi-award fs-1 text-muted"></i>
                <h5 class="fw-bold mt-3">
                    Belum Ada Prestasi
                </h5>
                <div class="text-muted">
                    Prestasi yang ditambahkan akan muncul di sini.
                </div>
                <div class="small text-secondary mt-3">
                    <i class="bi bi-arrow-up-circle"></i>
                    Gunakan tombol <b>Tambah</b> atau <b>Import</b> di atas.
                </div>
            </div>
        @endif
        <div class="mt-3">
            {{ $prestasi->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    </main>
@endsection
