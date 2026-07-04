<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PRESMA | Registrasi</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Boxicons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/registrasi.css') }}">

</head>

<body>

    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>
    <div class="bg-circle bg-circle-3"></div>

    <div class="container-fluid p-0">

        <div class="row g-0 min-vh-100">

            {{-- ==============================
            LEFT
        =============================== --}}
            <div class="col-lg-6 d-none d-lg-flex">

                <div class="register-left">

                    <div class="overlay"></div>

                    <div class="left-content">

                        <span class="badge-presma">
                            JMA | Penmad Kanwil Kemenag Prov. DKI Jakarta
                        </span>

                        <h1>
                            PRESMA
                        </h1>

                        <h3>
                            Jakarta Madrasah Awards
                        </h3>

                        <p>
                            Sistem informasi terintegrasi untuk mendukung proses pendataan,
                            validasi, dan penilaian prestasi madrasah pada program Jakarta Madrasah Awards
                            di lingkungan Kanwil Kementerian Agama Provinsi DKI Jakarta.
                        </p>

                        <div class="feature-list">

                            <div class="feature-item">

                                <i class='bx bx-check-circle'></i>

                                <span>
                                    Verifikasi Prestasi Terstandar
                                </span>

                            </div>

                            <div class="feature-item">

                                <i class='bx bx-check-circle'></i>

                                <span>
                                    Penilaian Berbasis Sistem
                                </span>

                            </div>

                            <div class="feature-item">

                                <i class='bx bx-check-circle'></i>

                                <span>
                                    Monitoring oleh Pengawas Wilayah
                                </span>

                            </div>

                            <div class="feature-item">

                                <i class='bx bx-check-circle'></i>

                                <span>
                                    Dashboard Modern
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ==============================
            RIGHT
        =============================== --}}
            <div class="col-lg-6">

                <div class="register-right">

                    <div class="register-card">

                        <div class="text-center mb-4 mt-5">

                            <h2 class="fw-bold  mb-2">
                                Registrasi Akun
                            </h2>

                            <p class="text-muted mb-0">
                                Pilih jenis akun terlebih dahulu
                            </p>

                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">

                                {{ session('success') }}

                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">

                                {{ session('error') }}

                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST" id="registerForm">

                            @csrf

                            {{-- =============================
                            ROLE
                        ============================== --}}
                            <div id="roleError" class="text-danger text-center mb-2 d-none">
                                Silakan pilih role terlebih dahulu
                            </div>
                            <div class="role-container">
                                @foreach ($roles as $role)
                                    <label class="role-card">
                                        <input type="radio" name="role_id" value="{{ $role->id }}"
                                            class="role-radio" data-role="{{ strtolower($role->nama) }}">
                                        <div class="role-body">
                                            <div class="role-icon">
                                                @if (strtolower($role->nama) == 'madrasah')
                                                    <i class='bx bxs-school'></i>
                                                @else
                                                    <i class='bx bx-map'></i>
                                                @endif
                                            </div>

                                            <div>
                                                <h5>
                                                    {{ $role->nama }}
                                                </h5>

                                                <small>
                                                    @if (strtolower($role->nama) == 'madrasah')
                                                        Operator Madrasah
                                                    @else
                                                        Pengawas Madrasah
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div id="dynamicForm" class="dynamic-form">
                                {{-- =============================
        MADRASAH
    ============================== --}}

                                <div id="madrasahFields" class="role-fields d-none">
                                    <div class="">
                                        <label class="form-label">
                                            Nama Madrasah
                                        </label>
                                        <div class="search-select">
                                            <input type="text" id="madrasahSearch" class="form-control"
                                                placeholder="Cari nama madrasah..." autocomplete="off">
                                            <input type="hidden" name="madrasah_id" id="madrasahValue"
                                                value="{{ old('madrasah_id') }}">
                                            <div class="search-dropdown" id="madrasahDropdown">
                                                @foreach ($madrasahs as $madrasah)
                                                    <div class="dropdown-item-custom" data-id="{{ $madrasah->id }}">
                                                        {{ $madrasah->nama_madrasah }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- =============================
        PENGAWAS
    ============================== --}}

                                <div id="pengawasFields" class="role-fields d-none">
                                    <div class="">
                                        <label class="form-label">
                                            Wilayah Pengawas
                                        </label>
                                        <select class="form-select" name="wilayah_pengawas_id">
                                            <option value="">
                                                -- Pilih Wilayah --
                                            </option>
                                            <option value="1">
                                                Jakarta Pusat
                                            </option>

                                            <option value="2">
                                                Jakarta Utara
                                            </option>

                                            <option value="3">
                                                Jakarta Barat
                                            </option>

                                            <option value="4">
                                                Jakarta Timur
                                            </option>

                                            <option value="5">
                                                Jakarta Selatan
                                            </option>

                                            <option value="6">
                                                Kepulauan Seribu
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Nama Lengkap
                                        </label>
                                        <input type="text" name="nama" class="form-control"
                                            value="{{ old('nama') }}" placeholder="Masukkan nama">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Email
                                        </label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email') }}" placeholder="email@gmail.com">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Username
                                        </label>

                                        <input type="text" name="username" class="form-control"
                                            value="{{ old('username') }}" placeholder="Username">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            No. HP
                                        </label>
                                        <input type="text" name="no_hp" class="form-control"
                                            value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Password
                                        </label>

                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="******">

                                        <div class="password-check mt-2">

                                            <small id="rule-length" class="invalid-rule">
                                                <i class='bx bx-x-circle'></i>
                                                Minimal 8 karakter
                                            </small>

                                            <small id="rule-number" class="invalid-rule">
                                                <i class='bx bx-x-circle'></i>
                                                Mengandung minimal 1 angka
                                            </small>

                                            <small id="rule-confirm" class="invalid-rule">
                                                <i class='bx bx-x-circle'></i>
                                                Password dan konfirmasi harus sama
                                            </small>

                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">
                                            Konfirmasi Password
                                        </label>

                                        <input type="password" name="password_confirmation"
                                            id="password_confirmation" class="form-control" placeholder="******">
                                    </div>
                                </div>

                                <button type="submit" class="btn-register">
                                    <i class='bx bx-user-plus'></i>
                                    Daftar Sekarang
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            Sudah punya akun?
                            <a href="{{ route('login.form') }}">
                                Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const radios = document.querySelectorAll(".role-radio");

        const madrasah = document.getElementById("madrasahFields");
        const pengawas = document.getElementById("pengawasFields");

        const form = document.getElementById("registerForm");
        const roleError = document.getElementById("roleError");

        form.addEventListener("submit", function(e) {

            const roleChecked = document.querySelector('input[name="role_id"]:checked');

            if (!roleChecked) {
                e.preventDefault();

                roleError.classList.remove("d-none");

                document.querySelector(".role-container")
                    .scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });

                return false;
            }

        });
        // ============================
        // FUNCTION: set required
        // ============================
        function setRequired(container, status) {
            container.querySelectorAll("input, select").forEach(el => {
                if (status) {
                    el.setAttribute("required", "required");
                } else {
                    el.removeAttribute("required");
                    el.value = "";
                }
            });
        }

        // ============================
        // ROLE CHANGE HANDLER
        // ============================
        radios.forEach(r => {
            r.addEventListener("change", function() {

                // hide semua dulu
                madrasah.classList.add("d-none");
                pengawas.classList.add("d-none");

                // remove required semua dulu
                setRequired(madrasah, false);
                setRequired(pengawas, false);

                // MADRASAH
                if (this.dataset.role === "madrasah") {
                    madrasah.classList.remove("d-none");
                    setRequired(madrasah, true);
                }

                // PENGAWAS
                if (this.dataset.role === "pengawas") {
                    pengawas.classList.remove("d-none");
                    setRequired(pengawas, true);
                }

            });
        });

        // ============================
        // MADRASAH SEARCH SELECT
        // ============================
        const search = document.getElementById("madrasahSearch");
        const dropdown = document.getElementById("madrasahDropdown");
        const hidden = document.getElementById("madrasahValue");

        if (search) {

            // show dropdown
            search.addEventListener("focus", () => {
                dropdown.style.display = "block";
            });

            // filter search
            search.addEventListener("keyup", () => {
                const keyword = search.value.toLowerCase();

                document.querySelectorAll(".dropdown-item-custom").forEach(item => {
                    item.style.display = item.innerText.toLowerCase().includes(keyword) ?
                        "block" :
                        "none";
                });
            });

            // click item
            document.querySelectorAll(".dropdown-item-custom").forEach(item => {
                item.addEventListener("click", () => {
                    search.value = item.innerText;
                    hidden.value = item.dataset.id;
                    dropdown.style.display = "none";
                });
            });

            // close when outside click
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".search-select")) {
                    dropdown.style.display = "none";
                }
            });
        }

        const password = document.getElementById("password");

        const confirmPassword = document.getElementById("password_confirmation");

        const ruleLength = document.getElementById("rule-length");

        const ruleNumber = document.getElementById("rule-number");

        const ruleConfirm = document.getElementById("rule-confirm");

        function setRule(element, valid) {

            if (valid) {

                element.classList.remove("invalid-rule");
                element.classList.add("valid-rule");

                element.innerHTML =
                    "<i class='bx bx-check-circle'></i>" +
                    element.textContent.trim();

            } else {

                element.classList.remove("valid-rule");
                element.classList.add("invalid-rule");

                element.innerHTML =
                    "<i class='bx bx-x-circle'></i>" +
                    element.textContent.trim();

            }

        }

        function validatePassword() {

            const pass = password.value;

            const confirm = confirmPassword.value;

            // minimal 8 karakter
            setRule(ruleLength, pass.length >= 8);

            // mengandung angka
            setRule(ruleNumber, /\d/.test(pass));

            // konfirmasi password
            setRule(
                ruleConfirm,
                confirm.length > 0 && pass === confirm
            );

        }

        password.addEventListener("input", validatePassword);

        confirmPassword.addEventListener("input", validatePassword);
    </script>
</body>

</html>
