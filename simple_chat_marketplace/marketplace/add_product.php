<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit();
}

// Check if user has a shop
$stmt = $pdo->prepare("SELECT id FROM shops WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$shop = $stmt->fetch();

if (!$shop) {
    header('Location: shop.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if ($title && $price !== false && $description) {
        try {
            $imageUrl = 'default_product.jpg'; // Default image
            
            // Handle file upload if present
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../assets/images/products/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadDir . $filename);
                $imageUrl = 'products/' . $filename;
            }

            $stmt = $pdo->prepare("INSERT INTO products (shop_id, title, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$shop['id'], $title, $price, $description, $imageUrl]);
            
            $_SESSION['success'] = 'Product added successfully!';
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $error = 'Error adding product: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill all required fields correctly';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - ChatMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold text-blue-600">ChatMarket</a>
            <div class="flex items-center space-x-4">
                <a href="../user/logout.php" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-blue-600 py-4 px-6">
                <h1 class="text-white text-xl font-bold">Add New Product</h1>
            </div>
            
            <div class="p-6">
                <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 font-medium mb-2">Product Title</label>
                        <input type="text" id="title" name="title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 font-medium mb-2">Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="product_image" class="block text-gray-700 font-medium mb-2">Product Image</label>
                        <input type="file" id="product_image" name="product_image" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                            Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>