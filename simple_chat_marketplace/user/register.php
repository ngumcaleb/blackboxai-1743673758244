<?php require_once '../includes/config.php'; ?>
<?php require_once '../includes/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ChatMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 py-4 px-6">
                <h1 class="text-white text-2xl font-bold">Create Account</h1>
            </div>
            
            <div class="p-6">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $result = registerUser($_POST['username'], $_POST['email'], $_POST['password']);
                    if ($result['success']) {
                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">'.$result['message'].'</div>';
                        header("Refresh: 2; url=login.php");
                    } else {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">'.$result['message'].'</div>';
                    }
                }
                ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                            Register
                        </button>
                    </div>
                </form>
                
                <div class="mt-4 text-center">
                    <p class="text-gray-600">Already have an account? 
                        <a href="login.php" class="text-blue-600 hover:underline">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>