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
    | HITUNG NILAI AKHIR SETELAH POTONGAN — PER BIDANG (JMA menentukan juara
    | per Bidang x Jenjang, jadi potongan juga harus dipecah per bidang)
    |--------------------------------------------------------------------------
    | Aturan pembagian:
    | - Aduan Masyarakat: TETAP cuma menyunat bidang LEMBAGA saja (tidak
    |   berubah dari aturan asli).
    | - Keterlambatan Berkas: sifatnya administratif untuk keseluruhan
    |   madrasah (bukan spesifik satu bidang), jadi poinnya DIBAGI RATA ke
    |   5 bidang.
    | - Bidang Lembaga jadi satu-satunya yang kena DUA potongan sekaligus
    |   (Aduan + jatah Keterlambatan) -- bidang lain cuma kena jatah
    |   Keterlambatan.
    |
    | $nilaiPerBidang = ['Akademik' => x, 'Non Akademik' => y, 'Keagamaan' => z,
    |                     'GTK' => w, 'Lembaga' => v] -- nilai MENTAH (asli
    |                     dari asesor, belum ada potongan apapun).
    |--------------------------------------------------------------------------
    */
    public function hitungSetelahPotonganPerBidang(int $madrasahId, int $periode, array $nilaiPerBidang): array
    {
        $persenAduan = $this->persenPotonganAduan($madrasahId, $periode);
        $poinKeterlambatanTotal = $this->poinPotonganKeterlambatan($madrasahId, $periode);

        $jumlahBidang = count($nilaiPerBidang) ?: 1;
        $potonganKeterlambatanPerBidang = round($poinKeterlambatanTotal / $jumlahBidang, 2);

        $hasil = [];
        $totalNilaiMentah = 0;
        $totalPotongan = 0;
        $totalNilaiAkhir = 0;

        foreach ($nilaiPerBidang as $bidang => $nilaiMentah) {

            $potonganAduanBidang = $bidang === 'Lembaga'
                ? round($nilaiMentah * ($persenAduan / 100), 2)
                : 0.0;

            $totalPotonganBidang = round($potonganAduanBidang + $potonganKeterlambatanPerBidang, 2);
            $nilaiAkhirBidang = round(max(0, $nilaiMentah - $totalPotonganBidang), 2);

            $hasil[$bidang] = [
                'nilai_mentah'           => round($nilaiMentah, 2),
                'potongan_aduan'         => $potonganAduanBidang,
                'potongan_keterlambatan' => $potonganKeterlambatanPerBidang,
                'total_potongan'         => $totalPotonganBidang,
                'nilai_akhir'            => $nilaiAkhirBidang,
            ];

            $totalNilaiMentah += $nilaiMentah;
            $totalPotongan += $totalPotonganBidang;
            $totalNilaiAkhir += $nilaiAkhirBidang;
        }

        return [
            'per_bidang'         => $hasil,
            'persen_aduan'       => $persenAduan,
            'total_nilai_mentah' => round($totalNilaiMentah, 2),
            'total_potongan'     => round($totalPotongan, 2),
            // Referensi/statistik saja -- BUKAN dasar penentuan juara lagi,
            // karena juara sekarang ditentukan per Bidang x Jenjang.
            'total_nilai_akhir'  => round($totalNilaiAkhir, 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | [LAMA] HITUNG NILAI AKHIR SETELAH POTONGAN -- AGREGAT
    |--------------------------------------------------------------------------
    | Dipertahankan untuk kompatibilitas kalau ada pemanggil lain, TAPI
    | sudah tidak dipakai lagi oleh RankingController/RankingArsipController
    | (keduanya sudah pindah ke hitungSetelahPotonganPerBidang() di atas).
    | Secara matematis hasil 'total_akhir' di sini identik dengan
    | 'total_nilai_akhir' hasil penjumlahan per bidang di atas.
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