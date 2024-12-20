<?php
// Mulai sesi
session_start();

// Sertakan file konfigurasi
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan pengguna ke database
    $sql = "INSERT INTO users (username, email, password)
            VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // Redirect ke halaman login
        header('Location: index.php');
        exit();
    } else {
        $error = 'Error: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pengguna</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Animasi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* Latar belakang dan styling */
        body {
            background: url('assets/img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            animation: fadeInUp 1s ease;
        }
        .card-header {
            background: #0d6efd;
            color: #fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card animate__animated animate__fadeInDown">
                <div class="card-header">
                    <h2 class="mb-0">Daftar Pengguna</h2>
                </div>
                <div class="card-body animate__animated animate__fadeInUp">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form action="signup.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Pengguna</label>
                            <input type="text" name="username" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>
                    <p class="mt-3 text-center">
                        Sudah punya akun? <a href="index.php">Masuk di sini</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>