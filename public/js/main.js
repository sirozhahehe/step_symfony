const chat = $('.chat')
const sendMessageURL = '/chat/' + chat.data('chat-id');
const fetchMessagesURL = sendMessageURL + '/getMessages';
const currentUser = chat.data('user-id');
const chatWindow = chat.find('.chat-window');
let isLoading = false;
let allLoaded = false;

$(document)
    .on('click', '#send_message', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const formData = form.serialize();
        let input = form.find('input#message_text');
        if (input.val() === '') {
            fireAlert('Message can not be empty!');
            return false;
        }
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
chatWindow.scroll(function() {
    if(chatWindow.scrollTop() == chatWindow.height() - chatWindow.height()) {
        if (!isLoading && !allLoaded) {
            isLoading = true;
            let messages = fetchMessages(chatWindow.find('.chat-message').length).reverse();
            if (messages.length === 0) {
                allLoaded = true;
                fireAlert('All messages loaded!', 'info');
            }
            $(messages).each(function (i) {
                drawMessage(this, 'top');
            })
            chatWindow.scrollTop(1)
            isLoading = false;
        }
    }
});
$(document).ready(function (e) {
    chatWindow.scrollTop(chatWindow[0].scrollHeight);

    setInterval(function () {
        let messages = fetchMessages();
        $(messages).each(function (i) {
            drawMessage(this);
        })
    }, 5000);
})

function fetchMessages(offset = 0)
{
    let messages = [];
    $.ajax({
        url: fetchMessagesURL,
        method: 'GET',
        data: {offset: offset},
        async: false,
        success: function (data) {
            messages = data;
        },
    })
    return messages;
}

function drawMessage(message, place = 'bottom')
{
    if (chatWindow.find('.chat-message[data-message-id="' + message.id + '"]').length > 0) {
        return false;
    }
    let prototypeClass = 'message-to-me-prototype';
    if (message.sender != null && message.sender.id === currentUser) {
        prototypeClass = 'message-from-me-prototype';
    }
    let prototype = $('.' + prototypeClass).clone();
    prototype.removeClass(prototypeClass);
    prototype.data('message-id', message.id)
    prototype.attr('data-message-id', message.id)
    prototype.find('p.message-text').text(message.text);

    let date = new Date(message.sentAt);
    let time = date.getHours()+":"+date.getMinutes();

    prototype.find('p.message-time').text(time)
    if (place === "bottom") {
        chatWindow.append(prototype)
    } else {
        chatWindow.prepend(prototype)
    }
}

function fireAlert(message, type = 'danger')
{
    let alert = `<div class="alert alert-${type}" role="alert">${message}</div>`;
    alert = $(alert);
    chat.prepend(alert);
    setTimeout(function () {
        alert.remove();
    }, 3000)
}