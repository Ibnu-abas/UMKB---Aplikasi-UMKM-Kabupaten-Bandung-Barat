# 🏪 UMKB - Aplikasi UMKM Kabupaten Bandung Barat
> **Persembahan untuk mengembangkan UMKM Bandung Barat**  
> *Platform Promosi, Pemetaan, dan Katalog Digital Pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) Kabupaten Bandung Barat.*

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-blue.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange.svg)](https://mysql.com)
[![Frontend](https://img.shields.io/badge/UI-Bootstrap%205%20%26%20Boxicons-purple.svg)](https://getbootstrap.com)
[![Author](https://img.shields.io/badge/Pengembang-Ibnu--abas-success.svg)](#)
[![Source](https://img.shields.io/badge/Basis-projectUnjuk-informational.svg)](https://github.com/Alvian14/projectUnjuk)

---

## 📌 Tentang Aplikasi

**UMKB** (Aplikasi UMKM Kabupaten Bandung Barat) adalah platform direktori, e-katalog, dan sistem informasi terpadu yang dirancang untuk mendukung akselerasi digital dan memajukan para pelaku usaha mikro, kecil, dan menengah di **16 kecamatan** wilayah Kabupaten Bandung Barat.

Aplikasi ini dikembangkan oleh **Ibnu-abas** dengan basis pengembangan dari proyek open source **[projectUnjuk](https://github.com/Alvian14/projectUnjuk)** oleh Alvian14.

---

## ✨ Fitur Unggulan

* **🛍️ Katalog Produk Terpadu (4 Kategori)**:
  - **Makanan**: Olahan makanan khas lokal (tahu susu, peuyeum, aneka keripik, dll).
  - **Minuman**: Kopi khas lereng Burangrang, olahan herbal, sirup lokal, dll.
  - **Kerajinan (Kriya)**: Kerajinan bambu, kayu, tenun, dan cinderamata kreatif.
  - **Jasa**: Desain kemasan, reparasi, fotografi produk, dan jasa lokal lainnya.
* **📲 Integrasi Langsung WhatsApp**:
  - Tombol order/tanya via WhatsApp langsung dengan nomor pelaku usaha dalam format internasional (`628...`) serta pesan otomatis yang siap kirim.
* **📍 Direktori 16 Kecamatan Kabupaten Bandung Barat**:
  - Pemetaan dan penghitungan jumlah pelaku usaha terdaftar di setiap kecamatan:
    1. Batujajar
    2. Cihampelas
    3. Cikalongwetan
    4. Cililin
    5. Cipatat
    6. Cipeundeuy
    7. Cipongkor
    8. Cisarua
    9. Gununghalu
    10. Lembang
    11. Ngamprah *(Ibu Kota KBB)*
    12. Padalarang
    13. Parongpong
    14. Rongga
    15. Saguling
    16. Sindangkerta
* **📑 Transparansi Legalitas Usaha**:
  - Informasi Nomor Induk Berusaha (NIB), izin P-IRT, BPOM, dan Sertifikasi Halal (SEHATI).
* **📰 Warta & Pelatihan UMKM**:
  - Informasi pelatihan digital marketing, fasilitasi legalitas, dan agenda pameran/bazaar.
* **🔐 Dashboard Administrator Responsif**:
  - Statistik total UMKM, total produk, manajemen warta kegiatan, dan cetak laporan per kecamatan (Excel/PDF via DataTables).
* **📱 REST API (`Mobile/`)**:
  - Endpoint backend JSON untuk integrasi aplikasi Android/iOS.

---

## ⚙️ Persyaratan Sistem (Requirements)

* **Web Server**: Apache / Nginx / LiteSpeed / aaPanel / cPanel / XAMPP
* **PHP**: Versi 7.4 atau PHP 8.0, 8.1, 8.2+
* **Ekstensi PHP**: `mysqli`, `json`, `mbstring`
* **Database**: MySQL 5.7+ atau MariaDB 10.3+

---

## 🚀 Panduan Instalasi Cepat

### Cara 1: Menggunakan Web Installer Wizard (Sangat Mudah)
1. Clone atau ekstrak repositori ini ke direktori web server Anda (misal `htdocs/umkb` atau `/www/wwwroot/domain-anda/`).
2. Buka browser dan buka file `install.php`:
   ```
   http://localhost/umkb/install.php
   ```
   *(atau `http://domain-anda.com/install.php`)*
3. Masukkan koneksi database Anda:
   - **Host**: `127.0.0.1` (atau `localhost`)
   - **User Database**: Username database MySQL Anda
   - **Password Database**: Password database MySQL Anda
   - **Nama Database**: misal `umkb_bandung_barat`
4. Klik **Test Koneksi**, lalu centang **Impor Sample Data Bandung Barat**, dan klik **Mulai Instalasi Database Sekarang**.
5. Sistem otomatis menginisialisasi skema tabel, akun admin, serta sample data wilayah KBB.

---

### Cara 2: Instalasi Manual
1. Buat database baru di MySQL / phpMyAdmin:
   ```sql
   CREATE DATABASE umkb_bandung_barat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Impor file skema database:
   ```bash
   database.sql
   ```
3. Buat file `koneksi.php` dari template:
   ```bash
   cp koneksi.sample.php koneksi.php
   ```
4. Sesuaikan kredensial di dalam `koneksi.php`:
   ```php
   $server   = "127.0.0.1";
   $user     = "user_db_anda";
   $pass     = "pass_db_anda";
   $database = "umkb_bandung_barat";
   $port     = 3306;
   ```
5. Buka `index.php` melalui peramban.

---

## 🔑 Kredensial Login Default

| Level Akses | Username / Email | Password Default | Halaman Login |
| :--- | :--- | :--- | :--- |
| **Superadmin** | `admin@risafani.id` *(atau `Superadmin`)* | `Superadmin` | `tem-login/tem-login-admin.php` |
| **Akun Demo UMKM** | `budi@gmail.com` | `123456` | Portal Pengguna |

---

## 📁 Struktur Direktori Proyek

```text
├── assets/                  # CSS, JS, Vendor Bootstrap, dan aset visual
├── assets1/                 # Direktori berkas unggahan warta kegiatan
├── daftar-kecamatan/        # Modul rekapitulasi & cetak laporan per kecamatan
├── forms/                   # Template form kontak & validasi
├── Mobile/                  # Endpoint API JSON untuk integrasi mobile
├── public/                  # Berkas foto produk & aset publik
├── tem-login/               # Modul login administrator UMKB
├── user/                    # Handler pengalihan portal publik
├── admin-beranda.php        # Dashboard utama administrator
├── admin-edit-warkopUmi.php # Formulir ubah warta kegiatan UMKM
├── admin-tabel-warkopUmi.php# Tabel rekapitulasi warta kegiatan
├── admin-warkopUmi.php      # Formulir tambah warta kegiatan
├── auto_setup.php           # Skrip mandiri pembuat database 1-klik
├── database.sql             # Skrip SQL skema tabel & data awal KBB
├── index.php                # Halaman beranda utama portal UMKB
├── install.php              # Web Installer Wizard interaktif
├── koneksi.php              # Konfigurasi koneksi MySQL aktif
├── koneksi.sample.php       # Template konfigurasi koneksi
├── user-detail-kegiatan.php # Halaman detail warta & kegiatan UMKM
├── user-detail-produk.php   # Halaman detail produk & pemesanan WA
├── user-jasa.php            # Katalog produk kategori Jasa
├── user-kerajinan.php       # Katalog produk kategori Kerajinan
├── user-layanan.php         # Informasi layanan & konsultasi UMKM
├── user-makanan.php         # Katalog produk kategori Makanan
├── user-minuman.php         # Katalog produk kategori Minuman
├── .gitignore               # Konfigurasi file ignore untuk Git / GitHub
└── README.md                # Dokumentasi resmi proyek
```

---

## 🛡️ Keamanan & Rekomendasi Deployment

1. **Perlindungan Installer**: File `install.php` otomatis terkunci setelah instalasi berhasil (menggunakan `install.lock`). Anda juga dapat menghapus file `install.php` di server produksi melalui tombol yang disediakan.
2. **Koneksi Stabil**: `koneksi.php` telah dilengkapi proteksi reconnection otomatis dan timeout 15 detik guna mencegah galat *`MySQL server has gone away`*.
3. **Pemberian Izin Folder (Permission)**: Pastikan folder `public/img/produk-photo/` dan `assets1/` memiliki hak akses tulis (`chmod 755` atau `777` jika diperlukan) untuk kebutuhan upload foto.

---

## 🤝 Atribusi & Penghargaan

- **Nama Aplikasi**: UMKB (Aplikasi UMKM Kabupaten Bandung Barat)
- **Pengembang**: **Ibnu-abas**
- **Repository Asli**: Diadaptasi dan dikembangkan dari **[projectUnjuk](https://github.com/Alvian14/projectUnjuk)** oleh **Alvian14**.
- **Persembahan**: *Untuk memajukan dan mengembangkan UMKM Kabupaten Bandung Barat.*
