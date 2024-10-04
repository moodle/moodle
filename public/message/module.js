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

M.core_message.init_editsettings = function(Y) {
    var editsettings = {

        init : function() {
            var disableall = Y.one(".disableallcheckbox");
            disableall.on('change', editsettings.changeState);
            disableall.simulate("change");
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
