<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MadrasahController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RankingArsipController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\MonitoringAsesorController;
use App\Http\Controllers\PengaturanPenguranganPoinController;
use App\Http\Controllers\AduanMasyarakatController;
use App\Http\Controllers\KeterlambatanBerkasController;
use App\Http\Controllers\DashboardMadrasahController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AssignAsesorController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\DashboardAsesorController;
// use App\Http\Controllers\WilayahPengawasController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login.form');
    }

    $user = auth()->user();

    if ($user->hasRole('Madrasah')) {
        return redirect()->route('dashboard.madrasah');
    }

    if ($user->hasRole('Pengawas')) {
        return redirect()->route('dashboard.asesor');
    }

    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login.form');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])
        ->name('register.form');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::get('/ubah-password', [LoginController::class, 'changePassword'])
        ->name('ubah-password');

    Route::post('/ubah-password', [LoginController::class, 'updatePassword'])
        ->name('ubah-password.update');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD (SEMUA ROLE)
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('activity', ActivityController::class);

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrator')->group(function () {

        Route::resource('user-management', UserManagementController::class)
            ->parameters([
                'user-management' => 'user'
            ]);

        Route::get(
            'assign-asesor/export-pdf',
            [AssignAsesorController::class, 'exportPdf']
        )->name('assign-asesor.export-pdf');

        Route::resource('assign-asesor', AssignAsesorController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::prefix('periode')->name('periode.')->group(function () {

            Route::get('/', [PeriodeController::class, 'index'])
                ->name('index');

            Route::post('/', [PeriodeController::class, 'aktifkan'])
                ->name('aktifkan');
        });

        Route::get('ranking', [RankingController::class, 'index'])
            ->name('ranking.index');

        Route::get('ranking/export', [RankingController::class, 'export'])
            ->name('ranking.export');

        Route::prefix('ranking-arsip')->name('ranking-arsip.')->group(function () {

            Route::get('/', [RankingArsipController::class, 'index'])
                ->name('index');

            Route::post('/', [RankingArsipController::class, 'store'])
                ->name('store');

            Route::get('{ranking_arsip}', [RankingArsipController::class, 'show'])
                ->name('show');

            Route::get('{ranking_arsip}/export', [RankingArsipController::class, 'export'])
                ->name('export');

            Route::delete('{ranking_arsip}', [RankingArsipController::class, 'destroy'])
                ->name('destroy');
        });

        Route::get('monitoring-asesor', [MonitoringAsesorController::class, 'index'])
            ->name('monitoring-asesor.index');

        Route::prefix('pengurangan-poin')->name('pengurangan-poin.')->group(function () {

            Route::get('pengaturan', [PengaturanPenguranganPoinController::class, 'index'])
                ->name('pengaturan');

            Route::post('pengaturan', [PengaturanPenguranganPoinController::class, 'update'])
                ->name('pengaturan.update');
        });

        Route::resource('aduan-masyarakat', AduanMasyarakatController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('keterlambatan-berkas', KeterlambatanBerkasController::class)
            ->only(['index', 'store', 'destroy']);
    });
    
    /*
    |--------------------------------------------------------------------------
    | PENGAWAS / ASESOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Pengawas')->group(function () {

        Route::resource('asesor', AsesorController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ASESOR
        |--------------------------------------------------------------------------
        | Didaftarkan SEBELUM asesor.show (GET /asesor/{madrasah}) supaya
        | "/asesor/dashboard" tidak ketiban {madrasah} = "dashboard".
        */

        Route::get('asesor/dashboard', [DashboardAsesorController::class, 'index'])
            ->name('dashboard.asesor');

        /*
        |--------------------------------------------------------------------------
        | SHOW PENILAIAN MADRASAH
        |--------------------------------------------------------------------------
        */

        Route::get(
            'asesor/{madrasah}',
            [AsesorController::class, 'show']
        )->name('asesor.show');

        /*
        |--------------------------------------------------------------------------
        | SIMPAN NILAI PRESTASI
        |--------------------------------------------------------------------------
        */

        Route::post(
            'asesor/madrasah/{madrasah}/prestasi/{prestasi}/nilai',
            [AsesorController::class, 'simpanNilai']
        )->name('asesor.nilai.store');

        /*
        |--------------------------------------------------------------------------
        | FINALISASI PENILAIAN
        |--------------------------------------------------------------------------
        */

        Route::post(
            'asesor/madrasah/{madrasah}/finalisasi',
            [AsesorController::class, 'finalisasi']
        )->name('asesor.finalisasi');
    });

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD MADRASAH
    | Madrasah
    |--------------------------------------------------------------------------
    | Didaftarkan SEBELUM Route::resource('madrasah', ...) di bawah supaya
    | tidak ketiban route madrasah.show (GET /madrasah/{madrasah}), yang kalau
    | didaftarkan lebih dulu akan mencocokkan "/madrasah/dashboard" sebagai
    | {madrasah} = "dashboard" duluan.
    */

    Route::middleware('role:Madrasah')->group(function () {

        Route::get('madrasah/dashboard', [DashboardMadrasahController::class, 'index'])
            ->name('dashboard.madrasah');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATOR + MADRASAH
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Administrator,Madrasah')->group(function () {

        Route::resource('madrasah', MadrasahController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | PRESTASI
    | Administrator (lihat semua), Madrasah (kelola milik sendiri)
    |--------------------------------------------------------------------------
    | Pengawas TIDAK diikutkan di sini -- mereka menilai lewat alur
    | asesor.* (AsesorController) yang terpisah, bukan lewat halaman ini.
    */

    Route::middleware('role:Administrator,Madrasah')->group(function () {

        Route::prefix('prestasi')->name('prestasi.')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | ENTRY POINT "TAMBAH PRESTASI" (PILIH METODE)
            |--------------------------------------------------------------------------
            | Tidak lagi terikat $jenis, karena bidang prestasi kini dibaca dari
            | inputan form / isi Excel itu sendiri, bukan dari halaman mana ia dibuka.
            |
            | Semua route "menambah/mengubah data" di bawah ini SENGAJA ditambah
            | ->middleware('role:Madrasah') lagi -- di atas middleware grup
            | (Administrator,Madrasah) -- supaya Administrator tetap BISA lihat
            | (index/data), tapi TIDAK BISA menambah/mengedit/menghapus prestasi
            | milik madrasah manapun.
            */

            Route::get('tambah', [PrestasiController::class, 'pilihMetode'])
                ->middleware('role:Madrasah')
                ->name('tambah');

            /*
            |--------------------------------------------------------------------------
            | CREATE (INPUT MANUAL)
            |--------------------------------------------------------------------------
            */

            Route::get('create', [PrestasiController::class, 'create'])
                ->middleware('role:Madrasah')
                ->name('create');

            Route::post('/', [PrestasiController::class, 'store'])
                ->middleware('role:Madrasah')
                ->name('store');

            /*
            |--------------------------------------------------------------------------
            | IMPORT
            |--------------------------------------------------------------------------
            */

            Route::get('import', [PrestasiController::class, 'import'])
                ->middleware('role:Madrasah')
                ->name('import');

            Route::post('import', [PrestasiController::class, 'upload'])
                ->middleware('role:Madrasah')
                ->name('import.upload');

            Route::post('checking_import', [PrestasiController::class, 'checking_import_prestasi'])
                ->middleware('role:Madrasah')
                ->name('checking_import');

            Route::post('save-preview', [PrestasiController::class, 'save_preview'])
                ->middleware('role:Madrasah')
                ->name('save_preview');

            Route::get('preview', [PrestasiController::class, 'preview'])
                ->middleware('role:Madrasah')
                ->name('preview');

            Route::post('store-import', [PrestasiController::class, 'store_import'])
                ->middleware('role:Madrasah')
                ->name('store_import');

            Route::get('template', [PrestasiController::class, 'template'])
                ->middleware('role:Madrasah')
                ->name('template');

            /*
            |--------------------------------------------------------------------------
            | CRUD PRESTASI (MASIH TERIKAT $jenis, DIAKSES DARI DAFTAR PER BIDANG)
            |--------------------------------------------------------------------------
            */

            Route::get('{jenis}/{id}/edit', [PrestasiController::class, 'edit'])
                ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
                ->middleware('role:Madrasah')
                ->name('edit');

            Route::put('{jenis}/{id}', [PrestasiController::class, 'update'])
                ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
                ->middleware('role:Madrasah')
                ->name('update');

            Route::delete('{jenis}/{id}', [PrestasiController::class, 'destroy'])
                ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
                ->middleware('role:Madrasah')
                ->name('destroy');

            /*
            |--------------------------------------------------------------------------
            | DATATABLE & INDEX -- Administrator (semua madrasah) + Madrasah (milik sendiri)
            |--------------------------------------------------------------------------
            */

            Route::get('{jenis}/data', [PrestasiController::class, 'data'])
                ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
                ->name('data');

            Route::get('{jenis}', [PrestasiController::class, 'index'])
                ->where('jenis', 'akademik|non-akademik|keagamaan|gtk|lembaga')
                ->name('index');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN PRESTASI
    | Madrasah
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Madrasah')->group(function () {

        Route::prefix('pengajuan')->name('pengajuan.')->group(function () {

            Route::get('/', [PengajuanController::class, 'index'])
                ->name('index');

            Route::post('/', [PengajuanController::class, 'submit'])
                ->name('submit');
        });

        Route::get('prestasi-export', [PrestasiController::class, 'export'])
            ->name('prestasi.export');

        Route::get('hasil', [HasilController::class, 'index'])
            ->name('hasil.index');
    });
});