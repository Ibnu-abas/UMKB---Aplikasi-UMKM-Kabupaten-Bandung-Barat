<?php
include "koneksi.php";

$row = null;
if (isset($_GET['id_kegiatan']) && is_numeric($_GET['id_kegiatan'])) {
    $id_kegiatan = intval($_GET['id_kegiatan']);
    $sqlDetail = "SELECT * FROM kegiatan WHERE id_kegiatan = $id_kegiatan";
    $resultDetail = mysqli_query($conn, $sqlDetail);

    if ($resultDetail && mysqli_num_rows($resultDetail) > 0) {
        $row = mysqli_fetch_assoc($resultDetail);
    }
}

// Format link di deskripsi
function makeClickableLinks($text) {
    $pattern = '/(http[s]?:\/\/[^\s]+)/';
    $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>';
    return preg_replace($pattern, $replacement, $text);
}

// Cek foto
$fotoSrc = "assets/img/hero-img.png";
if ($row && !empty($row['foto'])) {
    $rawFoto = trim($row['foto']);
    if (file_exists($rawFoto)) {
        $fotoSrc = $rawFoto;
    } elseif (file_exists("assets1/" . basename($rawFoto))) {
        $fotoSrc = "assets1/" . basename($rawFoto);
    } elseif (file_exists("assets/img/" . basename($rawFoto))) {
        $fotoSrc = "assets/img/" . basename($rawFoto);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $row ? htmlspecialchars($row['judul']) : 'Warta Kegiatan'; ?> - UMKB Kabupaten Bandung Barat</title>
  <meta content="Warta dan Informasi Kegiatan Pelaku UMKM Kabupaten Bandung Barat" name="description">
  <meta content="UMKB, Kegiatan UMKM Bandung Barat, Pelatihan UMKM, Warkop UMI" name="keywords">

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
    .kegiatan-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.06);
      padding: 35px;
      margin-bottom: 50px;
    }
    .kegiatan-title {
      font-size: 28px;
      font-weight: 700;
      color: #235088;
      line-height: 1.3;
      margin-bottom: 15px;
    }
    .kegiatan-meta {
      font-size: 14px;
      color: #6c757d;
      margin-bottom: 25px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: center;
      padding-bottom: 15px;
      border-bottom: 1px solid #eef2f6;
    }
    .kegiatan-meta span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .kegiatan-img-wrapper {
      text-align: center;
      margin-bottom: 30px;
    }
    .kegiatan-img {
      max-width: 100%;
      max-height: 480px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      object-fit: cover;
    }
    .kegiatan-body {
      font-size: 16px;
      line-height: 1.8;
      color: #333;
    }
    .kegiatan-body p {
      margin-bottom: 1.2rem;
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
          <li class="dropdown"><a href="#"><span>Produk UMKM</span> <i class="bi bi-chevron-down"></i></a>
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
        <a href="index.php#warkop" style="color: #235088; text-decoration: none;">Warta Kegiatan</a>
        <span class="mx-2 text-muted">/</span>
        <span class="text-secondary"><?php echo $row ? htmlspecialchars($row['judul']) : 'Tidak ditemukan'; ?></span>
      </div>

      <?php if (!$row): ?>
        <div class="alert alert-warning text-center my-5 p-5">
          <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
          <h4>Kegiatan Tidak Ditemukan</h4>
          <p class="text-muted">Maaf, data warta kegiatan yang Anda cari tidak tersedia atau parameter ID salah.</p>
          <a href="index.php" class="btn btn-primary mt-3">Kembali ke Beranda</a>
        </div>
      <?php else: ?>
        <div class="kegiatan-container">
          <h1 class="kegiatan-title"><?php echo htmlspecialchars($row['judul']); ?></h1>

          <div class="kegiatan-meta">
            <span>
              <i class="bi bi-calendar-check text-primary"></i>
              <?php echo date('d F Y', strtotime($row['tgl'])); ?>
            </span>
            <span>
              <i class="bi bi-clock text-primary"></i>
              <?php echo date('H:i', strtotime($row['jam'])); ?> WIB
            </span>
            <span>
              <i class="bi bi-tag text-primary"></i>
              Warta & Agenda Dinas UMKM KBB
            </span>
          </div>

          <div class="kegiatan-img-wrapper">
            <img src="<?php echo htmlspecialchars($fotoSrc); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>" class="kegiatan-img">
          </div>

          <div class="kegiatan-body">
            <?php echo makeClickableLinks(nl2br(htmlspecialchars($row['deskripsi']))); ?>
          </div>

          <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="index.php#warkop" class="btn btn-outline-primary">
              <i class="bi bi-arrow-left"></i> Kembali ke Warta Kegiatan
            </a>
            <div class="text-muted small">
              Bagikan agenda ini kepada pelaku UMKM di lingkungan Anda.
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
