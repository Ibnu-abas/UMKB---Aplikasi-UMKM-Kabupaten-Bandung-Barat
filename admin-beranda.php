<?php
  include "koneksi.php";
  
  // Total UMKM
  $total_umkm = 0;
  $query = "SELECT COUNT(*) AS total_umkm FROM umkm";
  $result = @mysqli_query($conn, $query);
  if ($result && $row = mysqli_fetch_assoc($result)) {
      $total_umkm = $row['total_umkm'];
  }
  
  // Total Produk
  $total_produk = 0;
  $query = "SELECT COUNT(*) AS total_produk FROM produk";
  $result = @mysqli_query($conn, $query);
  if ($result && $row = mysqli_fetch_assoc($result)) {
      $total_produk = $row['total_produk'];
  }

  // Daftar 16 Kecamatan di Kabupaten Bandung Barat
  $daftar_kecamatan = [
      'Batujajar', 'Cihampelas', 'Cikalongwetan', 'Cililin',
      'Cipatat', 'Cipeundeuy', 'Cipongkor', 'Cisarua',
      'Gununghalu', 'Lembang', 'Ngamprah', 'Padalarang',
      'Parongpong', 'Rongga', 'Saguling', 'Sindangkerta'
  ];

  // Ambil jumlah UMKM per kecamatan dalam 1 query efisien
  $kec_counts = [];
  $qKec = @mysqli_query($conn, "SELECT kecamatan_umkm, COUNT(*) as jml FROM umkm GROUP BY kecamatan_umkm");
  if ($qKec) {
      while ($rKec = mysqli_fetch_assoc($qKec)) {
          $kec_counts[strtolower(trim($rKec['kecamatan_umkm']))] = (int)$rKec['jml'];
      }
  }
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Dashboard Admin - UMKB Kabupaten Bandung Barat</title>

  

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">
    <link rel="stylesheet" href="https://boxicons.com/css/boxicons.min.css">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
    <link rel="website icon" href="assets/img/logoUnjuk.png">

    <!-- Bootstrap core CSS -->
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }


    </style>

    
    <!-- template tabel data-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

      <script defer src="https://code.jquery.com/jquery-3.7.0.js"></script>
      <script defer src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
      <script defer src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
      <script defer src="assets/js/tabel-daftar.js"></script>
    <!-- <link href="assets/dashboard.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/dashboard.css?v=<?php echo time(); ?>">
  </head>
  <body>
    
      <header class="navbar navbar-dark sticky-top flex-md-nowrap p-0 shadow" style="background-color: #235088;">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-center" style="background-color: #235088; font-size: 20px; font-weight: bold; line-height: 1.2; padding: 12px 15px;"
        href="admin-beranda.php">UMKB <br><span style="font-size: 11px; font-weight: normal; opacity: 0.9;">Kabupaten Bandung Barat</span></a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        
      </header>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse  mt-3" > 
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active"  style="font-size: 18px;"  aria-current="page" href="#" >
            <i class="bx bx-home"></i>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"  style="font-size: 18px;" href="admin-warkopUmi.php">
            <i class='bx bx-upload' ></i>
              Update Warkop Umi
            </a>
          <li class="nav-item">
            <a class="nav-link" style="font-size: 18px;" href="#" onclick="konfirmasiKeluar()">
            <i class='bx bx-log-out' ></i>
              Keluar
            </a>
          </li>
        </ul>

        
      </div>
    </nav>
    <!-- ##################### pendataan kotak  ####################-->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <h2 class="mt-4 mb-4">Dashboard</h2>
      <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card bg-light">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title" style="color: black; font-weight: bold;">UMKM</h5>
                        <p class="card-text" style="color: black; font-size: 18px;"><?php echo $total_umkm; ?></p>
                        <hr style="border-top: 1px solid black; margin-top: 10px;">
                        <p class="card-text" style="color: black; font-size: 15px;">Total UMKM Terdaftar</p>
                    </div>
                    <i class='bx bxs-store bx-lg'></i> 
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card bg-light">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title" style="color: black; font-weight: bold;">PRODUK</h5>
                        <p class="card-text" style="color: black; font-size: 18px;"><?php echo $total_produk?></p>
                        <hr style="border-top: 1px solid black; margin-top: 10px;">
                        <p class="card-text" style="color: black; font-size: 15px;">Total Produk Terdaftar</p>
                    </div>
                    <i class='bx bxs-box bx-lg'></i> <!-- Ganti dengan ikon Boxicons sesuai kebutuhan -->
                </div>
            </div>
        </div>
    </div>
      <br>
      <br>
      <!-- ############# selesai pendataan kotak ################## -->

      <!-- ############# Tabel kecamataan ####################### -->
      <h3 class="title mt-3">Total UMKM Per Kecamatan (Kabupaten Bandung Barat)</h3>
      <div class="table-responsive">
      <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 70px;">No.</th>
                    <th>Nama Kecamatan</th>
                    <th>Jumlah UMKM Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($daftar_kecamatan as $kec):
                    $slug = strtolower($kec);
                    $count = $kec_counts[$slug] ?? 0;
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <a href="daftar-kecamatan/daftar-perkecamatan.php?kecamatan=<?php echo urlencode($kec); ?>" style="text-decoration: none; font-weight: 500;">
                            <i class='bx bx-map-pin text-primary'></i> <?php echo htmlspecialchars($kec); ?>
                        </a>
                    </td>
                    <td>
                        <span class="badge <?php echo $count > 0 ? 'bg-primary' : 'bg-secondary'; ?>">
                            <?php echo $count; ?> UMKM
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>

      <!-- ################ selesai tabel kecamatan ####################### -->

    </main>
  </div>
</div>


    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>

    <!-- digunakan untuk animasi keluar -->
    <script >
      function konfirmasiKeluar() {
        Swal.fire({
          title: 'Konfirmasi Keluar',
          text: 'Apakah Anda yakin ingin keluar?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, Keluar!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "tem-login/tem-login-admin.php";
          }
        });
      }
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- untuk mengambil data umkm -->
    <script>
            function updateTotalUMKM() {
          // Lakukan pengambilan data dengan AJAX
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (xhttp.readyState === 4 && xhttp.status === 200) {
              var totalUMKM = xhttp.responseText;
              document.getElementById('total-umkm').textContent = totalUMKM;
            }
          };
          xhttp.open('GET', 'admin-beranda.php', true); // Ganti "ambil_total_umkm.php" dengan skrip yang sesuai
          xhttp.send();
        }

        // Perbarui total UMKM 
        setInterval(updateTotalUMKM, 30000); // 30000 milidetik = 30 detik
    </script>

    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
    
  </body>
</html>
