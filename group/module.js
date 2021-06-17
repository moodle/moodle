/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
M.core_group = {
    hoveroverlay : null
};

M.core_group.init_index = function(Y, wwwroot, courseid) {
    M.core_group.groupsCombo = new UpdatableGroupsCombo(wwwroot, courseid);
    M.core_group.membersCombo = new UpdatableMembersCombo(wwwroot, courseid);
};

M.core_group.groupslist = function(Y, preventgroupremoval) {
    var actions = {
        init : function() {
            // We need to add check_deletable both on change for the groups, and then call it the first time the page loads
            Y.one('#groups').on('change', this.check_deletable, this);
            this.check_deletable();
        },
        check_deletable : function() {
            // Ensure that if the 'preventremoval' attribute is set, the delete button is greyed out
            var candelete = true;
            var optionselected = false;
            Y.one('#groups').get('options').each(function(option) {
                if (option.get('selected')) {
                    optionselected = true;
                    if (option.getAttribute('value') in preventgroupremoval) {
                        candelete = false;
                    }
                }
            }, this);
            var deletebutton = Y.one('#deletegroup');
            if (candelete && optionselected) {
                deletebutton.removeAttribute('disabled');
            } else {
                deletebutton.setAttribute('disabled', 'disabled');
            }
        }
    }
    actions.init();
};
