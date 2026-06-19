@extends('layouts.base')

@section('breadcrumb')
    <div class="breadcrumb-modern">
        <div class="crumb">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-house-door-fill home-icon"></i>
                Home
            </a>
        </div>

        <span class="separator">
            <i class="bi bi-chevron-right"></i>
        </span>

        <div class="active">
            Ubah Password
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .page-title {
            padding: 0 1rem;
            margin-bottom: 1rem;
        }

        .page-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .25rem;
        }

        .page-title p {
            color: #64748b;
            margin: 0;
        }

        .content-card {
            margin: 0 1rem 1rem;
            background: #fff;
            border-radius: 18px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        }

        .card-header-custom {
            padding: 1.5rem;
            border-bottom: 1px solid #eef2f7;
            background: #f8fafc;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .card-header-custom p {
            margin: .35rem 0 0;
            color: #64748b;
            font-size: .9rem;
        }

        .content-card-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: .5rem;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
        }

        .form-control:focus {
            border-color: #0f8a43;
            box-shadow: 0 0 0 .15rem rgba(15, 138, 67, .15);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
        }

        .password-hint {
            margin-top: .5rem;
            font-size: .85rem;
            color: #64748b;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eef2f7;
        }

        .btn-success {
            background: #0f8a43;
            border-color: #0f8a43;
        }

        .btn-success:hover {
            background: #0c7438;
            border-color: #0c7438;
        }

        @media(max-width:768px) {
            .content-card-body {
                padding: 1.25rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <main class="content">

        <div class="page-title">
            <h2>Ubah Password</h2>
            <p>Perbarui password akun Anda untuk menjaga keamanan sistem.</p>
        </div>

        <div class="content-card">

            <div class="card-header-custom">
                <h5>Form Ubah Password</h5>
                <p>Masukkan password lama dan password baru Anda.</p>
            </div>

            <div class="content-card-body">

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Password Lama</label>
                        <div class="password-wrapper">
                            <input type="password" name="current_password" class="form-control"
                                placeholder="Masukkan password lama">
                            <i class="bi bi-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control"
                                placeholder="Masukkan password baru">
                            <i class="bi bi-eye toggle-password"></i>
                        </div>
                        <div class="password-hint">
                            Minimal 8 karakter, kombinasi huruf dan angka disarankan.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password baru">
                            <i class="bi bi-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-shield-lock-fill"></i>
                            Simpan Password
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');

                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('bi-eye');
                    this.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('bi-eye-slash');
                    this.classList.add('bi-eye');
                }
            });
        });
    </script>
@endpush
