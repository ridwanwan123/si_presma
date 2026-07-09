<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRESMA | Daftar Akun</title>

    {{-- Bootstrap (grid & alert utilities only) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Boxicons --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    {{-- Fonts: Sora (display) + Inter (UI) + JetBrains Mono (stat numbers) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600;700&display=swap"
        rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/images/logo_p_remove_bg.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/registrasi.css') }}">
</head>

<body>

    <div class="auth-wrapper">

        {{-- ============================== ASIDE — signature panel ============================== --}}
        <div class="auth-aside">

            <div class="aside-brand">
                <div class="mark"><i class='bx bxs-graduation'></i></div>
                <span>PRESMA | JMA 2027</span>
            </div>

            <div class="aside-copy">
                <span class="eyebrow"><i class='bx bxs-certification'></i> Sistem Prestasi Madrasah</span>
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
                        <div class="track"><i style="width:84%"></i></div>
                        <span class="pct">84%</span>
                    </div>
                    <div class="bar-row">
                        <span>Non Akademik</span>
                        <div class="track"><i style="width:81%"></i></div>
                        <span class="pct">81%</span>
                    </div>
                    <div class="bar-row">
                        <span>Keagamaan</span>
                        <div class="track"><i style="width:92%"></i></div>
                        <span class="pct">92%</span>
                    </div>
                    <div class="bar-row">
                        <span>GTK</span>
                        <div class="track"><i style="width:88%"></i></div>
                        <span class="pct">88%</span>
                    </div>
                    <div class="bar-row">
                        <span>Kelembagaan</span>
                        <div class="track"><i style="width:79%"></i></div>
                        <span class="pct">79%</span>
                    </div>
                </div>

                <div class="chip-float chip-1"><i class='bx bxs-trophy'></i> Juara 1 Nasional</div>
                <div class="chip-float chip-2"><i class='bx bxs-check-shield'></i> Data terverifikasi</div>
            </div>

            <div class="aside-foot">
                <div><strong>120+</strong> madrasah aktif</div>
                <div><strong>98%</strong> tingkat kepuasan</div>
                <div><strong>24/7</strong> dukungan</div>
            </div>
        </div>

        {{-- ============================== FORM ============================== --}}
        <div class="auth-main">
            <div class="auth-card">

                <div class="card-head">
                    <span class="step-tag">Jakarta Madrasah Awards | Account</span>
                    <h2>Buat akun baru</h2>
                    <p>Pilih jenis akun, lalu lengkapi data di bawah ini</p>
                </div>

                @if (session('success'))
                    <div class="toast-alert alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="toast-alert alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('register') }}" method="POST" id="registerForm">
                    @csrf

                    <div id="roleError" class="text-danger text-center mb-2 d-none">
                        Silakan pilih role terlebih dahulu
                    </div>

                    <span class="section-label">Jenis akun</span>
                    <div class="role-container">
                        @foreach ($roles->where('nama', 'Madrasah') as $role)
                            <label class="role-card">
                                <input type="radio" name="role_id" value="{{ $role->id }}" class="role-radio"
                                    data-role="madrasah">

                                <div class="role-body">
                                    <div class="role-icon"><i class='bx bxs-school'></i></div>
                                    <div>
                                        <h5>{{ $role->nama }}</h5>
                                        <small>Admin Madrasah</small>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="dynamicForm" class="dynamic-form">

                        <div id="madrasahFields" class="role-fields d-none">
                            <div class="field-group">
                                <label class="form-label">Nama Madrasah</label>
                                <div class="search-select">
                                    <input type="text" id="madrasahSearch" class="form-control"
                                        placeholder="Cari nama madrasah atau NPSN..." autocomplete="off">
                                    <input type="hidden" name="madrasah_id" id="madrasahValue"
                                        value="{{ old('madrasah_id') }}">
                                    <div class="search-dropdown" id="madrasahDropdown">
                                        @foreach ($madrasahs as $madrasah)
                                            <div class="dropdown-item-custom" data-id="{{ $madrasah->id }}">
                                                {{ $madrasah->nama_madrasah }} - {{ $madrasah->npsn }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="pengawasFields" class="role-fields d-none">
                            <div class="field-group">
                                <label class="form-label">Wilayah Pengawas</label>
                                <select class="form-select" name="wilayah_pengawas_id">
                                    <option value="">-- Pilih Wilayah --</option>
                                    <option value="1">Jakarta Pusat</option>
                                    <option value="2">Jakarta Utara</option>
                                    <option value="3">Jakarta Barat</option>
                                    <option value="4">Jakarta Timur</option>
                                    <option value="5">Jakarta Selatan</option>
                                    <option value="6">Kepulauan Seribu</option>
                                </select>
                            </div>
                        </div>

                        <span class="section-label">Data akun</span>
                        <div class="form-row">
                            <div class="field-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control"
                                    value="{{ old('nama') }}" placeholder="Masukkan nama">
                            </div>

                            <div class="field-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email') }}" placeholder="email@gmail.com">
                            </div>

                            <div class="field-group">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username') }}" placeholder="Username">
                            </div>

                            <div class="field-group">
                                <label class="form-label">No. HP</label>
                                <input type="number" name="no_hp" class="form-control"
                                    value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="field-group">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="******">

                                <div class="password-check">
                                    <small id="rule-length" class="invalid-rule">
                                        <i class='bx bx-x-circle'></i> Minimal 8 karakter
                                    </small>
                                    <small id="rule-number" class="invalid-rule">
                                        <i class='bx bx-x-circle'></i> Mengandung minimal 1 angka
                                    </small>
                                    <small id="rule-confirm" class="invalid-rule">
                                        <i class='bx bx-x-circle'></i> Password dan konfirmasi harus sama
                                    </small>
                                </div>
                            </div>

                            <div class="field-group">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="******">
                            </div>
                        </div>

                        <button type="submit" class="btn-register">
                            <i class='bx bx-user-plus'></i>
                            Buat Akun
                        </button>
                    </div>
                </form>

                <div class="footer-link">
                    Sudah punya akun?
                    <a href="{{ route('login.form') }}">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll(".toast-alert").forEach(toast => {
            setTimeout(() => toast.remove(), 4300);
        });

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

        radios.forEach(r => {
            r.addEventListener("change", function() {
                madrasah.classList.add("d-none");
                pengawas.classList.add("d-none");
                setRequired(madrasah, false);
                setRequired(pengawas, false);

                if (this.dataset.role === "madrasah") {
                    madrasah.classList.remove("d-none");
                    setRequired(madrasah, true);
                }

                if (this.dataset.role === "pengawas") {
                    pengawas.classList.remove("d-none");
                    setRequired(pengawas, true);
                }
            });
        });

        const search = document.getElementById("madrasahSearch");
        const dropdown = document.getElementById("madrasahDropdown");
        const hidden = document.getElementById("madrasahValue");

        if (search) {
            search.addEventListener("focus", () => {
                dropdown.style.display = "block";
            });

            search.addEventListener("keyup", () => {
                const keyword = search.value.toLowerCase();
                document.querySelectorAll(".dropdown-item-custom").forEach(item => {
                    item.style.display = item.innerText.toLowerCase().includes(keyword) ? "block" : "none";
                });
            });

            document.querySelectorAll(".dropdown-item-custom").forEach(item => {
                item.addEventListener("click", () => {
                    search.value = item.innerText;
                    hidden.value = item.dataset.id;
                    dropdown.style.display = "none";
                });
            });

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
        const passwordCheck = document.querySelector(".password-check");

        function setRule(element, valid) {
            element.classList.remove(valid ? "invalid-rule" : "valid-rule");
            element.classList.add(valid ? "valid-rule" : "invalid-rule");
            element.innerHTML = (valid ? "<i class='bx bx-check-circle'></i> " : "<i class='bx bx-x-circle'></i> ") +
                element.textContent.trim();
        }

        function validatePassword() {
            const pass = password.value;
            const confirm = confirmPassword.value;

            passwordCheck.classList.toggle("show", pass.length > 0 || confirm.length > 0);

            setRule(ruleLength, pass.length >= 8);
            setRule(ruleNumber, /\d/.test(pass));
            setRule(ruleConfirm, confirm.length > 0 && pass === confirm);
        }

        password.addEventListener("input", validatePassword);
        confirmPassword.addEventListener("input", validatePassword);
    </script>
</body>

</html>
