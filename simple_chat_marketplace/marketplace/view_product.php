<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit;
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    header('Location: index.php');
    exit;
}

// Get product details with shop and owner info
$stmt = $pdo->prepare("
    SELECT p.*, s.shop_name, s.user_id as seller_id,
           u.username as seller_name, u.profile_image as seller_image
    FROM products p
    JOIN shops s ON p.shop_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE p.id = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> - ChatMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .product-image {
            height: 400px;
            background-color: #f7fafc;
        }
        @media (max-width: 640px) {
            .product-image {
                height: 250px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Product Image -->
            <div class="product-image flex items-center justify-center p-4">
                <img src="../assets/images/<?= $product['image_url'] ?>" 
                     alt="<?= htmlspecialchars($product['title']) ?>"
                     class="max-h-full max-w-full object-contain">
            </div>

            <!-- Product Details -->
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($product['title']) ?></h1>
                        <p class="text-gray-600 mt-1">From <?= htmlspecialchars($product['shop_name']) ?></p>
                    </div>
                    <span class="text-2xl font-bold text-green-600">$<?= number_format($product['price'], 2) ?></span>
                </div>

                <div class="mt-6">
                    <h2 class="text-xl font-semibold text-gray-800">Description</h2>
                    <p class="text-gray-700 mt-2"><?= htmlspecialchars($product['description']) ?></p>
                </div>

                <!-- Seller Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Seller Information</h3>
                    <div class="mt-4 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden">
                            <img src="../assets/images/<?= $product['seller_image'] ?>" 
                                 alt="<?= htmlspecialchars($product['seller_name']) ?>"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium"><?= htmlspecialchars($product['seller_name']) ?></h4>
                            <p class="text-sm text-gray-500">Shop: <?= htmlspecialchars($product['shop_name']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex flex-col space-y-3">
                    <?php if ($product['seller_id'] == $_SESSION['user_id']): ?>
                        <a href="edit_product.php?id=<?= $product['id'] ?>" 
                           class="bg-yellow-500 text-white py-3 px-6 rounded-lg hover:bg-yellow-600 transition flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i> Edit Product
                        </a>
                        <a href="delete_product.php?id=<?= $product['id'] ?>" 
                           class="bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition flex items-center justify-center"
                           onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="fas fa-trash mr-2"></i> Delete Product
                        </a>
                    <?php else: ?>
                        <a href="../chat/chat.php?contact=<?= $product['seller_id'] ?>" 
                           class="bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                            <i class="fas fa-comment-dots mr-2"></i> Message Seller
                        </a>
                        <button class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around py-3">
        <a href="../marketplace/index.php" class="text-gray-600 hover:text-green-600 flex flex-col items-center">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="../chat/chat.php" class="text-gray-600 hover:text-green-600 flex flex-col items-center">
            <i class="fas fa-comment-dots text-xl"></i>
            <span class="text-xs mt-1">Chats</span>
        </a>
        <a href="../user/profile.php" class="text-gray-600 hover:text-green-600 flex flex-col items-center">
            <i class="fas fa-user text-xl"></i>
            <span class="text-xs mt-1">Profile</span>
        </a>
    </div>
</body>
</html>