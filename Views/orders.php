<?php
require_once '../Config/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}
// Ambil data pesanan user
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fitur pencarian sederhana
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id'], $search]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>My Orders - ARK Sentient</title>
    <link href="../Asset/css/dashboard.css" rel="stylesheet">
    <style>
        body {
            background: #eafaf1;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .orders-card {
            max-width: 700px;
            margin: 40px auto;
            padding: 32px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,128,64,0.10);
            border: 1px solid #b2e5c7;
        }
        .orders-card h2 {
            margin-bottom: 18px;
            text-align: center;
            color: #218c4a;
            letter-spacing: 1px;
        }
        .search-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }
        .search-bar input[type="text"] {
            padding: 7px 12px;
            border: 1px solid #b2e5c7;
            border-radius: 6px 0 0 6px;
            outline: none;
        }
        .search-bar button {
            padding: 7px 16px;
            background: #218c4a;
            color: #fff;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            font-weight: bold;
        }
        .search-bar button:hover {
            background: #176c36;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .orders-table th, .orders-table td {
            border: 1px solid #e0f5e9;
            padding: 10px 14px;
            text-align: left;
        }
        .orders-table th {
            background: #d4f5e9;
            color: #218c4a;
        }
        .orders-table tr:nth-child(even) {
            background: #f6fff9;
        }
        .status-icon {
            font-size: 1.2em;
            margin-right: 6px;
        }
        .status-proses { color: #f7b731; }
        .status-selesai { color: #218c4a; }
        .status-batal { color: #eb3b5a; }
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
    <div class="orders-card">
        <h2>Daftar Pesanan Saya</h2>
        <form class="search-bar" method="get">
            <input type="text" name="search" placeholder="Cari ID Pesanan..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Cari</button>
        </form>
        <?php if (empty($orders)): ?>
            <p style="text-align:center;color:#218c4a;">Belum ada pesanan.</p>
        <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <?php
                                    $status = strtolower($order['status']);
                                    if ($status === 'proses') {
                                        echo '<span class="status-icon status-proses">&#128336;</span> Proses';
                                    } elseif ($status === 'selesai') {
                                        echo '<span class="status-icon status-selesai">&#10004;</span> Selesai';
                                    } elseif ($status === 'batal') {
                                        echo '<span class="status-icon status-batal">&#10006;</span> Dibatalkan';
                                    } else {
                                        echo htmlspecialchars($order['status']);
                                    }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a class="back-link" href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
