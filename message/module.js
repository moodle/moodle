M.core_message = {};

M.core_message.init_focus = function(Y.eid) {
    document.getElementById(eid).focus();
}

M.core_message.init_refresh_parent_frame = function(Y, msgcount, msg) {

	var add_message = function (messagestr) {
	    var messageblock = parent.messages.document.getElementById('messages');
	    var message = document.createElement('div');
	    message.innerHTML = messagestr;
	    messageblock.appendChild(message);
	}

    if (msgcount>0) {
        for (var i=0; i < msgcount; i++) {
            add_message(msg[i])
        }
    }
    parent.messages.scroll(1,5000000);
    parent.send.focus();
}

M.core_message.init_refresh_page = function(Y, delay, url) {
	var delay_callback = function() {
		document.location.replace(url);
	}
	setTimeout(delay_callback, delay);
}
