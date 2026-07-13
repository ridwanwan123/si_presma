<?php

namespace App\Http\Controllers;

use App\Models\AssignAsesor;
use App\Models\Madrasah;
use App\Models\PenilaianPrestasi;
use App\Models\PrestasiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AsesorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OPSI PERSENTASE NILAI (rubrik penilaian tetap, bukan dari database)
    |--------------------------------------------------------------------------
    */
    private const OPSI_PERSENTASE = [0, 5, 70, 80, 85, 90, 95, 100];

    /*
    |--------------------------------------------------------------------------
    | STATUS PENILAIAN — satu-satunya sumber kebenaran pemetaan status.
    |--------------------------------------------------------------------------
    | Dipakai di index() (badge & progress bar tabel) maupun show() (badge
    | header), supaya label/warna status tidak dihitung ulang di Blade dan
    | selalu konsisten mengikuti assign_asesors.status.
    |
    | assigned / not_assigned / null -> belum   (Belum Dinilai)
    | in_progress                    -> proses  (Sedang Dinilai)
    | completed                      -> selesai (Selesai)
    */
    private function statusPenilaianInfo(?string $statusAssignment): array
    {
        return match ($statusAssignment) {
            'completed' => [
                'key' => 'selesai',
                'label' => 'Selesai',
                'badge' => 'badge-selesai',
                'bar' => 'bg-selesai',
            ],
            'in_progress' => [
                'key' => 'proses',
                'label' => 'Sedang Dinilai',
                'badge' => 'badge-proses',
                'bar' => 'bg-proses',
            ],
            default => [
                'key' => 'belum',
                'label' => 'Belum Dinilai',
                'badge' => 'badge-belum',
                'bar' => 'bg-belum',
            ],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | AKSI TABEL "MADRASAH YANG DINILAI" — label/class/icon tombol Aksi,
    | mengikuti status penilaian yang sama (bukan dihitung lagi di Blade).
    |--------------------------------------------------------------------------
    */
    private function aksiPenilaianInfo(string $statusKey): array
    {
        return match ($statusKey) {
            'selesai' => [
                'label' => 'Lihat Hasil',
                'class' => 'btn-hasil',
                'icon' => 'bi-eye',
            ],
            'proses' => [
                'label' => 'Lanjutkan',
                'class' => 'btn-lanjutkan',
                'icon' => 'bi-pencil',
            ],
            default => [
                'label' => 'Mulai Menilai',
                'class' => 'btn-mulai',
                'icon' => 'bi-play-fill',
            ],
        };
    }

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
            | Status murni mengikuti assign_asesors.status (bukan dihitung dari
            | jumlah penilaian), supaya selalu konsisten dengan hasil finalisasi.
            | Label, class badge, dan class progress bar sekaligus disiapkan di
            | sini lewat statusPenilaianInfo() supaya Blade tidak perlu menghitung
            | ulang apa pun — Blade hanya menampilkan.
            */
            $statusInfo = $this->statusPenilaianInfo($assignment->status);
            $aksiInfo = $this->aksiPenilaianInfo($statusInfo['key']);

            return [
                'id' => $madrasah->id,
                'nama' => $madrasah->nama_madrasah,
                'npsn' => $madrasah->npsn,
                'jenjang' => $madrasah->jenjang_madrasah,
                'wilayah' => $madrasah->kota,
                'prestasi' => $totalPrestasi,
                'status' => $statusInfo['key'],
                'status_label' => $statusInfo['label'],
                'status_badge' => $statusInfo['badge'],
                'status_bar' => $statusInfo['bar'],
                'progress' => $progress,
                'aksi_label' => $aksiInfo['label'],
                'aksi_class' => $aksiInfo['class'],
                'aksi_icon' => $aksiInfo['icon'],
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
                'sumber_skor' => $prestasi->skor_luring > 0 ? 'Luring' : 'Daring',
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
            'statusAssignment' => $assignment->status,
            'statusLabel' => $this->statusPenilaianInfo($assignment->status)['label'],
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
    | Dipanggil dari modal "Beri Nilai" di halaman show(). Status penilaian
    | selalu disimpan sebagai 'draft' pada tahap ini — finalisasi ke
    | 'completed' dilakukan lewat finalisasi(). Assignment otomatis
    | berpindah dari 'assigned' ke 'in_progress' begitu penilaian pertama
    | disimpan (lihat DB::transaction di bawah).
    */

    public function simpanNilai(Request $request, Madrasah $madrasah, PrestasiSiswa $prestasi){
        // Security: asesor hanya boleh menilai madrasah yang memang
        // menjadi assignment aktifnya. Divalidasi ke tabel assign_asesors,
        // bukan cuma mengandalkan middleware role.
        $assignment = $this->assignmentAtauGagal($madrasah);

        // Setelah finalisasi (completed), seluruh nilai terkunci — asesor
        // tidak boleh mengubah nilai lagi lewat request langsung ke endpoint ini.
        if ($assignment->status === 'completed') {
            abort(403, 'Penilaian untuk madrasah ini sudah difinalisasi dan tidak dapat diubah.');
        }

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

        DB::transaction(function () use ($assignment, $prestasi, $data, $nilaiAkhir) {
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

            // Begitu asesor mulai menilai, assignment otomatis berpindah dari
            // "assigned" ke "in_progress". Kalau sudah in_progress (atau
            // completed — tapi itu sudah ditolak di atas), tidak diubah lagi.
            if ($assignment->status === 'assigned') {
                $assignment->update(['status' => 'in_progress']);
            }
        });

        return back()->with('success', 'Nilai prestasi berhasil disimpan.');
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES "KUMPULKAN PENILAIAN" — Finalisasi seluruh penilaian madrasah.
    |--------------------------------------------------------------------------
    | Hanya boleh dijalankan kalau:
    | 1) Assignment memang milik asesor yang login (assignmentAtauGagal).
    | 2) Assignment belum completed (tidak bisa difinalisasi dua kali).
    | 3) Seluruh prestasi madrasah sudah punya record penilaian.
    |
    | Update status draft -> completed dilakukan lewat DB transaction supaya
    | penilaian_prestasis dan assign_asesors konsisten sama-sama berubah,
    | atau sama-sama batal kalau salah satu gagal.
    */

    public function finalisasi(Request $request, Madrasah $madrasah){
        // Security: sama seperti show() & simpanNilai(), validasi kepemilikan
        // assignment berbasis data assign_asesors, bukan cuma middleware.
        $assignment = $this->assignmentAtauGagal($madrasah);

        if ($assignment->status === 'completed') {
            abort(403, 'Penilaian untuk madrasah ini sudah difinalisasi sebelumnya.');
        }

        $totalPrestasi = $madrasah->prestasis()->count();

        $sudahDinilai = $madrasah->prestasis()
            ->whereHas('penilaianPrestasi')
            ->count();

        // Validasi progress 100% di server (jangan percaya state tombol di UI).
        if ($totalPrestasi === 0 || $sudahDinilai < $totalPrestasi) {
            return back()->with('error', 'Masih terdapat prestasi yang belum dinilai. Selesaikan seluruh penilaian sebelum mengumpulkan.');
        }

        DB::transaction(function () use ($assignment) {
            PenilaianPrestasi::where('assign_asesor_id', $assignment->id)
                ->where('status', 'draft')
                ->update([
                    'status' => 'completed',
                    'dinilai_pada' => now(),
                ]);

            $assignment->update([
                'status' => 'completed',
            ]);
        });

        return redirect()
            ->route('asesor.index')
            ->with('success', 'Penilaian berhasil dikumpulkan dan difinalisasi.');
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