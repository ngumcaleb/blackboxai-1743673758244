<?php
require_once 'auth.php';
$isLoggedIn = isLoggedIn();
?>
<nav class="bg-white shadow">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="../index.php" class="text-xl font-bold text-green-600">ChatMarket</a>
        <div class="flex items-center space-x-4">
            <?php if ($isLoggedIn): ?>
                <a href="../marketplace/manage_shop.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-store-alt"></i>
                </a>
                <a href="../chat/chat.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-comment-dots"></i>
                </a>
                <a href="../user/profile.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-user"></i>
                </a>
                <a href="../user/logout.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="../user/login.php" class="text-gray-600 hover:text-green-600">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>