<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\Madrasah;
use App\Models\PenilaianPrestasi;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AsesorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OPSI PERSENTASE NILAI (rubrik penilaian tetap, bukan dari database)
    |--------------------------------------------------------------------------
    */
    private const OPSI_PERSENTASE = [0, 5, 70, 80, 85, 90, 95, 100];

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | DATA UTAMA — Assignment milik asesor yang sedang login
        |--------------------------------------------------------------------------
        */

        $assignments = AssignAsesor::query()
            ->where('asesor_id', auth()->id())
            ->with([
                'madrasah' => function ($query) {
                    $query->withCount('prestasis');
                }
            ])
            ->withCount('penilaianPrestasis')
            ->orderByDesc('assigned_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MAPPING DATA
        |--------------------------------------------------------------------------
        */

        $daftarMadrasah = $assignments->map(function (AssignAsesor $assignment) {
            $madrasah = $assignment->madrasah;
            $totalPrestasi = $madrasah->prestasis_count ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Sudah Dinilai
            |--------------------------------------------------------------------------
            | Menghitung seluruh prestasi yang sudah mempunyai record penilaian,
            | baik draft maupun completed.
            */
            $dinilai = $assignment->penilaian_prestasis_count ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Progress
            |--------------------------------------------------------------------------
            */
            $progress = $totalPrestasi > 0
                ? (int) round(($dinilai / $totalPrestasi) * 100)
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Status Penilaian
            |--------------------------------------------------------------------------
            |
            | belum
            |   = belum ada satupun penilaian
            |
            | proses
            |   = sudah mulai menilai, tetapi assignment belum difinalisasi
            |
            | selesai
            |   = assignment sudah completed
            |
            */
            if ($dinilai == 0) {
                $status = 'belum';
            } elseif ($assignment->status === 'completed') {
                $status = 'selesai';
            } else {
                $status = 'proses';
            }

            return [
                'id' => $madrasah->id,
                'nama' => $madrasah->nama_madrasah,
                'npsn' => $madrasah->npsn,
                'jenjang' => $madrasah->jenjang_madrasah,
                'wilayah' => $madrasah->kota,
                'prestasi' => $totalPrestasi,
                'status' => $status,
                'progress' => $progress,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalMadrasah = $daftarMadrasah->count();

        $belumDinilai = $daftarMadrasah
            ->where('status', 'belum')
            ->count();

        $sedangDinilai = $daftarMadrasah
            ->where('status', 'proses')
            ->count();

        $selesaiDinilai = $daftarMadrasah
            ->where('status', 'selesai')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | BREADCRUMB
        |--------------------------------------------------------------------------
        */

        $breadcrumb = breadcrumb([
            'Madrasah yang Dinilai'
        ]);

        return view('asesor.index', compact(
            'breadcrumb',
            'daftarMadrasah',
            'totalMadrasah',
            'belumDinilai',
            'sedangDinilai',
            'selesaiDinilai'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN PENILAIAN MADRASAH
    |--------------------------------------------------------------------------
    | Security: madrasah hanya boleh dibuka kalau memang assignment milik
    | asesor yang sedang login. Validasi berbasis data assign_asesors,
    | BUKAN cuma mengandalkan middleware role.
    */

    public function show(Request $request, Madrasah $madrasah)
    {
        $assignment = $this->assignmentAtauGagal($madrasah);

        /*
        |--------------------------------------------------------------------------
        | STATISTIK — dihitung terpisah dari daftar yang di-paginate.
        |--------------------------------------------------------------------------
        | PENTING: setelah daftar prestasi di-paginate (20/halaman), collection
        | $daftarPrestasi TIDAK BOLEH lagi dipakai untuk menghitung total/rata-rata,
        | karena isinya cuma 20 baris per halaman. Statistik berikut sengaja
        | di-query terpisah supaya tetap merefleksikan SELURUH prestasi madrasah,
        | bukan cuma yang tampil di halaman saat ini — statistik ini juga
        | SENGAJA tidak ikut terpengaruh filter Status Penilaian di bawah.
        */

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $totalPrestasi = $madrasah->prestasis()->count();

        /*
        |--------------------------------------------------------------------------
        | SUDAH DINILAI
        |--------------------------------------------------------------------------
        | Sudah Dinilai = sudah memiliki record pada tabel penilaian_prestasis,
        | baik status draft maupun completed.
        */

        $sudahDinilai = $madrasah->prestasis()
            ->whereHas('penilaianPrestasi')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | BELUM DINILAI
        |--------------------------------------------------------------------------
        | Belum Dinilai = belum memiliki record sama sekali pada tabel
        | penilaian_prestasis.
        */

        $belumDinilai = $madrasah->prestasis()
            ->whereDoesntHave('penilaianPrestasi')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PROGRESS
        |--------------------------------------------------------------------------
        */

        $progresPenilaian = $totalPrestasi > 0
            ? round(($sudahDinilai / $totalPrestasi) * 100)
            : 0;

        // Total & rata-rata nilai akhir: langsung agregasi di database (SUM/AVG),
        // bukan di-loop di PHP, supaya tidak perlu menarik semua baris.
        $agregatNilai = PenilaianPrestasi::whereHas('prestasiSiswa', function ($query) use ($madrasah) {
                $query->where('madrasah_id', $madrasah->id);
            })
            ->whereNotNull('nilai_akhir')
            ->selectRaw('SUM(nilai_akhir) as total_nilai, AVG(nilai_akhir) as rata_nilai')
            ->first();

        $totalNilaiAkhir = round($agregatNilai->total_nilai ?? 0, 2);
        $rataRata = $agregatNilai->total_nilai !== null ? round($agregatNilai->rata_nilai, 1) : 0;

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PRESTASI — Eloquent + Pagination (20/halaman)
        |--------------------------------------------------------------------------
        | Urutan sesuai flow bisnis:
        |   1) Prestasi yang BELUM memiliki penilaian
        |   2) Prestasi dengan status draft
        |   3) Prestasi dengan status completed
        |   lalu di dalam masing-masing kelompok: waktu_kegiatan terbaru duluan.
        |
        | Dipakai LEFT JOIN (bukan whereHas/with saja) supaya status penilaian
        | bisa dijadikan kolom urutan di level SQL — tidak mungkin dilakukan
        | lewat sorting di PHP kalau datanya sudah di-paginate.
        */

        /*
        |--------------------------------------------------------------------------
        | FILTER: Status Penilaian (query string, divalidasi whitelist di server)
        |--------------------------------------------------------------------------
        | belum = BELUM ADA record penilaian sama sekali (penilaian_prestasis.id NULL)
        | sudah = SUDAH ADA record penilaian, apapun statusnya — draft maupun
        |         completed tetap dianggap "sudah dinilai" karena asesor sudah
        |         pernah memberi nilai. Filter ini HANYA soal eksistensi record,
        |         BUKAN soal status final-nya (itu urusan card Ringkasan Nilai).
        | Difilter langsung di level query (leftJoin penilaian_prestasis di
        | bawah), BUKAN filter Collection, supaya tetap konsisten dengan
        | pagination (count & data sama-sama kena filter).
        */

        // Opsi filter (dropdown Tingkat & Penyelenggara) sengaja diambil dari
        // SELURUH prestasi madrasah (bukan dari 20 baris yang tampil di halaman
        // ini), supaya daftar pilihannya tetap lengkap di setiap halaman.
        $daftarTingkat = $madrasah->prestasis()
            ->whereNotNull('tingkat')
            ->distinct()
            ->orderBy('tingkat')
            ->pluck('tingkat');
        
        $daftarBidang = $madrasah->prestasis()
            ->whereNotNull('bidang_prestasi')
            ->distinct()
            ->orderBy('bidang_prestasi')
            ->pluck('bidang_prestasi');

        $daftarPenyelenggara = $madrasah->prestasis()
            ->whereNotNull('lembaga_penyelenggara')
            ->distinct()
            ->orderBy('lembaga_penyelenggara')
            ->pluck('lembaga_penyelenggara');

        $statusPenilaian = $request->query('status_penilaian');
        if (! in_array($statusPenilaian, ['belum', 'sudah'], true)) {
            $statusPenilaian = null;
        }

        $bidang = $request->query('bidang');
        $tingkat = $request->query('tingkat');
        $penyelenggara = $request->query('penyelenggara');

        $prestasiPaginator = $madrasah->prestasis()
            ->select('prestasi_siswas.*')
            ->leftJoin('penilaian_prestasis', 'penilaian_prestasis.prestasi_siswa_id', '=', 'prestasi_siswas.id')
            ->selectRaw("
                CASE
                    WHEN penilaian_prestasis.id IS NULL THEN 0
                    WHEN penilaian_prestasis.status = 'draft' THEN 1
                    WHEN penilaian_prestasis.status = 'completed' THEN 2
                    ELSE 3
                END as urutan_status
            ")
            ->when($bidang, function ($query, $bidang) {
                $query->where('prestasi_siswas.bidang_prestasi', $bidang);
            })
            ->when($tingkat, function ($query, $tingkat) {
                $query->where('prestasi_siswas.tingkat', $tingkat);
            })
            ->when($penyelenggara, function ($query, $penyelenggara) {
                $query->where('prestasi_siswas.lembaga_penyelenggara', $penyelenggara);
            })
            ->when($statusPenilaian === 'belum', function ($query) {
                $query->whereNull('penilaian_prestasis.id');
            })
            ->when($statusPenilaian === 'sudah', function ($query) {
                $query->whereNotNull('penilaian_prestasis.id');
            })
            ->with('penilaianPrestasi')
            ->orderBy('urutan_status')
            ->orderByDesc('prestasi_siswas.waktu_kegiatan')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | MAPPING KE BENTUK ARRAY UNTUK BLADE (tetap paginator, cuma isinya di-map)
        |--------------------------------------------------------------------------
        */

        $daftarPrestasi = $prestasiPaginator->through(function ($prestasi) {
            $penilaian = $prestasi->penilaianPrestasi;

            // Skor Awal: pakai kolom yang benar-benar berisi nilai.
            $skorAwal = $prestasi->skor_luring > 0
                ? $prestasi->skor_luring
                : $prestasi->skor_daring;

            return [
                'id' => $prestasi->id,
                'nama' => $prestasi->nama_kegiatan,
                // ASUMSI: kolom kategori (akademik/nonakademik/keagamaan) = bidang_prestasi.
                // Ganti ke kolom lain kalau ternyata bukan ini.
                'link_drive' => $prestasi->link_drive_bukti,
                'kategori' => $prestasi->bidang_prestasi,
                'tingkat' => $prestasi->tingkat,
                'tahun' => optional($prestasi->waktu_kegiatan)->format('Y'),
                'penyelenggara' => $prestasi->lembaga_penyelenggara,
                // ASUMSI nama kolom: juara, kategori_penyelenggara.
                // Kalau nama kolom di tabel prestasi_siswas beda, ganti di sini saja.
                'juara' => $prestasi->juara,
                'kategori_penyelenggara' => $prestasi->kategori_penyelenggara,
                'bobot' => $skorAwal,
                'nilai' => $penilaian->persentase ?? null,
                'nilai_akhir' => $penilaian->nilai_akhir ?? null,
                'ada_penilaian' => $penilaian !== null,
            ];
        });

        // Inisial avatar asesor, dari nama user yang login (dulu hardcode "RA").
        $inisialAsesor = collect(explode(' ', trim(auth()->user()->nama)))
            ->map(fn ($kata) => strtoupper(substr($kata, 0, 1)))
            ->join('');
        $inisialAsesor = substr($inisialAsesor, 0, 2);

        return view('asesor.show', [
            'madrasah' => [
                'id' => $madrasah->id,
                'nama' => $madrasah->nama_madrasah,
                'npsn' => $madrasah->npsn,
                'jenjang' => $madrasah->jenjang_madrasah,
                'wilayah' => $madrasah->kota,
                'jumlah_prestasi' => $totalPrestasi,
                'asesor' => auth()->user()->nama,
            ],
            'inisialAsesor' => $inisialAsesor,
            'progresPenilaian' => $progresPenilaian,
            'opsiPersentase' => self::OPSI_PERSENTASE,
            'daftarPrestasi' => $daftarPrestasi,
            'totalPrestasi' => $totalPrestasi,
            'sudahDinilai' => $sudahDinilai,
            'belumDinilai' => $belumDinilai,
            'rataRata' => $rataRata,
            'totalNilaiAkhir' => $totalNilaiAkhir,
            'daftarTingkat' => $daftarTingkat,
            'daftarBidang' => $daftarBidang,
            'daftarPenyelenggara' => $daftarPenyelenggara,
            'statusPenilaian' => $statusPenilaian,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES "BERI NILAI" — Insert kalau belum ada, Update kalau sudah ada.
    |--------------------------------------------------------------------------
    | Dipanggil dari modal "Beri Nilai" di halaman show(). Status selalu
    | disimpan sebagai 'draft' pada tahap ini — finalisasi ke 'completed'
    | adalah fitur terpisah di tahap berikutnya (di luar scope ini).
    */

    public function simpanNilai(Request $request, Madrasah $madrasah, PrestasiSiswa $prestasi)
    {
        // Security: asesor hanya boleh menilai madrasah yang memang
        // menjadi assignment aktifnya. Divalidasi ke tabel assign_asesors,
        // bukan cuma mengandalkan middleware role.
        $assignment = $this->assignmentAtauGagal($madrasah);

        // Security tambahan: pastikan prestasi yang dinilai benar-benar
        // milik madrasah yang sedang dibuka, supaya asesor tidak bisa
        // menilai prestasi madrasah lain lewat manipulasi parameter URL.
        if ($prestasi->madrasah_id !== $madrasah->id) {
            abort(403);
        }

        $data = $request->validate([
            'persentase' => ['required', 'integer', Rule::in(self::OPSI_PERSENTASE)],
        ]);

        $skorAwal = $prestasi->skor_luring > 0
            ? $prestasi->skor_luring
            : $prestasi->skor_daring;

        $nilaiAkhir = round($skorAwal * $data['persentase'] / 100, 2);

        // updateOrCreate dikunci ke prestasi_siswa_id saja (relasi hasOne di
        // PrestasiSiswa), bukan ke assign_asesor_id. Kalau madrasah pernah
        // dipindah ke asesor lain oleh admin, assign_asesor_id pada baris
        // yang sudah ada ikut diperbarui ke assignment yang aktif sekarang —
        // supaya catatan penilaian tidak "nyangkut" ke assignment lama.
        PenilaianPrestasi::updateOrCreate(
            ['prestasi_siswa_id' => $prestasi->id],
            [
                'assign_asesor_id' => $assignment->id,
                'persentase' => $data['persentase'],
                'nilai_akhir' => $nilaiAkhir,
                'status' => 'draft',
            ]
        );

        return back()->with('success', 'Nilai prestasi berhasil disimpan.');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — validasi assignment aktif milik asesor yang login.
    |--------------------------------------------------------------------------
    | Dipakai di show() maupun simpanNilai() supaya aturan keamanannya
    | konsisten dan tidak diketik ulang di dua tempat.
    */
    private function assignmentAtauGagal(Madrasah $madrasah): AssignAsesor
    {
        $assignment = AssignAsesor::where('madrasah_id', $madrasah->id)
            ->where('asesor_id', auth()->id())
            ->first();

        if (! $assignment) {
            abort(403);
        }

        return $assignment;
    }
}