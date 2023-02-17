const chat = $('.chat');
const chatWindow = chat.find('.chat-window');
const currentUser = chat.data('user-id');

const sendMessageURL = '/chat/' + chat.data('chat-id');
const fetchMessageURL = sendMessageURL + '/getMessages';

$(document)
    .on('click', '#send_message', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');
        let formData = form.serialize();
        let input = form.find('input#message_text');
        input.val('');
        $.ajax({
            url: sendMessageURL,
            method: 'POST',
            data: formData,
            success: function(data) {
                drawMessage(data);
            }
        });
    })

;
chatWindow.scroll(function () {
    if (chatWindow.scrollTop() == 0) {
        let firstMessage = chatWindow.find('.chat-message:first');
        let messages = fetchMessages(chatWindow.find('.chat-message').length);
        $(messages).each(function () {
            drawMessage(this, 'top');
        });
        chatWindow.scrollTop(firstMessage.offset().top - 170);
    }
});

$(document).ready(function () {
    if (chatWindow.length > 0) {
        chatWindow.scrollTop(chatWindow[0].scrollHeight)
        setInterval(function () {
            let messages = fetchMessages();
            $(messages).each(function () {
                drawMessage(this);
            });
        }, 5000);
    }
});

function fetchMessages(offset = 0)
{
    let messages = [];
    $.ajax({
        url: fetchMessageURL,
        method: 'GET',
        async: false,
        data: {offset: offset},
        success: function(data) {
            messages = data;
        }
    })
    return messages;
}

function drawMessage(message, place = 'bottom')
{
    if (chatWindow.find('.chat-message[data-message-id="' + message.id + '"]').length > 0) {
        return false;
    }

    let prototypeClass = 'message-to-me-prototype'; 
    if (message.sender != null && message.sender.id == currentUser) {
        prototypeClass = 'message-from-me-prototype';
    }

    let prototype = $('.' + prototypeClass).clone();
    prototype.removeClass(prototypeClass);
    prototype.data('message-id', message.id);
    prototype.attr('data-message-id', message.id);
    prototype.find('p.message-text').text(message.text);

    let date = new Date(message.sentAt);
    let time = date.getHours()+":"+date.getMinutes();
    
    prototype.find('p.message-time').text(time)
    if (place === 'bottom') {
        chatWindow.append(prototype);
    } else {
        chatWindow.prepend(prototype);
    }
}

const imageUploadURL = '/image/upload';
$(document)
    .on('change', '.upload-image', function(e) {
        let fd = new FormData();
        fd.append('file', this.files[0]);
        $.ajax({
            url: imageUploadURL,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(data) {
                $('input.image-id').val(data.id);
            },
        });
    })
;
