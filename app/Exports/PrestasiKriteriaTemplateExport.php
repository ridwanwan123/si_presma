<?php

namespace App\Exports;

use App\Models\RubrikPenilaian;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

/*
|--------------------------------------------------------------------------
| TEMPLATE EXPORT "KRITERIA" (Template B) -- dipakai GTK-Karya, Keagamaan-
| Hafalan, Lembaga. Constructor butuh tau PERSIS bidang+jenis_rubrik+tahun
| mana yang diminta, karena isi dropdown-nya beda-beda per kombinasi itu
| (mis. dropdown Lembaga isinya "Adiwiyata, Sekolah Sehat, ...", dropdown
| Hafalan isinya "1 Juz, 2 Juz, ..., 30 Juz") -- BUKAN di-hardcode di sini,
| tapi query langsung ke RubrikPenilaian, supaya kalau Admin ubah/tambah
| rubrik lewat halaman Kelola Rubrik, template yang di-download otomatis
| ikut update tanpa perlu ubah kode export ini.
|
| BEDA UTAMA dari PrestasiTemplateExport (Template A / Lomba): kolom-kolom
| di Template A dibiarkan teks bebas TANPA validasi Excel. Di sini validasi
| WAJIB ada -- kriteria_khusus dicocokkan EXACT STRING ke database (lihat
| RubrikPenilaian::cariKriteriaDariKoleksi()), jadi satu typo/spasi beda
| bikin baris ditolak sistem tanpa Madrasah tahu kenapa. Dropdown mencegah
| user ngetik bebas dari awal, bukan cuma mengandalkan validasi setelah
| upload.
|--------------------------------------------------------------------------
*/
class PrestasiKriteriaTemplateExport implements FromArray, WithHeadings, WithEvents
{
    // Jumlah baris kosong di bawah header yang disiapkan buat diisi user,
    // sekaligus batas berapa baris yang dikasih dropdown validation.
    private const BARIS_KOSONG_DISIAPKAN = 500;

    public function __construct(
        private string $bidangPrestasi,
        private string $jenisRubrik,
        private int $tahun
    ) {
    }

    public function headings(): array
    {
        $headings = ['bidang_prestasi', 'nama_kegiatan'];

        if ($this->pakaiTingkat()) {
            $headings[] = 'tingkat';
        }

        $headings[] = 'kriteria_khusus';
        $headings[] = 'waktu_kegiatan';
        $headings[] = 'link_drive_bukti';
        $headings[] = 'keterangan';

        return $headings;
    }

    public function array(): array
    {
        return [
            // kosong -- sama seperti PrestasiTemplateExport, template cuma
            // berisi header + dropdown, Madrasah yang isi datanya.
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | jenis_rubrik 'Hafalan' (Keagamaan) TIDAK PUNYA kolom tingkat --
    | rubriknya cuma dibedakan per kriteria_khusus ("1 Juz".."30 Juz"),
    | lihat RubrikPenilaianSeeder::seedHafalanQuran(). 'Karya' (GTK) dan
    | 'Kelembagaan' (Lembaga) SAMA-SAMA pakai tingkat.
    |--------------------------------------------------------------------------
    */
    private function pakaiTingkat(): bool
    {
        return $this->jenisRubrik !== 'Hafalan';
    }

    /*
    |--------------------------------------------------------------------------
    | PASANG DROPDOWN -- 'tingkat' (kalau ada) dan 'kriteria_khusus' SELALU.
    | Posisi kolomnya dihitung dinamis dari headings(), bukan di-hardcode
    | 'C'/'D' langsung -- supaya kalau urutan headings() berubah nanti,
    | dropdown-nya nggak ikut salah tempel tanpa ketahuan.
    |--------------------------------------------------------------------------
    */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $urutanKolom = array_values($this->headings());

                if ($this->pakaiTingkat()) {
                    $this->pasangDropdown(
                        $sheet,
                        $this->hurufKolom($urutanKolom, 'tingkat'),
                        $this->daftarTingkat()
                    );
                }

                $this->pasangDropdown(
                    $sheet,
                    $this->hurufKolom($urutanKolom, 'kriteria_khusus'),
                    $this->daftarKriteria()
                );
            },
        ];
    }

    private function hurufKolom(array $urutanKolom, string $namaKolom): string
    {
        $indeks = array_search($namaKolom, $urutanKolom, true);

        // +1 karena index PHP mulai dari 0, tapi kolom Excel mulai dari 1 (A).
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indeks + 1);
    }

    private function daftarTingkat(): \Illuminate\Support\Collection
    {
        return RubrikPenilaian::where('bidang_prestasi', $this->bidangPrestasi)
            ->where('jenis_rubrik', $this->jenisRubrik)
            ->where('tahun_berlaku', $this->tahun)
            ->whereNotNull('tingkat')
            ->distinct()
            ->orderByRaw("FIELD(tingkat, 'Internasional', 'Nasional', 'Provinsi', 'Kabupaten/Kota')")
            ->pluck('tingkat');
    }

    private function daftarKriteria(): \Illuminate\Support\Collection
    {
        return RubrikPenilaian::where('bidang_prestasi', $this->bidangPrestasi)
            ->where('jenis_rubrik', $this->jenisRubrik)
            ->where('tahun_berlaku', $this->tahun)
            ->whereNotNull('kriteria_khusus')
            ->orderBy('tingkat')
            ->orderBy('kriteria_khusus')
            ->get(['tingkat', 'kriteria_khusus'])
            ->pluck('kriteria_khusus')
            ->unique()
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | CATATAN PENTING: Excel data validation TYPE_LIST dengan formula
    | literal ("opsi1,opsi2,...") dibatasi ±255 karakter TOTAL oleh Excel.
    | Untuk Lembaga (29 kriteria, beberapa labelnya panjang) ini SUDAH
    | melebihi batas itu kalau digabung jadi satu string -- makanya dropdown
    | di sini ditulis ke SHEET TERSEMBUNYI ('DaftarOpsi') lalu direferensi
    | pakai range, bukan literal string. Ini juga otomatis jadi solusi buat
    | GTK-Karya (14 opsi) dan Hafalan (30 opsi) sekalian, konsisten satu
    | pendekatan buat semua kolom dropdown di file ini.
    |--------------------------------------------------------------------------
    */
    private function pasangDropdown($sheet, string $kolom, \Illuminate\Support\Collection $opsi): void
    {
        if ($opsi->isEmpty()) {
            return;
        }

        $spreadsheet = $sheet->getParent();
        $namaSheetOpsi = 'DaftarOpsi_' . $kolom;

        // Sheet tersembunyi khusus nampung daftar opsi kolom ini -- 1 sheet
        // per kolom (bukan 1 sheet gabungan semua kolom), supaya rangenya
        // simpel (selalu mulai dari A1) tanpa perlu hitung offset kolom.
        $sheetOpsi = $spreadsheet->createSheet();
        $sheetOpsi->setTitle($namaSheetOpsi);
        $sheetOpsi->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        foreach ($opsi->values() as $i => $nilai) {
            $sheetOpsi->setCellValue('A' . ($i + 1), $nilai);
        }

        $rangeFormula = "={$namaSheetOpsi}!\$A\$1:\$A\$" . $opsi->count();

        for ($i = 2; $i <= self::BARIS_KOSONG_DISIAPKAN; $i++) {
            $validasi = $sheet->getCell("{$kolom}{$i}")->getDataValidation();
            $validasi->setType(DataValidation::TYPE_LIST);
            $validasi->setErrorStyle(DataValidation::STYLE_STOP);
            $validasi->setAllowBlank(true);
            $validasi->setShowDropDown(true);
            $validasi->setShowInputMessage(true);
            $validasi->setShowErrorMessage(true);
            $validasi->setErrorTitle('Input tidak valid');
            $validasi->setError('Pilih dari daftar dropdown yang tersedia, jangan ketik manual -- sistem mencocokkan skor berdasarkan teks yang PERSIS sama.');
            $validasi->setFormula1($rangeFormula);
        }
    }
}