<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$receiverId = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if (!$receiverId || !$message) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid input']));
}

try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $receiverId, $message]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>