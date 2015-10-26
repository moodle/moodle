
M.core_user = {};

M.core_user.init_participation = function(Y) {
	Y.on('change', function() {
		var action = Y.one('#formactionid');
		if (action.get('value') == '') {
			return;
		}
        var ok = false;
        Y.all('input.usercheckbox').each(function() {
            if (this.get('checked')) {
                ok = true;
            }
        });
        if (!ok) {
            // no checkbox selected
            return;
        }
        Y.one('#participantsform').submit();
	}, '#formactionid');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', 'checked');
        });
    }, '#checkall');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', '');
        });
    }, '#checknone');
};

M.core_user.init_tree = function(Y, expand_all, htmlid) {
    Y.use('yui2-treeview', function(Y) {
        var tree = new Y.YUI2.widget.TreeView(htmlid);

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


M.core_user.init_profile_edit = function(Y) {
    var addwork2 = Y.one('#id_profile_field_addwork2');
    var addwork3 = Y.one('#id_profile_field_addwork3');
    
    if (addwork2)
    {
        if (addwork2.get('checked'))
        {
            Y.one('#fitem_id_profile_field_faculty2').show();
            Y.one('#fitem_id_profile_field_department2').show();
            Y.one('#fitem_id_profile_field_worktype2').show();
            Y.one('#fitem_id_profile_field_post2').show();
            Y.one('#fitem_id_profile_field_addwork3').show();
        }
        else
        {
            Y.one('#fitem_id_profile_field_faculty2').hide();
            Y.one('#fitem_id_profile_field_department2').hide();
            Y.one('#fitem_id_profile_field_worktype2').hide();
            Y.one('#fitem_id_profile_field_post2').hide();
            Y.one('#fitem_id_profile_field_addwork3').hide();
        }
    }
    
    if (addwork3)
    {
        if (addwork3.get('checked'))
        {
            Y.one('#fitem_id_profile_field_faculty3').show();
            Y.one('#fitem_id_profile_field_department3').show();
            Y.one('#fitem_id_profile_field_worktype3').show();
            Y.one('#fitem_id_profile_field_post3').show();
        }
        else
        {
            Y.one('#fitem_id_profile_field_faculty3').hide();
            Y.one('#fitem_id_profile_field_department3').hide();
            Y.one('#fitem_id_profile_field_worktype3').hide();
            Y.one('#fitem_id_profile_field_post3').hide();
        }
    }
    if (addwork2 && addwork3) 
    {
        addwork2.on('change', function (e)
        {
            if (e.target.get('checked'))
            {
                Y.one('#fitem_id_profile_field_faculty2').show();
                Y.one('#fitem_id_profile_field_department2').show();
                Y.one('#fitem_id_profile_field_worktype2').show();
                Y.one('#fitem_id_profile_field_post2').show();
                Y.one('#fitem_id_profile_field_addwork3').show();
            }
            else
            {
                Y.one('#id_profile_field_faculty2').set('selectedIndex',0);
                Y.one('#id_profile_field_department2').set('selectedIndex',0);
                Y.one('#id_profile_field_worktype2').set('selectedIndex',0);
                Y.one('#id_profile_field_post2').set('selectedIndex',0);
                Y.one('#id_profile_field_addwork3').set('checked',0);
                Y.one('#fitem_id_profile_field_faculty2').hide();
                Y.one('#fitem_id_profile_field_department2').hide();
                Y.one('#fitem_id_profile_field_worktype2').hide();
                Y.one('#fitem_id_profile_field_post2').hide();
                Y.one('#fitem_id_profile_field_addwork3').hide();
                
                Y.one('#id_profile_field_faculty3').set('selectedIndex',0);
                Y.one('#id_profile_field_department3').set('selectedIndex',0);
                Y.one('#id_profile_field_worktype3').set('selectedIndex',0);
                Y.one('#id_profile_field_post3').set('selectedIndex',0);
                Y.one('#fitem_id_profile_field_faculty3').hide();
                Y.one('#fitem_id_profile_field_department3').hide();
                Y.one('#fitem_id_profile_field_worktype3').hide();
                Y.one('#fitem_id_profile_field_post3').hide();
            }
        });

        addwork3.on('change', function (e)
        {
            if (e.target.get('checked'))
            {
                Y.one('#fitem_id_profile_field_faculty3').show();
                Y.one('#fitem_id_profile_field_department3').show();
                Y.one('#fitem_id_profile_field_worktype3').show();
                Y.one('#fitem_id_profile_field_post3').show();
            }
            else
            {
                Y.one('#id_profile_field_faculty3').set('selectedIndex',0);
                Y.one('#id_profile_field_department3').set('selectedIndex',0);
                Y.one('#id_profile_field_worktype3').set('selectedIndex',0);
                Y.one('#id_profile_field_post3').set('selectedIndex',0);
                Y.one('#fitem_id_profile_field_faculty3').hide();
                Y.one('#fitem_id_profile_field_department3').hide();
                Y.one('#fitem_id_profile_field_worktype3').hide();
                Y.one('#fitem_id_profile_field_post3').hide();
            }
        });
    }
};
