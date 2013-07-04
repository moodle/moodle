/**
 * Provides drop down menus for list of action links.
 *
 * @module moodle-core-actionmenu
 */

CSS = {
    ACTIONS : '.actions'
};

RESOURCES = {
    MENUICON : {
        pix : 't/contextmenu',
        component : 'moodle'
    }
};

/**
 * Action menu support.
 * This converts a generic list of links into a drop down menu opened by hovering or clicking
 * on a menu icon.
 *
 * @namespace M.core.actionmenu
 * @class ActionMenu
 * @constructor
 * @extends Y.Base
 */
var ACTIONMENU = function() {
    ACTIONMENU.superclass.constructor.apply(this, arguments);
};

Y.extend(ACTIONMENU, Y.Base, {
    initializer : function() {
        var rightalignpoints = [Y.WidgetPositionAlign.TR, Y.WidgetPositionAlign.BR];
        var leftalignpoints = [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL];

        Y.all(CSS.ACTIONS).each(function() {

            // Prepend menu icon before the list.
            var imgnode = Y.Node.create('<img/>');
            var imgsrc = M.util.image_url(RESOURCES.MENUICON.pix, RESOURCES.MENUICON.component);
            var linknode = Y.Node.create('<a/>');
            var parentnode = this.get('parentNode');
            var alignpoints = rightalignpoints;
            var menux = 0;

            if (!this.hasChildNodes()) {
                return this;
            }

            linknode.setAttribute('href', '#');
            linknode.addClass('actionmenu');

            imgnode.setAttribute('src', imgsrc);

            linknode.appendChild(imgnode);
            parentnode.insertBefore(linknode, this);

            menux = (parentnode.getX() + parseInt(parentnode.getComputedStyle('width'), 10));

            if (menux < (this.get('winWidth') / 2)) {
                alignpoints = leftalignpoints;
            }

            var overlay = new Y.Overlay({
                bodyContent : this,
                visible: false,
                align: {node: imgnode, points: alignpoints},
                zIndex: 10
            });

            overlay.render();
            overlay.get('boundingBox').on('focusoutside', overlay.hide, overlay);

            // Create bi-directional links between the menu and the open button.
            linknode.setData('actionmenu-menu', overlay);
            this.all('a').each(function () {
                this.setData('actionmenu-link', linknode);
            });
            return this;
        });

        Y.one('body').delegate('click', this.toggleMenu, '.actionmenu');
        Y.one('body').on('key', this.hideAllMenus, 'esc', this);
    },

    hideAllMenus : function() {
        // Hide all actionmenus.
        Y.all('.actionmenu').each(function() {
            var overlay = this.getData('actionmenu-menu');
            if (overlay) {
                overlay.hide();
            }
        });
    },

    toggleMenu : function(e) {
        var overlay = this.getData('actionmenu-menu');
        if (typeof overlay !== "undefined") {
            if (overlay.get('visible')) {
                overlay.hide();
            } else {
                M.core.actionmenu.instance.hideAllMenus();
                overlay.show();
            }
        }
        e.preventDefault();
    }

}, {
    NAME : 'moodle-core-actionmenu',
    ATTRS : { }
});

/**
 * Core namespace.
 */
M.core = M.core || {};

/**
 * Actionmenu namespace.
 */
M.core.actionmenu = M.core.actionmenu || {};

/**
 * Init function - will only ever create one instance of the actionmenu class.
 */
M.core.actionmenu.init = M.core.actionmenu.init || function(params) {
    M.core.actionmenu.instance = M.core.actionmenu.instance || new ACTIONMENU(params);
};
