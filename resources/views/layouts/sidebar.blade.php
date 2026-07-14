@php
    $user = auth()->user();
@endphp

<aside class="sidebar" id="sidebar">

    {{-- ===================== BRAND ===================== --}}
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logo p.png') }}" alt="Logo">
        <span class="brand-text">PRESMA</span>
    </div>

    {{-- ===================== MENU ===================== --}}
    <div class="sidebar-menu">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        {{-- =========================================================
            MASTER DATA
        ========================================================== --}}
        @if ($user->hasRole(['Administrator', 'Madrasah', 'Pengawas']))
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

        {{-- =========================================================
            BIDANG PRESTASI
        ========================================================== --}}
        <div class="menu-title">BIDANG PRESTASI</div>

        <a href="#"
            class="menu-item has-submenu {{ request()->routeIs('prestasi.*') ? 'open' : '' }}">
            <i class="bi bi-trophy"></i>
            <span>Prestasi</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="submenu {{ request()->routeIs('prestasi.*') ? 'show' : '' }}">

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