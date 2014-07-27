function sareProcessSubscriber(subscriber_id){
    $.ajax({
        dataType: "json",
        url: currentUrl+'process/1/subscriber_id/'+subscriber_id,
        data: null,
        success: function(msg){
            if(msg.subscriber_id){
                $.each(msg.messages, function(i, item) {
                    $('#wrapper').append('<div class="row '+item.class+'">'+item.text+'</div>');
                });
                sareProcessSubscriber(msg.subscriber_id);
            } else {
                $('#wrapper').append(finalMessage);
            }
        },
        error: function(msg){
            alert(msg);
        }
    });
}