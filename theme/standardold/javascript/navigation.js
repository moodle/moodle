/**
 * This attaches a shadow to the dock when it has been drawn (added to the page)
 */
var shadow = {
    /**
     * This function create a series of DIV's appended to an element to give it a
     * shadow
     * @param {YUI} Y
     * @param {bool} top Displays a top shadow if true
     * @param {bool} right Displays a right shadow if true
     * @param {bool} bottom Displays a bottom shadow if true
     * @param {bool} left Displays a left shadow if true
     * @return {Y.Node}
     */
    create : function(Y, top, right, bottom, left) {
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

function customise_dock_for_theme() {
    // If we don't have M.blocks or Y then bail
    if (!M.core_dock) {
        return false;
    }
    // On draw completed add the ability to move the dock to from the left to the right
    M.core_dock.on('dock:drawcompleted', function() {
        M.core_dock.node.append(shadow.create(M.core_dock.Y, true, true, true, true));
        var movedock = M.core_dock.Y.Node.create('<img src="'+M.util.image_url('movedock', 'theme')+'" />');
        var c = M.core_dock.node.one('.controls');
        c.insertBefore(M.core_dock.Y.Node.create('<br />'), c.one('img'));
        c.insertBefore(movedock, c.one('br'));
        movedock.on('click', M.core_dock.move_dock, M.core_dock);
    });
    // When an item is added append a shadow
    M.core_dock.on('dock:itemadded', function(item) {
        item.on('dockeditem:showcomplete', function() {
            switch (M.core_dock.cfg.position) {
                case 'left':
                    M.core_dock.Y.one(this.panel.body).append(shadow.create(M.core_dock.Y, true, true, true, false));
                    break;
                case 'right':
                    M.core_dock.Y.one(this.panel.body).append(shadow.create(M.core_dock.Y, true, false, true, true));
                    break;
            }
        });
        item.on('dockeditem:hidestart', function() {
            shadow.remove(M.core_dock.Y.one('#dock_item_panel_'+this.id));
        });
        item.on('dockeditem:showstart', item.correct_panel_x_pos, item);
        item.on('dockeditem:resizecomplete', item.correct_panel_x_pos, item);
    });
    // Corrects the panel x position for the theme
    M.core_dock.item.prototype.correct_panel_x_pos = function() {
        var dockoffset = M.core_dock.Y.one('#dock_item_'+this.id+'_title').get('offsetWidth');
        var panelwidth = M.core_dock.Y.one(this.panel.body).get('offsetWidth');
        var screenwidth = parseInt(M.core_dock.Y.get(document.body).get('winWidth'));
        switch (M.core_dock.cfg.position) {
            case 'left':
                this.panel.cfg.setProperty('x', dockoffset);
                break;
            case 'right':
                this.panel.cfg.setProperty('x', (screenwidth-panelwidth-dockoffset-1));
                break;
        }
    }
    // Moves the dock from the left to right or vise versa
    M.core_dock.move_dock = function(e) {
        var oldclass = this.cfg.css.dock+'_'+this.cfg.position+'_'+this.cfg.orientation;
        switch (this.cfg.position) {
            case 'right':this.cfg.position = 'left';break;
            case 'left':this.cfg.position = 'right';break;
        }
        var newclass = this.cfg.css.dock+'_'+this.cfg.position+'_'+this.cfg.orientation;
        this.node.replaceClass(oldclass, newclass);
        this.Y.Cookie.set('dock_position', M.core_dock.cfg.position);
    };
    // When the dock is first drawn check to see if it should be moved
    M.core_dock.on('dock:drawstarted', function() {
        var positioncookie = M.core_dock.Y.Cookie.get('dock_position');
        if (positioncookie && positioncookie != 'null' && positioncookie !== M.core_dock.cfg.position) {
            var oldposition = M.core_dock.cfg.position;
            M.core_dock.cfg.position = positioncookie;
            if (M.core_dock.node) {
                M.core_dock.node.replaceClass(M.core_dock.cfg.css.dock+'_'+oldposition+'_'+M.core_dock.cfg.orientation, M.core_dock.cfg.css.dock+'_'+M.core_dock.cfg.position+'_'+M.core_dock.cfg.orientation);
            }
        }
    });
    
    return true;
}