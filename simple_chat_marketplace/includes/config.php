<?php
// Try MySQL first, fallback to SQLite
try {
    // MySQL configuration
    $host = 'localhost';
    $db   = 'chat_marketplace';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Fallback to SQLite
    $sqlitePath = __DIR__ . '/../database/chat_marketplace.db';
    $pdo = new PDO("sqlite:$sqlitePath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    require_once __DIR__ . '/../database/setup_sqlite.php';
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('BASE_URL', 'http://localhost/simple_chat_marketplace/');
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
?>