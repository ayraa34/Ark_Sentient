<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../Config/config.php';

if (!isLoggedIn()) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Ternak - ARK Sentient</title>
    <link href="../../Asset/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success" href="home.php">
                <i class="fas fa-seedling me-2"></i>ARK Sentient
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Marketplace Ternak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="priksaternak.php">Pemeriksaan Ternak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../Views/smartasis.php">Smart Assistant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">History</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <?php
                            // Cart count
                            $cart_count = 0;
                            if (isLoggedIn()) {
                                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $cart_count = $stmt->fetchColumn();
                            }
                            if ($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php
                            $user_display = isset($_SESSION['full_name']) && $_SESSION['full_name'] !== ''
                                ? htmlspecialchars($_SESSION['full_name'])
                                : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User');
                            echo $user_display;
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-list me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">Periksa Kesehatan Hewanmu</h4>
                        <div class="mb-2 text-muted">Upload Photo/Video</div>
                        <div class="p-4 mb-4 border rounded text-center pemeriksaan-upload-bg">
                            <div class="fw-semibold mb-2">Upload a photo or video of your animal</div>
                            <div class="mb-3 pemeriksaan-desc">
                                Our system will automatically detect and highlight the face, nose, and eyes of the animal in the image or video.
                            </div>
                            <input type="file" id="mediaInput" accept="image/*,video/*" hidden>
                            <button class="btn btn-success" id="uploadMediaBtn">Upload Media</button>
                            <div id="mediaPreview" class="mt-3"></div>
                        </div>
                        <div class="p-4 border rounded text-center pemeriksaan-upload-bg">
                            <div class="mb-3 text-muted">Or, use your camera to capture a new image or video</div>
                            <button class="btn btn-light p-0 border-0 pemeriksaan-cam-btn" onclick="openCamera()" id="openCameraBtn">
                                <img src="../Asset/icon/Cam.png" alt="camera" class="pemeriksaan-cam-img"/>
                            </button>
                            <div id="cameraPreview" class="mt-3"></div>
                        </div>
                    </div>
                </div>
                <!-- Tambahkan tombol kembali ke home dengan margin -->
                <div class="d-flex justify-content-end mt-4 mb-3">
                    <a href="home.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openCamera() {
            // Sembunyikan tombol kamera saat diklik
            const camBtn = document.getElementById('openCameraBtn');
            if (camBtn) camBtn.style.display = 'none';

            const preview = document.getElementById('cameraPreview');
            preview.innerHTML = '';

            // Create video element
            const video = document.createElement('video');
            video.autoplay = true;
            video.className = 'camera-video-preview mb-2';
            preview.appendChild(video);

            // Create capture button
            const captureBtn = document.createElement('button');
            captureBtn.textContent = 'Capture Photo';
            captureBtn.className = 'btn btn-success mb-2';
            preview.appendChild(captureBtn);

            // Access webcam
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    video.srcObject = stream;

                    captureBtn.onclick = function() {
                        // Create canvas only in memory, not appended to DOM
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Stop video stream
                        stream.getTracks().forEach(track => track.stop());
                        video.style.display = 'none';
                        captureBtn.style.display = 'none';

                        // Show captured image (only once)
                        const img = document.createElement('img');
                        img.src = canvas.toDataURL('image/png');
                        img.className = 'img-fluid rounded mb-2 pemeriksaan-preview-img';
                        preview.appendChild(img);

                        // Optionally, send img.src (base64) to backend for AI check
                        // Example:
                        // sendCapturedImage(img.src);
                    };
                })
                .catch(function(err) {
                    preview.innerHTML = '<div class="alert alert-danger">Tidak dapat mengakses kamera: ' + err.message + '</div>';
                });
        }
        document.getElementById('uploadMediaBtn').onclick = function() {
            document.getElementById('mediaInput').click();
        };

        document.getElementById('mediaInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('mediaPreview');
            preview.innerHTML = '';
            if (!file) {
                alert('Silakan pilih file gambar atau video terlebih dahulu.');
                return;
            }
            // Preview image/video
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-fluid rounded mb-2 pemeriksaan-preview-img';
                preview.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.className = 'img-fluid rounded mb-2 pemeriksaan-preview-video';
                preview.appendChild(video);
            } else {
                alert('File harus berupa gambar atau video.');
                return;
            }

            // Tampilkan loading
            const loading = document.createElement('div');
            loading.textContent = 'Memproses dengan AI GPT...';
            loading.className = 'text-info my-2 pemeriksaan-loading';
            preview.appendChild(loading);

            // Kirim ke backend untuk AI detection
            const formData = new FormData();
            formData.append('media', file);
            fetch('../../Controllers/ai_cheack.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(function(data) {
                loading.remove();
                if (data && data.success) {
                    // Tampilkan hasil AI di bawah preview
                    const resultDiv = document.createElement('div');
                    resultDiv.className = 'alert alert-success mt-2';
                    resultDiv.textContent = 'Hasil AI GPT: ' + data.result;
                    preview.appendChild(resultDiv);
                } else if (data && data.message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.textContent = 'Gagal memproses gambar/video dengan AI: ' + data.message;
                    preview.appendChild(errorDiv);
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.textContent = 'Gagal memproses gambar/video dengan AI.';
                    preview.appendChild(errorDiv);
                }
            })
            .catch(function(error) {
                loading.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-2';
                errorDiv.textContent = 'Terjadi kesalahan saat mengirim ke AI.';
                preview.appendChild(errorDiv);
            });
        });
    </script>
</body>
</html>
        });
    </script>
</body>
</html>
