<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>SIPRESMA Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
</head>

<body>
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <!-- LOADER -->
    <div class="loader-wrapper" id="loader">
        <div class="loader-circle"></div>
    </div>

    <div class="app-wrapper">
        @include('layouts.sidebar')

        <div class="main">
            <div class="bg-ornament"></div>
            @include('layouts.navbar')

            @yield('content')


            <footer class="footer">
                © 2026 Bidang Pendidikan Madrasah Kanwil Kemenag Prov. DKI Jakarta •
                All Rights Reserved
            </footer>
        </div>
    </div>

    <!-- =========================================================
    SCRIPT
    ========================================================== -->

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS -->
    <script src="{{ asset('assets/js/base.js') }}"></script>
</body>

</html>
