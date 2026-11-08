<?php
/**
 * Contoh Template Konfigurasi Koneksi Database
 * UMKB - Aplikasi UMKM Kabupaten Bandung Barat
 * Pengembang: Ibnu-abas
 * Dikembangkan dari: https://github.com/Alvian14/projectUnjuk
 * Persembahan untuk mengembangkan UMKM Bandung Barat
 * Salin file ini menjadi 'koneksi.php' jika melakukan konfigurasi manual.
 */

$server   = "127.0.0.1";
$user     = "root";
$pass     = "";
$database = "umkb_bandung_barat";
$port     = 3306;

// Re-use koneksi aktif jika masih hidup (mencegah MySQL server has gone away)
if (isset($conn) && ($conn instanceof mysqli) && @mysqli_ping($conn)) {
    return;
}

// Inisialisasi koneksi dengan setting reconnect & timeout
$conn = @mysqli_init();
if ($conn) {
    @mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 15);
    @mysqli_real_connect($conn, $server, $user, $pass, $database, $port);
}

if (!$conn || mysqli_connect_errno()) {
    $connErr = mysqli_connect_error() ?: "Tidak dapat tersambung ke MySQL server ($server:$port)";
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        $curFile = basename($_SERVER['PHP_SELF'] ?? '');
        if ($curFile !== 'install.php') {
            echo '<!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Koneksi Database Gagal - UNJUK</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    body { font-family: system-ui, -apple-system, sans-serif; background: #0b1329; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
                    .card { background: #111e38; border: 1px solid #1e3a5f; border-radius: 16px; padding: 32px; max-width: 540px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
                    .icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px; border: 1px solid rgba(239, 68, 68, 0.3); }
                    h2 { font-size: 20px; margin-bottom: 10px; color: #ffffff; }
                    p { font-size: 13px; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
                    .btn { display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 14px rgba(37,99,235,0.4); }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="icon"><i class="fa fa-triangle-exclamation"></i></div>
                    <h2>Database Belum Terhubung</h2>
                    <p>Aplikasi <strong>UNjuk & Warkop UMI</strong> tidak dapat menghubungi database MySQL. Silakan gunakan wizard installer untuk menyambungkan koneksi.</p>
                    <a href="install.php" class="btn"><i class="fa fa-bolt"></i> Buka Wizard Instalasi (install.php)</a>
                </div>
            </body>
            </html>';
            exit;
        }
    }
} else {
    @mysqli_set_charset($conn, "utf8mb4");
}
?>
