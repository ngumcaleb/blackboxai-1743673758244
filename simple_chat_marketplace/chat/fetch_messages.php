<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit('Unauthorized');
}

$contactId = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT);

if (!$contactId) {
    http_response_code(400);
    exit('Invalid contact ID');
}

$currentUserId = $_SESSION['user_id'];

// Get messages between current user and contact
$stmt = $pdo->prepare("
    SELECT m.*, u.username, u.profile_image 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$currentUserId, $contactId, $contactId, $currentUserId]);
$messages = $stmt->fetchAll();

if (empty($messages)) {
    echo '<div class="text-center text-gray-500 py-10">No messages yet. Start the conversation!</div>';
    exit;
}

foreach ($messages as $message) {
    $isSender = $message['sender_id'] == $currentUserId;
    $time = date('h:i A', strtotime($message['created_at']));
    
    if ($isSender) {
        echo '
        <div class="flex justify-end mb-4">
            <div class="max-w-xs lg:max-w-md bg-blue-500 text-white rounded-lg py-2 px-4">
                <p>'.htmlspecialchars($message['content']).'</p>
                <div class="text-right text-xs text-blue-100 mt-1">
                    '.$time.'
                    <span class="ml-1">
                        <i class="fas fa-check'.($message['status'] == 'delivered' ? '-double' : '').'"></i>
                    </span>
                </div>
            </div>
        </div>';
    } else {
        echo '
        <div class="flex justify-start mb-4">
            <div class="max-w-xs lg:max-w-md bg-white rounded-lg py-2 px-4 shadow">
                <p>'.htmlspecialchars($message['content']).'</p>
                <div class="text-right text-xs text-gray-500 mt-1">'.$time.'</div>
            </div>
        </div>';
    }
}

// Update message status to delivered
$stmt = $pdo->prepare("
    UPDATE messages 
    SET status = 'delivered' 
    WHERE sender_id = ? AND receiver_id = ? AND status = 'sent'
");
$stmt->execute([$contactId, $currentUserId]);
?>