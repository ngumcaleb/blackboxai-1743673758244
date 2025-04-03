<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit;
}

// Get user's shop and products
$stmt = $pdo->prepare("
    SELECT s.*, COUNT(p.id) as product_count 
    FROM shops s
    LEFT JOIN products p ON s.id = p.shop_id
    WHERE s.user_id = ?
    GROUP BY s.id
");
$stmt->execute([$_SESSION['user_id']]);
$shop = $stmt->fetch();

if (!$shop) {
    header('Location: create_shop.php');
    exit;
}

// Get shop products
$stmt = $pdo->prepare("SELECT * FROM products WHERE shop_id = ?");
$stmt->execute([$shop['id']]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shop - ChatMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold text-green-600">ChatMarket</a>
            <div class="flex items-center space-x-4">
                <a href="../chat/chat.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-comment-dots"></i>
                </a>
                <a href="../user/profile.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-user"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Shop Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($shop['shop_name']) ?></h1>
                        <p class="text-gray-600"><?= $shop['product_count'] ?> products</p>
                    </div>
                    <a href="edit_shop.php?id=<?= $shop['id'] ?>" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Edit Shop
                    </a>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Your Products</h2>
                    <a href="add_product.php" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </a>
                </div>

                <div class="divide-y divide-gray-200">
                    <?php foreach ($products as $product): ?>
                    <div class="p-4 flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                <img src="../assets/images/<?= $product['image_url'] ?>" 
                                     alt="<?= htmlspecialchars($product['title']) ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800"><?= htmlspecialchars($product['title']) ?></h3>
                                <p class="text-green-600 font-bold">$<?= number_format($product['price'], 2) ?></p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="edit_product.php?id=<?= $product['id'] ?>" 
                               class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" 
                               class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700"
                               onclick="return confirm('Delete this product?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>