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
    private const OPSI_PERSENTASE = [5, 70, 80, 85, 90, 95, 100];

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | DATA UTAMA — assignment milik asesor yang sedang login
        |--------------------------------------------------------------------------
        | JANGAN ambil seluruh madrasah. Sumber utama adalah assign_asesors,
        | difilter berdasarkan asesor yang login, baru load relasi madrasah
        | & hitung progress penilaiannya.
        */

        $assignments = AssignAsesor::query()
            ->where('asesor_id', auth()->id())
            ->with(['madrasah' => function ($query) {
                $query->withCount('prestasis');
            }])
            ->withCount(['penilaianPrestasis as dinilai_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MAPPING KE BENTUK YANG DIBUTUHKAN BLADE
        |--------------------------------------------------------------------------
        | Bentuk array & key-nya sengaja dibuat identik dengan data dummy
        | sebelumnya ('nama', 'npsn', 'jenjang', dst) supaya Blade tidak
        | perlu diubah sama sekali.
        */

        $daftarMadrasah = $assignments->map(function (AssignAsesor $assignment) {
            $madrasah = $assignment->madrasah;

            $totalPrestasi = $madrasah->prestasis_count ?? 0;
            $dinilai = $assignment->dinilai_count ?? 0;

            $status = match (true) {
                $dinilai === 0 => 'belum',
                $dinilai < $totalPrestasi => 'proses',
                default => 'selesai',
            };

            $progress = $totalPrestasi > 0
                ? (int) round(($dinilai / $totalPrestasi) * 100)
                : 0;

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
        | Dihitung otomatis dari hasil mapping di atas — bukan query terpisah,
        | supaya angkanya selalu konsisten dengan isi tabel.
        */

        $totalMadrasah = $daftarMadrasah->count();
        $belumDinilai = $daftarMadrasah->where('status', 'belum')->count();
        $sedangDinilai = $daftarMadrasah->where('status', 'proses')->count();
        $selesaiDinilai = $daftarMadrasah->where('status', 'selesai')->count();

        return view('asesor.index', [
            'daftarMadrasah' => $daftarMadrasah,
            'totalMadrasah' => $totalMadrasah,
            'belumDinilai' => $belumDinilai,
            'sedangDinilai' => $sedangDinilai,
            'selesaiDinilai' => $selesaiDinilai,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN PENILAIAN MADRASAH
    |--------------------------------------------------------------------------
    | Security: madrasah hanya boleh dibuka kalau memang assignment milik
    | asesor yang sedang login. Validasi berbasis data assign_asesors,
    | BUKAN cuma mengandalkan middleware role.
    */

    public function show(Madrasah $madrasah)
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
        | bukan cuma yang tampil di halaman saat ini.
        */

        $totalPrestasi = $madrasah->prestasis()->count();

        $sudahDinilai = $madrasah->prestasis()
            ->whereHas('penilaianPrestasi', function ($query) {
                $query->where('status', 'completed');
            })
            ->count();

        $belumDinilai = $totalPrestasi - $sudahDinilai;

        $progresPenilaian = $totalPrestasi > 0
            ? (int) round(($sudahDinilai / $totalPrestasi) * 100)
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
                'bobot' => $skorAwal,
                'nilai' => $penilaian->persentase ?? null,
                'nilai_akhir' => $penilaian->nilai_akhir ?? null,
                'ada_penilaian' => $penilaian !== null,
            ];
        });

        // Opsi filter (dropdown Tingkat & Penyelenggara) sengaja diambil dari
        // SELURUH prestasi madrasah (bukan dari 20 baris yang tampil di halaman
        // ini), supaya daftar pilihannya tetap lengkap di setiap halaman.
        $daftarTingkat = $madrasah->prestasis()
            ->whereNotNull('tingkat')
            ->distinct()
            ->orderBy('tingkat')
            ->pluck('tingkat');

        $daftarPenyelenggara = $madrasah->prestasis()
            ->whereNotNull('lembaga_penyelenggara')
            ->distinct()
            ->orderBy('lembaga_penyelenggara')
            ->pluck('lembaga_penyelenggara');

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
            'daftarPenyelenggara' => $daftarPenyelenggara,
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