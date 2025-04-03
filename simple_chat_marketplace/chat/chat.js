// Chat functionality using jQuery
$(function() {
    // Message display functions
    function scrollToBottom() {
        const container = $('#messages-container');
        container.scrollTop(container[0].scrollHeight);
    }

    function displayMessage(message) {
        const isCurrentUser = message.sender_id == CURRENT_USER_ID;
        const messageClass = isCurrentUser ? 
            'bg-blue-500 text-white text-blue-100' : 
            'bg-white shadow text-gray-500';
        
        const messageHtml = `
        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'} mb-4">
            <div class="max-w-xs lg:max-w-md ${messageClass.split(' ')[0]} rounded-lg py-2 px-4">
                <p>${message.content}</p>
                <div class="text-right text-xs ${messageClass.split(' ')[2]} mt-1">
                    ${message.time}
                    ${isCurrentUser ? `
                    <span class="ml-1">
                        <i class="fas fa-check${message.status === 'delivered' ? '-double' : ''}"></i>
                    </span>` : ''}
                </div>
            </div>
        </div>`;
        
        $('#messages-container').append(messageHtml);
        scrollToBottom();
    }

    // Contact selection handler
    $('.contact-item').click(function() {
        $('.contact-item').removeClass('active');
        $(this).addClass('active');
        
        const contactId = $(this).data('userid');
        const contactName = $(this).find('h3').text();
        
        // Update chat header
        $('#chat-contact-name').text(contactName);
        $('#chat-contact-initial').text(contactName.charAt(0).toUpperCase());
        
        // Mobile view handling
        if(window.innerWidth < 768) {
            $('#contacts-list').hide();
            $('#chat-area').show();
        }
        
        loadMessages(contactId);
    });

    // Message loading
    function loadMessages(contactId) {
        $.get('fetch_messages.php', {contact_id: contactId})
            .done(html => {
                $('#messages-container').html(html);
                scrollToBottom();
            })
            .fail(err => console.error('Error loading messages:', err));
    }

    // Message sending
    $('#send-button, #message-input').on('click keypress', function(e) {
        if(e.type === 'click' || (e.type === 'keypress' && e.which === 13)) {
            const message = $('#message-input').val().trim();
            const contactId = $('.contact-item.active').data('userid');
            
            if(message && contactId) {
                $.post('send_message.php', {
                    receiver_id: contactId, 
                    message: message
                })
                .done(res => {
                    if(res.success) {
                        $('#message-input').val('');
                        displayMessage(res.message);
                    }
                })
                .fail(err => console.error('Error sending message:', err));
            }
        }
    });

    // Message polling
    setInterval(() => {
        const contactId = $('.contact-item.active').data('userid');
        if(contactId) loadMessages(contactId);
    }, 2000);
});