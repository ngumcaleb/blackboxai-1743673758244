<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: chat/chat.php');
} else {
    header('Location: user/login.php');
}
exit();
?>