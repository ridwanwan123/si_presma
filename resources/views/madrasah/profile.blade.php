@extends('layouts.base')

@push('styles')
    <style>
        :root {
            --primary: #0f172a;
            --primary-light: #38bdf8;
            --success: #10b981;
            --warning: #f59e0b;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        .profile-container {
            font-family: "Inter", system-ui, -apple-system, sans-serif;
            color: var(--text-main);
            padding: 2rem 1.5rem;
            max-width: 1200px;
            margin: auto;
        }

        /* ================================
                                                   HERO
                                                ================================ */

        .profile-hero {
            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            gap: 2rem;

            padding: 2.5rem;
            margin-bottom: 2rem;

            border-radius: 16px;

            background-image: url("/assets/images/cover_madrasah_jakarta.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            color: white;
            isolation: isolate;
        }

        .profile-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.60);
            z-index: 0;
        }

        .profile-hero>* {
            position: relative;
            z-index: 1;
        }

        .logo-container {
            width: 110px;
            height: 110px;
            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 8px;

            background: white;
            border-radius: 12px;

            box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hero-details h1 {
            margin: 0;
            font-size: 1.85rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .9rem;
        }

        .badge {
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .badge-status {
            background: var(--success);
            color: white;
        }

        .badge-jenjang {
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            color: white;
        }

        .badge-akreditasi {
            background: var(--warning);
            color: white;
        }

        /* ================================
                                                   GRID
                                                ================================ */

        .profile-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .left-column,
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* ================================
                                                   CARD
                                                ================================ */

        .saas-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.75rem;

            box-shadow:
                0 1px 2px rgba(15, 23, 42, .03),
                0 6px 20px rgba(15, 23, 42, .04);

            transition: all .25s ease;
        }

        .saas-card:hover {
            transform: translateY(-4px);
            box-shadow:
                0 10px 30px rgba(15, 23, 42, .08);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: .65rem;

            margin-top: 0;
            margin-bottom: 1.5rem;
            padding-bottom: .9rem;

            border-bottom: 1px solid var(--border-color);

            color: var(--primary);
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* ================================
                                                   INFO GRID
                                                ================================ */

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.4rem;
        }

        .info-group {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            margin-bottom: .35rem;

            font-size: .78rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .info-value {
            font-size: .95rem;
            font-weight: 500;
            color: var(--primary);
            line-height: 1.6;
            word-break: break-word;
        }

        /* ================================
                                                   SDM
                                                ================================ */

        .sdm-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .sdm-item {
            display: flex;
            align-items: center;
            gap: 1rem;

            padding: 1rem;

            background: #f8fafc;

            border: 1px solid var(--border-color);
            border-radius: 10px;

            transition: .25s;
        }

        .sdm-item:hover {
            background: white;
        }

        .sdm-photo {
            width: 70px;
            height: 70px;

            flex-shrink: 0;

            border-radius: 10px;
            object-fit: cover;

            background: #f1f5f9;

            border: 2px solid white;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .sdm-item img.sdm-photo[src^="https://placehold.co"] {
            object-fit: contain;
            padding: 5px;
        }

        .sdm-info {
            flex: 1;
        }

        .sdm-role {
            display: inline-block;

            margin-bottom: .3rem;

            color: var(--success);

            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .sdm-info h4 {
            margin: 0 0 .3rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .sdm-info p {
            margin: .15rem 0;
            color: var(--text-muted);
            font-size: .85rem;
        }

        /* ================================
                                                   MAP BUTTON
                                                ================================ */

        .map-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;

            margin-top: 1.5rem;

            padding: .65rem 1rem;

            border: 1px solid var(--border-color);
            border-radius: 8px;

            background: #f1f5f9;

            color: var(--primary);

            text-decoration: none;
            font-size: .85rem;
            font-weight: 600;

            transition: .25s;
        }

        .map-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* ================================
                                                   TABLET
                                                ================================ */

        @media (max-width: 992px) {

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-hero {
                flex-direction: column;
                text-align: center;
            }

            .badge-container {
                justify-content: center;
            }

        }

        /* ================================
                                                   MOBILE
                                                ================================ */

        @media (max-width:768px) {

            .profile-container {
                padding: 1rem;
            }

            .profile-hero {
                padding: 1.5rem;
                gap: 1rem;
            }

            .logo-container {
                width: 90px;
                height: 90px;
            }

            .hero-details h1 {
                font-size: 1.45rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .card-title {
                font-size: 1rem;
            }

        }

        /* ================================
                                                   SMALL MOBILE
                                                ================================ */

        @media (max-width:576px) {

            .profile-hero {
                padding: 1.25rem;
            }

            .hero-details h1 {
                font-size: 1.25rem;
            }

            .badge {
                font-size: .7rem;
                padding: .4rem .7rem;
            }

            .sdm-item {
                flex-direction: column;
                text-align: center;
            }

            .sdm-photo {
                width: 85px;
                height: 85px;
            }

            .sdm-info {
                width: 100%;
            }

            .map-btn {
                width: 100%;
                justify-content: center;
            }

        }

        .edit-profile-btn {
            position: absolute;
            top: 24px;
            right: 24px;
            z-index: 2;

            display: inline-flex;
            align-items: center;
            gap: .5rem;

            padding: .7rem 1rem;

            border-radius: 10px;

            background: rgba(255, 255, 255, 0.417);
            backdrop-filter: blur(10px);

            border: 1px solid rgba(255, 255, 255, .25);

            color: #fff;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 600;

            transition: .25s;
        }

        .edit-profile-btn:hover {
            background: #fff;
            color: var(--primary);
            transform: translateY(-2px);
            text-decoration: none;
        }

        .profile-info {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem 1.25rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 5px solid #2563eb;
            border-radius: 12px;
        }

        .profile-info i {
            font-size: 1.2rem;
            color: #2563eb;
            margin-top: 2px;
        }

        .profile-info h5 {
            margin: 0 0 .25rem;
            color: #1e3a8a;
            font-size: 1rem;
            font-weight: 600;
        }

        .profile-info p {
            margin: 0;
            color: #475569;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="profile-container">
            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Informasi!</strong> Mohon untuk melengkapi data profil madrasah agar informasi yang ditampilkan
                    lebih lengkap dan akurat. Pastikan data identitas, alamat, serta informasi kepala madrasah dan tata
                    usaha
                    telah diisi dengan benar.
                </div>
            </div>
            <div class="profile-hero">
                <div class="logo-container">
                    @if ($madrasah->logo)
                        <img src="{{ asset('storage/' . $madrasah->logo) }}" alt="Logo {{ $madrasah->nama_madrasah }}">
                    @else
                        <img src="https://placehold.co/100x100?text=LOGO" alt="Placeholder Logo">
                    @endif
                </div>
                <div class="hero-details">
                    <h1>{{ $madrasah->nama_madrasah }}</h1>
                    <div class="badge-container">
                        <span class="badge badge-jenjang">{{ $madrasah->jenjang_madrasah }}</span>
                        <span class="badge badge-status">{{ $madrasah->status_madrasah }}</span>
                        <span class="badge badge-akreditasi">Akreditasi:
                            {{ $madrasah->akreditasi ?? 'Belum Terakreditasi' }}</span>
                    </div>
                </div>
                <a href="{{ route('madrasah.edit', $madrasah->id) }}" class="edit-profile-btn">
                    <i class="fas fa-pen"></i>
                    Edit Madrasah
                </a>
            </div>

            <div class="profile-grid">

                <div class="left-column">

                    <div class="saas-card">
                        <h3 class="card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Informasi Umum & Legalitas
                        </h3>
                        <div class="info-grid">
                            <div class="info-group">
                                <span class="info-label">NPSN</span>
                                <span class="info-value">{{ $madrasah->npsn ?? '-' }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Jenjang Pendidikan</span>
                                <span class="info-value">{{ $madrasah->jenjang_madrasah }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Status Madrasah</span>
                                <span class="info-value">{{ $madrasah->status_madrasah }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Nilai Akreditasi</span>
                                <span class="info-value">{{ $madrasah->akreditasi ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="saas-card">
                        <h3 class="card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Lokasi & Alamat
                        </h3>
                        <div class="info-grid" style="grid-template-columns: 1fr; row-gap: 1.25rem;">
                            <div class="info-group">
                                <span class="info-label">Alamat Lengkap</span>
                                <span class="info-value">{{ $madrasah->alamat_sekolah ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="info-grid" style="margin-top: 1.25rem;">
                            <div class="info-group">
                                <span class="info-label">Kelurahan / Desa</span>
                                <span class="info-value">{{ $madrasah->kelurahan ?? '-' }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Kecamatan</span>
                                <span class="info-value">{{ $madrasah->kecamatan ?? '-' }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Kota / Kabupaten</span>
                                <span class="info-value">{{ $madrasah->kota ?? '-' }}</span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Provinsi</span>
                                <span class="info-value">{{ $madrasah->provinsi ?? '-' }}</span>
                            </div>
                        </div>

                        @if ($madrasah->latitude && $madrasah->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $madrasah->latitude }},{{ $madrasah->longitude }}"
                                target="_blank" class="map-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
                                    <line x1="9" y1="3" x2="9" y2="18"></line>
                                    <line x1="15" y1="6" x2="15" y2="21"></line>
                                </svg>
                                Lihat di Google Maps
                            </a>
                        @endif
                    </div>

                </div>

                <div class="right-column">

                    <div class="saas-card">
                        <h3 class="card-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Manajemen Madrasah
                        </h3>
                        <div class="sdm-wrapper">

                            <div class="sdm-item">
                                @if ($madrasah->foto_kamad)
                                    <img src="{{ asset('storage/' . $madrasah->foto_kamad) }}" alt="Foto Kamad"
                                        class="sdm-photo">
                                @else
                                    <img src="https://placehold.co/150x150?text=User" alt="Default Avatar"
                                        class="sdm-photo">
                                @endif
                                <div class="sdm-info">
                                    <span class="sdm-role">Kepala Madrasah</span>
                                    <h4>{{ $madrasah->nama_kepala_madrasah ?? 'Belum Diisi' }}</h4>
                                    <p>NIP. {{ $madrasah->nip_kepala_madrasah ?? '-' }}</p>
                                    <p style="font-size: 0.8rem; margin-top: 2px;">📞
                                        {{ $madrasah->no_telepon_kamad ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="sdm-item">
                                @if ($madrasah->foto_katu)
                                    <img src="{{ asset('storage/' . $madrasah->foto_katu) }}" alt="Foto Katu"
                                        class="sdm-photo">
                                @else
                                    <img src="https://placehold.co/150x150?text=User" alt="Default Avatar"
                                        class="sdm-photo">
                                @endif
                                <div class="sdm-info">
                                    <span class="sdm-role">Kepala Tata Usaha</span>
                                    <h4>{{ $madrasah->nama_kepala_urusan_tata_usaha ?? 'Belum Diisi' }}</h4>
                                    <p>NIP. {{ $madrasah->nip_kepala_urusan_tata_usaha ?? '-' }}</p>
                                    <p style="font-size: 0.8rem; margin-top: 2px;">📞
                                        {{ $madrasah->no_telepon_katu ?? '-' }}</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>
@endsection
