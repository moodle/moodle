M.mod_assign = {};

M.mod_assign.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree = new YAHOO.widget.TreeView(htmlid);

        tree.subscribe("clickEvent", function(node, event) {
            // we want normal clicking which redirects to url
            return false;
        });

        if (expand_all) {
            tree.expandAll();
        }
        tree.render();
    });
};


M.mod_assign.init_grading_table = function(Y) {
    Y.use('node', function(Y) {
        checkboxes = Y.all('td.c0 input');
        checkboxes.each(function(node) {
            node.on('change', function(e) {
                rowelement = e.currentTarget.get('parentNode').get('parentNode');
                if (e.currentTarget.get('checked')) {
                    rowelement.setAttribute('class', 'selectedrow');
                } else {
                    rowelement.setAttribute('class', 'unselectedrow');
                }
            });

            rowelement = node.get('parentNode').get('parentNode');
            if (node.get('checked')) {
                rowelement.setAttribute('class', 'selectedrow');
            } else {
                rowelement.setAttribute('class', 'unselectedrow');
            }
        });

        var selectall = Y.one('th.c0 input');
        if (selectall) {
            selectall.on('change', function(e) {
                if (e.currentTarget.get('checked')) {
                    checkboxes = Y.all('td.c0 input');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', true);
                        rowelement.setAttribute('class', 'selectedrow');
                    });
                } else {
                    checkboxes = Y.all('td.c0 input');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', false);
                        rowelement.setAttribute('class', 'unselectedrow');
                    });
                }
            });
        }

        var batchform = Y.one('form.gradingbatchoperationsform');
        batchform.on('submit', function(e) {
            checkboxes = Y.all('td.c0 input');
            var selectedusers = [];
            checkboxes.each(function(node) {
                if (node.get('checked')) {
                    selectedusers[selectedusers.length] = node.get('value');
                }
            });

            operation = Y.one('#id_operation');
            usersinput = Y.one('input.selectedusers');
            usersinput.set('value', selectedusers.join(','));
            if (selectedusers.length == 0) {
                alert(M.str.assign.nousersselected);
                e.preventDefault();
            } else {
                if (!confirm(eval('M.str.assign.batchoperationconfirm' + operation.get('value')))) {
                    e.preventDefault();
                }
            }
        });


        Y.use('node-menunav', function(Y) {
            var menus = Y.all('.gradingtable .actionmenu');

            menus.each(function(menu) {
                Y.on("contentready", function() {
                    this.plug(Y.Plugin.NodeMenuNav, {autoSubmenuDisplay: true});
                    var submenus = this.all('.yui3-loading');
                    submenus.each(function (n) {
                        n.removeClass('yui3-loading');
                    });

                }, "#" + menu.getAttribute('id'));


            });


        });
        quickgrade = Y.all('.gradingtable .quickgrade');
        quickgrade.each(function(quick) {
            quick.on('change', function(e) {
                parent = this.get('parentNode');
                parent.addClass('quickgrademodified');
            });
        });
    });
};

M.mod_assign.check_dirty_quickgrading_form = function(e) {

            if (!M.core_formchangechecker.get_form_dirty_state()) {
                // the form is not dirty, so don't display any message
                return;
            }

            // This is the error message that we'll show to browsers which support it
            var warningmessage = 'There are unsaved quickgrading changes. Do you really wanto to leave this page?';

            // Most browsers are happy with the returnValue being set on the event
            // But some browsers do not consistently pass the event
            if (e) {
                e.returnValue = warningmessage;
            }

            // But some require it to be returned instead
            return warningmessage;
}
M.mod_assign.init_grading_options = function(Y) {
    Y.use('node', function(Y) {

        var paginationelement = Y.one('#id_perpage');
        paginationelement.on('change', function(e) {
            Y.one('form.gradingoptionsform').submit();
        });
        var filterelement = Y.one('#id_filter');
        filterelement.on('change', function(e) {
            Y.one('form.gradingoptionsform').submit();
        });
        var quickgradingelement = Y.one('#id_quickgrading');
        quickgradingelement.on('change', function(e) {
            Y.one('form.gradingoptionsform').submit();
        });

    });


};
// override the default dirty form behaviour to ignore any input with the class "ignoredirty"
M.mod_assign.set_form_changed = M.core_formchangechecker.set_form_changed;
M.core_formchangechecker.set_form_changed = function(e) {
    target = e.currentTarget;
    if (!target.hasClass('ignoredirty')) {
        M.mod_assign.set_form_changed(e);
    }
}

M.mod_assign.get_form_dirty_state = M.core_formchangechecker.get_form_dirty_state;
M.core_formchangechecker.get_form_dirty_state = function() {
    var state = M.core_formchangechecker.stateinformation;
    if (state.focused_element) {
        if (state.focused_element.element.hasClass('ignoredirty')) {
            state.focused_element.initial_value = state.focused_element.element.get('value')
        }
    }
    return M.mod_assign.get_form_dirty_state();
}

