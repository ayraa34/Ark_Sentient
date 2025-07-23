<?php
require_once '../Config/config.php';


// Get livestock data
$stmt = $pdo->prepare("
    SELECT l.*, c.name as category_name, u.full_name as farmer_name 
    FROM livestock l 
    JOIN categories c ON l.category_id = c.id 
    JOIN users u ON l.farmer_id = u.id 
    WHERE l.status = 'available'
    ORDER BY l.created_at DESC
    LIMIT 12
");
$stmt->execute();
$livestock = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart count
$cart_count = 0;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARK Sentient - Marketplace Ternak</title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success" href="dashboard.php">
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
                        <a class="nav-link" href="#">Pemeriksaan Ternak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Smart Assistant</a>
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
                            <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
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

    <!-- Toast for notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-shopping-cart text-success me-2"></i>
                <strong class="me-auto">Cart</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Item berhasil ditambahkan ke keranjang!
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="px-3">
                    <h6 class="text-muted mb-3">Marketplace Farm</h6>
                    <nav class="nav flex-column">
                        <?php foreach ($categories as $category): ?>
                            <a class="nav-link" href="category.php?id=<?php echo $category['id']; ?>">
                                <i class="fas fa-cow"></i><?php echo $category['name']; ?>
                            </a>
                        <?php endforeach; ?>
                        <a class="nav-link" href="../Views/cart.php">
                            <i class="fas fa-shopping-cart"></i>Cart
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <!-- Hero Section -->
                <div class="hero-section">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="display-4 fw-bold mb-3">ARK Sentient</h1>
                                <p class="lead">Aplikasi pintar berbasis AI yang dirancang khusus untuk membantu peternak memelihara kesehatan ternak, mengelola data ternak secara digital, serta menyediakan marketplace dalam satu sistem yang terintegrasi.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-cow fa-5x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="container mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" placeholder="Search livestock...">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Livestock Grid -->
                <div class="container">
                    <h3 class="mb-4">Available Livestock</h3>
                    <div class="row">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(livestockId) {
            fetch('../Controllers/add_to_cart.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'livestock_id=' + livestockId
            })
            .then(response => response.json())
            .then(function(data) {
                if (data.success) {
                    // Show toast notification
                    const toastElement = document.getElementById('cartToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                    
                    // Update cart count badge
                    updateCartBadge(data.cart_count);
                } else {
                    alert(data.message || 'Gagal menambahkan ke keranjang');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan ke keranjang');
            });
        }

        function updateCartBadge(count) {
            const cartLink = document.querySelector('.nav-link.position-relative');
            let badge = cartLink.querySelector('.badge');
            
            if (count > 0) {
                if (!badge) {
                    // Create badge if it doesn't exist
                    badge = document.createElement('span');
                    badge.className = "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
                    cartLink.appendChild(badge);
                }
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>