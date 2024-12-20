<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'includes/config.php';

// Kredensial Azure Vision OCR
$subscriptionKey = 'FgRf4Fwgq06TC1UtMMY4elHRP0ZToaG7JfzHppklbSUTiCTwRgKlJQQJ99ALACqBBLyXJ3w3AAAFACOGZ3df';
$endpoint = 'https://webocr.cognitiveservices.azure.com/';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        $file_tmp  = $file['tmp_name'];
        $file_name = basename($file['name']);
        $target_path = "uploads/" . $file_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $imageData = file_get_contents($target_path);
            $url = $endpoint . '/vision/v3.2/ocr?language=unk&detectOrientation=true';
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/octet-stream',
                'Ocp-Apim-Subscription-Key: ' . $subscriptionKey,
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $imageData);

            $response = curl_exec($ch);

            if ($response === false) {
                $error = 'Error: ' . curl_error($ch);
                $status = 'error';
            } else {
                $responseData = json_decode($response, true);
                $extractedText = '';
                if (isset($responseData['regions'])) {
                    foreach ($responseData['regions'] as $region) {
                        foreach ($region['lines'] as $line) {
                            foreach ($line['words'] as $word) {
                                $extractedText .= $word['text'] . ' ';
                            }
                            $extractedText .= "\n";
                        }
                    }
                    $status = 'success';
                } else {
                    $extractedText = 'Tidak ada teks yang terdeteksi.';
                    $status = 'warning';
                }
            }

            curl_close($ch);

        } else {
            $error = 'Gagal memindahkan file yang diunggah.';
            $status = 'error';
        }
    } else {
        $error = 'Terjadi kesalahan saat mengunggah file.';
        $status = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil OCR - WebApp Pro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .result-card {
            opacity: 0;
            transform: translateY(20px);
            animation: slideIn 0.5s ease forwards;
        }
        
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .copy-btn {
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            transform: translateY(-2px);
        }
        
        .status-badge {
            animation: bounce 1s ease;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }
        
        .text-content {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .btn-back {
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            transform: translateX(-5px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-cube me-2"></i>WebApp Pro</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm result-card">
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?>
                            <div class="text-center mb-4">
                                <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                                <h4 class="text-danger">Terjadi Kesalahan</h4>
                                <div class="alert alert-danger animate__animated animate__fadeIn">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center mb-4">
                                <?php if ($status === 'success'): ?>
                                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                    <h4 class="text-success">Berhasil Memproses Gambar</h4>
                                <?php elseif ($status === 'warning'): ?>
                                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                    <h4 class="text-warning">Peringatan</h4>
                                <?php endif; ?>
                            </div>
                            
                            <div class="status-badge text-center mb-4">
                                <span class="badge bg-<?php echo $status === 'success' ? 'success' : 'warning'; ?> p-2">
                                    <i class="fas fa-<?php echo $status === 'success' ? 'check' : 'exclamation'; ?>-circle me-2"></i>
                                    <?php echo $status === 'success' ? 'Teks Berhasil Diekstrak' : 'Tidak Ada Teks Terdeteksi'; ?>
                                </span>
                            </div>

                            <div class="card bg-light">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Hasil Ekstraksi Teks</h5>
                                    <button class="btn btn-sm btn-primary copy-btn" onclick="copyText()">
                                        <i class="fas fa-copy me-2"></i>Salin Teks
                                    </button>
                                </div>
                                <div class="card-body text-content">
                                    <pre class="mb-0" id="extractedText"><?php echo htmlspecialchars($extractedText); ?></pre>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-primary btn-lg btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
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
        function copyText() {
            const text = document.getElementById('extractedText').innerText;
            navigator.clipboard.writeText(text).then(() => {
                const copyBtn = document.querySelector('.copy-btn');
                copyBtn.innerHTML = '<i class="fas fa-check me-2"></i>Tersalin!';
                copyBtn.classList.add('btn-success');
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fas fa-copy me-2"></i>Salin Teks';
                    copyBtn.classList.remove('btn-success');
                }, 2000);
            });
        }

        // Add smooth scroll animation
        document.querySelector('.btn-back').addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            document.body.style.opacity = '0';
            setTimeout(() => {
                window.location.href = href;
            }, 500);
        });
    </script>
</body>
</html>