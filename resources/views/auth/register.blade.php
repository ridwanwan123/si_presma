<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>PRESMA Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}" />
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">

            <!-- LEFT SIDE (FORM) -->
            <div class="col-lg-5 right-side">
                <div class="form-wrapper">

                    <img src="{{ asset('assets/images/logo.png') }}" class="logo" />

                    <h1 class="brand-title">REGISTER</h1>
                    <h5 class="brand-subtitle">PRESMA</h5>

                    <p class="desc">Buat akun sistem madrasah</p>

                    <!-- ERROR -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- ROLE -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-shield"></i></span>

                                <select name="role_id" class="form-control" required>
                                    <option value="">Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- MADRASAH -->
                        <div class="mb-3">
                            <label class="form-label">Madrasah (Opsional)</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>

                                <select name="madrasah_id" class="form-control">
                                    <option value="">-- Pilih Madrasah --</option>
                                    @foreach ($madrasahs as $madrasah)
                                        <option value="{{ $madrasah->id }}"
                                            {{ old('madrasah_id') == $madrasah->id ? 'selected' : '' }}>
                                            {{ $madrasah->nama_madrasah }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama" value="{{ old('nama') }}" class="form-control"
                                    required>
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                    required>
                            </div>
                        </div>

                        <!-- USERNAME -->
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input type="text" name="username" value="{{ old('username') }}"
                                    class="form-control" required>
                            </div>
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group custom-input">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <button class="btn login-btn w-100">
                            Register
                        </button>
                    </form>

                    <!-- LINK -->
                    <div class="text-center mt-3">
                        <a href="{{ route('login.form') }}">
                            Sudah punya akun? Login
                        </a>
                    </div>

                </div>
            </div>

            <!-- RIGHT SIDE (COVER) -->
            <div class="col-lg-7 left-side d-none d-lg-flex">
                <img src="{{ asset('assets/images/cover.png') }}" class="cover-image" />
            </div>

        </div>
    </div>
</body>

</html>
