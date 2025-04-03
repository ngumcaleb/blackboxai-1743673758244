<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit();
}

// Check if user already has a shop
$stmt = $pdo->prepare("SELECT * FROM shops WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$existingShop = $stmt->fetch();

if ($existingShop) {
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopName = filter_input(INPUT_POST, 'shop_name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if ($shopName && $description) {
        try {
            $stmt = $pdo->prepare("INSERT INTO shops (user_id, shop_name, description) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $shopName, $description]);
            
            $_SESSION['success'] = 'Shop created successfully!';
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $error = 'Error creating shop: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill all required fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Shop - ChatMarket</title>
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
                <h1 class="text-white text-xl font-bold">Create Your Shop</h1>
            </div>
            
            <div class="p-6">
                <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label for="shop_name" class="block text-gray-700 font-medium mb-2">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                            Create Shop
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>