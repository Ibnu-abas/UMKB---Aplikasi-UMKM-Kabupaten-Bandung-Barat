<?php
/**
 * Konfigurasi Koneksi Database
 * UMKB - Aplikasi UMKM Kabupaten Bandung Barat
 * Pengembang: Ibnu-abas
 * Dikembangkan dari: https://github.com/Alvian14/projectUnjuk
 */

$server   = "127.0.0.1";
$user     = "sql_kop_risafani_my_id";
$pass     = "1fe733d6411fc8";
$database = "sql_kop_risafani_my_id";
$port     = 3306;

// Jika koneksi sudah aktif dan masih hidup, gunakan kembali (hindari reconnect / closed socket)
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
                <title>Koneksi Database Gagal - UMKB Bandung Barat</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    body { font-family: system-ui, -apple-system, sans-serif; background: #0b1329; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
                    .card { background: #111e38; border: 1px solid #1e3a5f; border-radius: 16px; padding: 32px; max-width: 540px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
                    .icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); color: #f87171; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px; border: 1px solid rgba(239, 68, 68, 0.3); }
                    h2 { font-size: 20px; margin-bottom: 10px; color: #ffffff; }
                    p { font-size: 13px; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
                    .btn { display: inline-flex; align-items: center; gap: 8px; background: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 14px rgba(37,99,235,0.4); }
                    .err-detail { background: #080f1e; border: 1px solid #1e3a5f; border-radius: 8px; padding: 12px; font-family: monospace; font-size: 12px; color: #f87171; margin-bottom: 20px; text-align: left; word-break: break-all; }
                </style>
            </head>
            <body>
                <div class="card">
                    <div class="icon"><i class="fa fa-triangle-exclamation"></i></div>
                    <h2>Database Belum Terhubung</h2>
                    <p>Aplikasi <strong>UMKB (UMKM Kabupaten Bandung Barat)</strong> tidak dapat menghubungi database MySQL. Silakan gunakan wizard installer untuk menyambungkan koneksi.</p>
                    <div class="err-detail">Pesan Error: ' . htmlspecialchars($connErr) . '</div>
                    <a href="install.php" class="btn"><i class="fa fa-bolt"></i> Buka Wizard Instalasi (install.php)</a>
                </div>
            </body>
            </html>';
            exit;
        }
    }
} else {
    @mysqli_set_charset($conn, "utf8mb4");

    // Auto-sync data awal Bandung Barat jika belum pernah dijalankan
    $migratedFile = __DIR__ . '/.kbb_migrated';
    if (!file_exists($migratedFile) && $conn) {
        // Bersihkan admin lama dan pastikan akun Superadmin baru aktif
        @mysqli_query($conn, "DELETE FROM akun WHERE Level = 'admin' AND email != 'admin@risafani.id'");
        @mysqli_query($conn, "INSERT INTO akun (id_akun, email, password, nama_user, alamat_user, notelp_user, Level, photo_user) 
            VALUES (1, 'admin@risafani.id', 'Superadmin', 'Superadmin', 'Kompleks Pemkab Bandung Barat, Ngamprah', '081234567890', 'admin', 'assets/img/logoUnjuk.png')
            ON DUPLICATE KEY UPDATE email='admin@risafani.id', password='Superadmin', nama_user='Superadmin'");

        @mysqli_query($conn, "UPDATE umkm SET nama_umkm = 'Tahu Susu & Olahan Lembang', kecamatan_umkm = 'Lembang', alamat_umkm = 'Jl. Raya Lembang No. 120, Lembang' WHERE id_umkm = 1");
        @mysqli_query($conn, "UPDATE umkm SET nama_umkm = 'Kopi Arabika Gunung Burangrang', kecamatan_umkm = 'Parongpong', alamat_umkm = 'Jl. Kolonel Masturi No. 45, Parongpong' WHERE id_umkm = 2");
        @mysqli_query($conn, "UPDATE umkm SET nama_umkm = 'Kriya Anyaman Bambu Kreatif', kecamatan_umkm = 'Cikalongwetan', alamat_umkm = 'Jl. Raya Cikalong No. 15, Cikalongwetan' WHERE id_umkm = 3");
        @mysqli_query($conn, "UPDATE umkm SET nama_umkm = 'Jasa Desain Grafis & Legalitas KBB', kecamatan_umkm = 'Padalarang', alamat_umkm = 'Jl. Raya Padalarang No. 88, Padalarang' WHERE id_umkm = 4");

        @mysqli_query($conn, "UPDATE produk SET nama_produk = 'Tahu Susu Lembut Khas Lembang', deskripsi_produk = 'Tahu susu segar khas Lembang dengan tekstur super lembut dan rasa gurih alami tanpa pengawet.', pirt_produk = 'P-IRT 2063217010202', bpom_produk = 'BPOM RI MD 234567', idhalal_produk = 'ID321100012345' WHERE id_produk = 1");
        @mysqli_query($conn, "UPDATE produk SET nama_produk = 'Kopi Arabika Single Origin Burangrang 250gr', harga_produk = 45000.00, deskripsi_produk = 'Kopi arabika spesialti lereng Gunung Burangrang, cita rasa fruity cerah.', pirt_produk = 'P-IRT 2103217020303', idhalal_produk = 'ID321100054321' WHERE id_produk = 2");
        @mysqli_query($conn, "UPDATE produk SET nama_produk = 'Keranjang Anyaman Bambu Multifungsi', harga_produk = 65000.00, deskripsi_produk = 'Kerajinan anyaman bambu tangan khas pengrajin kreatif Bandung Barat, ramah lingkungan dan estetik.' WHERE id_produk = 3");
        @mysqli_query($conn, "UPDATE produk SET nama_produk = 'Jasa Desain Kemasan & Branding UMKM KBB', harga_produk = 50000.00, deskripsi_produk = 'Layanan bantuan pembuatan logo, kemasan dan branding produk UMKM.' WHERE id_produk = 4");

        @mysqli_query($conn, "UPDATE kegiatan SET judul = 'Sosialisasi Sertifikasi Halal Gratis (SEHATI) Pelaku Usaha KBB', deskripsi = 'Dinas Koperasi dan Usaha Kecil Menengah Kabupaten Bandung Barat menyelenggarakan pendampingan sertifikasi halal gratis untuk para pelaku UMKM makanan & minuman se-Kabupaten Bandung Barat. Daftarkan usaha Anda melalui portal UMKB.' WHERE id_kegiatan = 1");
        @mysqli_query($conn, "UPDATE kegiatan SET judul = 'Pelatihan Pemasaran Digital & E-Commerce UMKM Bandung Barat 2026', deskripsi = 'Tingkatkan omzet dan jangkauan pemasaran produk unggulan KBB dengan workshop intensif optimasi media sosial, foto katalog produk, dan integrasi marketplace nasional.' WHERE id_kegiatan = 2");

        @file_put_contents($migratedFile, date('Y-m-d H:i:s'));
    }
}
?>
