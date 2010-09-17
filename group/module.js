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
