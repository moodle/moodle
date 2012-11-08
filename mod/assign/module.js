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
        var quickgrade = Y.all('.gradingtable .quickgrade');
        quickgrade.each(function(quick) {
            quick.on('change', function(e) {
                this.get('parentNode').addClass('quickgrademodified');
            });
        });
    });
};

M.mod_assign.init_grading_options = function(Y) {
    Y.use('node', function(Y) {
        var paginationelement = Y.one('#id_perpage');
        paginationelement.on('change', function(e) {
            Y.one('form.gradingoptionsform').submit();
        });
        var filterelement = Y.one('#id_filter');
        if (filterelement) {
            filterelement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
        var quickgradingelement = Y.one('#id_quickgrading');
        if (quickgradingelement) {
            quickgradingelement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
    });
};

M.mod_assign.init_grade_change = function(Y) {
    var gradenode = Y.one('#id_grade');
    if (gradenode) {
        var originalvalue = gradenode.get('value');
        gradenode.on('change', function() {
            if (gradenode.get('value') != originalvalue) {
                alert(M.str.mod_assign.changegradewarning);
            }
        });
    }
};
