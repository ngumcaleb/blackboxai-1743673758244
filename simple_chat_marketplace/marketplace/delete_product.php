<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit;
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Verify product belongs to user's shop
$stmt = $pdo->prepare("
    SELECT p.id 
    FROM products p
    JOIN shops s ON p.shop_id = s.id
    WHERE p.id = ? AND s.user_id = ?
");
$stmt->execute([$productId, $_SESSION['user_id']]);
$product = $stmt->fetch();

if ($product) {
    // Delete product
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
}

header('Location: manage_shop.php');
exit;
?>