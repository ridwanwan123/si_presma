@extends('layouts.base')

@push('styles')
    <style>
        .container-fluid {
            padding: 0 1rem;
        }

        .page-title {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
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

        .content-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eef0f2;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            padding: 1.25rem 1.4rem;
            margin-bottom: 1.25rem;
        }

        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .filter-form .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .filter-form .form-select {
            border-radius: 10px;
            min-width: 140px;
        }

        .keterlambatan-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .86rem;
        }

        .keterlambatan-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .keterlambatan-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .badge-hari {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
            background: #fef3c7;
            color: #b45309;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="page-title">
            <div>
                <h2>Keterlambatan Berkas</h2>
                <p>Catatan keterlambatan pengumpulan berkas per madrasah, periode {{ $periode }}. Satu madrasah cuma
                    boleh punya satu catatan per periode.</p>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahKeterlambatan">
                <i class="bi bi-plus-lg"></i> Catat Keterlambatan
            </button>
        </div>

        <div class="container-fluid">

            <div class="content-card">
                <form method="GET" class="filter-form">
                    <div>
                        <label class="form-label">Periode</label>
                        <select name="periode" class="form-select" onchange="this.form.submit()">
                            @foreach ($daftarPeriode as $item)
                                <option value="{{ $item }}" {{ $periode == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="content-card p-0">
                <div class="table-responsive">
                    <table class="keterlambatan-table">
                        <thead>
                            <tr>
                                <th>Madrasah</th>
                                <th class="text-center">Jumlah Hari Terlambat</th>
                                <th>Keterangan</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarKeterlambatan as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->madrasah->nama_madrasah ?? '-' }}</td>
                                    <td class="text-center"><span class="badge-hari">{{ $item->jumlah_hari_terlambat }}
                                            hari</span></td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('keterlambatan-berkas.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus catatan keterlambatan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">Belum ada catatan keterlambatan
                                        untuk periode {{ $periode }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $daftarKeterlambatan->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection
{{-- MODAL TAMBAH/PERBARUI --}}
<div class="modal fade" id="modalTambahKeterlambatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('keterlambatan-berkas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Catat Keterlambatan Berkas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="periode" value="{{ $periode }}">

                    <div class="alert alert-warning-subtle small mb-3"
                        style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.6rem .9rem;color:#92400e;">
                        <i class="bi bi-info-circle"></i>
                        Kalau madrasah yang dipilih sudah punya catatan di periode ini, datanya akan
                        <strong>diperbarui</strong> (bukan dobel).
                    </div>

                    <label class="form-label">Madrasah</label>
                    <select name="madrasah_id" class="form-select mb-3" required>
                        <option value="" disabled selected>-- Pilih Madrasah --</option>
                        @foreach ($daftarMadrasah as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_madrasah }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Jumlah Hari Terlambat</label>
                    <input type="number" min="1" name="jumlah_hari_terlambat" class="form-control mb-3"
                        required>

                    <label class="form-label">Keterangan (opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
