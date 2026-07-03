<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PRESMA</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <link rel="icon" href="{{ asset('assets/images/logo_p_remove_bg.png') }}" type="image/x-icon" />

    @stack('styles')
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


            <div class="main-scroll">

                <div class="page-header">
                    @if (isset($breadcrumb))
                        <nav aria-label="breadcrumb" class="breadcrumb-wrapper">

                            <div class="breadcrumb-modern">

                                <div class="crumb">
                                    <a href="{{ route('dashboard') }}">
                                        <i class="bi bi-house-door-fill home-icon"></i>
                                        Home
                                    </a>
                                </div>

                                @foreach ($breadcrumb as $item)
                                    <span class="separator">
                                        <i class="bi bi-chevron-right"></i>
                                    </span>

                                    @if ($item['url'])
                                        <div class="crumb">
                                            <a href="{{ $item['url'] }}">
                                                {{ $item['label'] }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="active">
                                            {{ $item['label'] }}
                                        </div>
                                    @endif
                                @endforeach

                            </div>

                        </nav>
                    @endif
                </div>


                @yield('content')


            </div>
            <footer class="footer">

                <span class="footer-desktop">
                    © 2026 Bidang Pendidikan Madrasah Kanwil Kemenag Prov. DKI Jakarta •
                    All Rights Reserved
                </span>

                <span class="footer-mobile">
                    © 2026 Penmad • Kanwil Kemenag Prov. DKI Jakarta
                </span>

            </footer>


        </div>

        <!-- =========================================================
    SCRIPT
    ========================================================== -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Bootstrap -->
        @stack('scripts')
        <!-- JS -->
        <script src="{{ asset('assets/js/base.js') }}"></script>
        <script>
            document.querySelectorAll('.has-submenu').forEach(menu => {

                menu.addEventListener('click', function(e) {

                    e.preventDefault();

                    const submenu = this.nextElementSibling;

                    submenu.classList.toggle('show');

                    this.classList.toggle('open');

                });

            });
        </script>
</body>

</html>
