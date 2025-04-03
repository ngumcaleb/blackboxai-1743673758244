<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    exit;
}

$contactId = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT);
$currentUserId = $_SESSION['user_id'];

if (!$contactId) {
    echo '<div class="text-center text-gray-500 py-10">Select a contact to start chatting</div>';
    exit;
}

// Update message status to delivered for received messages
$stmt = $pdo->prepare("
    UPDATE messages 
    SET status = 'delivered' 
    WHERE sender_id = ? AND receiver_id = ? AND status = 'sent'
");
$stmt->execute([$contactId, $currentUserId]);

// Get messages with formatted time
$stmt = $pdo->prepare("
    SELECT m.*, u.username, u.profile_image,
           DATE_FORMAT(m.created_at, '%h:%i %p') as time
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
    if ($message['sender_id'] == $currentUserId) {
        // Current user's message
        echo '
        <div class="flex justify-end mb-4">
            <div class="max-w-xs lg:max-w-md bg-blue-500 text-white rounded-lg py-2 px-4">
                <p>'.htmlspecialchars($message['content']).'</p>
                <div class="text-right text-xs text-blue-100 mt-1">
                    '.$message['time'].'
                    <span class="ml-1">
                        <i class="fas fa-check'.($message['status'] == 'delivered' ? '-double' : '').'"></i>
                    </span>
                </div>
            </div>
        </div>';
    } else {
        // Contact's message
        echo '
        <div class="flex justify-start mb-4">
            <div class="max-w-xs lg:max-w-md bg-white rounded-lg py-2 px-4 shadow">
                <p>'.htmlspecialchars($message['content']).'</p>
                <div class="text-right text-xs text-gray-500 mt-1">'.$message['time'].'</div>
            </div>
        </div>';
    }
}
?>