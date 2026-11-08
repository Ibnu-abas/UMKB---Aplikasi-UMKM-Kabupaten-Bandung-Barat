<?php
/**
 * ==============================================================================
 * UMKB - WEB INSTALLATION WIZARD
 * Aplikasi UMKM Kabupaten Bandung Barat
 * Pengembang: Ibnu-abas
 * Dikembangkan dari: https://github.com/Alvian14/projectUnjuk
 * Persembahan untuk mengembangkan UMKM Bandung Barat
 * ==============================================================================
 * File: install.php
 * Digunakan untuk setup database awal dan konfigurasi koneksi secara otomatis.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$lockFile    = __DIR__ . '/install.lock';
$koneksiFile = __DIR__ . '/koneksi.php';
$sqlFile     = __DIR__ . '/database.sql';

// Fitur self-destruct atau hapus installer demi keamanan
if (isset($_GET['action']) && $_GET['action'] === 'delete_installer') {
    if (file_exists(__FILE__)) {
        @unlink(__FILE__);
        if (file_exists($lockFile)) @unlink($lockFile);
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>Installer Dihapus</title><link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'><style>body{font-family:system-ui,sans-serif;background:#0b1329;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;text-align:center;}.card{background:#111e38;padding:40px;border-radius:16px;max-width:480px;border:1px solid #1e3a5f;box-shadow:0 15px 30px rgba(0,0,0,0.5);}a{display:inline-block;margin-top:20px;padding:12px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:10px;font-weight:700;}</style></head><body><div class='card'><i class='fa fa-shield-halved fa-3x' style='color:#10b981;margin-bottom:15px;'></i><h2>Installer Berhasil Dihapus!</h2><p style='color:#94a3b8;margin-top:10px;'>File <code>install.php</code> telah dibersihkan secara permanen dari server demi keamanan.</p><a href='index.php'>Buka Halaman Utama</a></div></body></html>";
        exit;
    }
}

// Buka kunci paksa jika tombol unlock ditekan
if (isset($_GET['action']) && $_GET['action'] === 'unlock') {
    if (file_exists($lockFile)) @unlink($lockFile);
    header("Location: install.php");
    exit;
}

$isLocked = file_exists($lockFile);

// Baca konfigurasi saat ini jika ada
$currentHost = '127.0.0.1';
$currentUser = 'root';
$currentPass = '';
$currentDb   = 'aplikasi_unjuk';
$currentPort = 3306;

if (file_exists($koneksiFile)) {
    $content = file_get_contents($koneksiFile);
    if (preg_match('/\$server\s*=\s*["\']([^"\']+)["\']/', $content, $m)) $currentHost = $m[1];
    if (preg_match('/\$user\s*=\s*["\']([^"\']+)["\']/', $content, $m)) $currentUser = $m[1];
    if (preg_match('/\$pass\s*=\s*["\']([^"\']*)["\']/', $content, $m)) $currentPass = $m[1];
    if (preg_match('/\$database\s*=\s*["\']([^"\']+)["\']/', $content, $m)) $currentDb = $m[1];
    if (preg_match('/\$port\s*=\s*(\d+)/', $content, $m)) $currentPort = (int)$m[1];
}

// Check Requirements
$phpVersionOk  = version_compare(PHP_VERSION, '7.4.0', '>=');
$mysqliOk      = extension_loaded('mysqli');
$rootWritable  = is_writable(__DIR__);
$allReqOk      = $phpVersionOk && $mysqliOk;

$errorMsg   = '';
$successMsg = '';
$installed  = false;
$logs       = [];

// AJAX Test Connection
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'test_conn') {
    header('Content-Type: application/json');
    $h = trim($_POST['db_host'] ?? '127.0.0.1');
    $u = trim($_POST['db_user'] ?? 'root');
    $p = $_POST['db_pass'] ?? '';
    $port = (int)($_POST['db_port'] ?? 3306);
    
    try {
        $link = @mysqli_init();
        @mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        if (@mysqli_real_connect($link, $h, $u, $p, '', $port)) {
            $ver = mysqli_get_server_info($link);
            mysqli_close($link);
            echo json_encode(['success' => true, 'message' => "Koneksi ke MySQL Server BERHASIL! (Versi: $ver)"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . mysqli_connect_error()]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Proses Instalasi Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_install']) && !$isLocked) {
    $dbHost        = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort        = (int)($_POST['db_port'] ?? 3306);
    $dbUser        = trim($_POST['db_user'] ?? 'root');
    $dbPass        = $_POST['db_pass'] ?? '';
    $dbName        = trim($_POST['db_name'] ?? 'aplikasi_unjuk');
    $autoCreateDb  = isset($_POST['auto_create_db']);
    $includeSample = isset($_POST['include_sample']);
    
    $adminName     = trim($_POST['admin_name'] ?? 'Superadmin');
    $adminEmail    = trim($_POST['admin_email'] ?? 'admin@unjuk.com');
    $adminPass     = trim($_POST['admin_pass'] ?? 'admin');
    $adminPhone    = trim($_POST['admin_phone'] ?? '081234567890');

    if (empty($dbHost) || empty($dbUser) || empty($dbName)) {
        $errorMsg = 'Host, User, dan Nama Database wajib diisi!';
    } elseif (empty($adminEmail) || empty($adminPass)) {
        $errorMsg = 'Email dan Password Admin wajib diisi!';
    } else {
        try {
            // 1. Hubungkan ke MySQL
            $mysqli = @mysqli_init();
            if (!$mysqli) throw new Exception("Gagal inisialisasi mysqli client.");
            @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 15);
            
            if (!@mysqli_real_connect($mysqli, $dbHost, $dbUser, $dbPass, '', $dbPort)) {
                throw new Exception("Gagal terhubung ke MySQL Server ($dbHost:$dbPort): " . mysqli_connect_error());
            }
            $logs[] = "Berhasil terhubung ke MySQL Server ($dbHost:$dbPort).";

            // 2. Buat database jika dicentang (abaikan jika tidak ada permission create db)
            if ($autoCreateDb) {
                $createDbSql = "CREATE DATABASE IF NOT EXISTS `" . $mysqli->real_escape_string($dbName) . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                @$mysqli->query($createDbSql);
            }

            // 3. Pilih database
            if (!@$mysqli->select_db($dbName)) {
                throw new Exception("Database `$dbName` tidak ditemukan atau gagal dipilih: " . $mysqli->error . ". Pastikan nama database sudah dibuat di panel hosting/server.");
            }
            $mysqli->set_charset("utf8mb4");
            $logs[] = "Database `$dbName` berhasil dipilih.";

            // 4. Buat Tabel-tabel
            $tables = [
                'akun' => "CREATE TABLE IF NOT EXISTS `akun` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                'umkm' => "CREATE TABLE IF NOT EXISTS `umkm` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                'produk' => "CREATE TABLE IF NOT EXISTS `produk` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                'kegiatan' => "CREATE TABLE IF NOT EXISTS `kegiatan` (
                    `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT,
                    `judul` varchar(255) NOT NULL,
                    `tgl` date NOT NULL,
                    `jam` time NOT NULL,
                    `deskripsi` text NOT NULL,
                    `foto` varchar(255) DEFAULT '',
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id_kegiatan`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                'verification' => "CREATE TABLE IF NOT EXISTS `verification` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
            ];

            foreach ($tables as $tName => $sql) {
                if (!$mysqli->query($sql)) {
                    throw new Exception("Gagal membuat tabel `$tName`: " . $mysqli->error);
                }
                $logs[] = "Tabel <strong>`$tName`</strong> berhasil diverifikasi/dibuat.";
            }

            // 5. Masukkan atau perbarui akun Administrator
            $chkAdmin = $mysqli->query("SELECT id_akun FROM akun WHERE email = '" . $mysqli->real_escape_string($adminEmail) . "'");
            if ($chkAdmin && $chkAdmin->num_rows > 0) {
                $upAdmin = "UPDATE akun SET password = '" . $mysqli->real_escape_string($adminPass) . "', nama_user = '" . $mysqli->real_escape_string($adminName) . "', notelp_user = '" . $mysqli->real_escape_string($adminPhone) . "', Level = 'admin' WHERE email = '" . $mysqli->real_escape_string($adminEmail) . "'";
                $mysqli->query($upAdmin);
                $logs[] = "Akun Administrator (<strong>$adminEmail</strong>) berhasil diperbarui.";
            } else {
                $insAdmin = "INSERT INTO akun (email, password, nama_user, notelp_user, Level, photo_user) VALUES ('" . $mysqli->real_escape_string($adminEmail) . "', '" . $mysqli->real_escape_string($adminPass) . "', '" . $mysqli->real_escape_string($adminName) . "', '" . $mysqli->real_escape_string($adminPhone) . "', 'admin', 'assets/img/logoUnjuk.png')";
                $mysqli->query($insAdmin);
                $logs[] = "Akun Administrator (<strong>$adminEmail</strong>) berhasil dibuat.";
            }

            // Juga masukkan akun admin cadangan jika email utama berbeda
            if ($adminEmail !== 'admin@risafani.id') {
                $mysqli->query("INSERT IGNORE INTO akun (email, password, nama_user, notelp_user, Level, photo_user) VALUES ('admin@risafani.id', 'Superadmin', 'Superadmin', '081234567890', 'admin', 'assets/img/logoUnjuk.png')");
            }

            // 6. Masukkan Sample Data jika dicentang
            if ($includeSample) {
                $mysqli->query("INSERT IGNORE INTO akun (id_akun, email, password, nama_user, alamat_user, notelp_user, Level) VALUES 
                    (4, 'budi@gmail.com', '123456', 'Budi Santoso', 'Jl. Raya Lembang No. 120, Lembang', '085712345678', 'user'),
                    (5, 'siti@gmail.com', '123456', 'Siti Rahmawati', 'Jl. Kolonel Masturi No. 45, Parongpong', '085898765432', 'user'),
                    (6, 'hendra@gmail.com', '123456', 'Hendra Wijaya', 'Jl. Raya Cikalong No. 15, Cikalongwetan', '081345678901', 'user'),
                    (7, 'asep@gmail.com', '123456', 'Asep Saepudin', 'Jl. Raya Padalarang No. 88, Padalarang', '081298765432', 'user')");
                
                $mysqli->query("INSERT IGNORE INTO umkm (id_umkm, id_akun, nama_umkm, Jenis_usahaumkm, Nib_umkm, notelp_umkm, kecamatan_umkm, alamat_umkm, umkm_foto) VALUES
                    (1, 4, 'Tahu Susu & Olahan Lembang', 'Makanan', '1234567890123', '085712345678', 'Lembang', 'Jl. Raya Lembang No. 120, Lembang', 'assets/img/logoUnjuk.png'),
                    (2, 5, 'Kopi Arabika Gunung Burangrang', 'Minuman', '9876543210987', '085898765432', 'Parongpong', 'Jl. Kolonel Masturi No. 45, Parongpong', 'assets/img/logoUnjuk.png'),
                    (3, 6, 'Kriya Anyaman Bambu Kreatif', 'Kerajinan', '4567890123456', '081345678901', 'Cikalongwetan', 'Jl. Raya Cikalong No. 15, Cikalongwetan', 'assets/img/logoUnjuk.png'),
                    (4, 7, 'Jasa Desain Grafis & Legalitas KBB', 'Jasa', '7890123456789', '081298765432', 'Padalarang', 'Jl. Raya Padalarang No. 88, Padalarang', 'assets/img/logoUnjuk.png')");

                $mysqli->query("INSERT IGNORE INTO produk (id_produk, id_umkm, nama_produk, harga_produk, kategori_produk, deskripsi_produk, pirt_produk, bpom_produk, idhalal_produk, gambar_produk1) VALUES
                    (1, 1, 'Tahu Susu Lembut Khas Lembang', 25000.00, 'Makanan', 'Tahu susu segar khas Lembang dengan tekstur super lembut dan rasa gurih alami tanpa pengawet.', 'P-IRT 2063217010202', 'BPOM RI MD 234567', 'ID321100012345', 'assets/img/hero-img.png'),
                    (2, 2, 'Kopi Arabika Single Origin Burangrang 250gr', 45000.00, 'Minuman', 'Kopi arabika spesialti hasil panen lereng Gunung Burangrang.', 'P-IRT 2103217020303', '-', 'ID321100054321', 'assets/img/hero-img.png'),
                    (3, 3, 'Keranjang Anyaman Bambu Multifungsi', 65000.00, 'Kerajinan', 'Kerajinan anyaman bambu tangan khas pengrajin kreatif Bandung Barat, ramah lingkungan dan estetik.', '-', '-', '-', 'assets/img/hero-img.png'),
                    (4, 4, 'Jasa Desain Kemasan & Branding UMKM KBB', 50000.00, 'Jasa', 'Layanan bantuan pembuatan desain kemasan dan branding produk UMKM.', '-', '-', '-', 'assets/img/hero-img.png')");

                $mysqli->query("INSERT IGNORE INTO kegiatan (id_kegiatan, judul, tgl, jam, deskripsi, foto) VALUES
                    (1, 'Sosialisasi Sertifikasi Halal Gratis (SEHATI) Pelaku Usaha KBB', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'Dinas Koperasi dan Usaha Kecil Menengah Kabupaten Bandung Barat menyelenggarakan pendampingan sertifikasi halal gratis untuk para pelaku UMKM.', 'assets/img/logoUnjuk.png'),
                    (2, 'Pelatihan Pemasaran Digital & E-Commerce UMKM Bandung Barat 2026', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '08:30:00', 'Tingkatkan omzet dan jangkauan pemasaran produk unggulan KBB dengan workshop intensif optimasi media sosial dan e-commerce.', 'assets/img/logoUnjuk.png')");
                
                $logs[] = "Sample data UMKM Bandung Barat, Produk, dan Kegiatan Warkop UMI berhasil diimpor.";
            }

            // 7. Simpan konfigurasi ke koneksi.php dengan reconnection handling & timeout
            $newKoneksiContent = "<?php\n" .
                "/**\n" .
                " * File Koneksi Database Otomatis - UMKB (Kabupaten Bandung Barat)\n" .
                " * Dibuat otomatis oleh Installer pada: " . date('Y-m-d H:i:s') . "\n" .
                " */\n\n" .
                "\$server   = \"" . addslashes($dbHost) . "\";\n" .
                "\$user     = \"" . addslashes($dbUser) . "\";\n" .
                "\$pass     = \"" . addslashes($dbPass) . "\";\n" .
                "\$database = \"" . addslashes($dbName) . "\";\n" .
                "\$port     = " . $dbPort . ";\n\n" .
                "// Jika koneksi sudah aktif dan masih hidup, gunakan kembali (mencegah MySQL server has gone away)\n" .
                "if (isset(\$conn) && (\$conn instanceof mysqli) && @mysqli_ping(\$conn)) {\n" .
                "    return;\n" .
                "}\n\n" .
                "// Inisialisasi koneksi dengan setting reconnect & timeout\n" .
                "\$conn = @mysqli_init();\n" .
                "if (\$conn) {\n" .
                "    @mysqli_options(\$conn, MYSQLI_OPT_CONNECT_TIMEOUT, 15);\n" .
                "    @mysqli_real_connect(\$conn, \$server, \$user, \$pass, \$database, \$port);\n" .
                "}\n\n" .
                "if (!\$conn || mysqli_connect_errno()) {\n" .
                "    \$connErr = mysqli_connect_error() ?: \"Tidak dapat tersambung ke MySQL server (\$server:\$port)\";\n" .
                "    if (php_sapi_name() !== 'cli' && !headers_sent()) {\n" .
                "        \$curFile = basename(\$_SERVER['PHP_SELF'] ?? '');\n" .
                "        if (\$curFile !== 'install.php') {\n" .
                "            echo '<div style=\"font-family:system-ui,sans-serif;padding:30px;background:#111e38;border:1px solid #1e3a5f;color:#f8fafc;border-radius:12px;max-width:540px;margin:60px auto;text-align:center;box-shadow:0 15px 30px rgba(0,0,0,0.5);\">';\n" .
                "            echo '<h3 style=\"margin-top:0;color:#f87171;\">Peringatan: Koneksi Database Gagal</h3>';\n" .
                "            echo '<p style=\"color:#94a3b8;font-size:14px;\">' . htmlspecialchars(\$connErr) . '</p>';\n" .
                "            echo '<p style=\"margin-top:20px;\"><a href=\"install.php\" style=\"background:#2563eb;color:#fff;padding:10px 20px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:14px;\">Buka Installer (install.php)</a></p>';\n" .
                "            echo '</div>';\n" .
                "            exit;\n" .
                "        }\n" .
                "    }\n" .
                "    die(\"Connection Failed: \" . \$connErr);\n" .
                "} else {\n" .
                "    @mysqli_set_charset(\$conn, 'utf8mb4');\n" .
                "}\n" .
                "?>\n";

            if (@file_put_contents($koneksiFile, $newKoneksiContent) === false) {
                throw new Exception("Database berhasil dibuat, namun gagal menulis ke file `koneksi.php`. Pastikan izin write folder sudah aktif.");
            }
            $logs[] = "File konfigurasi <strong>`koneksi.php`</strong> berhasil disimpan.";

            // 8. Tulis install.lock
            @file_put_contents($lockFile, "LOCKED ON: " . date('Y-m-d H:i:s'));

            $installed = true;
            $successMsg = "Selamat! Database & Aplikasi UMKB (Kabupaten Bandung Barat) berhasil diinstal dan siap digunakan.";

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Installer - UMKB Kabupaten Bandung Barat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-dark: #0b1329;
            --card-bg: #111e38;
            --border-color: #1e3a5f;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .installer-container {
            width: 100%;
            max-width: 680px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
        .header .badge {
            display: inline-block;
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 22px;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .header p {
            color: var(--text-muted);
            font-size: 13px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #60a5fa;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-box {
            background: #080f1e;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #cbd5e1;
        }
        .form-control {
            width: 100%;
            background: #111e38;
            border: 1px solid var(--border-color);
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            transition: border 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary);
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #cbd5e1;
            cursor: pointer;
            user-select: none;
            margin-top: 6px;
        }
        .checkbox-group input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        .btn-success {
            background: var(--success);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border-color);
            color: #cbd5e1;
            padding: 8px 14px;
            font-size: 12px;
            width: auto;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; }
        .alert-warning { background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; }
        .logs-box {
            background: #060a14;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            margin-top: 15px;
            font-size: 12px;
            max-height: 160px;
            overflow-y: auto;
        }
        .logs-box div {
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="installer-container">
    <div class="header">
        <span class="badge"><i class="fa fa-screwdriver-wrench"></i> Setup & Installation Wizard</span>
        <h1>UMKB</h1>
        <p>Aplikasi UMKM Kabupaten Bandung Barat</p>
        <div style="font-size:12px; color:#94a3b8; margin-top:5px;">Pengembang: <strong>Ibnu-abas</strong> | Basis: <a href="https://github.com/Alvian14/projectUnjuk" target="_blank" style="color:#60a5fa;">projectUnjuk</a></div>
        <div style="font-size:12px; color:#38bdf8; margin-top:3px;"><em>Persembahan untuk mengembangkan UMKM Bandung Barat</em></div>
    </div>

    <?php if ($isLocked && !$installed): ?>
        <div class="alert alert-warning">
            <i class="fa fa-lock fa-lg"></i>
            <div>
                <strong>Installer Terkunci!</strong><br>
                Sistem mendeteksi file <code>install.lock</code>. Aplikasi sudah pernah diinstal sebelumnya.
                <div style="margin-top:10px;">
                    <a href="install.php?action=unlock" class="btn btn-secondary" style="display:inline-flex;"><i class="fa fa-unlock"></i> Buka Kunci untuk Instal Ulang</a>
                    <a href="install.php?action=delete_installer" class="btn btn-secondary" style="display:inline-flex; color:#f87171;" onclick="return confirm('Hapus file install.php permanen?');"><i class="fa fa-trash"></i> Hapus Installer</a>
                </div>
            </div>
        </div>

        <div class="action-links">
            <a href="index.php" class="btn btn-primary"><i class="fa fa-house"></i> Buka Beranda</a>
            <a href="admin-beranda.php" class="btn btn-success"><i class="fa fa-gauge"></i> Admin Panel</a>
        </div>

    <?php elseif ($installed): ?>
        <div class="alert alert-success">
            <i class="fa fa-circle-check fa-lg"></i>
            <div>
                <strong>Instalasi Berhasil!</strong><br>
                <?= htmlspecialchars($successMsg) ?>
            </div>
        </div>

        <?php if (!empty($logs)): ?>
            <div class="logs-box">
                <?php foreach ($logs as $l): ?>
                    <div><i class="fa fa-check" style="color:#10b981;"></i> <?= $l ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="section-box" style="margin-top:20px;">
            <div class="section-title"><i class="fa fa-key"></i> Kredensial Administrator Anda</div>
            <div style="font-size:13px; color:#cbd5e1; line-height:1.8;">
                Nama: <strong><?= htmlspecialchars($adminName) ?></strong><br>
                Email: <code style="color:#60a5fa;background:#111e38;padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($adminEmail) ?></code><br>
                Password: <code style="color:#34d399;background:#111e38;padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($adminPass) ?></code>
            </div>
        </div>

        <div class="action-links">
            <a href="index.php" class="btn btn-primary"><i class="fa fa-house"></i> Buka Website Utama</a>
            <a href="admin-beranda.php" class="btn btn-success"><i class="fa fa-gauge"></i> Masuk Panel Admin</a>
        </div>

        <div style="text-align:center; margin-top:20px;">
            <a href="install.php?action=delete_installer" style="color:#f87171; font-size:12px; text-decoration:none;" onclick="return confirm('Hapus file install.php sekarang?');">
                <i class="fa fa-shield-halved"></i> Hapus file installer sekarang demi keamanan
            </a>
        </div>

    <?php else: ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                <i class="fa fa-circle-exclamation fa-lg"></i>
                <div>
                    <strong>Terjadi Kesalahan:</strong><br>
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$allReqOk): ?>
            <div class="alert alert-danger">
                <i class="fa fa-triangle-exclamation fa-lg"></i>
                <div>
                    <strong>Kebutuhan Sistem Belum Terpenuhi:</strong><br>
                    <?php if (!$phpVersionOk): ?>- PHP Minimal versi 7.4 (Versi saat ini: <?= PHP_VERSION ?>)<br><?php endif; ?>
                    <?php if (!$mysqliOk): ?>- Ekstensi PHP <code>mysqli</code> belum aktif.<br><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" id="installForm">
            <!-- Bagian 1: Database -->
            <div class="section-box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <div class="section-title" style="margin-bottom:0;"><i class="fa fa-database"></i> 1. Koneksi Database MySQL</div>
                    <button type="button" id="btnTestConn" class="btn btn-secondary"><i class="fa fa-network-wired"></i> Test Koneksi</button>
                </div>
                <div id="testConnResult" style="font-size:12px; margin-bottom:12px; display:none;"></div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Host / IP MySQL:</label>
                        <input type="text" name="db_host" id="db_host" class="form-control" value="<?= htmlspecialchars($currentHost) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Port MySQL:</label>
                        <input type="number" name="db_port" id="db_port" class="form-control" value="<?= htmlspecialchars($currentPort) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Username MySQL:</label>
                        <input type="text" name="db_user" id="db_user" class="form-control" value="<?= htmlspecialchars($currentUser) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password MySQL:</label>
                        <input type="password" name="db_pass" id="db_pass" class="form-control" value="<?= htmlspecialchars($currentPass) ?>" placeholder="(kosongkan jika tanpa password)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Database:</label>
                    <input type="text" name="db_name" id="db_name" class="form-control" value="<?= htmlspecialchars($currentDb) ?>" required>
                </div>

                <label class="checkbox-group">
                    <input type="checkbox" name="auto_create_db" value="1" checked>
                    <span>Otomatis buat database jika belum ada di server</span>
                </label>
            </div>

            <!-- Bagian 2: Administrator -->
            <div class="section-box">
                <div class="section-title"><i class="fa fa-user-shield"></i> 2. Akun Administrator</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Lengkap Admin:</label>
                        <input type="text" name="admin_name" class="form-control" value="Superadmin" required>
                    </div>
                    <div class="form-group">
                        <label>No. HP / WhatsApp:</label>
                        <input type="text" name="admin_phone" class="form-control" value="081234567890">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Admin:</label>
                        <input type="email" name="admin_email" class="form-control" value="admin@risafani.id" required>
                    </div>
                    <div class="form-group">
                        <label>Password Admin:</label>
                        <input type="text" name="admin_pass" class="form-control" value="Superadmin" required>
                    </div>
                </div>

                <label class="checkbox-group">
                    <input type="checkbox" name="include_sample" value="1" checked>
                    <span>Sertakan Contoh Data UMKM, Produk, dan Warta Koperasi</span>
                </label>
            </div>

            <button type="submit" name="do_install" class="btn btn-primary" style="padding:15px; font-size:15px;">
                <i class="fa fa-bolt"></i> Mulai Instalasi Database Sekarang
            </button>
        </form>

    <?php endif; ?>
</div>

<script>
document.getElementById('btnTestConn')?.addEventListener('click', function() {
    var btn = this;
    var resDiv = document.getElementById('testConnResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menghubungkan...';
    resDiv.style.display = 'none';

    var fd = new FormData();
    fd.append('ajax_action', 'test_conn');
    fd.append('db_host', document.getElementById('db_host').value);
    fd.append('db_port', document.getElementById('db_port').value);
    fd.append('db_user', document.getElementById('db_user').value);
    fd.append('db_pass', document.getElementById('db_pass').value);

    fetch('install.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-network-wired"></i> Test Koneksi';
        resDiv.style.display = 'block';
        if (data.success) {
            resDiv.style.color = '#34d399';
            resDiv.innerHTML = '<i class="fa fa-circle-check"></i> ' + data.message;
        } else {
            resDiv.style.color = '#f87171';
            resDiv.innerHTML = '<i class="fa fa-circle-xmark"></i> ' + data.message;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-network-wired"></i> Test Koneksi';
        resDiv.style.display = 'block';
        resDiv.style.color = '#f87171';
        resDiv.innerHTML = '<i class="fa fa-triangle-exclamation"></i> Gagal menghubungi server: ' + err;
    });
});
</script>

</body>
</html>
