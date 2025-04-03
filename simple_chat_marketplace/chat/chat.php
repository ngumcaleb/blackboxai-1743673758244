<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: ../user/login.php');
    exit();
}

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

// Get all users except current user for contacts list
$stmt = $pdo->prepare("SELECT id, username, profile_image FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - ChatMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const CURRENT_USER_ID = <?= $_SESSION['user_id'] ?>;
    </script>
</head>
<body class="bg-gray-100 h-screen">
    <!-- Mobile Header (hidden on desktop) -->
    <div class="md:hidden bg-green-600 text-white p-4 flex items-center">
        <a href="../marketplace/index.php" class="text-white mr-4">
            <i class="fas fa-store"></i>
        </a>
        <h1 class="text-xl font-bold">ChatMarket</h1>
    </div>
    
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header -->
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                    <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                </div>
                <div class="ml-3">
                    <h2 class="font-semibold"><?= htmlspecialchars($currentUser['username']) ?></h2>
                </div>
            </div>

            <!-- Search -->
            <div class="p-3 border-b border-gray-200">
                <div class="relative">
                    <input type="text" placeholder="Search or start new chat" 
                        class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-lg focus:outline-none">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 border-b border-gray-200 hidden md:block">
                <a href="../marketplace/index.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-store mr-2"></i> Marketplace
                </a>
            </div>
            
            <!-- Contacts List -->
            <div class="flex-1 overflow-y-auto">
                <?php foreach ($contacts as $contact): ?>
                <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer contact-item flex items-center"
                    data-userid="<?= $contact['id'] ?>">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                            <?= strtoupper(substr($contact['username'], 0, 1)) ?>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium"><?= htmlspecialchars($contact['username']) ?></h3>
                            <p class="text-xs text-gray-500">Last seen today</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="hidden md:flex flex-1 flex-col" id="chat-area">
            <!-- Chat Header -->
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                    <span id="chat-contact-initial">C</span>
                </div>
                <div class="ml-3">
                    <h2 id="chat-contact-name" class="font-semibold">Select a contact</h2>
                    <p id="chat-contact-status" class="text-xs text-gray-500">Online</p>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <div class="text-center text-gray-500 py-10">
                    Select a contact to start chatting
                </div>
            </div>

            <!-- Message Input -->
            <div class="p-4 bg-white border-t border-gray-200">
                <div class="flex items-center">
                    <button class="p-2 text-gray-500 hover:text-gray-700">
                        <i class="far fa-smile"></i>
                    </button>
                    <input type="text" id="message-input" placeholder="Type a message" 
                        class="flex-1 mx-2 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-blue-500">
                    <button id="send-button" class="p-2 text-blue-500 hover:text-blue-700">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Handle contact selection
        $('.contact-item').click(function() {
            const userId = $(this).data('userid');
            const username = $(this).find('h3').text();
            const initial = username.charAt(0).toUpperCase();
            
            $('#chat-contact-name').text(username);
            $('#chat-contact-initial').text(initial);
            
            // Load messages for this contact
            loadMessages(userId);
        });

        // Function to load messages
        function loadMessages(contactId) {
            $.ajax({
                url: 'fetch_messages.php',
                type: 'GET',
                data: { contact_id: contactId },
                success: function(data) {
                    $('#messages-container').html(data);
                    scrollToBottom();
                }
            });
        }

        // Function to scroll to bottom of messages
        function scrollToBottom() {
            const container = $('#messages-container');
            container.scrollTop(container[0].scrollHeight);
        }

        // Handle sending messages
        $('#send-button').click(sendMessage);
        $('#message-input').keypress(function(e) {
            if (e.which === 13) {
                sendMessage();
            }
        });

        function sendMessage() {
            const message = $('#message-input').val().trim();
            const contactId = $('.contact-item.active').data('userid');
            
            if (message && contactId) {
                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: {
                        receiver_id: contactId,
                        message: message
                    },
                    success: function() {
                        $('#message-input').val('');
                        loadMessages(contactId);
                    }
                });
            }
        }

        // Poll for new messages every 2 seconds
        setInterval(function() {
            const contactId = $('.contact-item.active').data('userid');
            if (contactId) {
                loadMessages(contactId);
            }
        }, 2000);
    });
    </script>
</body>
</html>