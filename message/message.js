function set_focus(eid) {
    document.getElementById(eid).focus();
}

function refresh_parent_messages_frame() {
    if (chatmessages.msgcount>0) {
        for (var i=0; i < chatmessages.msgcount; i++) {
            add_message(chatmessages.msg[i])
        }
    }
    parent.messages.scroll(1,5000000);
    parent.send.focus();
}

function add_message(messagestr) {
    var messageblock = parent.messages.document.getElementById('messages');
    var message = document.createElement('div');
    message.innerHTML = messagestr;
    messageblock.appendChild(message);
}

var urltorefreshto = '';
function refresh_page(delay, url) {
    urltorefreshto = url;
    setTimeout(callback_refresh_page, delay);
}

function callback_refresh_page() {
    document.location.replace(urltorefreshto);
}