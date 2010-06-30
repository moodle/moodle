M.core_message = M.core_message || {};

M.core_message.init_focus = function(Y, eid) {
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

M.core_message.init_search_page = function(Y, defaultsearchterm) {
    this.Y = Y;
    this.defaultsearchterm = defaultsearchterm;

    var combinedsearchbox = this.Y.one('#combinedsearch');
    combinedsearchbox.on('focus', this.combinedsearchgotfocus, this);
}


M.core_message.combinedsearchgotfocus = function(e) {
    if (e.target.get('value')==this.defaultsearchterm) {
        e.target.select();
    }
}

M.core_message.init_notification = function(Y, title, content, url) {
    Y.use('overlay', function() {
        var o = new Y.Overlay({
            headerContent :  title,
            bodyContent : content
        });
        o.render(Y.one(document.body));

        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            // Adjust for IE 6 (can't handle fixed pos)
            //align the bottom right corner of the overlay with the bottom right of the viewport
            o.set("align", {
                points:[Y.WidgetPositionAlign.BR, Y.WidgetPositionAlign.BR]
            });
        }        

        Y.one('#notificationyes').on('click', function(e) {
            window.location.href = url;
        }, o);
        Y.one('#notificationno').on('click', function(e) {
            o.hide();
            e.preventDefault();
            return false;
        }, o);
    });
}