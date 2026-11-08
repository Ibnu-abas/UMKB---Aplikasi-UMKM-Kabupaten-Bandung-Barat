<?php
// UMKB - Login Administrator
include("../koneksi.php");

$pesan = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
    $password = mysqli_real_escape_string($conn, trim($_POST["password"]));

    $query = "SELECT * FROM akun WHERE (email = '$username' OR nama_user = '$username') AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) >= 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['Level'] === 'admin') {
            echo '<script>window.location.href = "../admin-beranda.php";</script>';
            exit();
        } else {
            $pesan = "Anda bukan admin. Akun ini tidak memiliki hak akses administrator.";
        }
    } else {
        $pesan = "Username / Email atau Password salah. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Admin - UMKB Kabupaten Bandung Barat</title>
    <link rel="website icon" href="../assets/img/logoUnjuk.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <?php if (!empty($pesan)) { ?>
                <div class="notifikasi notifikasi-1">
                    <?php echo htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <div class="col-md-7 col-lg-5">
                <div class="login-wrap p-4 p-md-5">
                    <div class="text-center mb-3">
                        <img src="../assets/img/logoUnjuk.png" style="max-height: 80px;" alt="Logo UMKB">
                        <h4 class="mt-2 mb-0" style="font-weight: 700; color: #235088;">UMKB</h4>
                        <p class="text-muted small">Kabupaten Bandung Barat</p>
                    </div>

                    <form action="" class="login-form" method="POST">
                        <div class="form-group">
                            <input type="text" name="username" class="form-control rounded-left" placeholder="Email atau Username Admin" required>
                        </div>
                        <div class="form-group d-flex">
                            <input type="password" name="password" class="form-control rounded-left" placeholder="Password" required>
                        </div>
                        <div class="form-group" >
                            <button type="submit" class="form-control btn btn-primary rounded submit px-3">Masuk ke Panel Admin</button>
                        </div>
                    </form>

                    <div class="form-group text-center mt-3">
                        <p><a href="../index.php" style="color: #6c757d;"><i class="fa fa-arrow-left"></i> Kembali ke Beranda Portal</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="../assets/js/login.js"></script>

<!-- Redirect menggunakan JavaScript jika header tidak berfungsi -->
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('.login-form').addEventListener('submit', function(event) {
            // Matikan aksi formulir default
            event.preventDefault();
            // Redirect menggunakan JavaScript
            window.location.href = "../admin-beranda.php";
        });
    });
</script> -->

</body>
</html>
