# PRESMA — Penilaian Prestasi Madrasah

Sistem penilaian prestasi madrasah berbasis web untuk **Bidang Pendidikan Madrasah, Kantor Wilayah Kementerian Agama Provinsi DKI Jakarta**. PRESMA digunakan untuk mengelola data madrasah, akun pengguna, penilaian prestasi, serta pencatatan aktivitas sistem dalam rangka **Jakarta Madrasah Awards (JMA)**.

---

## Daftar Isi

- [Tentang](#tentang)
- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Kebutuhan Sistem](#kebutuhan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Akun Default (Seeder)](#akun-default-seeder)
- [Struktur Peran (Role)](#struktur-peran-role)
- [Struktur Proyek](#struktur-proyek)
- [Deployment](#deployment)
- [Lisensi](#lisensi)

---

## Tentang

PRESMA (**Pen**ilaian **S**iswa/**M**adrasah **A**chievement, atau Penilaian Prestasi Madrasah) dibangun untuk mendukung proses administrasi dan penilaian prestasi madrasah di lingkungan Kanwil Kemenag DKI Jakarta, termasuk proses standarisasi skoring untuk **Jakarta Madrasah Awards (JMA)**.

## Fitur

- **Manajemen Data Madrasah** — CRUD data madrasah (profil, kepala madrasah, kepala urusan tata usaha, lokasi peta, logo & foto).
- **Manajemen Akun Pengguna** — Kelola akun Administrator, Madrasah, dan Asesor/Pengawas beserta hak aksesnya.
- **Wilayah Pengawas** — Pengelompokan madrasah berdasarkan wilayah kerja pengawas/asesor (per Kota Administrasi DKI Jakarta).
- **Periode Aktif** — Penentuan tahun/periode penilaian yang sedang berjalan sebagai satu-satunya sumber kebenaran (single source of truth), menggantikan hardcode tahun berjalan.
- **Rubrik & Penilaian Prestasi** — Penilaian prestasi madrasah berdasarkan rubrik yang telah distandarisasi (Bidang Akademik, Keagamaan, Kelembagaan, dll).
- **Activity Log** — Pencatatan seluruh aktivitas pengguna dalam sistem (create, update, delete, login, logout, import) menggunakan `spatie/laravel-activitylog`, lengkap dengan detail perubahan data.
- **Dashboard Ringkasan** — Statistik ringkas (total data, status aktif/nonaktif, dsb.) di setiap modul.

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel |
| Database | MySQL / MariaDB |
| Frontend | Blade, Bootstrap 5, Bootstrap Icons / Boxicons |
| Activity Logging | `spatie/laravel-activitylog` |
| Peta | Leaflet.js + OpenStreetMap (Nominatim) |
| Cropping Gambar | Cropper.js |
| Web Server (produksi) | Nginx + PHP-FPM |
| Database Server (produksi) | MariaDB |
| SSL | Certbot (Let's Encrypt) |
| Process Manager | Supervisor |

## Kebutuhan Sistem

- PHP >= 8.2
- Composer
- Node.js & NPM (untuk build asset, jika ada)
- MySQL/MariaDB >= 10.x
- Ekstensi PHP: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

## Instalasi

```bash
# 1. Clone repository
git clone <url-repo> presma
cd presma

# 2. Install dependency
composer install
npm install && npm run build

# 3. Salin file environment
cp .env.example .env
php artisan key:generate

# 4. Sesuaikan koneksi database di .env, lalu migrasi + seed
php artisan migrate --seed

# 5. Buat symbolic link storage (untuk logo, foto kamad, foto KTU, dll)
php artisan storage:link

# 6. Jalankan server lokal
php artisan serve
```

## Konfigurasi Environment

Variabel `.env` penting yang perlu disesuaikan:

```env
APP_NAME=PRESMA
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jma_presma
DB_USERNAME=
DB_PASSWORD=

FILESYSTEM_DISK=public
```

## Akun Default (Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun berikut akan tersedia (password default: `penmad123`):

| Role | Username | Keterangan |
|---|---|---|
| Administrator | `superadmin` | Akses penuh ke seluruh sistem |
| Asesor | `asesor1`, `asesor2` | Akun penilai per wilayah pengawas |

> ⚠️ **Wajib diganti** setelah deployment ke lingkungan produksi.

## Struktur Peran (Role)

- **Administrator** — Kelola seluruh data master, akun pengguna, rubrik penilaian, dan periode aktif.
- **Madrasah** — Mengelola profil madrasah sendiri dan mengajukan data prestasi.
- **Asesor / Pengawas** — Menilai dan memverifikasi prestasi madrasah pada wilayah kerjanya.

## Struktur Proyek

```
app/
├── Helpers/
│   ├── ActivityLogger.php
│   └── ImageHelper.php
├── Http/Controllers/
│   ├── MadrasahController.php
│   ├── UserManagementController.php
│   └── ActivityController.php
├── Models/
│   ├── Madrasah.php
│   ├── User.php
│   ├── Role.php
│   ├── WilayahPengawas.php
│   └── PeriodeAktif.php
database/
└── seeders/
    ├── DatabaseSeeder.php
    ├── MadrasahSeeder.php
    ├── RubrikPenilaianSeeder.php
    └── PeriodeAktifSeeder.php
resources/views/
├── madrasah/
├── userManagement/
└── activity/
```

## Deployment

Aplikasi ini di-deploy pada **VPS KVM (Ubuntu 24.04)** dengan stack **Nginx + PHP-FPM + MariaDB + Certbot + Supervisor**. Panduan langkah demi langkah tersedia di `docs/modul-vps-presma.md` (arsitektur, konfigurasi SSH, Nginx, PHP-FPM, MariaDB, SSL, dan queue worker via Supervisor).

## Lisensi

Proyek internal — Kantor Wilayah Kementerian Agama Provinsi DKI Jakarta, Bidang Pendidikan Madrasah. Tidak untuk didistribusikan tanpa izin.