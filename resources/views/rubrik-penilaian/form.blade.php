@php
    $r = $rubrik; // null kalau mode Tambah
@endphp

<div class="rp-form-body">

    {{-- ================= KLASIFIKASI (selalu tampil) ================= --}}
    <div class="form-section">
        <div class="form-section-title">Klasifikasi</div>
        <div class="form-grid-3">
            <div>
                <label class="form-label">Bidang Prestasi *</label>
                <select name="bidang_prestasi" class="form-select" required>
                    <option value="" disabled {{ !$r ? 'selected' : '' }}>-- Pilih --</option>
                    @foreach ($bidangList as $item)
                        <option value="{{ $item }}" {{ $r && $r->bidang_prestasi == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Jenis Rubrik *</label>
                <select name="jenis_rubrik" class="form-select select-jenis-rubrik" required>
                    <option value="" disabled {{ !$r ? 'selected' : '' }}>-- Pilih --</option>
                    @foreach ($jenisRubrikList as $item)
                        <option value="{{ $item }}" {{ $r && $r->jenis_rubrik == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Tahun Berlaku *</label>
                <input type="number" name="tahun_berlaku" class="form-control" min="2000" max="2100" required
                    value="{{ old('tahun_berlaku', $r->tahun_berlaku ?? date('Y')) }}">
            </div>

            <div class="full-width">
                <p class="form-hint mb-0">
                    <strong>"Lomba"</strong> = kompetisi siswa/GTK, dinilai lewat kombinasi Tingkat &times; Juara.
                    Jenis lainnya (Karya, Kelembagaan, Hafalan) pakai kriteria bebas berdasarkan nama kriteria
                    atau rentang nilai.
                </p>
            </div>
        </div>
    </div>

    {{-- ================= GRUP "LOMBA" (Tingkat x Juara) ================= --}}
    <div class="form-panel grup-lomba">
        <div class="form-panel-head"><i class="bi bi-trophy"></i> Kriteria Lomba</div>
        <div class="form-grid-2">
            <div>
                <label class="form-label">Tingkat</label>
                <select name="tingkat" class="form-select">
                    <option value="">-- Tidak dipakai --</option>
                    @foreach ($tingkatList as $item)
                        <option value="{{ $item }}" {{ $r && $r->tingkat == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Juara</label>
                <select name="juara" class="form-select">
                    <option value="">-- Tidak dipakai --</option>
                    @foreach ($juaraList as $item)
                        <option value="{{ $item }}" {{ $r && $r->juara == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Kategori Kegiatan</label>
                <select name="kategori_kegiatan" class="form-select">
                    <option value="">-- Tidak dipakai --</option>
                    @foreach ($kategoriKegiatanList as $item)
                        <option value="{{ $item }}"
                            {{ $r && $r->kategori_kegiatan == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Kategori Penyelenggara</label>
                <select name="kategori_penyelenggara" class="form-select">
                    <option value="">-- Tidak dipakai --</option>
                    @foreach ($penyelenggaraList as $item)
                        <option value="{{ $item }}"
                            {{ $r && $r->kategori_penyelenggara == $item ? 'selected' : '' }}>{{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="full-width">
                <label class="form-label">Metode Pelaksanaan</label>
                <select name="metode_pelaksanaan" class="form-select">
                    <option value="">-- Tidak dipakai (khusus GTK) --</option>
                    @foreach ($metodeList as $item)
                        <option value="{{ $item }}"
                            {{ $r && $r->metode_pelaksanaan == $item ? 'selected' : '' }}>
                            {{ $item }}</option>
                    @endforeach
                </select>
                <p class="form-hint">Kosongkan untuk rubrik GTK (Juknis tidak membedakan Luring/Daring untuk GTK).</p>
            </div>
        </div>
    </div>

    {{-- ================= GRUP "FLEKSIBEL" (Karya/Kelembagaan/Hafalan) ================= --}}
    <div class="form-panel grup-fleksibel">
        <div class="form-panel-head"><i class="bi bi-sliders"></i> Kriteria Bebas</div>
        <div class="form-grid-2">
            <div class="full-width">
                <label class="form-label">Nama Kriteria</label>
                <input type="text" name="kriteria_khusus" class="form-control"
                    placeholder="Mis. Adiwiyata, Penulis Jurnal Scopus, 5 Juz"
                    value="{{ old('kriteria_khusus', $r->kriteria_khusus ?? '') }}">
            </div>

            <div>
                <label class="form-label">Nilai Minimal (opsional)</label>
                <input type="number" step="0.01" name="nilai_min" class="form-control"
                    placeholder="Buat rentang angka" value="{{ old('nilai_min', $r->nilai_min ?? '') }}">
            </div>

            <div>
                <label class="form-label">Nilai Maksimal (opsional)</label>
                <input type="number" step="0.01" name="nilai_max" class="form-control"
                    placeholder="Buat rentang angka" value="{{ old('nilai_max', $r->nilai_max ?? '') }}">
            </div>

            <div class="full-width">
                <p class="form-hint mb-0">
                    Isi "Nilai Minimal/Maksimal" <strong>hanya</strong> kalau kriterianya berbasis rentang angka
                    (misal hafalan Alquran per Juz, atau persentase serapan lulusan). Kalau kriterianya cuma nama
                    tetap (misal "Adiwiyata"), cukup isi "Nama Kriteria" saja, biarkan rentang kosong.
                </p>
            </div>
        </div>
    </div>

    {{-- ================= HASIL ================= --}}
    <div class="form-section">
        <div class="form-section-title">Hasil</div>
        <div class="form-grid-3">
            <div>
                <label class="form-label">Skor *</label>
                <input type="number" step="0.01" min="0" name="skor" class="form-control" required
                    value="{{ old('skor', $r->skor ?? '') }}">
            </div>

            <div class="full-width">
                <label class="form-label">Keterangan (opsional)</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $r->keterangan ?? '') }}</textarea>
            </div>
        </div>
    </div>

</div>
