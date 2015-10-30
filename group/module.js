/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
M.core_group = {
    hoveroverlay : null
};

M.core_group.init_hover_events = function(Y, events) {
    // Prepare the overlay if it hasn't already been created
    this.hoveroverlay = this.hoveroverlay || (function(){
        // New Y.Overlay
        var overlay = new Y.Overlay({
            bodyContent : 'Loading',
            visible : false,
            zIndex : 2
        });
        // Render it against the page
        overlay.render(Y.one('#page'));
        return overlay;
    })();

    // Iterate over the events and attach an event to display the description on
    // hover
    for (var id in events) {
        var node = Y.one('#'+id);
        if (node) {
            node = node.ancestor();
            node.on('mouseenter', function(e, content){
                M.core_group.hoveroverlay.set('xy', [this.getX()+(this.get('offsetWidth')/2),this.getY()+this.get('offsetHeight')-5]);
                M.core_group.hoveroverlay.set("bodyContent", content);
                M.core_group.hoveroverlay.show();
                M.core_group.hoveroverlay.get('boundingBox').setStyle('visibility', 'visible');
            }, node, events[id]);
            node.on('mouseleave', function(e){
                M.core_group.hoveroverlay.hide();
                M.core_group.hoveroverlay.get('boundingBox').setStyle('visibility', 'hidden');
            }, node);
        }
    }
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
