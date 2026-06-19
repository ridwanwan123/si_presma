<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>PRESMA Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('assets/images/kemenag.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}" />
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- LEFT SIDE -->
            <div class="col-lg-7 left-side d-none d-lg-flex">
                <img src="{{ asset('assets/images/cover.png') }}" class="cover-image" alt="cover" />
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-lg-5 right-side">
                <div class="form-wrapper">
                    <!-- LOGO -->
                    <img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="logo" />

                    <!-- TITLE -->
                    <h1 class="brand-title">PRESMA</h1>

                    <h5 class="brand-subtitle">DKI JAKARTA</h5>

                    <p class="desc"><b>Sistem Informasi Prestasi Madrasah</b></p>

                    <!-- FORM -->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <!-- ALERT ERROR -->
                        <div class="position-relative">
                            @if (session('error'))
                                <div class="alert alert-danger position-absolute w-100" style="top: -60px;">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>

                        <!-- Username / Email -->
                        <div class="mb-4">
                            <label class="form-label">Username / Email</label>

                            <div class="input-group custom-input">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>

                                <input type="text" name="login" class="form-control"
                                    placeholder="Masukkan username atau email" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label">Password</label>

                            <div class="input-group custom-input">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>

                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="Masukkan password" required>

                                <span class="input-group-text toggle-password" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn login-btn w-100">
                            Masuk
                        </button>
                    </form>

                    <!-- HELP -->
                    <div class="help-text">
                        <i class="bi bi-question-circle-fill"></i>
                        Butuh bantuan? Hubungi Administrator
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PASSWORD TOGGLE -->
    <script>
        const togglePassword = document.getElementById("togglePassword");

        const password = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const type =
                password.getAttribute("type") === "password" ? "text" : "password";

            password.setAttribute("type", type);

            togglePassword.innerHTML =
                type === "password" ?
                '<i class="bi bi-eye"></i>' :
                '<i class="bi bi-eye-slash"></i>';
        });
    </script>
</body>

</html>
