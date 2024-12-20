<?php
session_start();

// Jika pengguna sudah masuk, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Ambil data pengguna dari database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set sesi pengguna
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];

            // Redirect ke dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Email atau kata sandi salah.";
        }
    } else {
        $error = "Email atau kata sandi salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Text To Image AI</title>
    <link rel="icon" href="assets/image/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-cube me-2"></i>Image To Text AI</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center align-items-center">
            <!-- Login Form Column -->
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-white text-center py-4">
                        <h3 class="mb-0 fw-bold text-primary">Selamat Datang Kembali</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="index.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted"><i class="fas fa-envelope me-2"></i>Alamat Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" id="email" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label text-muted"><i class="fas fa-lock me-2"></i>Kata Sandi</label>
                                <input type="password" name="password" class="form-control form-control-lg" id="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0 text-muted">Belum punya akun? 
                                <a href="signup.php" class="text-primary fw-bold text-decoration-none">Daftar di sini</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Column -->
            <div class="col-md-6 col-lg-5 mt-4 mt-md-0">
                <h4 class="text-primary mb-4"><i class="fas fa-star me-2"></i>Keunggulan Aplikasi</h4>
                
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-shield-alt text-primary me-2"></i>Keamanan Tinggi</h5>
                        <p class="card-text text-muted">Sistem keamanan berlapis dengan enkripsi data terbaru.</p>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-tachometer-alt text-primary me-2"></i>Performa Cepat</h5>
                        <p class="card-text text-muted">Optimasi sistem untuk kecepatan dan efisiensi maksimal.</p>
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-mobile-alt text-primary me-2"></i>Responsif</h5>
                        <p class="card-text text-muted">Tampilan yang responsif di semua perangkat.</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clock text-primary me-2"></i>24/7 Support</h5>
                        <p class="card-text text-muted">Dukungan teknis 24 jam untuk semua pengguna.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white py-4 mt-5">
        <div class="container">
            <div class="text-center text-muted">
                <p class="mb-2">&copy; 2024 Image To Text AI. All rights reserved.</p>
                <div class="developers">
                    <h6 class="text-primary mb-3"><i class="fas fa-code me-2"></i>Dikembangkan oleh:</h6>
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-2">
                            <div class="developer-info">
                                <p class="mb-0 fw-bold">Rusni Mawarni</p>
                                <small class="text-muted">NIM: 22076020</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="developer-info">
                                <p class="mb-0 fw-bold">Selvia Permata Sari</p>
                                <small class="text-muted">NIM: 22076022</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>