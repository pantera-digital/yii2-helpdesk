$(document).on('click','.all-messages',function () {
    var href = $(this).attr('href');
    var allMessagesWrap = $(this).parents('.panel').first().find('.messages-wrapper').first();
    allMessagesWrap.css('opacity','0.3')
    getMessagesData(href, allMessagesWrap);
    $(this).toggleClass('hidden');
    $(this).parents('.panel').first().find('.hide-history').first().toggleClass('hidden');
    return false;
});
$(document).on('click','.hide-history',function () {
    var href = $(this).attr('href');
    var allMessagesWrap = $(this).parents('.panel').first().find('.messages-wrapper').first();
    getMessagesData(href, allMessagesWrap);
    $(this).toggleClass('hidden');
    $(this).parents('.panel').first().find('.all-messages').first().toggleClass('hidden');
    return false;
});
function getMessagesData(href,selector) {
    selector.css('opacity','0.3')
    $.get(href, function(data){
        selector.html(data)
        selector.css('opacity','1')
    });
}
$(document).on('beforeSubmit', 'form.ticket-response-form', function(e) {
        var data = $(this).serialize();
        var action = $(this).attr('action');
        var href = $(this).parents('.panel').find('.hide-history').first().attr('href');
        var allMessagesWrap = $(this).parents('.panel').first().find('.messages-wrapper').first();
        $.ajax({
            type: "POST",
            url:action ,
            data: data,
            async:true,
            success:function(d) {
                if(d.status === 'success') {
                    getMessagesData(href, allMessagesWrap);
                    return false;
                }
            }
        });
        $(this).reset();
        return false;
});