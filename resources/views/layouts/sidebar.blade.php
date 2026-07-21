@php
    $user = auth()->user();

    // Dipakai buat menyembunyikan "Tambah Prestasi" begitu siklus periode
    // aktif sudah bukan OPEN lagi (SUBMITTED/ASSESSMENT/FINISHED) -- reuse
    // helper canInput() yang sama persis dipakai backend (cekAksesSiklus()),
    // bukan pengecekan baru. Cuma dihitung untuk role Madrasah karena
    // Administrator/Pengawas tidak punya relasi madrasah().
    $siklusAktif = $user->hasRole('Madrasah') ? $user->madrasah->prestasiSiklusAktif() : null;
@endphp

<style>
    .menu-item.menu-usulan {
        opacity: .55;
        cursor: not-allowed;
        pointer-events: none;
    }

    .badge-usulan {
        margin-left: auto;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .03em;
        padding: 2px 7px;
        border-radius: 999px;
        background: #fef3c7;
        color: #b45309;
        white-space: nowrap;
    }
</style>

<aside class="sidebar" id="sidebar">

    {{-- ===================== BRAND ===================== --}}
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logo p.png') }}" alt="Logo">
        <span class="brand-text">PRESMA</span>
    </div>

    {{-- ===================== MENU ===================== --}}
    <div class="sidebar-menu">

        {{-- Dashboard --}}
        <a href="{{ dashboardRoute() }}"
            class="menu-item {{ request()->routeIs('dashboard', 'dashboard.madrasah', 'dashboard.asesor') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        {{-- =========================================================
            MASTER DATA
        ========================================================== --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <div class="menu-title">MASTER DATA</div>
        @endif

        {{-- Madrasah --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <a href="{{ route('madrasah.index') }}"
                class="menu-item {{ request()->routeIs('madrasah.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Madrasah</span>
            </a>
        @endif

        {{-- Assign Asesor --}}
        @if ($user->hasRole('Administrator'))
            <a href="{{ route('assign-asesor.index') }}"
                class="menu-item {{ request()->routeIs('assign-asesor.*') ? 'active' : '' }}">
                <i class="bi bi-send-plus"></i>
                <span>Assign To</span>
            </a>
        @endif

        {{-- Rubrik Penilaian -- tabel skor resmi Juknis JMA, dipakai buat
             mencocokkan skor yang diinput Madrasah di halaman Asesor. --}}
        @if ($user->hasRole('Administrator'))
            <a href="{{ route('rubrik-penilaian.index') }}"
                class="menu-item {{ request()->routeIs('rubrik-penilaian.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Rubrik Penilaian</span>
            </a>
        @endif

        {{-- =========================================================
            BIDANG PRESTASI
        ========================================================== --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <div class="menu-title">BIDANG PRESTASI</div>
        @endif

        {{-- Tambah Prestasi (entry point pilih metode) -- Madrasah saja, DAN
             cuma tampil kalau siklus periode aktif masih OPEN. Begitu status
             sudah SUBMITTED/ASSESSMENT/FINISHED, menu ini hilang -- konsisten
             sama backend yang sudah menolak akses lewat cekAksesSiklus(). --}}
        @if ($user->hasRole('Madrasah') && $siklusAktif && $siklusAktif->canInput())
            <a href="{{ route('prestasi.tambah') }}"
                class="menu-item {{ request()->routeIs('prestasi.tambah', 'prestasi.create', 'prestasi.store', 'prestasi.import', 'prestasi.import.upload', 'prestasi.checking_import', 'prestasi.save_preview', 'prestasi.preview', 'prestasi.store_import', 'prestasi.template') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Prestasi</span>
            </a>
        @endif

        {{-- Prestasi Madrasah -- Administrator (lihat semua madrasah) + Madrasah
             (lihat prestasi miliknya sendiri). TETAP tampil di status apapun,
             karena ini cuma melihat data (read), bukan menambah. --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <a href="#"
                class="menu-item has-submenu {{ request()->routeIs('prestasi.index', 'prestasi.data', 'prestasi.edit', 'prestasi.update', 'prestasi.destroy') ? 'open' : '' }}">
                <i class="bi bi-trophy"></i>
                <span>Prestasi Madrasah</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div
                class="submenu {{ request()->routeIs('prestasi.index', 'prestasi.data', 'prestasi.edit', 'prestasi.update', 'prestasi.destroy') ? 'show' : '' }}">

                <a href="{{ route('prestasi.index', 'akademik') }}"
                    class="menu-item {{ request()->route('jenis') == 'akademik' ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Akademik</span>
                </a>

                <a href="{{ route('prestasi.index', 'non-akademik') }}"
                    class="menu-item {{ request()->route('jenis') == 'non-akademik' ? 'active' : '' }}">
                    <i class="bi bi-award"></i>
                    <span>Non Akademik</span>
                </a>

                <a href="{{ route('prestasi.index', 'keagamaan') }}"
                    class="menu-item {{ request()->route('jenis') == 'keagamaan' ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Keagamaan</span>
                </a>

                <a href="{{ route('prestasi.index', 'gtk') }}"
                    class="menu-item {{ request()->route('jenis') == 'gtk' ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>GTK</span>
                </a>

                <a href="{{ route('prestasi.index', 'lembaga') }}"
                    class="menu-item {{ request()->route('jenis') == 'lembaga' ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Lembaga</span>
                </a>

            </div>
        @endif

        {{-- Pengajuan Prestasi --}}
        @if ($user->hasRole('Madrasah'))
            <a href="{{ route('pengajuan.index') }}"
                class="menu-item {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                <i class="bi bi-send-check"></i>
                <span>Pengajuan Prestasi</span>
            </a>
        @endif

        {{-- Hasil Penilaian (Madrasah) -- ditahan dulu (href="#") sampai
             ada keputusan dari atasan soal risiko komplain nilai. --}}
        @if ($user->hasRole('Madrasah'))
            <a href="#" class="menu-item">
                <i class="bi bi-clipboard-data"></i>
                <span>Hasil Penilaian</span>
            </a>
        @endif

        {{-- =========================================================
            LAPORAN (Administrator)
        ========================================================== --}}
        @if ($user->hasRole('Administrator'))
            <div class="menu-title">LAPORAN</div>

            {{-- Hasil & Ranking -- sekarang sudah aktif, dilengkapi perhitungan
                 potongan Aduan Masyarakat & Keterlambatan Berkas. --}}
            <a href="{{ route('ranking.index') }}"
                class="menu-item {{ request()->routeIs('ranking.*') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i>
                <span>Hasil & Ranking</span>
            </a>

            {{-- Arsip Ranking -- snapshot beku ranking per periode, hasil
                 dari tombol "Arsipkan Ranking" di halaman Hasil & Ranking --}}
            <a href="{{ route('ranking-arsip.index') }}"
                class="menu-item {{ request()->routeIs('ranking-arsip.*') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                <span>Arsip Ranking</span>
            </a>

            {{-- Monitoring Asesor -- rangkuman progress semua asesor sekaligus --}}
            <a href="{{ route('monitoring-asesor.index') }}"
                class="menu-item {{ request()->routeIs('monitoring-asesor.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Monitoring Asesor</span>
            </a>

            {{-- Pengurangan Poin -- pengaturan nilai potongan + data
                 Aduan Masyarakat & Keterlambatan Berkas --}}
            <a href="#"
                class="menu-item has-submenu {{ request()->routeIs('pengurangan-poin.*', 'aduan-masyarakat.*', 'keterlambatan-berkas.*') ? 'open' : '' }}">
                <i class="bi bi-dash-circle"></i>
                <span>Pengurangan Poin</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div
                class="submenu {{ request()->routeIs('pengurangan-poin.*', 'aduan-masyarakat.*', 'keterlambatan-berkas.*') ? 'show' : '' }}">

                <a href="{{ route('pengurangan-poin.pengaturan') }}"
                    class="menu-item {{ request()->routeIs('pengurangan-poin.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span>Pengaturan Nilai</span>
                </a>

                <a href="{{ route('aduan-masyarakat.index') }}"
                    class="menu-item {{ request()->routeIs('aduan-masyarakat.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Aduan Masyarakat</span>
                </a>

                <a href="{{ route('keterlambatan-berkas.index') }}"
                    class="menu-item {{ request()->routeIs('keterlambatan-berkas.*') ? 'active' : '' }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Keterlambatan Berkas</span>
                </a>

            </div>

            {{-- USULAN -- lihat catatan status_verifikasi/catatan_verifikasi
                 di model PrestasiSiswa yang belum kepakai di controller manapun --}}
            <a href="#" class="menu-item menu-usulan">
                <i class="bi bi-patch-check"></i>
                <span>Verifikasi Prestasi</span>
                <span class="badge-usulan">USULAN</span>
            </a>
        @endif

        {{-- =========================================================
            PENGAWAS
        ========================================================== --}}
        @if ($user->hasRole('Pengawas'))
            <div class="menu-title">PENGAWAS</div>

            <a href="{{ route('asesor.index') }}"
                class="menu-item {{ request()->routeIs('asesor.*') ? 'active' : '' }}">
                <i class="bi bi-check2-circle"></i>
                <span>Asesor</span>
            </a>

            {{-- USULAN -- riwayat penilaian yang sudah completed dari
                 periode-periode lalu, terpisah dari daftar kerja aktif --}}
            <a href="#" class="menu-item menu-usulan">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Penilaian</span>
                <span class="badge-usulan">USULAN</span>
            </a>
        @endif

        {{-- =========================================================
            AKUN
        ========================================================== --}}
        <div class="menu-title">AKUN</div>

        <a href="{{ route('ubah-password') }}"
            class="menu-item {{ request()->routeIs('ubah-password') ? 'active' : '' }}">
            <i class="bi bi-key"></i>
            <span>Ubah Password</span>
        </a>

        @if ($user->hasRole('Administrator'))
            <a href="{{ route('user-management.index') }}"
                class="menu-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Manajemen Akun</span>
            </a>
        @endif

        {{-- =========================================================
            SYSTEM
        ========================================================== --}}
        <div class="menu-title">SYSTEM</div>

        @if ($user->hasRole('Administrator'))
            <a href="{{ route('periode.index') }}"
                class="menu-item {{ request()->routeIs('periode.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-range"></i>
                <span>Kelola Periode</span>
            </a>
        @endif

        <a href="{{ route('activity.index') }}"
            class="menu-item {{ request()->routeIs('activity.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Activity Log</span>
        </a>

    </div>

    {{-- ===================== PROFILE ===================== --}}
    <div class="sidebar-footer">
        <div class="profile-card">

            <div class="profile-avatar">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Profile">
            </div>

            <div class="profile-info">
                <div class="profile-name">
                    {{ $user->nama }}
                </div>

                <div class="profile-role">
                    {{ $user->role->nama }}
                </div>
            </div>

        </div>
    </div>

</aside>
