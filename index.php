<?php
/**
 * UMKB - Aplikasi UMKM Kabupaten Bandung Barat
 * Pengembang: Ibnu-abas
 * Dikembangkan dari: https://github.com/Alvian14/projectUnjuk
 * Persembahan untuk mengembangkan UMKM Bandung Barat
 */

include "koneksi.php";

date_default_timezone_set('Asia/Jakarta');

// Ambil data kegiatan dan urutkan berdasarkan tanggal secara descending
$sql = "SELECT * FROM kegiatan ORDER BY id_kegiatan DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "Gagal mengambil data kegiatan: " . mysqli_error($conn);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>UMKB - Aplikasi UMKM Kabupaten Bandung Barat</title>
  <meta content="Aplikasi UMKM Kabupaten Bandung Barat - Persembahan untuk mengembangkan UMKM Bandung Barat. Pengembang: Ibnu-abas." name="description">
  <meta content="UMKB, UMKM Bandung Barat, Koperasi Bandung Barat, Produk UMKM KBB" name="keywords">

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
  <link rel="stylesheet" href="assets/css/warta.css?v=<?php echo time(); ?>">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top" style="background-color: #235088;">
    <div class="container d-flex align-items-center">
      <h1 class="logo me-auto"><a href="index.php">UMKB</a></h1>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="#hero">Beranda</a></li>
          <li><a class="nav-link scrollto" href="#warkop">Warta UMKM</a></li>
          <li><a class="nav-link" href="user-layanan.php">Layanan</a></li>
          <li class="dropdown"><a href="#"><span>Produk</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="user-makanan.php">Makanan</a></li>
              <li><a href="user-minuman.php">Minuman</a></li>
              <li><a href="user-kerajinan.php">Kerajinan</a></li>
              <li><a href="user-jasa.php">Jasa</a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="#footer">Tentang Kami</a></li>
          <li><a class="getstarted scrollto" href="tem-login/tem-login-admin.php">Masuk Admin</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center" style="background-color: #235088; min-height: 80vh;">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 d-flex flex-column justify-content-center pt-4 pt-lg-0 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200">
          <h1>Inspirasikan Bisnismu Bersama UMKB</h1>
          <h2>Aplikasi UMKM Kabupaten Bandung Barat.<br>Persembahan untuk mengembangkan UMKM Bandung Barat.</h2>
          <div class="d-flex justify-content-center justify-content-lg-start mt-3">
            <a href="user-makanan.php" class="btn-get-started scrollto" style="background: #47b2e4; color: #fff; padding: 10px 28px; border-radius: 50px; text-decoration: none; font-weight: 600; margin-right: 12px;">Jelajahi Produk</a>
            <a href="user-layanan.php" class="btn-get-started scrollto" style="border: 2px solid #47b2e4; color: #fff; padding: 8px 24px; border-radius: 50px; text-decoration: none; font-weight: 600;">Layanan UMKM</a>
          </div>
        </div>
        <div class="col-lg-6 order-1 order-lg-2 hero-img text-center" data-aos="zoom-in" data-aos-delay="200">
          <img src="assets/img/X-Cart_Web_Development___X-Cart_Customization_Solutions-removebg-preview.png" class="img-fluid animated" alt="UMKB Bandung Barat" style="max-height: 380px;">
        </div>
      </div>
    </div>
  </section><!-- End Hero -->

  <!-- ======= Warta / Agenda Section ======= -->
  <section id="warkop" style="padding: 60px 0;">
    <div id="wrapper">
      <div class="kotak text-center">
        <h1>Warta Koperasi & UMKM Bandung Barat</h1>
        <p style="color: #cbd5e1; margin-top: 8px;">Informasi agenda pelatihan, sosialisasi legalitas, dan kegiatan pemberdayaan UMKM</p>
      </div>
    </div>

    <?php
      function makeClickableLinks($text) {
        $pattern = '/(http[s]?:\/\/[^\s]+)/';
        $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>';
        return preg_replace($pattern, $replacement, $text);
      }

      if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              echo '<div class="container-kotak mt-5">';
              echo '<div class="title" style="text-align: left;">' . nl2br(htmlspecialchars($row['judul'])) . '</div>';
              
              $tanggal = date('j F Y', strtotime($row['tgl']));
              $jam = date('H:i', strtotime($row['jam']));
              
              echo '<div class="datetime">';
              echo '<i class="bx bx-calendar"></i> ' . $tanggal . ' | <i class="bx bx-time"></i> ' . $jam . ' WIB<br>';
              echo '</div>';
              
              $foto = !empty($row['foto']) ? $row['foto'] : 'assets/img/hero-img.png';
              echo '<div class="image-container mt-2">';
              echo '<img class="image" src="' . htmlspecialchars($foto) . '" alt="' . htmlspecialchars($row['judul']) . '">';
              echo '</div>';
              
              echo '<div class="content">';
              $deskripsi = nl2br(htmlspecialchars($row['deskripsi']));
              $deskripsi_pendek = (strlen($deskripsi) > 200) ? substr($deskripsi, 0, 200) . '...' : $deskripsi;
      
              echo '<div class="description mt-4" id="deskripsi">' . makeClickableLinks($deskripsi_pendek) . '</div>';
              echo '<a href="user-detail-kegiatan.php?id_kegiatan=' . $row['id_kegiatan'] . '&nama=' . urlencode($row['judul']) . '" class="read-more-link">Lihat selengkapnya &rarr;</a>';
              echo '</div>';
              echo '</div>';
          }
      } else {
          echo '<p class="paragraf text-center mt-5">Belum ada warta kegiatan yang ditampilkan saat ini.</p>';
      }
    ?>
  </section>

  <!-- ======= Footer ======= -->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 footer-contact">
            <h3 class="deskripsi" style="font-weight:bold; color: #fff;">UMKB</h3>
            <p style="font-size: 14px; line-height: 1.6; color: #e2e8f0;">
              <strong>Aplikasi UMKM Kabupaten Bandung Barat</strong><br>
              Wadah digital bagi para pelaku UMKM makanan, minuman, kerajinan, dan jasa di seluruh 16 kecamatan Kabupaten Bandung Barat untuk mempromosikan produk secara online.<br><br>
              <em>Persembahan untuk mengembangkan UMKM Bandung Barat.</em>
            </p>
          </div>

          <div class="col-lg-4 col-md-6 footer-links">
            <h4 class="h4-footer" style="font-size: 20px; font-weight: bold;">HUBUNGI KAMI</h4>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
              <li><i class='bx bxs-map' style='color:#47b2e4'></i> Komplek Pemkab Bandung Barat, Jl. Raya Padalarang - Cisarua Km. 2, Mekarsari, Kec. Ngamprah, Kab. Bandung Barat, Jawa Barat 40552</li>
              <li><i class='bx bxs-envelope' style='color:#47b2e4'></i> diskopumkm.kbb@gmail.com</li>
              <li><i class='bx bxs-user-check' style='color:#47b2e4'></i> Pengembang: <strong>Ibnu-abas</strong></li>
              <li><i class='bx bxl-github' style='color:#47b2e4'></i> Sumber: <a href="https://github.com/Alvian14/projectUnjuk" target="_blank" rel="noopener noreferrer">github.com/Alvian14/projectUnjuk</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-links">
            <h4 style="font-size: 20px; font-weight: bold;">KATEGORI PRODUK</h4>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-makanan.php">Kuliner Makanan Khas Bandung Barat</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-minuman.php">Aneka Minuman & Kopi Olahan</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-kerajinan.php">Kerajinan Tangan & Kriya Lokal</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-jasa.php">Jasa Kreatif & Digital UMKM</a></li>
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-layanan.php">Layanan Perizinan & NIB</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container footer-bottom clearfix">
      <div class="copyright" id="copyright">
        Copyright &copy; 2026 <strong><span>UMKB - Ibnu-abas</span></strong>. All Rights Reserved.
      </div>
      <div class="credits">
        Persembahan untuk mengembangkan UMKM Bandung Barat | Dikembangkan dari <a href="https://github.com/Alvian14/projectUnjuk" target="_blank" rel="noopener noreferrer" style="color:#47b2e4;">projectUnjuk</a>
      </div>
    </div>
  </footer><!-- End Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/klik-menu.js"></script>
</body>
</html>
