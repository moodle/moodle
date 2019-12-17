M.mod_assign = {};

M.mod_assign.init_tree = function(Y, expand_all, htmlid) {
    var treeElement = Y.one('#'+htmlid);
    if (treeElement) {
        Y.use('yui2-treeview', 'node-event-simulate', function(Y) {
            var tree = new Y.YUI2.widget.TreeView(htmlid);

            tree.subscribe("clickEvent", function(node, event) {
                // We want normal clicking which redirects to url.
                return false;
            });

            tree.subscribe("enterKeyPressed", function(node) {
                // We want keyboard activation to trigger a click on the first link.
                Y.one(node.getContentEl()).one('a').simulate('click');
                return false;
            });

            if (expand_all) {
                tree.expandAll();
            }
            tree.render();
        });
    }
};

M.mod_assign.init_grading_table = function(Y) {
    Y.use('node', function(Y) {
        checkboxes = Y.all('td.c0 input');
        checkboxes.each(function(node) {
            node.on('change', function(e) {
                rowelement = e.currentTarget.get('parentNode').get('parentNode');
                if (e.currentTarget.get('checked')) {
                    rowelement.removeClass('unselectedrow');
                    rowelement.addClass('selectedrow');
                } else {
                    rowelement.removeClass('selectedrow');
                    rowelement.addClass('unselectedrow');
                }
            });

            rowelement = node.get('parentNode').get('parentNode');
            if (node.get('checked')) {
                rowelement.removeClass('unselectedrow');
                rowelement.addClass('selectedrow');
            } else {
                rowelement.removeClass('selectedrow');
                rowelement.addClass('unselectedrow');
            }
        });

        var selectall = Y.one('th.c0 input');
        if (selectall) {
            selectall.on('change', function(e) {
                if (e.currentTarget.get('checked')) {
                    checkboxes = Y.all('td.c0 input[type="checkbox"]');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', true);
                        rowelement.removeClass('unselectedrow');
                        rowelement.addClass('selectedrow');
                    });
                } else {
                    checkboxes = Y.all('td.c0 input[type="checkbox"]');
                    checkboxes.each(function(node) {
                        rowelement = node.get('parentNode').get('parentNode');
                        node.set('checked', false);
                        rowelement.removeClass('selectedrow');
                        rowelement.addClass('unselectedrow');
                    });
                }
            });
        }

        var batchform = Y.one('form.gradingbatchoperationsform');
        if (batchform) {
            batchform.on('submit', function(e) {
                M.util.js_pending('mod_assign/module.js:batch:submit');
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
                    alert(M.util.get_string('nousersselected', 'assign'));
                    e.preventDefault();
                } else {
                    action = operation.get('value');
                    prefix = 'plugingradingbatchoperation_';
                    if (action.indexOf(prefix) == 0) {
                        pluginaction = action.substr(prefix.length);
                        plugin = pluginaction.split('_')[0];
                        action = pluginaction.substr(plugin.length + 1);
                        confirmmessage = M.util.get_string('batchoperationconfirm' + action, 'assignfeedback_' + plugin);
                    } else {
                        confirmmessage = M.util.get_string('batchoperationconfirm' + operation.get('value'), 'assign');
                    }
                    if (!confirm(confirmmessage)) {
                        M.util.js_complete('mod_assign/module.js:batch:submit');
                        e.preventDefault();
                    }
                    // Note: Do not js_complete. The page being reloaded will empty it.
                }
            });
        }

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
        var markerfilterelement = Y.one('#id_markerfilter');
        if (markerfilterelement) {
            markerfilterelement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
        var workflowfilterelement = Y.one('#id_workflowfilter');
        if (workflowfilterelement) {
            workflowfilterelement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
        var quickgradingelement = Y.one('#id_quickgrading');
        if (quickgradingelement) {
            quickgradingelement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
        var showonlyactiveenrolelement = Y.one('#id_showonlyactiveenrol');
        if (showonlyactiveenrolelement) {
            showonlyactiveenrolelement.on('change', function(e) {
            Y.one('form.gradingoptionsform').submit();
            });
        }
        var downloadasfolderselement = Y.one('#id_downloadasfolders');
        if (downloadasfolderselement) {
            downloadasfolderselement.on('change', function(e) {
                Y.one('form.gradingoptionsform').submit();
            });
        }
    });
};

M.mod_assign.init_plugin_summary = function(Y, subtype, type, submissionid) {
    suffix = subtype + '_' + type + '_' + submissionid;
    classname = 'contract_' + suffix;
    contract = Y.one('.' + classname);
    if (contract) {
        contract.on('click', function(e) {
            img = e.target;
            imgclasses = img.getAttribute('class').split(' ');
            for (i = 0; i < imgclasses.length; i++) {
                classname = imgclasses[i];
                if (classname.indexOf('contract_') == 0) {
                    thissuffix = classname.substr(9);
                }
            }
            fullclassname = 'full_' + thissuffix;
            full = Y.one('.' + fullclassname);
            if (full) {
                full.hide(false);
            }
            summaryclassname = 'summary_' + thissuffix;
            summary = Y.one('.' + summaryclassname);
            if (summary) {
                summary.show(false);
            }
        });
    }
    classname = 'expand_' + suffix;
    expand = Y.one('.' + classname);

    full = Y.one('.full_' + suffix);
    if (full) {
        full.hide(false);
        full.toggleClass('hidefull');
    }
    if (expand) {
        expand.on('click', function(e) {
            img = e.target;
            imgclasses = img.getAttribute('class').split(' ');
            for (i = 0; i < imgclasses.length; i++) {
                classname = imgclasses[i];
                if (classname.indexOf('expand_') == 0) {
                    thissuffix = classname.substr(7);
                }
            }
            summaryclassname = 'summary_' + thissuffix;
            summary = Y.one('.' + summaryclassname);
            if (summary) {
                summary.hide(false);
            }
            fullclassname = 'full_' + thissuffix;
            full = Y.one('.' + fullclassname);
            if (full) {
                full.show(false);
            }
        });
    }
}
