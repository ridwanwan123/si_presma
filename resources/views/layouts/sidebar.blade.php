@php
    $user = auth()->user();
@endphp

<aside class="sidebar" id="sidebar">
    <!-- BRAND -->
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logo p.png') }}" alt="logo" />
        <span class="brand-text">PRESMA</span>
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <!-- DASHBOARD -->
        <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- MASTER DATA -->
        @if ($user->hasRole(['Administrator', 'Madrasah', 'Pengawas']))
            <div class="menu-title">MASTER DATA</div>
        @endif

        {{-- MADRASAH --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <a href="{{ route('madrasah.index') }}"
                class="menu-item {{ request()->routeIs('madrasah.index') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Madrasah</span>
            </a>
        @endif

        {{-- PENYELENGGARA --}}
        @if ($user->hasRole(['Administrator', 'Madrasah']))
            <a href="#" class="menu-item">
                <i class="bi bi-briefcase"></i>
                <span>Penyelenggara</span>
            </a>
        @endif

        {{-- PENGAWAS --}}
        @if ($user->hasRole(['Administrator', 'Pengawas']))
            <a href="#" class="menu-item">
                <i class="bi bi-people"></i>
                <span>Pengawas</span>
            </a>
        @endif

        <!-- Bidang Prestasi -->
        <div class="menu-title">Bidang Prestasi</div>

        <a href="#" class="menu-item has-submenu {{ request()->routeIs('prestasi.*') ? 'open' : '' }}">
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

                <span>Non-Akademik</span>

            </a>

            <a href="{{ route('prestasi.index', 'keagamaan') }}"
                class="menu-item {{ request()->route('jenis') == 'keagamaan' ? 'active' : '' }}">

                <i class="bi bi-book"></i>

                <span>Keagamaan</span>

            </a>

        </div>

        <!-- LAINNYA -->
        @if ($user->hasRole(['Administrator', 'Pengawas']))
            <div class="menu-title">LAINNYA</div>

            <a href="#" class="menu-item">
                <i class="bi bi-check2-circle"></i>
                <span>Verifikasi</span>
            </a>

            <a href="#" class="menu-item">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>
        @endif

        <!-- UBAH PASSWORD -->
        <div class="menu-title">AKUN</div>
        <a href="{{ route('ubah-password') }}"
            class="menu-item {{ request()->routeIs('ubah-password') ? 'active' : '' }}">
            <i class="bi bi-key"></i>
            <span>Ubah Password</span>
        </a>

        <!-- SYSTEM -->
        <div class="menu-title">SYSTEM</div>

        <a href="{{ route('activity.index') }}"
            class="menu-item {{ request()->routeIs('activity.index') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Activity Log</span>
        </a>

    </div>

    <!-- PROFILE -->
    <div class="sidebar-footer">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="{{ asset('assets/images/logo.png') }}" alt="admin" />
            </div>

            <div class="profile-info">
                <div class="profile-name">
                    {{ auth()->user()->nama }}
                </div>

                <div class="profile-role">
                    {{ auth()->user()->role->nama }}
                </div>
            </div>
        </div>
    </div>
</aside>
