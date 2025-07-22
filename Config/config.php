<?php
// config.php - Database Configuration
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'ark_sentient');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function redirectTo($url) {
    header("Location: $url");
    exit();
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Base URL
define('BASE_URL', 'http://localhost/ark_sentient/');
?>