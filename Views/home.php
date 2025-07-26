<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Inisialisasi cart_count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    require_once '../Config/config.php';
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Home Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../Asset/css/home.css" rel="stylesheet">
    <style>
        /* Dropdown menu always on top */
        .navbar .dropdown-menu {
            z-index: 1050;
            position: absolute;
        }
    </style>
</head>
<body>
    <?php
    // PHP variables for dynamic content
    $company_name = "ARK Sentient";
    $tagline = "Smart Livestock Management Solution";
    $hero_description = "ARK Sentient adalah aplikasi pintar berbasis AI yang dirancang khusus untuk membantu peternak memantau kesehatan ternak, mengelola pakan secara otomatis, mendeteksi penyakit lebih awal dengan teknologi computer vision, dan memberikan rekomendasi berbasis data untuk meningkatkan produktivitas peternakan modern yang dapat digunakan langsung oleh peternak untuk meningkatkan produksi susu, daging dan kesehatan ternak lainnya.";
    
    $features = [
        [
            'icon' => 'fas fa-store',
            'title' => 'Marketplace Ternak',
            'description' => 'Platform jual beli ternak yang aman dan terpercaya dengan sistem verifikasi lengkap'
        ],
        [
            'icon' => 'fas fa-pills',
            'title' => 'Pemeriksaan Ternak',
            'description' => 'Sistem AI untuk menentukan jenis ternak terbaik sesuai dengan kondisi dan kebutuhan Anda'
        ],
        [
            'icon' => 'fas fa-utensils',
            'title' => 'Smart Feeding Assistant',
            'description' => 'Asisten pintar untuk mengatur jadwal pemberian pakan yang optimal dan efisien'
        ]
    ];
    
    $nav_items = [
        'Marketplace Ternak' => '#marketplace',
        'Pemeriksaan Ternak' => '#penentuan', 
        'Smart Assistant' => '#assistant',
        'History' => '#history'
    ];
    ?>

    <!-- Navigation -->
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
                        <a class="nav-link" href="../Views/priksaternak.php">Pemeriksaan Ternak</a>
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
                            <?php if ($cart_count > 0): ?>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1 class="hero-title"><?php echo $company_name; ?></h1>
                        <p class="hero-description">
                            <?php echo $hero_description; ?>
                        </p>
                        <div class="mt-4">
                            <button class="btn btn-custom btn-lg me-3">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-rocket me-2"></i>Mulai Sekarang
                            </a>    
                            </button>
                            <button class="btn btn-outline-light btn-lg">
                                <i class="fas fa-play me-2"></i>Lihat Demo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Fitur Utama</h2>
            <div class="row">
                <?php foreach($features as $feature): ?>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="<?php echo $feature['icon']; ?>"></i>
                        </div>
                        <h3 class="feature-title"><?php echo $feature['title']; ?></h3>
                        <p class="feature-description"><?php echo $feature['description']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5" style="background: #2c5530; color: white;">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold">1000+</h2>
                    <p>Peternak Aktif</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold">50k+</h2>
                    <p>Ternak Terpantau</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold">95%</h2>
                    <p>Akurasi AI</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h2 class="display-4 fw-bold">24/7</h2>
                    <p>Monitoring</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo $company_name; ?></h5>
                    <p>Solusi pintar untuk peternakan modern Indonesia</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Tentang Kami</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Kontak</a></li>
                        <li><a href="#" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Support</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Layanan</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Monitoring Ternak</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Smart Feeding</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Marketplace</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Konsultasi</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $company_name; ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk peternak Indonesia</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255,255,255,0.98)';
            } else {
                navbar.style.background = 'rgba(255,255,255,0.95)';
            }
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>