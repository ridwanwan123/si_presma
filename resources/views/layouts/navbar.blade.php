<nav class="topbar d-flex justify-content-between align-items-center">

    <!-- LEFT -->
    <div class="left d-flex align-items-center gap-2">

        <i class="bi bi-list toggle-btn" id="toggleSidebar"></i>

        <!-- LOGO -->
        <img src="{{ asset('assets/images/kemenag.png') }}" alt="logo"
            style="height:32px; width:32px; object-fit:contain;">

        <!-- APP NAME -->
        @php
            $periodeAktifNavbar = \App\Models\PeriodeAktif::aktif();
        @endphp
        <div class="app-title d-flex flex-column lh-sm">
            <span class="app-name font-weight-bold">JMA {{ $periodeAktifNavbar + 1 }} | PRESTASI TAHUN
                {{ $periodeAktifNavbar }}</span>
            <span class="app-region text-muted small">Penmad DKI Jakarta</span>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right d-flex align-items-center gap-2">

        <!-- BANTUAN / CUSTOMER SERVICE -->
        <div class="dropdown">
            <button type="button" class="icon-btn" id="btnBantuan" data-bs-toggle="dropdown" aria-expanded="false"
                title="Bantuan">
                <i class="bi bi-headset fs-5"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end help-menu" aria-labelledby="btnBantuan">

                <div class="help-menu-header">
                    <i class="bi bi-headset"></i>
                    <div>
                        <div class="help-menu-title">Butuh Bantuan?</div>
                        <div class="help-menu-subtitle">Tim Tendik siap membantu Anda</div>
                    </div>
                </div>

                <div class="help-contact-list">

                    <div class="help-contact-item">
                        <div class="help-contact-avatar">MR</div>
                        <div class="help-contact-info">
                            <div class="help-contact-name">Muhamad Ridwan</div>
                            <div class="help-contact-role">Tim Tendik</div>
                        </div>
                        <a href="https://wa.me/6281381752590" target="_blank" rel="noopener" class="help-contact-wa-btn"
                            title="Chat WhatsApp Muhamad Ridwan">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                    <div class="help-contact-item">
                        <div class="help-contact-avatar">ED</div>
                        <div class="help-contact-info">
                            <div class="help-contact-name">Eilien Dwi K.</div>
                            <div class="help-contact-role">Tim Tendik</div>
                        </div>
                        <a href="https://wa.me/6285213458681" target="_blank" rel="noopener" class="help-contact-wa-btn"
                            title="Chat WhatsApp Eilien">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                    <div class="help-contact-item">
                        <div class="help-contact-avatar">AS</div>
                        <div class="help-contact-info">
                            <div class="help-contact-name">Abdus Salam</div>
                            <div class="help-contact-role">Tim Tendik</div>
                        </div>
                        <a href="https://wa.me/6285220723948" target="_blank" rel="noopener" class="help-contact-wa-btn"
                            title="Chat WhatsApp Salam">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-right"></i>
                <span class="btn-label">Logout</span>
            </button>
        </form>

    </div>

</nav>
