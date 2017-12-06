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

M.core_message.init_defaultoutputs = function(Y) {
    var defaultoutputs = {

        init : function() {
            Y.all('#defaultmessageoutputs select').each(function(node) {
                // attach event listener
                node.on('change', defaultoutputs.changeState);
                // set initial layout
                node.simulate("change");
            }, this);

            Y.all('#defaultmessageoutputs input.messagedisable').each(function(node) {
                // Attach event listener
                node.on('change', defaultoutputs.changeProviderState);
                node.simulate("change");
            }, this);
        },

        changeState : function(e) {
            var value = e.target._node.options[e.target.get('selectedIndex')].value;
            var parentnode = e.target.ancestor('td');
            switch (value) {
            case 'forced':
                defaultoutputs.updateCheckboxes(parentnode, 1, 1);
                break;
            case 'disallowed':
                defaultoutputs.updateCheckboxes(parentnode, 1, 0);
                break;
            case 'permitted':
                defaultoutputs.updateCheckboxes(parentnode, 0, 0);
                break;
            }
        },

        updateCheckboxes : function(blocknode, disabled, checked) {
            blocknode.all('input[type=checkbox]').each(function(node) {
                node.removeAttribute('disabled');
                if (disabled) {
                    node.setAttribute('disabled', 1)
                    node.removeAttribute('checked');
                }
                if (checked) {
                    node.setAttribute('checked', 1)
                }
            }, this);
        },

        changeProviderState : function(e) {
            var isenabled = e.target.get('checked') || undefined;
            var parentnode = e.target.ancestor('tr');
            if (!isenabled) {
                parentnode.all('select').each(function(node) {
                    node.set('value', 'disallowed');
                    node.setAttribute('disabled', 1);
                    defaultoutputs.updateCheckboxes(node.ancestor('td'), 1, 0);
                }, this);
                parentnode.addClass('dimmed_text');
            } else {
                parentnode.all('select[disabled]').each(function(node) {
                    node.removeAttribute('disabled');
                    node.set('value', 'permitted');
                    defaultoutputs.updateCheckboxes(node.ancestor('td'), 0, 0);
                }, this);
                parentnode.removeClass('dimmed_text');
            }
        }
    }

    defaultoutputs.init();
}

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
