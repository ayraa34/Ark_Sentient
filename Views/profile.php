<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}
// Ambil data user dari database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profile - ARK Sentient</title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <h2>Profile</h2>
    <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
