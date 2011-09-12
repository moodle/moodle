M.core_message = M.core_message || {};

M.core_message.init_focus = function(Y, eid) {
    document.getElementById(eid).focus();
};

M.core_message.init_refresh_page = function(Y, delay, url) {
	var delay_callback = function() {
		document.location.replace(url);
	};
	setTimeout(delay_callback, delay);
};

M.core_message.combinedsearchgotfocus = function(e) {
    if (e.target.get('value')==this.defaultsearchterm) {
        e.target.select();
    }
};

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
};

M.core_message.init_editsettings = function(Y) {
    var editsettings = {

        init : function() {
            var disableall = Y.one(".disableallcheckbox");
            disableall.on('change', editsettings.changeState);
            //disableall.simulate("change"); simulate() not available in this version of YUI
        },

        changeState : function(e) {
            Y.all('.notificationpreference').each(function(node) {
                var disabled = e.target.get('checked');

                node.removeAttribute('disabled');
                if (disabled) {
                    node.setAttribute('disabled', 1)
                }
            }, this);
        }
    }

    editsettings.init();
}