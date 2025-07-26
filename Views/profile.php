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
    <style>
        body {
            background: #eafaf1;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .profile-card {
            max-width: 420px;
            margin: 40px auto;
            padding: 32px 24px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,128,64,0.10);
            border: 1px solid #b2e5c7;
            text-align: center;
        }
        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #d4f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            font-size: 2.8em;
            color: #218c4a;
        }
        .profile-card h2 {
            margin-bottom: 18px;
            color: #218c4a;
            letter-spacing: 1px;
        }
        .profile-info {
            margin-bottom: 16px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-info strong {
            display: inline-block;
            width: 110px;
            color: #218c4a;
        }
        .profile-info span {
            color: #222;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #218c4a;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #176c36;
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="profile-avatar">
            &#128100;
        </div>
        <h2>Profil Pengguna</h2>
        <div class="profile-info">
            <strong>Nama:</strong> <span><?php echo htmlspecialchars($user['full_name']); ?></span>
        </div>
        <div class="profile-info">
            <strong>Username:</strong> <span><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="profile-info">
            <strong>Email:</strong> <span><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <a class="back-link" href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
