<aside class="sidebar" id="sidebar">
    <!-- BRAND -->
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logosidebar.png') }}" alt="logo" />
    </div>

    <!-- MENU -->
    <div class="sidebar-menu">
        <a href="dashboard.html" class="menu-item active">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <div class="menu-title">MASTER DATA</div>

        <a href="#" class="menu-item">
            <i class="bi bi-building"></i>
            <span>Madrasah</span>
        </a>

        <a href="#" class="menu-item">
            <i class="bi bi-briefcase"></i>
            <span>Penyelenggara</span>
        </a>

        <a href="#" class="menu-item">
            <i class="bi bi-people"></i>
            <span>Asesor</span>
        </a>

        <div class="menu-title">KATEGORI PRESTASI</div>

        <a href="#" class="menu-item has-submenu">
            <i class="bi bi-trophy"></i>
            <span>Prestasi</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="submenu">
            <a href="#" class="menu-item"><span>Akademik</span></a>
            <a href="#" class="menu-item"><span>Non-Akademik</span></a>
            <a href="#" class="menu-item"><span>Keagamaan</span></a>
            <a href="#" class="menu-item"><span>GTK</span></a>
            <a href="#" class="menu-item"><span>Lembaga</span></a>
        </div>

        <div class="menu-title">LAINNYA</div>

        <a href="#" class="menu-item">
            <i class="bi bi-check2-circle"></i>
            <span>Verifikasi</span>
        </a>

        <a href="#" class="menu-item">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Laporan</span>
        </a>

        <a href="#" class="menu-item">
            <i class="bi bi-key"></i>
            <span>Ubah Password</span>
        </a>
    </div>

    <!-- PROFILE -->
    <div class="sidebar-footer">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="{{ asset('assets/images/logo.png') }}" alt="admin" />
            </div>

            <div class="profile-info">
                <div class="profile-name">{{ auth()->user()->nama }}</div>
                <div class="profile-role">{{ auth()->user()->role->nama }}</div>
            </div>
        </div>
    </div>
</aside>
