<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit;
}

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Get product details
$stmt = $pdo->prepare("
    SELECT p.*, s.user_id 
    FROM products p
    JOIN shops s ON p.shop_id = s.id
    WHERE p.id = ? AND s.user_id = ?
");
$stmt->execute([$productId, $_SESSION['user_id']]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: manage_shop.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    
    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $productId]);
    
    header('Location: manage_shop.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ChatMarket</title>
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
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
            </div>
            
            <form method="POST" class="p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Product Title
                    </label>
                    <input type="text" id="title" name="title" 
                           value="<?= htmlspecialchars($product['title']) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                           required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        required><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                        Price ($)
                    </label>
                    <input type="number" step="0.01" id="price" name="price" 
                           value="<?= htmlspecialchars($product['price']) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                           required>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="manage_shop.php" 
                       class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>