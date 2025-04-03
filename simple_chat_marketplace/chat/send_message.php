<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$receiverId = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if (!$receiverId || empty($message)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    // Insert message with 'sent' status
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content, status) VALUES (?, ?, ?, 'sent')");
    $stmt->execute([$_SESSION['user_id'], $receiverId, $message]);
    
    // Get the full message details with sender info
    $messageId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT m.*, u.username, u.profile_image 
                          FROM messages m 
                          JOIN users u ON m.sender_id = u.id 
                          WHERE m.id = ?");
    $stmt->execute([$messageId]);
    $newMessage = $stmt->fetch();
    
    // Format the created_at time
    $newMessage['time'] = date('h:i A', strtotime($newMessage['created_at']));
    
    echo json_encode([
        'success' => true,
        'message' => $newMessage
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
