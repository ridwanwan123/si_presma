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

        .aduan-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .86rem;
        }

        .aduan-table thead th {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            padding: 10px 12px;
            border-bottom: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .aduan-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .badge-tingkat {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .74rem;
            font-weight: 700;
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-tindak {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .78rem;
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
                <h2>Aduan Masyarakat</h2>
                <p>Catatan aduan masyarakat yang memengaruhi potongan nilai bidang Lembaga, periode {{ $periode }}.</p>
            </div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahAduan">
                <i class="bi bi-plus-lg"></i> Tambah Aduan
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
                    <table class="aduan-table">
                        <thead>
                            <tr>
                                <th>Madrasah</th>
                                <th>Tingkat Aduan</th>
                                <th>Permasalahan</th>
                                <th class="text-center">Tindak Lanjut</th>
                                <th>Tanggal</th>
                                <th style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftarAduan as $aduan)
                                <tr>
                                    <td class="fw-semibold">{{ $aduan->madrasah->nama_madrasah ?? '-' }}</td>
                                    <td><span class="badge-tingkat">{{ $aduan->tingkat_aduan }}</span></td>
                                    <td>{{ $aduan->permasalahan }}</td>
                                    <td class="text-center"><span
                                            class="badge-tindak">{{ $aduan->jumlah_tindak_lanjut }}x</span></td>
                                    <td>{{ $aduan->tanggal_aduan->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#modalEditAduan{{ $aduan->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('aduan-masyarakat.destroy', $aduan->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Hapus catatan aduan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- MODAL EDIT --}}
                                <div class="modal fade" id="modalEditAduan{{ $aduan->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('aduan-masyarakat.update', $aduan->id) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Aduan Masyarakat</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="periode" value="{{ $aduan->periode }}">

                                                    <label class="form-label">Madrasah</label>
                                                    <select name="madrasah_id" class="form-select mb-3" required>
                                                        @foreach ($daftarMadrasah as $m)
                                                            <option value="{{ $m->id }}"
                                                                {{ $aduan->madrasah_id == $m->id ? 'selected' : '' }}>
                                                                {{ $m->nama_madrasah }}</option>
                                                        @endforeach
                                                    </select>

                                                    <label class="form-label">Tingkat Aduan</label>
                                                    <select name="tingkat_aduan" class="form-select mb-3" required>
                                                        @foreach ($tingkatAduanList as $t)
                                                            <option value="{{ $t }}"
                                                                {{ $aduan->tingkat_aduan == $t ? 'selected' : '' }}>
                                                                {{ $t }}</option>
                                                        @endforeach
                                                    </select>

                                                    <label class="form-label">Permasalahan</label>
                                                    <input type="text" name="permasalahan" class="form-control mb-3"
                                                        value="{{ $aduan->permasalahan }}" required>

                                                    <label class="form-label">Jumlah Tindak Lanjut</label>
                                                    <input type="number" min="1" name="jumlah_tindak_lanjut"
                                                        class="form-control mb-3"
                                                        value="{{ $aduan->jumlah_tindak_lanjut }}" required>

                                                    <label class="form-label">Tanggal Aduan</label>
                                                    <input type="date" name="tanggal_aduan" class="form-control mb-3"
                                                        value="{{ $aduan->tanggal_aduan->format('Y-m-d') }}" required>

                                                    <label class="form-label">Catatan (opsional)</label>
                                                    <textarea name="catatan" class="form-control" rows="2">{{ $aduan->catatan }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">Belum ada catatan aduan
                                        masyarakat untuk periode {{ $periode }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $daftarAduan->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection
{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahAduan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('aduan-masyarakat.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aduan Masyarakat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="periode" value="{{ $periode }}">

                    <label class="form-label">Madrasah</label>
                    <select name="madrasah_id" class="form-select mb-3" required>
                        <option value="" disabled selected>-- Pilih Madrasah --</option>
                        @foreach ($daftarMadrasah as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_madrasah }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Tingkat Aduan</label>
                    <select name="tingkat_aduan" class="form-select mb-3" required>
                        <option value="" disabled selected>-- Pilih Tingkat --</option>
                        @foreach ($tingkatAduanList as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Permasalahan</label>
                    <input type="text" name="permasalahan" class="form-control mb-3"
                        placeholder="Ringkasan permasalahan" required>

                    <label class="form-label">Jumlah Tindak Lanjut</label>
                    <input type="number" min="1" name="jumlah_tindak_lanjut" class="form-control mb-3"
                        required>

                    <label class="form-label">Tanggal Aduan</label>
                    <input type="date" name="tanggal_aduan" class="form-control mb-3" required>

                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
