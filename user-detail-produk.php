<?php
include "koneksi.php";

$row = null;
if (isset($_GET['id_produk'])) {
    $id_produk = intval($_GET['id_produk']);

    if ($id_produk > 0) {
        $query = "SELECT pd.*, um.nama_umkm, um.notelp_umkm, um.alamat_umkm, um.kecamatan_umkm
                  FROM produk AS pd
                  INNER JOIN umkm AS um ON pd.id_umkm = um.id_umkm
                  WHERE pd.id_produk = $id_produk";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        }
    }
}

// Koleksi gambar yang valid
$images = [];
if ($row) {
    foreach (['gambar_produk1', 'gambar_produk2', 'gambar_produk3'] as $col) {
        if (!empty($row[$col])) {
            $val = trim($row[$col]);
            if (file_exists("public/img/produk-photo/" . $val)) {
                $images[] = "public/img/produk-photo/" . $val;
            } elseif (file_exists($val)) {
                $images[] = $val;
            }
        }
    }
}
if (empty($images)) {
    $images[] = "assets/img/hero-img.png";
}

// Nomor WhatsApp
$wa_url = "";
if ($row && !empty($row['notelp_umkm'])) {
    $clean_phone = preg_replace('/[^0-9]/', '', $row['notelp_umkm']);
    if (substr($clean_phone, 0, 1) === '0') {
        $clean_phone = '62' . substr($clean_phone, 1);
    } elseif (substr($clean_phone, 0, 2) !== '62') {
        $clean_phone = '62' . $clean_phone;
    }
    $wa_msg = urlencode("Halo UMKM " . ($row['nama_umkm'] ?? '') . ", saya tertarik untuk memesan produk \"" . ($row['nama_produk'] ?? '') . "\" melalui aplikasi UMKB Kabupaten Bandung Barat.");
    $wa_url = "https://wa.me/" . $clean_phone . "?text=" . $wa_msg;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $row ? htmlspecialchars($row['nama_produk']) : 'Detail Produk'; ?> - UMKB Kabupaten Bandung Barat</title>
  <meta content="Detail produk UMKM di Kabupaten Bandung Barat - UMKB" name="description">
  <meta content="UMKB, UMKM Bandung Barat, Produk Lokal KBB" name="keywords">

  <!-- icon web -->
  <link rel="website icon" href="assets/img/logoUnjuk.png">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

  <style>
    .breadcrumb-box {
      background: #f8f9fa;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 25px;
      font-size: 14px;
    }
    .detail-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.06);
      padding: 30px;
      margin-top: 30px;
      margin-bottom: 50px;
    }
    .product-carousel-img {
      width: 100%;
      height: 420px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #eef2f6;
    }
    .product-price-badge {
      font-size: 26px;
      font-weight: 700;
      color: #235088;
      margin: 15px 0;
    }
    .badge-category {
      background-color: #e8f1fa;
      color: #235088;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      display: inline-block;
    }
    .info-group {
      margin-bottom: 12px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f3f5;
    }
    .info-label {
      font-size: 13px;
      text-transform: uppercase;
      font-weight: 600;
      color: #8898aa;
      margin-bottom: 2px;
    }
    .info-value {
      font-size: 15px;
      color: #32325d;
      font-weight: 500;
    }
    .btn-wa {
      background-color: #25D366;
      color: white;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 4px 12px rgba(37,211,102,0.3);
      transition: all 0.3s ease;
    }
    .btn-wa:hover {
      background-color: #1ebd56;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(37,211,102,0.4);
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top" style="background-color: #235088;">
    <div class="container d-flex align-items-center">
      <h1 class="logo me-auto"><a href="index.php">UMKB</a></h1>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto" href="index.php">Beranda</a></li>
          <li><a class="nav-link scrollto" href="user-layanan.php">Layanan</a></li>
          <li class="dropdown active"><a href="#"><span>Produk UMKM</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="user-makanan.php">Makanan</a></li>
              <li><a href="user-minuman.php">Minuman</a></li>
              <li><a href="user-kerajinan.php">Kerajinan</a></li>
              <li><a href="user-jasa.php">Jasa</a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="#footer">Tentang Kami</a></li>
          <li><a class="getstarted scrollto" href="tem-login/tem-login-admin.php">Masuk</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header>

  <main style="margin-top: 100px;">
    <div class="container">
      
      <!-- Breadcrumb -->
      <div class="breadcrumb-box">
        <a href="index.php" style="color: #235088; text-decoration: none;"><i class="bi bi-house-door"></i> Beranda</a>
        <span class="mx-2 text-muted">/</span>
        <a href="<?php 
          $kat = strtolower($row['kategori_produk'] ?? '');
          if ($kat === 'makanan') echo 'user-makanan.php';
          elseif ($kat === 'minuman') echo 'user-minuman.php';
          elseif ($kat === 'kerajinan') echo 'user-kerajinan.php';
          else echo 'user-jasa.php';
        ?>" style="color: #235088; text-decoration: none;">
          <?php echo htmlspecialchars($row['kategori_produk'] ?? 'Produk'); ?>
        </a>
        <span class="mx-2 text-muted">/</span>
        <span class="text-secondary"><?php echo $row ? htmlspecialchars($row['nama_produk']) : 'Tidak ditemukan'; ?></span>
      </div>

      <?php if (!$row): ?>
        <div class="alert alert-warning text-center my-5 p-5">
          <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
          <h4>Produk Tidak Ditemukan</h4>
          <p class="text-muted">Maaf, data produk yang Anda cari tidak tersedia atau telah dihapus.</p>
          <a href="index.php" class="btn btn-primary mt-3">Kembali ke Beranda</a>
        </div>
      <?php else: ?>
        <div class="detail-container">
          <div class="row g-4">
            <!-- Kolom Gambar -->
            <div class="col-lg-6">
              <div id="productDetailCarousel" class="carousel slide" data-bs-ride="carousel">
                <?php if (count($images) > 1): ?>
                  <div class="carousel-indicators">
                    <?php foreach ($images as $idx => $img): ?>
                      <button type="button" data-bs-target="#productDetailCarousel" data-bs-slide-to="<?php echo $idx; ?>" class="<?php echo $idx === 0 ? 'active' : ''; ?>" aria-current="<?php echo $idx === 0 ? 'true' : 'false'; ?>"></button>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <div class="carousel-inner">
                  <?php foreach ($images as $idx => $img): ?>
                    <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo htmlspecialchars($img); ?>" class="product-carousel-img" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                    </div>
                  <?php endforeach; ?>
                </div>

                <?php if (count($images) > 1): ?>
                  <button class="carousel-control-prev" type="button" data-bs-target="#productDetailCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sebelumnya</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#productDetailCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Selanjutnya</span>
                  </button>
                <?php endif; ?>
              </div>
            </div>

            <!-- Kolom Info Produk -->
            <div class="col-lg-6">
              <span class="badge-category mb-2"><?php echo htmlspecialchars($row['kategori_produk']); ?></span>
              <h2 style="color: #2c3e50; font-weight: 700; margin-top: 8px;"><?php echo htmlspecialchars($row['nama_produk']); ?></h2>
              
              <div class="product-price-badge">
                Rp <?php echo number_format((float)$row['harga_produk'], 0, ',', '.'); ?>
              </div>

              <div class="info-group">
                <div class="info-label">Deskripsi Produk</div>
                <div class="info-value" style="line-height: 1.6; color: #555;">
                  <?php echo nl2br(htmlspecialchars($row['deskripsi_produk'] ?? '-')); ?>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4 info-group">
                  <div class="info-label">P-IRT</div>
                  <div class="info-value"><?php echo htmlspecialchars($row['pirt_produk'] ?: '-'); ?></div>
                </div>
                <div class="col-sm-4 info-group">
                  <div class="info-label">BPOM</div>
                  <div class="info-value"><?php echo htmlspecialchars($row['bpom_produk'] ?: '-'); ?></div>
                </div>
                <div class="col-sm-4 info-group">
                  <div class="info-label">Sertifikat Halal</div>
                  <div class="info-value"><?php echo htmlspecialchars($row['idhalal_produk'] ?: '-'); ?></div>
                </div>
              </div>

              <div class="info-group">
                <div class="info-label">Nama Pelaku UMKM</div>
                <div class="info-value"><i class="bi bi-shop text-primary me-2"></i><?php echo htmlspecialchars($row['nama_umkm']); ?></div>
              </div>

              <div class="info-group">
                <div class="info-label">Alamat & Kecamatan</div>
                <div class="info-value">
                  <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                  <?php echo htmlspecialchars($row['alamat_umkm']); ?>
                  <?php if (!empty($row['kecamatan_umkm'])): ?>
                    <span class="badge bg-secondary ms-1">Kec. <?php echo htmlspecialchars($row['kecamatan_umkm']); ?></span>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Tombol WhatsApp -->
              <div class="mt-4">
                <?php if (!empty($wa_url)): ?>
                  <a href="<?php echo $wa_url; ?>" target="_blank" class="btn-wa">
                    <i class="bi bi-whatsapp fs-5"></i> Hubungi Penjual via WhatsApp (<?php echo htmlspecialchars($row['notelp_umkm']); ?>)
                  </a>
                <?php else: ?>
                  <span class="text-muted"><i class="bi bi-telephone-x"></i> Nomor kontak penjual belum dicantumkan.</span>
                <?php endif; ?>
              </div>

            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 footer-contact">
            <h3 style="font-weight:bold; color: #235088;">UMKB</h3>
            <p style="font-size: 15px; margin-top: 10px;">
              <strong>Aplikasi UMKM Kabupaten Bandung Barat</strong><br>
              Wadah promosi, pemetaan, dan katalog digital bagi para pelaku usaha mikro, kecil, dan menengah di 16 kecamatan Kabupaten Bandung Barat.
            </p>
            <p style="font-size: 13px; color: #666; margin-top: 8px;">
              Persembahan untuk mengembangkan UMKM Bandung Barat.<br>
              Pengembang: <strong>Ibnu-abas</strong>
            </p>
          </div>

          <div class="col-lg-4 col-md-6 footer-links">
            <h4 style="font-size: 20px;">HUBUNGI KAMI</h4>
            <ul>
              <li><i class='bx bxs-map' style='color:#47b2e4'></i> 
                Kompleks Perkantoran Pemkab Bandung Barat, Jl. Raya Padalarang - Cisarua KM 2, Mekarsari, Ngamprah, Kab. Bandung Barat
              </li>
              <li><i class='bx bxs-envelope' style='color:#47b2e4'></i> 
                <a href="mailto:diskoperindag@bandungbaratkab.go.id">diskoperindag@bandungbaratkab.go.id</a>
              </li>
              <li><i class='bx bxl-github' style='color:#47b2e4'></i> 
                <a href="https://github.com/Alvian14/projectUnjuk" target="_blank">Repository Asli: projectUnjuk</a>
              </li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-links">
            <h4 style="font-size: 20px;">KATEGORI PRODUK</h4>
            <ul>
              <li><i class="bi bi-chevron-right text-primary"></i> <a href="user-makanan.php">Produk Makanan Olahan</a></li>
              <li><i class="bi bi-chevron-right text-primary"></i> <a href="user-minuman.php">Minuman Khas & Olahan</a></li>
              <li><i class="bi bi-chevron-right text-primary"></i> <a href="user-kerajinan.php">Kerajinan Tangan Kreatif</a></li>
              <li><i class="bi bi-chevron-right text-primary"></i> <a href="user-jasa.php">Jasa & Pelayanan Lokal</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container footer-bottom clearfix">
      <div class="copyright text-center">
        Copyright &copy; <?php echo date('Y'); ?> <strong><span>UMKB - Kab. Bandung Barat</span></strong>.
        Dikembangkan oleh <strong>Ibnu-abas</strong> dari basis <a href="https://github.com/Alvian14/projectUnjuk" target="_blank" style="color: #47b2e4;">projectUnjuk</a>.
      </div>
    </div>
  </footer>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

</body>
</html>
