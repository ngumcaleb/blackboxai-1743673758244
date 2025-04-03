<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's shop if exists
$shop = null;
$stmt = $pdo->prepare("SELECT * FROM shops WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$shop = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $profileImage = $_FILES['profile_image'] ?? null;

    try {
        // Update basic info
        $updateData = [
            'username' => $username,
            'email' => $email,
            'id' => $_SESSION['user_id']
        ];

        // Handle password change if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $updateData['password'] = $hashedPassword;
        }

        // Handle profile image upload
        if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/profiles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($profileImage['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            move_uploaded_file($profileImage['tmp_name'], $uploadDir . $filename);
            $updateData['profile_image'] = 'profiles/' . $filename;
        }

        // Build and execute update query
        $setParts = [];
        $params = [];
        foreach ($updateData as $key => $value) {
            if ($key !== 'id') {
                $setParts[] = "$key = ?";
                $params[] = $value;
            }
        }
        $params[] = $updateData['id'];

        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Update session data
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        $success = 'Profile updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating profile: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ChatMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <a href="logout.php" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-blue-600 py-4 px-6">
                    <h1 class="text-white text-xl font-bold">My Profile</h1>
                </div>
                
                <div class="p-6">
                    <?php if (isset($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($success) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>

                    <div class="md:flex">
                        <!-- Profile Picture -->
                        <div class="md:w-1/3 flex justify-center mb-6 md:mb-0">
                            <div class="relative">
                                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200">
                                    <?php if (!empty($user['profile_image']) && file_exists('../assets/images/' . $user['profile_image'])): ?>
                                    <img src="../assets/images/<?= $user['profile_image'] ?>" 
                                         alt="<?= htmlspecialchars($user['username']) ?>" 
                                         class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-4xl text-gray-600">
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Form -->
                        <div class="md:w-2/3">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="block text-gray-700 font-medium mb-2">New Password (leave blank to keep current)</label>
                                    <input type="password" id="password" name="password"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="profile_image" class="block text-gray-700 font-medium mb-2">Profile Image</label>
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="mt-6">
                                    <button type="submit" 
                                        class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Shop Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">My Shop</h2>
                        
                        <?php if ($shop): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($shop['shop_name']) ?></h3>
                            <p class="text-gray-600 mt-1"><?= htmlspecialchars($shop['description']) ?></p>
                            <div class="mt-4">
                                <a href="../marketplace/index.php" class="text-blue-600 hover:underline mr-4">
                                    <i class="fas fa-store mr-1"></i> View Shop
                                </a>
                                <a href="../marketplace/add_product.php" class="text-blue-600 hover:underline">
                                    <i class="fas fa-plus mr-1"></i> Add Product
                                </a>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600 mb-4">You don't have a shop yet. Create one to start selling products!</p>
                            <a href="../marketplace/shop.php" 
                               class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition inline-block">
                                Create Shop
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>