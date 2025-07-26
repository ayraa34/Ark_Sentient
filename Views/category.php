<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../Config/config.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data kategori
$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Kategori tidak ditemukan.</div></div>";
    exit;
}

// Ambil produk berdasarkan kategori
$stmt = $conn->prepare("
    SELECT l.*, u.full_name as farmer_name 
    FROM livestock l 
    JOIN users u ON l.farmer_id = u.id 
    WHERE l.category_id = ? AND l.status = 'available'
    ORDER BY l.created_at DESC
");
$stmt->execute([$category_id]);
$livestock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk Kategori - <?php echo htmlspecialchars($category['name']); ?></title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
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

    <div class="container mt-4">
        <h2 class="mb-4">Category: <?php echo htmlspecialchars($category['name']); ?></h2>
        <div class="row">
            <?php if (count($livestock) === 0): ?>
                <div class="col-12">
                    <div class="alert alert-warning">Tidak ada produk pada kategori ini.</div>
                </div>
            <?php else: ?>
                <?php foreach ($livestock as $animal): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card livestock-card">
                            <div class="livestock-image card-img-top">
                                <i class="fas fa-cow"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                                <p class="text-muted mb-2">
                                    <small>Age: <?php echo $animal['age_months']; ?> months</small><br>
                                    <small>Weight: <?php echo $animal['weight_kg']; ?> kg</small><br>
                                    <small>Breed: <?php echo htmlspecialchars($animal['breed']); ?></small>
                                </p>
                                <p class="mb-2">
                                    <span class="badge badge-location">
                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($animal['location']); ?>
                                    </span>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-tag"><?php echo formatRupiah($animal['price']); ?></span>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-primary btn-sm me-2" onclick="addToCart(<?php echo $animal['id']; ?>)">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                    <a href="livestock_detail.php?id=<?php echo $animal['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        See More...
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Marketplace</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(livestockId) {
            fetch('../Controllers/add_to_cart.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ livestock_id: livestockId }).toString()
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
                return data;
            })
            .then(function(data) {
                if (data.success) {
                    alert('Berhasil ditambahkan ke keranjang!');
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan ke keranjang: ' + error.message);
            });
        }
    </script>
</body>
</html>
