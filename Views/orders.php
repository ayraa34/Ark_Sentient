<?php
require_once '../Config/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
// Ambil data pesanan user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>My Orders - ARK Sentient</title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <h2>My Orders</h2>
    <?php if (empty($orders)): ?>
        <p>Belum ada pesanan.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($orders as $order): ?>
                <li>
                    Order #<?php echo $order['id']; ?> - <?php echo $order['status']; ?> - <?php echo $order['created_at']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
