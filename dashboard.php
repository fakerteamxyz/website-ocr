<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Image To Text AI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .nav-link:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .upload-area.dragover {
            background-color: #e9ecef;
            border-color: #0d6efd;
        }
        .stat-card {
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .loading-spinner {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-cube me-2"></i>Image To Text AI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-history me-2"></i>Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate__animated animate__fadeIn">
                    <div class="card-body">
                        <h3 class="card-title text-primary">
                            <i class="fas fa-user-circle me-2"></i>
                            Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                        </h3>
                        <p class="card-text text-muted">
                            Mulai unggah gambar Anda untuk diproses dengan OCR.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm stat-card" style="animation-delay: 0.1s">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="fas fa-image me-2"></i>Total Gambar</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm stat-card" style="animation-delay: 0.2s">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="fas fa-check-circle me-2"></i>Berhasil</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm stat-card" style="animation-delay: 0.3s">
                    <div class="card-body">
                        <h5 class="card-title text-warning"><i class="fas fa-clock me-2"></i>Pending</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm stat-card" style="animation-delay: 0.4s">
                    <div class="card-body">
                        <h5 class="card-title text-danger"><i class="fas fa-exclamation-circle me-2"></i>Gagal</h5>
                        <h3 class="mb-0">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">
                            <i class="fas fa-cloud-upload-alt text-primary me-2"></i>
                            Unggah Gambar untuk OCR
                        </h4>
                        <form action="process_image.php" method="post" enctype="multipart/form-data" id="uploadForm">
                            <div class="upload-area mb-3" id="dropZone">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                <h5>Seret & Lepas Gambar di Sini</h5>
                                <p class="text-muted">atau</p>
                                <input type="file" name="image" id="image" class="d-none" accept="image/*" required>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('image').click()">
                                    <i class="fas fa-folder-open me-2"></i>Pilih File
                                </button>
                            </div>
                            <div id="preview" class="text-center mb-3" style="display: none;">
                                <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                <p class="mb-0" id="fileName"></p>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="spinner-border spinner-border-sm loading-spinner me-2"></span>
                                    <i class="fas fa-cogs me-2"></i>Unggah dan Proses
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 mt-5">
        <div class="container">
            <div class="text-center text-muted">
                <p class="mb-2">&copy; 2024 WebApp Pro. All rights reserved.</p>
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
    <script>
        // File Upload Preview
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('preview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and Drop
        const dropZone = document.getElementById('dropZone');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const fileInput = document.getElementById('image');
            fileInput.files = files;
            
            if (files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('fileName').textContent = files[0].name;
                    document.getElementById('preview').style.display = 'block';
                }
                reader.readAsDataURL(files[0]);
            }
        }

        // Form Submit Animation
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            const spinner = button.querySelector('.loading-spinner');
            const icon = button.querySelector('.fas');
            
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            button.disabled = true;
        });

        // Stats Animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 100);
            });
        });
    </script>
</body>
</html>