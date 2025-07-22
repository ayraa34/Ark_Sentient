<?php
require_once '../Config/config.php';

if (!isLoggedIn()) {
    redirectTo('index.php');
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, l.name, l.breed, l.price, l.age_months, l.weight_kg, l.location
    FROM cart c 
    JOIN livestock l ON c.livestock_id = l.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    redirectTo('cart.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 200000;
$tax_rate = 0.11;
$tax_amount = $subtotal * $tax_rate;
$discount_rate = 0.05;
$discount_amount = $subtotal * $discount_rate;
$total = $subtotal + $shipping_cost + $tax_amount - $discount_amount;

// Handle form submission
$order_success = false;
$order_number = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $payment_method = sanitizeInput($_POST['payment_method']);
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    try {
        $pdo->beginTransaction();
        
        // Create order
        $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, shipping_cost, tax_amount, 
                              discount_amount, final_amount, payment_method, shipping_address, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'], $order_number, $subtotal, $shipping_cost, 
            $tax_amount, $discount_amount, $total, $payment_method, $shipping_address, $notes
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Add order items
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, livestock_id, quantity, unit_price, total_price)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id, $item['livestock_id'], $item['quantity'], 
                $item['price'], $item['price'] * $item['quantity']
            ]);
        }
        
        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $pdo->commit();

        // Set flag for order summary
        $order_success = true;

        // Optionally, you can comment out the redirect if you want to show summary here
        // redirectTo('payment.php?order=' . $order_number);

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to create order: ' . $e->getMessage();
    }
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ARK Sentient</title>
    <link href="../Asset/css/checkout.css" rel="stylesheet">
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
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i><?php echo $_SESSION['full_name']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4">Checkout</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($order_success): ?>
                    <div class="alert alert-success">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Order Placed Successfully!</h4>
                        <p>Your order <strong><?php echo htmlspecialchars($order_number); ?></strong> has been placed.</p>
                        <hr>
                        <h5>Order Summary</h5>
                        <ul>
                            <li><strong>Order Number:</strong> <?php echo htmlspecialchars($order_number); ?></li>
                            <li><strong>Total:</strong> <?php echo formatRupiah($total); ?></li>
                            <li><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method); ?></li>
                            <li><strong>Shipping Address:</strong> <?php echo htmlspecialchars($shipping_address); ?></li>
                        </ul>
                        <a href="dashboard.php" class="btn btn-success mt-3">Back to Marketplace</a>
                        <a href="orders.php" class="btn btn-outline-primary mt-3">View My Orders</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <!-- Shipping Information -->
                        <div class="checkout-step">
                            <h5 class="mb-3"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea class="form-control" rows="3" name="shipping_address" required 
                                          placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" rows="2" name="notes" 
                                          placeholder="Special instructions or notes for delivery"></textarea>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-step">
                            <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h5>
                            
                            <div class="payment-method" onclick="selectPayment('qris')">
                                <input type="radio" name="payment_method" value="qris" id="qris" required>
                                <label for="qris" class="ms-2">
                                    <i class="fas fa-qrcode me-2"></i>QRIS
                                    <small class="text-muted d-block">Pay with QR Code</small>
                                </label>
                            </div>
                            
                            <div class="payment-method" onclick="selectPayment('bank_transfer')">
                                <input type="radio" name="payment_method" value="bank_transfer" id="bank_transfer" required>
                                <label for="bank_transfer" class="ms-2">
                                    <i class="fas fa-university me-2"></i>Bank Transfer
                                    <small class="text-muted d-block">Transfer to our bank account</small>
                                </label>
                            </div>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-place-order btn-success btn-lg w-100">
                            <i class="fas fa-shopping-bag me-2"></i>Place Order - <?php echo formatRupiah($total); ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <?php if (!$order_success): ?>
                <div class="order-summary">
                    <h5 class="mb-3">Order Summary</h5>
                    
                    <!-- Items -->
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="d-flex justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($item['breed']); ?> • 
                                        <?php echo $item['age_months']; ?> months • 
                                        <?php echo $item['weight_kg']; ?> kg
                                    </small>
                                    <div class="text-muted small">Qty: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="text-end">
                                    <strong><?php echo formatRupiah($item['price'] * $item['quantity']); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
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
                            <span>Discount (New Member) (5%)</span>
                            <span>-<?php echo formatRupiah($discount_amount); ?></span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <span>Total</span>
                            <span><?php echo formatRupiah($total); ?></span>
                        </div>
                    </div>
                    
                    <!-- Security Badge -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure & encrypted payment
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>ARK Sentient</h5>
                    <p class="text-muted">Your trusted livestock marketplace</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">&copy; 2024 ARK Sentient. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(pm => {
                pm.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }
        
        // Auto-select payment method when radio button is clicked
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-method').forEach(pm => {
                    pm.classList.remove('selected');
                });
                this.closest('.payment-method').classList.add('selected');
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const shippingAddress = document.querySelector('[name="shipping_address"]').value.trim();
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (!shippingAddress) {
                e.preventDefault();
                alert('Please enter your shipping address');
                return;
            }
            
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('[name="place_order"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>