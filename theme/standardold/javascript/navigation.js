// This attaches a shadow to the dock when it has been drawn (added to the page)
blocks.dock.on('dock:drawcompleted', function() {
    blocks.dock.node.append(shadow.create(true, true, true, true));
});
blocks.dock.on('dock:itemadded', function(item) {
    item.on('dockeditem:showcomplete', function() {
        Y.one('#dock_item_panel_'+this.id).append(shadow.create(true, true, true, false));
    });
    item.on('dockeditem:hidestart', function() {
        shadow.remove(Y.one('#dock_item_panel_'+this.id));
    });
});

var shadow = {
    /**
     * This function create a series of DIV's appended to an element to give it a
     * shadow
     * @param {bool} top Displays a top shadow if true
     * @param {bool} right Displays a right shadow if true
     * @param {bool} bottom Displays a bottom shadow if true
     * @param {bool} left Displays a left shadow if true
     * @return {Y.Node}
     */
    create : function(top, right, bottom, left) {
        var shadow = Y.Node.create('<div class="divshadow"></div>');
        if (Y.UA.ie > 0 && Y.UA.ie < 7) {
            // IE 6 doesn't like this shadow method
            return shadow;
        }
        if (top) shadow.append(Y.Node.create('<div class="shadow_top"></div>'));
        if (right) shadow.append(Y.Node.create('<div class="shadow_right"></div>'));
        if (bottom) shadow.append(Y.Node.create('<div class="shadow_bottom"></div>'));
        if (left) shadow.append(Y.Node.create('<div class="shadow_left"></div>'));
        if (top && left) shadow.append(Y.Node.create('<div class="shadow_top_left"></div>'));
        if (top && right) shadow.append(Y.Node.create('<div class="shadow_top_right"></div>'));
        if (bottom && left) shadow.append(Y.Node.create('<div class="shadow_bottom_left"></div>'));
        if (bottom && right) shadow.append(Y.Node.create('<div class="shadow_bottom_right"></div>'));
        return shadow;
    },
    /**
     * This function removes any shadows that a node and its children may have
     * @param {Y.Node} node The element to remove the shadow from
     * @return {bool}
     */
    remove : function(node) {
        node.all('.divshadow').remove();
    }
}