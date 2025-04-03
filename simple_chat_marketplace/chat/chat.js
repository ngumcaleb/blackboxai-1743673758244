document.addEventListener('DOMContentLoaded', function() {
    // Mobile view toggle
    const mobileBackBtn = document.createElement('button');
    mobileBackBtn.id = 'mobile-back-btn';
    mobileBackBtn.className = 'md:hidden p-2 text-white mr-2';
    mobileBackBtn.innerHTML = '<i class="fas fa-arrow-left"></i>';
    document.getElementById('chat-area').prepend(mobileBackBtn);

    mobileBackBtn.addEventListener('click', function() {
        document.getElementById('contacts-list').classList.remove('hidden');
        document.getElementById('chat-area').classList.add('hidden');
    });

    // Contact selection
    document.querySelectorAll('.contact-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userid;
            const username = this.querySelector('h3').textContent;
            const initial = username.charAt(0).toUpperCase();
            
            // Update chat header
            document.getElementById('chat-contact-name').textContent = username;
            document.getElementById('chat-contact-initial').textContent = initial;
            
            // Toggle views on mobile
            if(window.innerWidth < 768) {
                document.getElementById('contacts-list').classList.add('hidden');
                document.getElementById('chat-area').classList.remove('hidden');
            }
            
            // Load messages
            loadMessages(userId);
        });
    });

    // Message sending
    document.getElementById('send-button').addEventListener('click', sendMessage);
    document.getElementById('message-input').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') sendMessage();
    });

    // Real-time polling
    setInterval(pollMessages, 2000);
});

function loadMessages(contactId) {
    fetch(`fetch_messages.php?contact_id=${contactId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('messages-container').innerHTML = data;
            scrollToBottom();
        });
}

function sendMessage() {
    const contactId = document.querySelector('.contact-item.active')?.dataset.userid;
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if(message && contactId) {
        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `receiver_id=${contactId}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            messageInput.value = '';
            loadMessages(contactId);
        });
    }
}

function pollMessages() {
    const contactId = document.querySelector('.contact-item.active')?.dataset.userid;
    if(contactId) loadMessages(contactId);
}

function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}