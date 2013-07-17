YUI.add('moodle-core-actionmenu', function (Y, NAME) {

/**
 * Provides drop down menus for list of action links.
 *
 * @module moodle-core-actionmenu
 */

var BODY = Y.one(Y.config.doc.body),
    CSS = {
        MENUSHOWN : 'action-menu-shown'
    },
    SELECTOR = {
        MENU : '.moodle-actionmenu[data-enhance=moodle-core-actionmenu]',
        MENUCONTENT : '.menu[data-rel=menu-content]',
        TOGGLE : '.toggle-display'
    },
    ACTIONMENU,
    ALIGN = {
        TL : 'tl',
        TR : 'tr',
        BL : 'bl',
        BR : 'br'
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
ACTIONMENU = function() {
    ACTIONMENU.superclass.constructor.apply(this, arguments);
};
ACTIONMENU.prototype = {

    /**
     * The dialogue used for all action menu displays.
     * @property type
     * @type M.core.dialogue
     * @protected
     */
    dialogue : null,

    /**
     * An array of events attached during the display of the dialogue.
     * @property events
     * @type Object
     * @protected
     */
    events : [],

    /**
     * The node that owns the currently displayed menu.
     */
    owner : null,

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        Y.log('Initialising action menu manager', 'note', ACTIONMENU.NAME);
        var defaultalign = this.get('align').join('-');
        Y.all(SELECTOR.MENU).each(function() {
            var menucontent = this.one(SELECTOR.MENUCONTENT),
                toggle = this.one(SELECTOR.TOGGLE),
                align = menucontent.getData('align') || defaultalign;
            if (!menucontent) {
                return false;
            }
            toggle.set('aria-haspopup', true);
            menucontent.set('aria-hidden', true).addClass('align-'+align);
            if (menucontent.hasChildNodes()) {
                this.setAttribute('data-enhanced', '1');
            }
        });
        BODY.delegate('click', this.toggleMenu, SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);
        BODY.delegate('key', this.toggleMenu, 'enter,space', SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);
    },

    /**
     * Hides the menu if it is visible.
     * @method hideMenu
     */
    hideMenu : function() {
        if (this.dialogue) {
            this.dialogue.removeClass('show');
            this.dialogue.one(SELECTOR.MENUCONTENT).set('aria-hidden', true);
            this.dialogue = null;
        }
        if (this.owner) {
            this.owner.removeClass(CSS.MENUSHOWN);
            this.owner = null;
        }
        for (var i in this.events) {
            if (this.events[i].detach) {
                this.events[i].detach();
            }
        }
        this.events = [];
    },

    /**
     * Toggles the display of the menu.
     * @method toggleMenu
     * @param {EventFacade} e
     */
    toggleMenu : function(e) {
        // Stop all immediate propogation of the events we attach later will be
        // triggered and the dialogue immediately hidden again.
        e.halt(true);
        this.hideMenu();
        this.showMenu(e.target.ancestor(SELECTOR.MENU));
        // Close the menu if the user presses escape.
        this.events.push(BODY.on('key', this.hideMenu, 'esc', this));
        // Close the menu if the user clicks outside the menu.
        this.events.push(BODY.on('click', this.hideIfOutside, this));
        // Close the menu if the user focuses outside the menu.
        this.events.push(BODY.delegate('focus', this.hideIfOutside, '*', this));
    },

    /**
     * Hides the menu if the event happened outside the menu.
     *
     * @protected
     * @method hideIfOutside
     * @param {EventFacade} e
     */
    hideIfOutside : function(e) {
        if (!e.target.test(SELECTOR.MENU) && !e.target.ancestor(SELECTOR.MENU)) {
            this.hideMenu();
        }
    },

    /**
     * Displays the menu with the given content and alignment.
     * @param {Node} menu
     * @param Array align
     * @returns {M.core.dialogue|dialogue}
     */
    showMenu : function(menu) {
        Y.log('Displaying action menu', 'note', ACTIONMENU.NAME);
        var ownerselector = menu.getData('owner'),
            menucontent = menu.one(SELECTOR.MENUCONTENT);
        this.owner = (ownerselector) ? menu.ancestor(ownerselector) : null;
        this.dialogue = menu;
        menu.addClass('show');
        this.owner.addClass(CSS.MENUSHOWN);
        this.constrain(menucontent.set('aria-hidden', false));
        return true;
    },

    /**
     * Constrains the node to its the page width.
     *
     * @method constrain
     * @param {Node} node
     */
    constrain : function(node) {
        var selector = node.getData('constraint'),
            nx = node.getX(),
            ny = node.getY(),
            nwidth = node.get('offsetWidth'),
            nheight = node.get('offsetHeight'),
            cx = 0,
            cy = 0,
            cwidth,
            cheight,
            newwidth = null,
            newheight = null,
            newleft = null,
            newtop = null;


        if (selector) {
            selector = node.ancestor(selector);
        }
        if (selector) {
            cwidth = selector.get('offsetWidth');
            cheight = selector.get('offsetHeight');
            cx = selector.getX();
            cy = selector.getY();
        } else {
            cwidth = node.get('docWidth');
            cheight = node.get('docHeight');
        }

        // Constrain X.
        // First up if the width is more than the constrain its easily full width + full height
        if (nwidth > cwidth) {
            // The width of the constraint.
            newwidth = nwidth = cwidth;
            // The constraints xpoint.
            newleft = nx = cx;
        } else {
            if (nx < cx) {
                // If nx is less than cx we need to move it right.
                newleft = nx = cx;
            } else if (nx + nwidth > cx + cwidth) {
                // The top right of the node is outside of the constraint, move it in.
                newleft = cx + cwidth - nwidth;
            }
        }

        // Constrain Y.
        // First up if the width is more than the constrain its easily full width + full height
        if (nheight > cheight) {
            // The width of the constraint.
            newheight = nheight = cheight;
            // The constraints xpoint.
            newtop = ny = cy;
        } else {
            if (ny < cy) {
                // If ny is less than cy we need to move it right.
                newtop = ny = cy;
            } else if (ny + nheight > cy + cheight) {
                // The top right of the node is outside of the constraint, move it in.
                newtop = cy + cheight - nheight;
            }
        }

        if (newleft !== null) {
            node.setX(newleft);
        }
        if (newtop !== null) {
            node.setY(newtop);
        }
        if (newwidth !== null) {
            node.setStyle('width', newwidth.toString() + 'px');
        }
        if (newheight !== null) {
            node.setStyle('height', newheight.toString() + 'px');
        }
    }
};

Y.extend(ACTIONMENU, Y.Base, ACTIONMENU.prototype, {
    NAME : 'moodle-core-actionmenu',
    ATTRS : {
        align : {
            value : [
                ALIGN.TR, // The dialogue.
                ALIGN.BR  // The button
            ]
        }
    }
});

/**
 * Core namespace.
 * @static
 * @class core
 */
M.core = M.core || {};

/**
 * Actionmenu namespace.
 * @namespace M.core
 * @class actionmenu
 * @static
 */
M.core.actionmenu = M.core.actionmenu || {};

/**
 * Init function - will only ever create one instance of the actionmenu class.
 * @method init
 * @static
 * @param {Object} params
 */
M.core.actionmenu.init = M.core.actionmenu.init || function(params) {
    M.core.actionmenu.instance = M.core.actionmenu.instance || new ACTIONMENU(params);
};


}, '@VERSION@', {"requires": ["base", "event"]});
