<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit();
}

// Get all products with shop info
$stmt = $pdo->prepare("
    SELECT p.*, s.shop_name, u.username 
    FROM products p
    JOIN shops s ON p.shop_id = s.id
    JOIN users u ON s.user_id = u.id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - ChatMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold text-blue-600">ChatMarket</a>
            <div class="flex items-center space-x-4">
                <a href="../chat/chat.php" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-comment-dots"></i>
                </a>
                <a href="../user/profile.php" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-user"></i>
                </a>
                <a href="../user/logout.php" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Marketplace</h1>
            <a href="add_product.php" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Product
            </a>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                <div class="h-48 bg-gray-200 overflow-hidden">
                    <img src="../assets/images/<?= $product['image_url'] ?>" 
                         alt="<?= htmlspecialchars($product['title']) ?>" 
                         class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg"><?= htmlspecialchars($product['title']) ?></h3>
                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($product['shop_name']) ?></p>
                        </div>
                        <span class="font-bold text-blue-600">$<?= number_format($product['price'], 2) ?></span>
                    </div>
                    <p class="text-gray-700 mt-2 text-sm"><?= htmlspecialchars($product['description']) ?></p>
                    <div class="mt-4 flex justify-between items-center">
                        <a href="view_product.php?id=<?= $product['id'] ?>" 
                           class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-sm transition">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </a>
                        <a href="../chat/chat.php?contact=<?= $product['username'] ?>" 
                           class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-200">
                            <i class="fas fa-comment-dots mr-1"></i> Message Seller
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>