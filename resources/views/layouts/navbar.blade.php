<nav class="topbar d-flex justify-content-between align-items-center">

    <!-- LEFT -->
    <div class="left d-flex align-items-center gap-2">

        <i class="bi bi-list toggle-btn" id="toggleSidebar"></i>

        <!-- LOGO -->
        <img src="{{ asset('assets/images/kemenag.png') }}" alt="logo"
            style="height:32px; width:32px; object-fit:contain;">

        <!-- APP NAME -->
        <div class="app-title d-flex flex-column lh-sm">
            <span class="app-name font-weight-bold">PRESMA</span>
            <span class="app-region text-muted small">Penmad DKI Jakarta</span>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right d-flex align-items-center gap-3">

        <!-- NOTIFICATION -->
        <div class="icon position-relative" style="cursor:pointer;">
            <i class="bi bi-bell fs-5"></i>
            <!-- optional badge -->
            <!-- <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">3</span> -->
        </div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>
        </form>

    </div>

</nav>
