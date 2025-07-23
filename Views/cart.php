<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isLoggedIn()) {
    redirectTo('index.php');
}

// Get cart items
$stmt = $conn->prepare("
    SELECT c.*, l.name, l.breed, l.price, l.age_months, l.weight_kg, l.location
    FROM cart c 
    JOIN livestock l ON c.livestock_id = l.id 
    WHERE c.user_id = ?
    ORDER BY c.added_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 200000; // Fixed shipping cost
$tax_rate = 0.11; // 11% tax
$tax_amount = $subtotal * $tax_rate;
$discount_rate = 0.05; // 5% discount for new members
$discount_amount = $subtotal * $discount_rate;
$total = $subtotal + $shipping_cost + $tax_amount - $discount_amount;

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $cart_id = (int)$_POST['cart_id'];
        $new_quantity = (int)$_POST['quantity'];
        
        if ($new_quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$new_quantity, $cart_id, $_SESSION['user_id']]);
        } else {
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
        }
        
        redirectTo('cart.php');
    }
    
    if (isset($_POST['remove_item'])) {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $_SESSION['user_id']]);
        
        redirectTo('cart.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ARK Sentient</title>
    <link href="../Asset/css/cart.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['full_name']; ?>
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

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="px-3">
                    <h6 class="text-muted mb-3">Marketplace Farm</h6>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-cow"></i>Cow
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-horse"></i>Goat
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-kiwi-bird"></i>Chicken
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-fish"></i>Seafood
                        </a>
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>Cart
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="mb-4">Shopping Cart</h3>
                            
                            <?php if (empty($cart_items)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Your cart is empty</h5>
                                    <a href="dashboard.php" class="btn btn-primary mt-3">Continue Shopping</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="cart-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 100px; border-radius: 8px;">
                                                    <i class="fas fa-cow fa-2x text-muted"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                <p class="text-muted mb-1">
                                                    <small>Breed: <?php echo htmlspecialchars($item['breed']); ?></small><br>
                                                    <small>Age: <?php echo $item['age_months']; ?> months</small><br>
                                                    <small>Weight: <?php echo $item['weight_kg']; ?> kg</small><br>
                                                    <small>Location: <?php echo htmlspecialchars($item['location']); ?></small>
                                                </p>
                                                <p class="mb-0">
                                                    <strong class="text-success"><?php echo formatRupiah($item['price']); ?></strong>
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quantity-control">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                                        <button type="submit" name="update_quantity" class="quantity-btn">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </form>
                                                    <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                                        <button type="submit" name="update_quantity" class="quantity-btn">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <p class="mb-2 fw-bold"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></p>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($cart_items)): ?>
                            <div class="col-md-4">
                                <div class="summary-card">
                                    <h5 class="mb-3">Order Summary</h5>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span><?php echo formatRupiah($subtotal); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span><?php echo formatRupiah($shipping_cost); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Taxes (11%)</span>
                                        <span><?php echo formatRupiah($tax_amount); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Discount (New Member - 5%)</span>
                                        <span>-<?php echo formatRupiah($discount_amount); ?></span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong class="text-success"><?php echo formatRupiah($total); ?></strong>
                                    </div>
                                    
                                    <a href="checkout.php" class="btn btn-checkout btn-success w-100">
                                        Proceed to Payment
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>