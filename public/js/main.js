$(document)
    .on('click', '#send_message', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');
        let input = form.find('input#message_text');
        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: form.serialize(),
            success: function(data) {
                let text = input.val();
                input.val('');
                
            }
        });
    })
;