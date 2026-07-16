<?php

namespace App\Services;

use App\Models\AduanMasyarakat;
use App\Models\KeterlambatanBerkas;
use App\Models\PengaturanPenguranganPoin;

class PenguranganPoinService
{
    /*
    |--------------------------------------------------------------------------
    | ADUAN MASYARAKAT — potongan PERSEN, dihitung dari total nilai bidang
    | LEMBAGA saja.
    |--------------------------------------------------------------------------
    | Kalau dalam satu periode ada BEBERAPA permasalahan aduan terpisah,
    | masing-masing dihitung tier persennya sendiri-sendiri berdasarkan
    | jumlah_tindak_lanjut MASING-MASING permasalahan itu, lalu persennya
    | DIJUMLAH -- karena tiap permasalahan adalah pelanggaran yang berdiri
    | sendiri, bukan diambil yang paling berat saja.
    |--------------------------------------------------------------------------
    */
    public function persenPotonganAduan(int $madrasahId, int $periode): float
    {
        $daftarAduan = AduanMasyarakat::where('madrasah_id', $madrasahId)
            ->where('periode', $periode)
            ->get();

        $totalPersen = 0;

        foreach ($daftarAduan as $aduan) {
            $totalPersen += $this->persenPerTierTindakLanjut($aduan->jumlah_tindak_lanjut);
        }

        return $totalPersen;
    }

    private function persenPerTierTindakLanjut(int $jumlahTindakLanjut): float
    {
        if ($jumlahTindakLanjut > 3) {
            return PengaturanPenguranganPoin::nilai('aduan_lebih_3_kali');
        }

        if ($jumlahTindakLanjut === 3) {
            return PengaturanPenguranganPoin::nilai('aduan_3_kali');
        }

        if ($jumlahTindakLanjut >= 1) {
            return PengaturanPenguranganPoin::nilai('aduan_1_2_kali');
        }

        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | KETERLAMBATAN BERKAS — potongan POIN FLAT, dari total nilai SELURUH
    | bidang (bukan cuma satu bidang). Satu madrasah cuma bisa punya SATU
    | catatan keterlambatan per periode (lihat unique constraint migration).
    |--------------------------------------------------------------------------
    */
    public function poinPotonganKeterlambatan(int $madrasahId, int $periode): float
    {
        $hariTerlambat = KeterlambatanBerkas::where('madrasah_id', $madrasahId)
            ->where('periode', $periode)
            ->value('jumlah_hari_terlambat');

        if (!$hariTerlambat) {
            return 0;
        }

        if ($hariTerlambat >= 3) {
            return PengaturanPenguranganPoin::nilai('telat_3_hari_lebih');
        }

        if ($hariTerlambat === 2) {
            return PengaturanPenguranganPoin::nilai('telat_2_hari');
        }

        return PengaturanPenguranganPoin::nilai('telat_1_hari');
    }

    /*
    |--------------------------------------------------------------------------
    | HITUNG NILAI AKHIR SETELAH POTONGAN
    |--------------------------------------------------------------------------
    | $totalLembaga     = total nilai_akhir KHUSUS bidang Lembaga saja
    | $totalKeseluruhan = total nilai_akhir SELURUH bidang digabung
    |
    | TIDAK mengubah data penilaian_prestasis yang asli -- potongan ini
    | murni dihitung ulang tiap dipanggil (on-the-fly), supaya nilai asli
    | hasil penilaian asesor tetap utuh dan bisa ditelusuri/diaudit.
    |--------------------------------------------------------------------------
    */
    public function hitungSetelahPotongan(
        int $madrasahId,
        int $periode,
        float $totalLembaga,
        float $totalKeseluruhan
    ): array {
        $persenAduan = $this->persenPotonganAduan($madrasahId, $periode);
        $potonganAduan = round($totalLembaga * ($persenAduan / 100), 2);

        $totalSetelahAduan = $totalKeseluruhan - $potonganAduan;

        $potonganKeterlambatan = $this->poinPotonganKeterlambatan($madrasahId, $periode);

        $totalAkhir = $totalSetelahAduan - $potonganKeterlambatan;

        return [
            'total_sebelum_potongan' => round($totalKeseluruhan, 2),
            'persen_potongan_aduan' => $persenAduan,
            'potongan_aduan' => $potonganAduan,
            'potongan_keterlambatan' => round($potonganKeterlambatan, 2),
            'total_potongan' => round($potonganAduan + $potonganKeterlambatan, 2),
            // Nilai akhir tidak boleh negatif -- dibatasi minimal 0.
            'total_akhir' => round(max(0, $totalAkhir), 2),
        ];
    }
}