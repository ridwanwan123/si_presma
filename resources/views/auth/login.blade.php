<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PRESMA | Masuk</title>

    {{-- Bootstrap (grid & alert utilities only) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    {{-- Boxicons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    {{-- Fonts: Sora (display) + Inter (UI) + JetBrains Mono (stat numbers) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600;700&display=swap"
        rel="stylesheet" />

    <link rel="icon" href="{{ asset('assets/images/logo_p_remove_bg.png') }}" type="image/x-icon" />

    <style>
        /* =====================================================
           TOKENS — identik dengan halaman register supaya
           dua halaman terasa satu produk yang sama
        ====================================================== */
        :root {
            --ink: #0d1b16;
            --muted: #5b6b64;
            --bg: #f5f7f6;
            --surface: #ffffff;
            --border: #e2e8e4;

            --brand: #15803d;
            --brand-dark: #166534;
            --brand-darker: #14532d;
            --brand-soft: #dcfce7;

            --gold: #c9971f;
            --gold-soft: #faf1dc;

            --danger: #dc3545;

            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 10px;

            --field-h: 50px;
            --btn-h: 52px;

            --shadow-sm: 0 4px 14px rgba(21, 128, 61, 0.08);
            --shadow-md: 0 14px 34px rgba(22, 101, 52, 0.15);
            --shadow-lg: 0 24px 60px rgba(20, 83, 45, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: "Inter", sans-serif;
            background: var(--bg);
            color: var(--ink);
            font-size: 15px;
            line-height: 1.5;
        }

        h1,
        h2,
        h3,
        .font-display {
            font-family: "Sora", sans-serif;
        }

        a {
            text-decoration: none;
        }

        .mono {
            font-family: "JetBrains Mono", monospace;
        }

        /* =====================================================
           LAYOUT
        ====================================================== */
        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           ASIDE — signature panel,
           sama seperti halaman register
        ========================= */
        .auth-aside {
            flex: 0 0 44%;
            max-width: 44%;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px 48px;
            color: #fff;
            background:
                radial-gradient(900px 500px at 15% -10%,
                    rgba(255, 255, 255, 0.08),
                    transparent 60%),
                linear-gradient(165deg,
                    var(--brand-darker),
                    var(--brand-dark) 55%,
                    var(--brand));
            overflow: hidden;
        }

        .auth-aside::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            right: -100px;
            bottom: -120px;
        }

        .aside-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .aside-brand .mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .aside-brand .mark i {
            font-size: 18px;
            color: var(--gold);
        }

        .aside-brand span {
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.3px;
        }

        .aside-copy {
            position: relative;
            z-index: 2;
            max-width: 380px;
            margin-top: 36px;
        }

        .aside-copy .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--gold-soft);
            background: rgba(201, 151, 31, 0.18);
            border: 1px solid rgba(201, 151, 31, 0.35);
            padding: 5px 12px;
            border-radius: 999px;
            margin-bottom: 16px;
        }

        .aside-copy h1 {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 10px;
        }

        .aside-copy h1 em {
            font-style: normal;
            color: var(--gold);
        }

        .aside-copy p {
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.7;
        }

        /* =========================
           DASHBOARD MOCKUP
           (signature element — sama
           dengan halaman register,
           bikin transisi antar
           halaman terasa konsisten)
        ========================= */
        .mock-stage {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
        }

        .dash-card {
            width: 100%;
            max-width: 300px;
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 20px;
            color: var(--ink);
            transform: rotate(-2deg);
            animation: floatY 6s ease-in-out infinite;
        }

        .dash-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .dash-card__head .dots {
            display: flex;
            gap: 5px;
        }

        .dash-card__head .dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--border);
        }

        .dash-card__head .dots span:first-child {
            background: #f59e0b;
        }

        .dash-card__head .dots span:nth-child(2) {
            background: #22c55e;
        }

        .dash-card__head strong {
            font-size: 12.5px;
            color: var(--muted);
            font-weight: 600;
        }

        .ring-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .ring {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: conic-gradient(var(--brand) 0deg 295deg,
                    var(--brand-soft) 295deg 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .ring span {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .ring-wrap .ring-label {
            font-size: 12px;
            color: var(--muted);
        }

        .ring-wrap .ring-label strong {
            display: block;
            font-size: 13.5px;
            color: var(--ink);
            font-weight: 600;
        }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .bar-row span:first-child {
            flex: 0 0 78px;
            color: var(--muted);
            font-weight: 500;
        }

        .bar-row .track {
            flex: 1;
            height: 6px;
            border-radius: 999px;
            background: var(--border);
            overflow: hidden;
        }

        .bar-row .track i {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: var(--brand);
        }

        .bar-row .pct {
            flex: 0 0 32px;
            text-align: right;
            font-weight: 700;
            color: var(--ink);
        }

        .chip-float {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: var(--ink);
            padding: 8px 12px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            font-size: 12px;
            font-weight: 600;
        }

        .chip-float i {
            color: var(--gold);
            font-size: 16px;
        }

        .chip-1 {
            top: 6%;
            right: 4%;
            transform: rotate(6deg);
            animation: floatY 5s ease-in-out infinite 0.3s;
        }

        .chip-2 {
            bottom: 10%;
            left: 2%;
            transform: rotate(-5deg);
            animation: floatY 5.5s ease-in-out infinite 0.8s;
        }

        .chip-2 i {
            color: var(--brand);
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0) rotate(-2deg);
            }

            50% {
                transform: translateY(-10px) rotate(-1deg);
            }
        }

        .aside-foot {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 22px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
        }

        .aside-foot strong {
            color: #fff;
            font-family: "JetBrains Mono", monospace;
            font-size: 15px;
            display: block;
        }

        /* =====================================================
           MAIN FORM PANEL
        ====================================================== */
        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
        }

        .auth-card .card-head {
            margin-bottom: 28px;
        }

        .card-head .step-tag {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--brand);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .card-head h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .card-head p {
            color: var(--muted);
            font-size: 14px;
        }

        /* toast alert */
        .toast-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 260px;
            max-width: 360px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            padding: 12px 16px;
            box-shadow: var(--shadow-md);
            animation:
                toastIn 0.3s ease,
                toastOut 0.3s ease 4s forwards;
        }

        /* ============ MODAL AKTIVASI AKUN ============ */
        /* Sengaja disalin persis dari .periode-modal-* di base.css --
           halaman login ini berdiri sendiri (tidak load base.css), jadi
           styling-nya perlu dibawa sendiri di sini supaya tampilannya
           konsisten dengan modal periode aktif yang sudah ada. */
        .aktivasi-modal-content {
            border-radius: 22px;
            border: none;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        }

        .aktivasi-modal-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .aktivasi-modal-subtitle {
            color: #64748b;
            font-size: 0.92rem;
            margin-bottom: 1.5rem;
        }

        .aktivasi-modal-box {
            border: 1.5px solid #dbe2ea;
            border-radius: 16px;
            padding: 1.75rem 1rem;
            margin-bottom: 1.75rem;
            background: #f8fafc;
        }

        .aktivasi-modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(37, 211, 102, 0.12);
            color: #25d366;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin: 0 auto 1rem;
        }

        .aktivasi-modal-nama {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.15rem;
        }

        .aktivasi-modal-unit {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 0.6rem;
        }

        .aktivasi-modal-wa-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #25d366;
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 12px;
            padding: 0.7rem 1.2rem;
            text-decoration: none;
            width: 100%;
        }

        .aktivasi-modal-telepon {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f8a43;
            text-decoration: none;
            display: inline-block;
        }

        .aktivasi-modal-telepon:hover {
            color: #0c7438;
        }

        .aktivasi-modal-wa-btn:hover {
            background: #1ebe5a;
            color: #fff;
        }

        .aktivasi-modal-wa-btn i {
            font-size: 1.2rem;
        }

        .aktivasi-modal-btn {
            background: #0f8a43;
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.7rem;
            border: none;
        }

        .aktivasi-modal-btn:hover {
            background: #0c7438;
            color: #fff;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-12px);
                visibility: hidden;
            }
        }

        /* form fields */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
            display: block;
        }

        .field-group {
            margin-bottom: 16px;
        }

        /* input with icon, dibuat manual (bukan bootstrap input-group)
           supaya tinggi & radius konsisten dengan field register */
        .field-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon>i.bx {
            position: absolute;
            left: 15px;
            font-size: 18px;
            color: #9aa8a2;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field-icon:focus-within>i.bx:first-child {
            color: var(--brand);
        }

        .form-control {
            height: var(--field-h);
            width: 100%;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            padding: 0 15px 0 44px;
            font-size: 14.5px;
            background: var(--surface);
            transition:
                border-color 0.2s,
                box-shadow 0.2s;
        }

        .form-control::placeholder {
            color: #9aa8a2;
        }

        .form-control:hover {
            border-color: #cbd8d1;
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px var(--brand-soft);
            outline: none;
        }

        .form-control.has-toggle {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            font-size: 18px;
            color: #9aa8a2;
            cursor: pointer;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--brand);
        }

        .form-extra {
            display: flex;
            justify-content: flex-end;
            margin: -6px 0 18px;
        }

        .form-extra a {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
        }

        .form-extra a:hover {
            color: var(--brand);
        }

        /* button */
        .btn-login {
            width: 100%;
            height: var(--btn-h);
            border: none;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition:
                transform 0.2s,
                box-shadow 0.2s;
            margin-top: 6px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login i {
            font-size: 18px;
        }

        .footer-link {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
            color: var(--muted);
        }

        .footer-link a {
            color: var(--brand);
            font-weight: 600;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        .help-text {
            margin-top: 28px;
            text-align: center;
            font-size: 12.5px;
            color: var(--muted);
        }

        .help-text i {
            margin-right: 4px;
            color: var(--brand);
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px white inset;
            -webkit-text-fill-color: #111827;
        }

        /* =====================================================
           RESPONSIVE
        ====================================================== */
        @media (max-width: 991px) {
            .auth-aside {
                display: none;
            }

            .auth-main {
                padding: 32px 20px;
            }
        }

        @media (max-width: 576px) {
            .auth-main {
                padding: 24px 16px;
            }

            .card-head h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        {{-- ============================== ASIDE — signature panel
      ============================== --}}
        <div class="auth-aside">
            <div class="aside-brand">
                <div class="mark"><i class="bx bxs-graduation"></i></div>
                <span>PRESMA | JMA 2027</span>
            </div>

            <div class="aside-copy">
                <span class="eyebrow"><i class="bx bxs-certification"></i> Sistem Prestasi Madrasah</span>
                <h1>Masuk untuk melihat <em>data prestasi</em></h1>
                <p>Semua data prestasi sudah menunggu di dashboard.</p>
            </div>

            <div class="mock-stage">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <div class="dots"><span></span><span></span><span></span></div>
                        <strong>Ringkasan Prestasi</strong>
                    </div>

                    <div class="ring-wrap">
                        <div class="ring"><span class="mono">82%</span></div>
                        <div class="ring-label">
                            Capaian keseluruhan
                            <strong>Semester genap 2026</strong>
                        </div>
                    </div>

                    <div class="bar-row">
                        <span>Akademik</span>
                        <div class="track"><i style="width: 84%"></i></div>
                        <span class="pct">84%</span>
                    </div>
                    <div class="bar-row">
                        <span>Non Akademik</span>
                        <div class="track"><i style="width: 81%"></i></div>
                        <span class="pct">81%</span>
                    </div>
                    <div class="bar-row">
                        <span>Keagamaan</span>
                        <div class="track"><i style="width: 92%"></i></div>
                        <span class="pct">92%</span>
                    </div>
                    <div class="bar-row">
                        <span>GTK</span>
                        <div class="track"><i style="width: 88%"></i></div>
                        <span class="pct">88%</span>
                    </div>
                    <div class="bar-row">
                        <span>Kelembagaan</span>
                        <div class="track"><i style="width: 79%"></i></div>
                        <span class="pct">79%</span>
                    </div>
                </div>

                <div class="chip-float chip-1">
                    <i class="bx bxs-trophy"></i> Juara 1 Nasional
                </div>
                <div class="chip-float chip-2">
                    <i class="bx bxs-check-shield"></i> Data terverifikasi
                </div>
            </div>

            <div class="aside-foot">
                <div><strong>120+</strong> madrasah aktif</div>
                <div><strong>98%</strong> tingkat kepuasan</div>
                <div><strong>24/7</strong> dukungan</div>
            </div>
        </div>

        {{-- ============================== FORM ==============================
      --}}
        <div class="auth-main">
            <div class="auth-card">
                <div class="card-head">
                    <span class="step-tag">Selamat datang kembali</span>
                    <h2>Masuk ke akun Anda</h2>
                    <p>Gunakan username dan password terdaftar</p>
                </div>

                @if (session('error') && !session('show_aktivasi_modal'))
                    <div class="toast-alert alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif @if (session('success') && !session('show_aktivasi_modal'))
                        <div class="toast-alert alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="field-group">
                            <label class="form-label">Username</label>
                            <div class="field-icon">
                                <i class="bx bx-user"></i>
                                <input type="text" name="login" class="form-control"
                                    placeholder="Masukkan username" required />
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">Password</label>
                            <div class="field-icon">
                                <i class="bx bx-lock-alt"></i>
                                <input type="password" id="password" name="password" class="form-control has-toggle"
                                    placeholder="Masukkan password" required />
                                <i class="bx bx-show toggle-password" id="togglePassword"></i>
                            </div>
                        </div>

                        <div class="form-extra">
                            <a href="#">Lupa password?</a>
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="bx bx-log-in"></i>
                            Masuk
                        </button>

                        <div class="footer-link">
                            Belum punya akun?
                            <a href="{{ route('register.form') }}">Daftar di sini</a>
                        </div>
                    </form>

                    <div class="help-text">
                        <i class="bx bx-help-circle"></i>
                        Butuh bantuan? Hubungi Administrator
                    </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= MODAL
    AKTIVASI AKUN — muncul otomatis kalau server flash 'show_aktivasi_modal'
    (pola sama seperti modal Periode Aktif di base.blade.php, cuma dipasang di
    sini karena halaman login tidak pakai layout base.blade.php).
    ========================================================== --}}
    <div class="modal fade" id="modalAktivasiAkun" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content aktivasi-modal-content">
                <div class="modal-body text-center px-4 py-5">
                    <div class="aktivasi-modal-icon">
                        <i class="bx bxl-whatsapp"></i>
                    </div>

                    <h4 class="aktivasi-modal-title">
                        {{ session('aktivasi_modal_judul', 'Akun Belum Aktif') }}
                    </h4>
                    <p class="aktivasi-modal-subtitle">
                        Akun perlu diaktifkan terlebih dahulu sebelum bisa dipakai login
                    </p>

                    <div class="aktivasi-modal-box">
                        <div class="aktivasi-modal-nama">Muhamad Ridwan</div>
                        <div class="aktivasi-modal-unit">Penmad DKI Jakarta</div>
                        <div class="aktivasi-modal-telepon">0813-8175-2590</div>

                        @php                         $pesanWaAktivasi = 'Assalamualaikum, mohon izin untuk approve
              akun PRESMA saya agar bisa segera digunakan. Terima kasih banyak
              atas bantuannya 🙏'; @endphp

                        <a href="https://wa.me/6281381752590?text={{ urlencode($pesanWaAktivasi) }}" target="_blank"
                            rel="noopener" class="aktivasi-modal-wa-btn">
                            <i class="bx bxl-whatsapp"></i> Kirim Pesan
                        </a>
                    </div>

                    <button type="button" class="btn aktivasi-modal-btn w-100" data-bs-dismiss="modal">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const showAktivasiModal = @json(session('show_aktivasi_modal', false));

            if (!showAktivasiModal) return;

            const modalEl = document.getElementById('modalAktivasiAkun');

            if (modalEl && window.bootstrap) {
                new bootstrap.Modal(modalEl).show();
            }
        });
    </script>

    <script>
        document.querySelectorAll(".toast-alert").forEach((toast) => {
            setTimeout(() => toast.remove(), 4300);
        });

        const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const isPassword = password.getAttribute("type") === "password";
            password.setAttribute("type", isPassword ? "text" : "password");

            togglePassword.classList.toggle("bx-show", !isPassword);
            togglePassword.classList.toggle("bx-hide", isPassword);
        });
    </script>
</body>

</html>
