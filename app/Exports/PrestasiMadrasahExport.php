<?php

namespace App\Exports;

use App\Models\Madrasah;
use App\Models\PrestasiSiswa;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrestasiMadrasahExport implements WithEvents
{
    /*
    |--------------------------------------------------------------------------
    | URUTAN BIDANG — satu tabel per bidang, ditumpuk berurutan dalam satu
    | sheet yang sama, sesuai format laporan tahunan Kanwil.
    |--------------------------------------------------------------------------
    */
    private const URUTAN_BIDANG = [
        'Akademik',
        'Non Akademik',
        'Keagamaan',
        'GTK',
        'Lembaga',
    ];

    public function __construct(
        private Madrasah $madrasah,
        private int $periode
    ) {
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->build($event->sheet->getDelegate());
            },
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD SHEET
    |--------------------------------------------------------------------------
    */
    private function build(Worksheet $sheet): void
    {
        $sheet->setTitle('Data Prestasi ' . $this->periode);

        $this->setLebarKolom($sheet);

        $row = 2;

        $row = $this->buildHeaderJudul($sheet, $row);
        $row = $this->buildInfoMadrasah($sheet, $row);

        foreach (self::URUTAN_BIDANG as $bidang) {
            $row = $this->buildTabelBidang($sheet, $bidang, $row);
            $row += 2; // jarak antar section
        }

        $this->setupHalamanCetak($sheet);
    }

    private function setLebarKolom(Worksheet $sheet): void
    {
        $lebar = [
            'A' => 2,
            'B' => 5,
            'C' => 32,
            'D' => 16,
            'E' => 16,
            'F' => 14,
            'G' => 22,
            'H' => 24,
            'I' => 14,
            'J' => 9,
            'K' => 9,
            'L' => 28,
        ];

        foreach ($lebar as $kolom => $width) {
            $sheet->getColumnDimension($kolom)->setWidth($width);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | JUDUL
    |--------------------------------------------------------------------------
    */
    private function buildHeaderJudul(Worksheet $sheet, int $row): int
    {
        $baris = [
            'DATA PRESTASI MADRASAH TAHUN ' . $this->periode,
            'KANTOR WILAYAH KEMENTERIAN AGAMA PROVINSI DKI JAKARTA',
            'TAHUN ' . $this->periode,
        ];

        foreach ($baris as $teks) {
            $sheet->mergeCells("B{$row}:L{$row}");
            $sheet->setCellValue("B{$row}", $teks);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        return $row + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | INFO MADRASAH — diambil langsung dari tabel madrasahs.
    |--------------------------------------------------------------------------
    */
    private function buildInfoMadrasah(Worksheet $sheet, int $row): int
    {
        $info = [
            'Nama Madrasah'        => $this->madrasah->nama_madrasah,
            'Nama Kepala Madrasah' => $this->madrasah->nama_kepala_madrasah,
            'Kota'                 => $this->madrasah->kota,
            'Akreditasi'           => $this->madrasah->akreditasi,
            'Nomor HP'             => $this->madrasah->no_telepon_kamad,
        ];

        foreach ($info as $label => $value) {
            $sheet->setCellValue("C{$row}", $label);
            $sheet->getStyle("C{$row}")->getFont()->setBold(true);

            $sheet->mergeCells("D{$row}:L{$row}");
            $sheet->setCellValue("D{$row}", $value ?: '-');

            $row++;
        }

        return $row + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | TABEL PER BIDANG
    |--------------------------------------------------------------------------
    */
    private function buildTabelBidang(Worksheet $sheet, string $bidang, int $row): int
    {
        // Judul section
        $sheet->mergeCells("B{$row}:L{$row}");
        $sheet->setCellValue("B{$row}", 'Prestasi Bidang ' . $bidang);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12);
        $row++;

        $headerRow1 = $row;
        $headerRow2 = $row + 1;
        $nomorRow   = $row + 2;

        // Kolom yang labelnya vertical-merge 2 baris (No s/d Waktu, Link Drive)
        $headerVertikal = [
            'B' => 'No',
            'C' => "Kegiatan Lomba/\nKompetisi/Penghargaan",
            'D' => "Tingkat\n(Internasional/Nasional/Provinsi/Kota)",
            'E' => "Kategori\nIndividu/Beregu",
            'F' => 'Juara Yang Diraih',
            'G' => "Lembaga Penyelenggara\nKompetisi",
            'H' => "Keterangan Penyelenggara\n(Pemerintah/Non Pemerintah)",
            'I' => 'Waktu (Tgl-Bln-Thn)',
            'L' => 'Link Drive Bukti Dukung',
        ];

        foreach ($headerVertikal as $kolom => $teks) {
            $sheet->setCellValue("{$kolom}{$headerRow1}", $teks);
            $sheet->mergeCells("{$kolom}{$headerRow1}:{$kolom}{$headerRow2}");
        }

        // Skor: header "Skor" merge horizontal J:K, lalu sub-header luring/daring
        $sheet->mergeCells("J{$headerRow1}:K{$headerRow1}");
        $sheet->setCellValue("J{$headerRow1}", 'Skor');
        $sheet->setCellValue("J{$headerRow2}", 'luring');
        $sheet->setCellValue("K{$headerRow2}", 'daring');

        // Baris nomor urut kolom (1..11), sesuai format resmi
        $kolomTabel = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        foreach ($kolomTabel as $i => $kolom) {
            $sheet->setCellValue("{$kolom}{$nomorRow}", $i + 1);
        }

        // Style header (bold, center, wrap, background abu tipis)
        $headerRange = "B{$headerRow1}:L{$nomorRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        $row = $nomorRow + 1;

        /*
        |--------------------------------------------------------------------------
        | DATA — mentah dari PrestasiSiswa, TIDAK join ke penilaian_prestasis.
        |--------------------------------------------------------------------------
        */
        $daftarPrestasi = PrestasiSiswa::visible()
            ->where('bidang_prestasi', $bidang)
            ->where('periode', $this->periode)
            ->orderBy('waktu_kegiatan')
            ->get();

        $no = 1;
        $totalLuring = 0;
        $totalDaring = 0;

        if ($daftarPrestasi->isEmpty()) {

            $sheet->mergeCells("B{$row}:L{$row}");
            $sheet->setCellValue("B{$row}", 'Belum ada data prestasi untuk bidang ini.');
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getFont()->setItalic(true);
            $row++;

        } else {

            foreach ($daftarPrestasi as $item) {

                $sheet->setCellValueExplicit("B{$row}", $no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $sheet->setCellValue("C{$row}", $item->nama_kegiatan);
                $sheet->setCellValue("D{$row}", $item->tingkat);
                $sheet->setCellValue("E{$row}", $item->kategori_kegiatan);
                $sheet->setCellValue("F{$row}", $item->juara);
                $sheet->setCellValue("G{$row}", $item->lembaga_penyelenggara);
                $sheet->setCellValue("H{$row}", $item->kategori_penyelenggara);
                $sheet->setCellValue("I{$row}", $item->waktu_kegiatan ? Carbon::parse($item->waktu_kegiatan)->format('d-m-Y') : '-');

                if ($item->metode_pelaksanaan === 'Luring') {
                    $sheet->setCellValue("J{$row}", $item->skor);
                    $totalLuring += (float) $item->skor;
                } else {
                    $sheet->setCellValue("K{$row}", $item->skor);
                    $totalDaring += (float) $item->skor;
                }

                $sheet->setCellValue("L{$row}", $item->link_drive_bukti);

                $no++;
                $row++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL SKOR
        |--------------------------------------------------------------------------
        */
        $sheet->mergeCells("B{$row}:I{$row}");
        $sheet->setCellValue("B{$row}", 'Total Skor');
        $sheet->getStyle("B{$row}:I{$row}")->getFont()->setBold(true);

        $sheet->setCellValue("J{$row}", $totalLuring);
        $sheet->setCellValue("K{$row}", $totalDaring);
        $sheet->getStyle("J{$row}:K{$row}")->getFont()->setBold(true);

        $lastRow = $row;

        // Border penuh untuk seluruh tabel section ini
        $tableRange = "B{$headerRow1}:L{$lastRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return $lastRow;
    }

    private function setupHalamanCetak(Worksheet $sheet): void
    {
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
    }
}