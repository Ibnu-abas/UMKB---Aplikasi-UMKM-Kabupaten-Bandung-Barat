-- ============================================================
-- DATABASE SCHEMA & SAMPLE DATA - UNJUK (WARKOP UMI)
-- Portal UMKM & Warta Koperasi Dinas Koperasi dan Usaha Mikro
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Opsional jika membuat database baru via CLI/Root (Buka komentar jika diperlukan):
-- CREATE DATABASE IF NOT EXISTS `aplikasi_unjuk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `aplikasi_unjuk`;

-- ------------------------------------------------------------
-- 1. Table structure for table `akun`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `akun`;
CREATE TABLE IF NOT EXISTS `akun` (
  `id_akun` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_user` varchar(150) NOT NULL,
  `alamat_user` text DEFAULT NULL,
  `notelp_user` varchar(30) DEFAULT NULL,
  `Level` enum('admin','user') NOT NULL DEFAULT 'user',
  `photo_user` varchar(255) DEFAULT 'assets/img/logoUnjuk.png',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_akun`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Table structure for table `umkm`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `umkm`;
CREATE TABLE IF NOT EXISTS `umkm` (
  `id_umkm` int(11) NOT NULL AUTO_INCREMENT,
  `id_akun` int(11) NOT NULL,
  `nama_umkm` varchar(150) NOT NULL,
  `Jenis_usahaumkm` varchar(100) NOT NULL,
  `Nib_umkm` varchar(50) NOT NULL DEFAULT '',
  `notelp_umkm` varchar(30) NOT NULL,
  `kecamatan_umkm` varchar(100) NOT NULL,
  `alamat_umkm` text NOT NULL,
  `umkm_foto` varchar(255) DEFAULT 'assets/img/logoUnjuk.png',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_umkm`),
  KEY `id_akun` (`id_akun`),
  KEY `kecamatan_umkm` (`kecamatan_umkm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Table structure for table `produk`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `produk`;
CREATE TABLE IF NOT EXISTS `produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `id_umkm` int(11) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `harga_produk` decimal(12,2) NOT NULL DEFAULT 0.00,
  `kategori_produk` varchar(50) NOT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `pirt_produk` varchar(100) DEFAULT '-',
  `bpom_produk` varchar(100) DEFAULT '-',
  `idhalal_produk` varchar(100) DEFAULT '-',
  `gambar_produk1` varchar(255) DEFAULT '',
  `gambar_produk2` varchar(255) DEFAULT '',
  `gambar_produk3` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`),
  KEY `id_umkm` (`id_umkm`),
  KEY `kategori_produk` (`kategori_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Table structure for table `kegiatan`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `kegiatan`;
CREATE TABLE IF NOT EXISTS `kegiatan` (
  `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `tgl` date NOT NULL,
  `jam` time NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Table structure for table `verification`
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `verification`;
CREATE TABLE IF NOT EXISTS `verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `type` varchar(50) DEFAULT 'register',
  `start_millis` bigint(20) DEFAULT 0,
  `end_millis` bigint(20) DEFAULT 0,
  `device` varchar(50) DEFAULT 'Mobile',
  `resend` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA AWAL - UMKB KABUPATEN BANDUNG BARAT
-- ============================================================

-- Akun Administrator & Pelaku Usaha
INSERT INTO `akun` (`id_akun`, `email`, `password`, `nama_user`, `alamat_user`, `notelp_user`, `Level`, `photo_user`) VALUES
(1, 'admin@risafani.id', 'Superadmin', 'Superadmin', 'Kompleks Pemkab Bandung Barat, Ngamprah', '081234567890', 'admin', 'assets/img/logoUnjuk.png'),
(2, 'budi@gmail.com', '123456', 'Budi Santoso', 'Jl. Raya Lembang No. 120, Lembang', '085712345678', 'user', 'assets/img/logoUnjuk.png'),
(3, 'siti@gmail.com', '123456', 'Siti Rahmawati', 'Jl. Kolonel Masturi No. 45, Parongpong', '085898765432', 'user', 'assets/img/logoUnjuk.png'),
(4, 'hendra@gmail.com', '123456', 'Hendra Wijaya', 'Jl. Raya Cikalong No. 15, Cikalongwetan', '081345678901', 'user', 'assets/img/logoUnjuk.png'),
(5, 'asep@gmail.com', '123456', 'Asep Saepudin', 'Jl. Raya Padalarang No. 88, Padalarang', '081298765432', 'user', 'assets/img/logoUnjuk.png');

-- Data UMKM (Kecamatan di Kabupaten Bandung Barat)
INSERT INTO `umkm` (`id_umkm`, `id_akun`, `nama_umkm`, `Jenis_usahaumkm`, `Nib_umkm`, `notelp_umkm`, `kecamatan_umkm`, `alamat_umkm`, `umkm_foto`) VALUES
(1, 2, 'Tahu Susu & Olahan Lembang', 'Makanan', '1234567890123', '085712345678', 'Lembang', 'Jl. Raya Lembang No. 120, Lembang', 'assets/img/logoUnjuk.png'),
(2, 3, 'Kopi Arabika Gunung Burangrang', 'Minuman', '9876543210987', '085898765432', 'Parongpong', 'Jl. Kolonel Masturi No. 45, Parongpong', 'assets/img/logoUnjuk.png'),
(3, 4, 'Kriya Anyaman Bambu Kreatif', 'Kerajinan', '4567890123456', '081345678901', 'Cikalongwetan', 'Jl. Raya Cikalong No. 15, Cikalongwetan', 'assets/img/logoUnjuk.png'),
(4, 5, 'Jasa Desain Grafis & Legalitas KBB', 'Jasa', '7890123456789', '081298765432', 'Padalarang', 'Jl. Raya Padalarang No. 88, Padalarang', 'assets/img/logoUnjuk.png');

-- Data Produk
INSERT INTO `produk` (`id_produk`, `id_umkm`, `nama_produk`, `harga_produk`, `kategori_produk`, `deskripsi_produk`, `pirt_produk`, `bpom_produk`, `idhalal_produk`, `gambar_produk1`, `gambar_produk2`, `gambar_produk3`) VALUES
(1, 1, 'Tahu Susu Lembut Khas Lembang', 25000.00, 'Makanan', 'Tahu susu segar khas Lembang dengan tekstur super lembut dan rasa gurih alami tanpa bahan pengawet.', 'P-IRT 2063217010202', 'BPOM RI MD 234567', 'ID321100012345', 'assets/img/hero-img.png', '', ''),
(2, 2, 'Kopi Arabika Single Origin Burangrang 250gr', 45000.00, 'Minuman', 'Kopi arabika spesialti hasil panen petani binaan lereng Gunung Burangrang, cita rasa fruity cerah dan aroma memikat.', 'P-IRT 2103217020303', '-', 'ID321100054321', 'assets/img/hero-img.png', '', ''),
(3, 3, 'Keranjang Anyaman Bambu Multifungsi', 65000.00, 'Kerajinan', 'Kerajinan anyaman bambu tangan khas pengrajin kreatif Bandung Barat, ramah lingkungan, kuat dan estetik.', '-', '-', '-', 'assets/img/hero-img.png', '', ''),
(4, 4, 'Jasa Desain Kemasan & Branding UMKM KBB', 50000.00, 'Jasa', 'Layanan bantuan pembuatan logo, desain stiker kemasan, dan profil usaha digital untuk meningkatkan daya saing UMKM.', '-', '-', '-', 'assets/img/hero-img.png', '', '');

-- Data Kegiatan / Warta UMKM Bandung Barat
INSERT INTO `kegiatan` (`id_kegiatan`, `judul`, `tgl`, `jam`, `deskripsi`, `foto`) VALUES
(1, 'Sosialisasi Sertifikasi Halal Gratis (SEHATI) Pelaku Usaha KBB', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'Dinas Koperasi dan Usaha Kecil Menengah Kabupaten Bandung Barat menyelenggarakan pendampingan sertifikasi halal gratis untuk para pelaku UMKM makanan & minuman se-Kabupaten Bandung Barat. Daftarkan usaha Anda melalui portal UMKB.', 'assets/img/logoUnjuk.png'),
(2, 'Pelatihan Pemasaran Digital & E-Commerce UMKM Bandung Barat 2026', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '08:30:00', 'Tingkatkan omzet dan jangkauan pemasaran produk unggulan KBB dengan workshop intensif optimasi media sosial, foto katalog produk, dan integrasi marketplace nasional.', 'assets/img/logoUnjuk.png');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
