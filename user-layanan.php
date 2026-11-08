<?php
/**
 * UMKB - Layanan Klinik UMKM & Koperasi
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

  <title>Layanan UMKM - UMKB Kabupaten Bandung Barat</title>
  <meta content="Layanan fasilitasi legalitas, NIB, sertifikasi halal, dan klinik digital UMKM Kabupaten Bandung Barat - UMKB." name="description">

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

  <style>
    .kotak1 {
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
      background: #ffffff;
      padding: 30px 20px;
    }
    .kotak1:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
    }
    .card-body {
      padding: 10px;
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
          <li><a class="nav-link active" href="user-layanan.php">Layanan</a></li>
          <li class="dropdown"><a href="#"><span>Produk</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="user-makanan.php">Makanan</a></li>
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

  <section id="layanan" style="padding-top: 100px;">
    <div id="wrapper">
      <div class="kotak">
          <h1 style="font-family: 'Jost', sans-serif; color: white; font-size: 27px; margin-left: 10px;">
              <i class="bx bxs-briefcase"></i> Layanan Terpadu UMKM Bandung Barat
          </h1>
          <p style="color: #cbd5e1; margin-left: 10px; margin-top: 5px;">Pusat konsultasi dan fasilitasi legalitas usaha bagi para pelaku usaha mikro Kabupaten Bandung Barat</p>
      </div>
    </div>
    
    <div class="container mt-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="kotak1 text-center shadow-sm">
                    <i class='bx bxs-clinic bx-tada bx-lg' style='color:#47b2e4'></i>
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">Klinik UMKM Go Digital</h5>
                        <p class="card-text text-muted mt-2">Pendampingan digitalisasi usaha, katalog online, marketplace, dan strategi pemasaran di internet.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="kotak1 text-center shadow-sm">
                    <i class='bx bxs-file-doc bx-tada bx-lg' style='color:#47b2e4'></i>
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">Fasilitasi Perizinan</h5>
                        <p class="card-text text-muted mt-2">Bantuan pendaftaran Nomor Induk Berusaha (NIB OSS), izin edar P-IRT, dan Sertifikasi Halal Gratis (SEHATI).</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="kotak1 text-center shadow-sm">
                    <i class='bx bxs-magic-wand bx-tada bx-lg' style='color:#47b2e4'></i>
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">Desain Kemasan & Branding</h5>
                        <p class="card-text text-muted mt-2">Bantuan konsultasi pembuatan logo, redesain packaging menarik, dan peningkatan daya saing produk.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row g-4 justify-content-center align-items-center">
            <div class="col-md-6 col-lg-4">
                <div class="kotak1 text-center shadow-sm">
                    <i class='bx bxs-store bx-tada bx-lg' style='color:#47b2e4'></i>
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">Market Hub KBB</h5>
                        <p class="card-text text-muted mt-2">Kanal penghubung produk UMKM Bandung Barat ke pasar ritel modern, hotel, dan pusat oleh-oleh wisata.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="kotak1 text-center shadow-sm">
                      <i class='bx bx-trending-up bx-tada bx-lg' style='color:#47b2e4'></i>
                      <div class="card-body">
                        <h5 class="card-title font-weight-bold">Program Kemitraan & Bazaar</h5>
                        <p class="card-text text-muted mt-2">Peluang mengikuti pameran UMKM daerah, bazaar festival wisata KBB, dan jejaring komunitas usaha.</p>
                      </div>
                </div>
            </div>
        </div>
    </div>

    <div class="informasi container mt-5 p-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-left: 5px solid #235088;">
        <h4 style="font-weight: bold; color: #235088; margin-bottom: 15px;"><i class="bx bxs-info-circle"></i> Cara Mengajukan Layanan:</h4>
        <p>Bagi pelaku UMKM di Kabupaten Bandung Barat yang ingin memanfaatkan layanan klinik digital dan fasilitasi perizinan, silakan hubungi tim kami melalui jalur resmi berikut:</p>
        <ol style="line-height: 1.8;">
            <li><strong>Klinik UMKM Go Digital</strong> (Pendampingan teknis dan media sosial)</li>
            <li><strong>Fasilitasi Perizinan Legalitas</strong> (NIB / P-IRT / Sertifikat Halal)</li>
            <li><strong>Konsultasi Desain & Kemasan Produk</strong></li>
            <li><strong>Kemitraan Promosi Produk Bandung Barat</strong></li>
        </ol>
        <div class="mt-4 d-flex flex-wrap gap-2">
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20UMKB%20Bandung%20Barat,%20saya%20ingin%20berkonsultasi%20layanan%20UMKM." target="_blank" class="btn btn-success" style="border-radius: 8px; font-weight: 600;"><i class="bx bxl-whatsapp"></i> Hubungi via WhatsApp</a>
            <a href="index.php" class="btn btn-outline-primary" style="border-radius: 8px;"><i class="bx bx-arrow-back"></i> Kembali ke Beranda</a>
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
            <h4 style="font-size: 20px; font-weight: bold;">KATEGORI PRODUK</h4>
            <ul style="list-style: none; padding-left: 0; line-height: 2;">
              <li><i class="bx bx-chevron-right" style="color:#47b2e4;"></i> <a href="user-makanan.php">Makanan Khas KBB</a></li>
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
