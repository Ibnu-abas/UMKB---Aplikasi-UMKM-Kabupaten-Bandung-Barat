<?php
/**
 * AUTO SETUP & DATABASE POPULATOR
 * UNjuk & Warkop UMI - Dinas Koperasi dan Usaha Mikro
 * File: auto_setup.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$koneksiFile = __DIR__ . '/koneksi.php';
$sqlFile     = __DIR__ . '/database.sql';

// Baca konfigurasi dari koneksi.php jika ada
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'aplikasi_unjuk';
$dbPort = 3306;

if (file_exists($koneksiFile)) {
    $cfg = file_get_contents($koneksiFile);
    if (preg_match('/\$server\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $dbHost = $m[1];
    if (preg_match('/\$user\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $dbUser = $m[1];
    if (preg_match('/\$pass\s*=\s*["\']([^"\']*)["\']/', $cfg, $m)) $dbPass = $m[1];
    if (preg_match('/\$database\s*=\s*["\']([^"\']+)["\']/', $cfg, $m)) $dbName = $m[1];
    if (preg_match('/\$port\s*=\s*(\d+)/', $cfg, $m)) $dbPort = (int)$m[1];
}

// Cek apakah ada input POST untuk update koneksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_credentials'])) {
    $dbHost = trim($_POST['db_host'] ?? $dbHost);
    $dbUser = trim($_POST['db_user'] ?? $dbUser);
    $dbPass = $_POST['db_pass'] ?? $dbPass;
    $dbName = trim($_POST['db_name'] ?? $dbName);
    $dbPort = (int)($_POST['db_port'] ?? $dbPort);
}

// Coba koneksi ke database
$connError = '';
$connSuccess = false;
$existingTables = [];
$mysqli = null;

try {
    $mysqli = @mysqli_init();
    if ($mysqli) {
        @mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        if (@mysqli_real_connect($mysqli, $dbHost, $dbUser, $dbPass, '', $dbPort)) {
            // Cek apakah database ada
            $chkDb = @mysqli_query($mysqli, "SHOW DATABASES LIKE '" . mysqli_real_escape_string($mysqli, $dbName) . "'");
            if ($chkDb && mysqli_num_rows($chkDb) > 0) {
                mysqli_select_db($mysqli, $dbName);
                $connSuccess = true;
                
                // Cek daftar tabel yang sudah ada
                $tRes = mysqli_query($mysqli, "SHOW TABLES");
                if ($tRes) {
                    while ($r = mysqli_fetch_array($tRes)) {
                        $existingTables[] = $r[0];
                    }
                }
            } else {
                $connError = "MySQL terhubung, namun database '$dbName' belum ada di MySQL server.";
            }
        } else {
            $connError = mysqli_connect_error() ?: "Gagal terhubung ke MySQL ($dbHost:$dbPort). Pastikan Username & Password benar.";
        }
    }
} catch (Exception $ex) {
    $connError = $ex->getMessage();
}

$actionMessage = '';
$actionStatus  = '';
$logs = [];

// Proses Eksekusi Populate / Isi Database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['do_populate']) || isset($_POST['update_and_populate']))) {
    $targetHost = trim($_POST['db_host'] ?? $dbHost);
    $targetUser = trim($_POST['db_user'] ?? $dbUser);
    $targetPass = $_POST['db_pass'] ?? $dbPass;
    $targetName = trim($_POST['db_name'] ?? $dbName);
    $targetPort = (int)($_POST['db_port'] ?? $dbPort);

    try {
        $m = @mysqli_init();
        @mysqli_options($m, MYSQLI_OPT_CONNECT_TIMEOUT, 15);
        if (!@mysqli_real_connect($m, $targetHost, $targetUser, $targetPass, '', $targetPort)) {
            throw new Exception("Koneksi gagal: " . mysqli_connect_error());
        }

        // Buat database jika belum ada
        $m->query("CREATE DATABASE IF NOT EXISTS `" . $m->real_escape_string($targetName) . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        if (!$m->select_db($targetName)) {
            throw new Exception("Tidak dapat memilih database `$targetName`: " . $m->error);
        }
        $m->set_charset("utf8mb4");

        // 1. Buat 5 Tabel Utama
        $schema = [
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            'kegiatan' => "CREATE TABLE IF NOT EXISTS `kegiatan` (
                `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT,
                `judul` varchar(255) NOT NULL,
                `tgl` date NOT NULL,
                `jam` time NOT NULL,
                `deskripsi` text NOT NULL,
                `foto` varchar(255) DEFAULT '',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_kegiatan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($schema as $tableName => $tableSql) {
            if (!$m->query($tableSql)) {
                throw new Exception("Gagal membuat tabel `$tableName`: " . $m->error);
            }
            $logs[] = "Tabel <strong>`$tableName`</strong> berhasil dibuat / diverifikasi.";
        }

        // 2. Masukkan Admin Default (admin@risafani.id / Superadmin)
        $m->query("INSERT IGNORE INTO `akun` (`id_akun`, `email`, `password`, `nama_user`, `alamat_user`, `notelp_user`, `Level`, `photo_user`) VALUES
            (1, 'admin@risafani.id', 'Superadmin', 'Superadmin', 'Kompleks Pemkab Bandung Barat, Ngamprah', '081234567890', 'admin', 'assets/img/logoUnjuk.png')");
        $logs[] = "Akun Administrator (<strong>admin@risafani.id</strong> / password: <strong>Superadmin</strong>) siap digunakan.";

        // 3. Masukkan Data Sampel UMKM & Produk
        $m->query("INSERT IGNORE INTO `akun` (`id_akun`, `email`, `password`, `nama_user`, `alamat_user`, `notelp_user`, `Level`, `photo_user`) VALUES
            (2, 'budi@gmail.com', '123456', 'Budi Santoso', 'Jl. Panglima Sudirman No. 12, Nganjuk', '085712345678', 'user', 'assets/img/logoUnjuk.png'),
            (3, 'siti@gmail.com', '123456', 'Siti Rahmawati', 'Jl. Ahmad Yani No. 45, Kertosono', '085898765432', 'user', 'assets/img/logoUnjuk.png'),
            (4, 'hendra@gmail.com', '123456', 'Hendra Wijaya', 'Desa Ganung Kidul, Sukomoro', '081345678901', 'user', 'assets/img/logoUnjuk.png')");

        $m->query("INSERT IGNORE INTO `umkm` (`id_umkm`, `id_akun`, `nama_umkm`, `Jenis_usahaumkm`, `Nib_umkm`, `notelp_umkm`, `kecamatan_umkm`, `alamat_umkm`, `umkm_foto`) VALUES
            (1, 2, 'Dumbleg Asli Nganjuk', 'Makanan', '1234567890123', '085712345678', 'Nganjuk', 'Jl. Panglima Sudirman No. 12, Ganung Kidul', 'assets/img/logoUnjuk.png'),
            (2, 3, 'Kopi Robusta Anjuk Ladang', 'Minuman', '9876543210987', '085898765432', 'Kertosono', 'Jl. Ahmad Yani No. 45, Pelem', 'assets/img/logoUnjuk.png'),
            (3, 4, 'Batik Tulis Anjuk Ladang', 'Kerajinan', '4567890123456', '081345678901', 'Sukomoro', 'Desa Ganung Kidul RT 02/03', 'assets/img/logoUnjuk.png'),
            (4, 2, 'Servis Elektronik & Jasa Digital', 'Jasa', '7890123456789', '085712345678', 'Bagor', 'Jl. Raya Bagor No. 88', 'assets/img/logoUnjuk.png')");

        $m->query("INSERT IGNORE INTO `produk` (`id_produk`, `id_umkm`, `nama_produk`, `harga_produk`, `kategori_produk`, `deskripsi_produk`, `pirt_produk`, `bpom_produk`, `idhalal_produk`, `gambar_produk1`) VALUES
            (1, 1, 'Dumbleg Manis Gurih Khas Nganjuk', 25000.00, 'Makanan', 'Dumbleg asli khas Nganjuk dengan resep turun temurun.', 'P-IRT 2063518010202', 'BPOM RI MD 234567', 'ID351100012345', 'assets/img/hero-img.png'),
            (2, 2, 'Kopi Robusta Premium Anjuk Ladang 250gr', 35000.00, 'Minuman', 'Kopi biji robusta pilihan hasil lereng gunung Wilis Nganjuk.', 'P-IRT 2103518020303', '-', 'ID351100054321', 'assets/img/hero-img.png'),
            (3, 3, 'Kain Batik Tulis Motif Candi Lor', 175000.00, 'Kerajinan', 'Batik tulis buatan tangan pengrajin lokal motif Candi Lor Nganjuk.', '-', '-', '-', 'assets/img/hero-img.png'),
            (4, 4, 'Jasa Desain Kemasan & Branding UMKM', 50000.00, 'Jasa', 'Bantuan pembuatan logo dan desain kemasan profesional.', '-', '-', '-', 'assets/img/hero-img.png')");

        $m->query("INSERT IGNORE INTO `kegiatan` (`id_kegiatan`, `judul`, `tgl`, `jam`, `deskripsi`, `foto`) VALUES
            (1, 'Sosialisasi Sertifikasi Halal Gratis (SEHATI) Bagi Pelaku Usaha Mikro', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'Dinas Koperasi dan Usaha Mikro Nganjuk memfasilitasi sertifikasi halal gratis untuk para UMKM.', 'assets/img/logoUnjuk.png'),
            (2, 'Pelatihan Pemasaran Digital & E-Commerce UMKM Nganjuk 2026', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '08:30:00', 'Workshop strategi digital marketing & promosi produk lokal.', 'assets/img/logoUnjuk.png')");
        $logs[] = "Data UMKM, Produk, dan Kegiatan / Warta berhasil diisi.";

        // 4. Update file koneksi.php
        $newKoneksi = "<?php\n" .
            "/**\n" .
            " * File Koneksi Database Otomatis - UNjuk & Warkop UMI\n" .
            " * Diperbarui oleh auto_setup.php pada: " . date('Y-m-d H:i:s') . "\n" .
            " */\n\n" .
            "\$server   = \"" . addslashes($targetHost) . "\";\n" .
            "\$user     = \"" . addslashes($targetUser) . "\";\n" .
            "\$pass     = \"" . addslashes($targetPass) . "\";\n" .
            "\$database = \"" . addslashes($targetName) . "\";\n" .
            "\$port     = " . $targetPort . ";\n\n" .
            "// Re-use koneksi aktif jika masih hidup (mencegah MySQL server has gone away)\n" .
            "if (isset(\$conn) && (\$conn instanceof mysqli) && @mysqli_ping(\$conn)) {\n" .
            "    return;\n" .
            "}\n\n" .
            "\$conn = @mysqli_init();\n" .
            "if (\$conn) {\n" .
            "    @mysqli_options(\$conn, MYSQLI_OPT_CONNECT_TIMEOUT, 15);\n" .
            "    @mysqli_real_connect(\$conn, \$server, \$user, \$pass, \$database, \$port);\n" .
            "}\n\n" .
            "if (!\$conn || mysqli_connect_errno()) {\n" .
            "    \$connErr = mysqli_connect_error() ?: \"Tidak dapat tersambung ke MySQL server (\$server:\$port)\";\n" .
            "    if (php_sapi_name() !== 'cli' && !headers_sent()) {\n" .
            "        \$curFile = basename(\$_SERVER['PHP_SELF'] ?? '');\n" .
            "        if (\$curFile !== 'install.php' && \$curFile !== 'auto_setup.php') {\n" .
            "            echo '<div style=\"font-family:system-ui,sans-serif;padding:30px;background:#111e38;border:1px solid #1e3a5f;color:#f8fafc;border-radius:12px;max-width:540px;margin:60px auto;text-align:center;\">';\n" .
            "            echo '<h3 style=\"margin-top:0;color:#f87171;\">Koneksi Database Gagal</h3>';\n" .
            "            echo '<p style=\"color:#94a3b8;font-size:14px;\">' . htmlspecialchars(\$connErr) . '</p>';\n" .
            "            echo '<p style=\"margin-top:20px;\"><a href=\"auto_setup.php\" style=\"background:#2563eb;color:#fff;padding:10px 20px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:14px;\">Buka Auto Setup (auto_setup.php)</a></p>';\n" .
            "            echo '</div>';\n" .
            "            exit;\n" .
            "        }\n" .
            "    }\n" .
            "} else {\n" .
            "    @mysqli_set_charset(\$conn, 'utf8mb4');\n" .
            "}\n" .
            "?>\n";

        @file_put_contents($koneksiFile, $newKoneksi);
        $logs[] = "File <strong>`koneksi.php`</strong> berhasil disinkronkan.";

        $actionStatus  = 'success';
        $actionMessage = "Database `$targetName` berhasil diisi secara lengkap dengan 5 tabel dan data awal!";
        $connSuccess   = true;
        $dbHost        = $targetHost;
        $dbUser        = $targetUser;
        $dbPass        = $targetPass;
        $dbName        = $targetName;
        $dbPort        = $targetPort;

        // Ambil daftar tabel yang baru dibuat
        $existingTables = ['akun', 'umkm', 'produk', 'kegiatan', 'verification'];

    } catch (Exception $e) {
        $actionStatus  = 'error';
        $actionMessage = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Setup Database - UNjuk & Warkop UMI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
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
        .container {
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
            background: rgba(59, 130, 246, 0.15);
            color: var(--primary);
            border: 1px solid rgba(59, 130, 246, 0.3);
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
        .status-box {
            background: #080f1e;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
        }
        .status-box h4 {
            font-size: 13px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 13px;
        }
        .grid-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .grid-item span {
            color: var(--text-muted);
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .grid-item strong {
            color: #ffffff;
            font-family: monospace;
            font-size: 13px;
        }
        .table-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .pill {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-family: monospace;
        }
        .pill-missing {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
        .form-group {
            margin-bottom: 14px;
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
            background: #080f1e;
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
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        .btn-success {
            background: var(--success);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: #cbd5e1;
            margin-top: 10px;
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
        .logs-box {
            background: #060a14;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 14px;
            margin-top: 15px;
            font-size: 12px;
            max-height: 180px;
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
        .info-card {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            margin-top: 20px;
        }
        .info-card strong { color: #93c5fd; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <span class="badge"><i class="fa fa-database"></i> Setup Database Otomatis</span>
        <h1>UNjuk & Warkop UMI</h1>
        <p>Inisialisasi Tabel & Data Awal ke Database MySQL</p>
    </div>

    <?php if ($actionStatus === 'success'): ?>
        <div class="alert alert-success">
            <i class="fa fa-circle-check fa-lg"></i>
            <div>
                <strong>Berhasil!</strong><br>
                <?= htmlspecialchars($actionMessage) ?>
            </div>
        </div>

        <?php if (!empty($logs)): ?>
            <div class="logs-box">
                <?php foreach ($logs as $l): ?>
                    <div><i class="fa fa-check text-success" style="color:#10b981;"></i> <?= $l ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="info-card">
            <strong><i class="fa fa-key"></i> Kredensial Login Administrator:</strong><br>
            Email: <code style="color:#ffffff;background:#080f1e;padding:2px 6px;border-radius:4px;">admin@unjuk.com</code><br>
            Password: <code style="color:#ffffff;background:#080f1e;padding:2px 6px;border-radius:4px;">admin</code>
        </div>

        <div class="action-links">
            <a href="index.php" class="btn btn-primary"><i class="fa fa-house"></i> Buka Beranda</a>
            <a href="admin-beranda.php" class="btn btn-success"><i class="fa fa-gauge"></i> Admin Panel</a>
        </div>

    <?php else: ?>

        <?php if ($actionStatus === 'error'): ?>
            <div class="alert alert-danger">
                <i class="fa fa-circle-exclamation fa-lg"></i>
                <div>
                    <strong>Proses Gagal:</strong><br>
                    <?= htmlspecialchars($actionMessage) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Status Koneksi Saat Ini -->
        <div class="status-box">
            <h4>
                <span>Koneksi Saat Ini (koneksi.php)</span>
                <?php if ($connSuccess): ?>
                    <span style="color:#34d399;"><i class="fa fa-circle"></i> Terhubung</span>
                <?php else: ?>
                    <span style="color:#f87171;"><i class="fa fa-circle"></i> Terputus</span>
                <?php endif; ?>
            </h4>

            <div class="grid-2">
                <div class="grid-item">
                    <span>Host / IP</span>
                    <strong><?= htmlspecialchars($dbHost) ?>:<?= $dbPort ?></strong>
                </div>
                <div class="grid-item">
                    <span>Database</span>
                    <strong><?= htmlspecialchars($dbName) ?></strong>
                </div>
                <div class="grid-item">
                    <span>User</span>
                    <strong><?= htmlspecialchars($dbUser) ?></strong>
                </div>
                <div class="grid-item">
                    <span>Password</span>
                    <strong><?= empty($dbPass) ? '<em>(kosong)</em>' : '••••••••' ?></strong>
                </div>
            </div>

            <?php if (!$connSuccess): ?>
                <div class="alert alert-warning" style="margin-top:14px; margin-bottom:0;">
                    <i class="fa fa-triangle-exclamation"></i>
                    <div>
                        <strong>Pesan Error:</strong> <?= htmlspecialchars($connError) ?><br>
                        <small>Silakan isi User, Password, atau Nama Database yang sesuai pada form di bawah ini.</small>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-top:14px;">
                    <span style="font-size:12px; color:var(--text-muted);">Tabel di Database saat ini:</span>
                    <div class="table-pills">
                        <?php 
                        $required = ['akun', 'umkm', 'produk', 'kegiatan', 'verification'];
                        $missing = array_diff($required, $existingTables);
                        ?>
                        <?php if (empty($existingTables)): ?>
                            <span class="pill pill-missing"><i class="fa fa-info-circle"></i> Database masih KOSONG (0 Tabel)</span>
                        <?php else: ?>
                            <?php foreach ($existingTables as $tb): ?>
                                <span class="pill"><i class="fa fa-check"></i> <?= htmlspecialchars($tb) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($connSuccess): ?>
            <!-- Jika sudah terhubung, tinggal klik tombol Isi Database -->
            <form method="POST">
                <input type="hidden" name="db_host" value="<?= htmlspecialchars($dbHost) ?>">
                <input type="hidden" name="db_port" value="<?= $dbPort ?>">
                <input type="hidden" name="db_user" value="<?= htmlspecialchars($dbUser) ?>">
                <input type="hidden" name="db_pass" value="<?= htmlspecialchars($dbPass) ?>">
                <input type="hidden" name="db_name" value="<?= htmlspecialchars($dbName) ?>">

                <button type="submit" name="do_populate" class="btn btn-primary" style="padding:16px; font-size:15px;">
                    <i class="fa fa-bolt"></i> Isi & Buat Semua Tabel Database Sekarang (1-Click)
                </button>
            </form>

            <details style="margin-top:20px; font-size:13px; color:var(--text-muted);">
                <summary style="cursor:pointer; color:#60a5fa;">Ganti nama database atau user/password?</summary>
                <form method="POST" style="margin-top:14px; padding:16px; background:#080f1e; border-radius:10px; border:1px solid var(--border-color);">
                    <div class="form-group">
                        <label>Host / IP MySQL:</label>
                        <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($dbHost) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Database:</label>
                        <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($dbName) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>User Database:</label>
                        <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($dbUser) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password Database:</label>
                        <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($dbPass) ?>">
                    </div>
                    <button type="submit" name="update_and_populate" class="btn btn-primary">
                        <i class="fa fa-floppy-disk"></i> Simpan & Populate Database Baru
                    </button>
                </form>
            </details>

        <?php else: ?>
            <!-- Jika belum terhubung, tampilkan form untuk memasukkan username & password database yang baru dibuat -->
            <form method="POST">
                <div style="background:#080f1e; border:1px solid var(--border-color); border-radius:12px; padding:18px; margin-bottom:16px;">
                    <h4 style="font-size:13px; margin-bottom:14px; color:#ffffff;"><i class="fa fa-sliders"></i> Masukkan Detail Database yang Baru Dibuat:</h4>
                    <div class="form-group">
                        <label>Host MySQL Server:</label>
                        <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($dbHost) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Database:</label>
                        <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($dbName) ?>" placeholder="Contoh: aplikasi_unjuk" required>
                    </div>
                    <div class="form-group">
                        <label>Username MySQL:</label>
                        <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($dbUser) ?>" placeholder="Contoh: root atau kop_risafani" required>
                    </div>
                    <div class="form-group">
                        <label>Password MySQL:</label>
                        <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($dbPass) ?>" placeholder="Masukkan password database jika ada">
                    </div>
                </div>

                <button type="submit" name="update_and_populate" class="btn btn-primary" style="padding:16px; font-size:15px;">
                    <i class="fa fa-play"></i> Hubungkan & Isi Database Secara Otomatis
                </button>
            </form>
        <?php endif; ?>

        <div style="text-align:center; margin-top:20px;">
            <a href="install.php" style="color:var(--text-muted); font-size:12px; text-decoration:none;">
                <i class="fa fa-screwdriver-wrench"></i> Atau buka Full Installation Wizard (install.php)
            </a>
        </div>

    <?php endif; ?>
</div>

</body>
</html>
