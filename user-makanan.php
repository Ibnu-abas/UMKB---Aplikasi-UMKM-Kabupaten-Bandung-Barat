<?php
/**
 * UMKB - Katalog Makanan
 * Aplikasi UMKM Kabupaten Bandung Barat
 * Pengembang: Ibnu-abas | Dikembangkan dari: https://github.com/Alvian14/projectUnjuk
 */
include "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Katalog Makanan - UMKB Kabupaten Bandung Barat</title>
  <meta content="Katalog kuliner dan makanan khas UMKM Kabupaten Bandung Barat - UMKB." name="description">

  <link rel="website icon" href="assets/img/logoUnjuk.png">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  
  <style>
    .search-button {
      width: auto !important;
      min-width: 80px;
      padding: 0 18px;
    }
    .card {
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      overflow: hidden;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
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
          <li><a class="nav-link" href="index.php">Beranda</a></li>
          <li><a class="nav-link" href="index.php#warkop">Warta UMKM</a></li>
          <li><a class="nav-link" href="user-layanan.php">Layanan</a></li>
          <li class="dropdown active"><a href="#"><span>Produk</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="user-makanan.php" class="active">Makanan</a></li>
              <li><a href="user-minuman.php">Minuman</a></li>
              <li><a href="user-kerajinan.php">Kerajinan</a></li>
              <li><a href="user-jasa.php">Jasa</a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="#footer">Tentang Kami</a></li>
          <li><a class="getstarted" href="tem-login/tem-login-admin.php">Masuk Admin</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header><!-- End Header -->

  <!-- makanan -->
  <section id="user-makanan" style="padding-top: 100px;">
    <div id="wrapper">
        <div class="kotak">
            <h1 style="font-family: 'Jost', sans-serif; color: white; font-size: 27px; margin-left: 10px;">
                <i class="bx bxs-food-menu"></i> Produk Kuliner & Makanan
            </h1>
            <form action="user-makanan.php" method="get">
                <div class="search-container">
                    <input class="search-input" type="search" id="searchInput" name="search" placeholder="Cari makanan khas Bandung Barat..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <input class="search-button" type="submit" value="Cari">
                </div>
            </form>
        </div>
    </div>

    <!-- menampilkan card produk -->
    <div class="container mt-5">
          <div class="row">
              <?php
              $searchCond = "";
              if (!empty($_GET['search'])) {
                  $search = mysqli_real_escape_string($conn, trim($_GET['search']));
                  $searchCond = "AND (pd.nama_produk LIKE '%$search%' OR um.nama_umkm LIKE '%$search%')";
              }

              $query = "SELECT pd.id_produk, pd.gambar_produk1, pd.gambar_produk2, pd.gambar_produk3, pd.nama_produk, um.nama_umkm, um.notelp_umkm, pd.harga_produk
                        FROM produk AS pd
                        INNER JOIN umkm AS um ON pd.id_umkm = um.id_umkm
                        WHERE pd.kategori_produk = 'Makanan' $searchCond
                        ORDER BY pd.id_produk DESC
                        LIMIT 30";
              $result = mysqli_query($conn, $query);

              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      $imgSrc = 'assets/img/hero-img.png';
                      if (!empty($row['gambar_produk1'])) {
                          $raw = $row['gambar_produk1'];
                          if (file_exists($raw)) {
                              $imgSrc = $raw;
                          } elseif (file_exists('public/img/produk-photo/' . $raw)) {
                              $imgSrc = 'public/img/produk-photo/' . $raw;
                          } elseif (file_exists('assets/img/' . $raw)) {
                              $imgSrc = 'assets/img/' . $raw;
                          } else {
                              $imgSrc = 'assets/img/hero-img.png';
                          }
                      }
              ?>
                      <div class="col-6 col-md-4 col-lg-3 mb-4">
                          <div class="card h-100 shadow-sm">
                              <a href="user-detail-produk.php?id_produk=<?php echo $row['id_produk']; ?>">
                                  <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" style="height: 200px; object-fit: cover;">
                              </a>
                              <div class="card-body d-flex flex-column">
                                  <h5 class="card-title" style="font-size: 15px; font-weight: 700;"><?php echo htmlspecialchars(substr($row['nama_produk'], 0, 45)); ?></h5>
                                  <p class="card-text text-muted" style="margin: 6px 0; font-size: 13px;"><i class="bx bx-store"></i> <?php echo htmlspecialchars($row['nama_umkm']); ?></p>
                                  <p class="card-text mt-auto" style="color: #2563eb; font-weight: bold; font-size: 16px;">Rp <?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></p>
                                  <a href="user-detail-produk.php?id_produk=<?php echo $row['id_produk']; ?>" class="btn btn-sm btn-outline-primary mt-2" style="border-radius: 8px;">Lihat Detail</a>
                              </div>
                          </div>
                      </div>
                  <?php
                  }
              } else {
                  echo '<div class="col-12 text-center py-5"><i class="bx bx-search-alt bx-lg text-muted"></i><h4 class="mt-3">Tidak ada produk makanan yang ditemukan.</h4><p class="text-muted">Coba kata kunci pencarian yang lain.</p><a href="user-makanan.php" class="btn btn-primary btn-sm">Tampilkan Semua</a></div>';
              }
              ?>
          </div>
      </div>
  </section>

  <!-- Footer -->
  <footer id="footer" class="mt-5">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 footer-contact">
            <h3 class="deskripsi" style="font-weight:bold; color:#fff;">UMKB</h3>
            <p style="font-size: 14px; line-height: 1.6; color:#e2e8f0;">
              <strong>Aplikasi UMKM Kabupaten Bandung Barat</strong><br>
              Wadah digital promosi produk UMKM Kabupaten Bandung Barat.<br>
              <em>Persembahan untuk mengembangkan UMKM Bandung Barat.</em>
            </p>
          </div>
          <div class="col-lg-4 col-md-6 footer-links">
            <h4 style="font-size: 20px; font-weight: bold;">HUBUNGI KAMI</h4>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
              <li><i class='bx bxs-map' style='color:#47b2e4'></i> Komplek Pemkab Bandung Barat, Kec. Ngamprah, Kab. Bandung Barat</li>
              <li><i class='bx bxs-user-check' style='color:#47b2e4'></i> Pengembang: <strong>Ibnu-abas</strong></li>
              <li><i class='bx bxl-github' style='color:#47b2e4'></i> Sumber: <a href="https://github.com/Alvian14/projectUnjuk" target="_blank" rel="noopener noreferrer">github.com/Alvian14/projectUnjuk</a></li>
            </ul>
          </div>
          <div class="col-lg-4 col-md-6 footer-links">
            <h4 style="font-size: 20px; font-weight: bold;">KATEGORI LAIN</h4>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-minuman.php">Minuman Khas KBB</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-kerajinan.php">Kerajinan & Kriya</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-jasa.php">Jasa & Servis</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="container footer-bottom clearfix">
      <div class="copyright" id="copyright">
        Copyright &copy; 2026 <strong><span>UMKB - Ibnu-abas</span></strong>. All Rights Reserved.
      </div>
    </div>
  </footer>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/klik-menu.js"></script>
</body>
</html>
