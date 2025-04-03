<?php
require_once 'config.php';

/**
 * Register a new user
 */
function registerUser($username, $email, $password) {
    global $pdo;
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email or username already exists'];
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword]);

    return ['success' => true, 'message' => 'Registration successful'];
}

/**
 * Authenticate user login
 */
function loginUser($email, $password) {
    global $pdo;
    
    // Find user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;

    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * End user session
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}
?>