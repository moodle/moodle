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
        CAN_RECEIVE_FOCUS_SELECTOR: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
        MENU : '.moodle-actionmenu[data-enhance=moodle-core-actionmenu]',
        MENUCONTENT : '.menu[data-rel=menu-content]',
        MENUCONTENTCHILD: 'li a',
        MENUCHILD: '.menu li a',
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
     * The menu button that toggles this open.
     *
     * @property menulink
     * @type Node
     * @protected
     */
    menulink: null,

    /**
     * Called during the initialisation process of the object.
     * @method initializer
     */
    initializer : function() {
        Y.log('Initialising the action menu manager', 'debug', ACTIONMENU.NAME);
        Y.all(SELECTOR.MENU).each(this.enhance, this);
        BODY.delegate('click', this.toggleMenu, SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);
        BODY.delegate('key', this.toggleMenu, 'enter,space', SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);
    },

    /**
     * Enhances a menu adding aria attributes and flagging it as functional.
     *
     * @param {Node} menu
     * @returns {boolean}
     */
    enhance : function(menu) {
        var menucontent = menu.one(SELECTOR.MENUCONTENT),
            align;
        if (!menucontent) {
            return false;
        }
        align = menucontent.getData('align') || this.get('align').join('-');
        menu.one(SELECTOR.TOGGLE).set('aria-haspopup', true);
        menucontent.set('aria-hidden', true);
        if (!menucontent.hasClass('align-'+align)) {
            menucontent.addClass('align-'+align);
        }
        if (menucontent.hasChildNodes()) {
            menu.setAttribute('data-enhanced', '1');
        }
    },

    /**
     * Hides the menu if it is visible.
     * @method hideMenu
     */
    hideMenu : function() {
        if (this.dialogue) {
            Y.log('Hiding an action menu', 'debug', ACTIONMENU.NAME);
            this.dialogue.removeClass('show');
            this.dialogue.one(SELECTOR.MENUCONTENT).set('aria-hidden', true);
            this.dialogue = null;
        }
        for (var i in this.events) {
            if (this.events[i].detach) {
                this.events[i].detach();
            }
        }
        this.events = [];
        if (this.owner) {
            this.owner.removeClass(CSS.MENUSHOWN);
            this.owner = null;
        }

        if (this.menulink) {
            this.menulink.focus();
            this.menulink = null;
        }
    },

    /**
     * Toggles the display of the menu.
     * @method toggleMenu
     * @param {EventFacade} e
     */
    toggleMenu : function(e) {
        var menu = e.target.ancestor(SELECTOR.MENU),
            menuvisible = (menu.hasClass('show'));
        // Stop all immediate propogation of the events we attach later will be
        // triggered and the dialogue immediately hidden again.
        e.halt(true);
        this.hideMenu();
        if (menuvisible) {
            // The menu was visible and the user has clicked to toggle it again.
            return;
        }
        this.showMenu(e, menu);
        // Close the menu if the user presses escape.
        this.events.push(BODY.on('key', this.hideMenu, 'esc', this));
        // Close the menu if the user clicks outside the menu.
        this.events.push(BODY.on('click', this.hideIfOutside, this));
        // Close the menu if the user focuses outside the menu.
        this.events.push(BODY.delegate('focus', this.hideIfOutside, '*', this));

        // Check tabbing.
        this.events.push(menu.delegate('key', this.checkFocus, 'down:9', SELECTOR.MENUCHILD, this));
    },

    /**
     * Check current focus when moving around with the tab key.
     * This will ensure that when the etreme menu items are reached, the
     * menu is closed and the next DOM element is focused.
     *
     * @method checkFocus
     * @param {EventFacade} e The key event
     */
    checkFocus: function(e) {
        var nodelist = this.dialogue.all(SELECTOR.MENUCHILD),
            firstNode,
            lastNode;

        if (nodelist) {
            firstNode = nodelist.item(0);
            lastNode = nodelist.pop();
        }

        var menulink = this.menulink;
        if (e.target === firstNode && e.shiftKey) {
            this.hideMenu();
            e.preventDefault();
        } else if (e.target === lastNode && !e.shiftKey) {
            var next;
            if (this.hideMenu()) {
                next = menulink.next(SELECTOR.CAN_RECEIVE_FOCUS_SELECTOR);
                if (next) {
                    next.focus();
                }
            }
        }
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
     * @param {EventFacade} e
     * @param {Node} menu
     * @returns {M.core.dialogue|dialogue}
     */
    showMenu : function(e, menu) {
        Y.log('Displaying an action menu', 'debug', ACTIONMENU.NAME);
        var ownerselector = menu.getData('owner'),
            menucontent = menu.one(SELECTOR.MENUCONTENT),
            menuchild;
        this.owner = (ownerselector) ? menu.ancestor(ownerselector) : null;
        this.dialogue = menu;
        menu.addClass('show');
        if (this.owner) {
            this.owner.addClass(CSS.MENUSHOWN);
            this.menulink = this.owner.one(SELECTOR.TOGGLE);
        }
        this.constrain(menucontent.set('aria-hidden', false));

        if (e.type && e.type === 'key') {
            menuchild = menucontent.one(SELECTOR.MENUCONTENTCHILD);
            if (menuchild) {
                menuchild.focus();
            }
        }

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
            coverflow = 'auto',
            newwidth = null,
            newheight = null,
            newleft = null,
            newtop = null,
            boxshadow = null;

        if (selector) {
            selector = node.ancestor(selector);
        }
        if (selector) {
            cwidth = selector.get('offsetWidth');
            cheight = selector.get('offsetHeight');
            cx = selector.getX();
            cy = selector.getY();
            coverflow = selector.getStyle('overflow') || 'auto';
        } else {
            cwidth = node.get('docWidth');
            cheight = node.get('docHeight');
        }

        // Constrain X.
        // First up if the width is more than the constrain its easily full width + full height.
        if (nwidth > cwidth) {
            // The width of the constraint.
            newwidth = nwidth = cwidth;
            // The constraints xpoint.
            newleft = nx = cx;
        } else {
            if (nx < cx) {
                // If nx is less than cx we need to move it right.
                newleft = nx = cx;
            } else if (nx + nwidth >= cx + cwidth) {
                // The top right of the node is outside of the constraint, move it in.
                newleft = cx + cwidth - nwidth;
            }
        }

        // Constrain Y.
        if (nheight > cheight && coverflow.toLowerCase() === 'hidden') {
            // The node extends over the constrained area and would be clipped.
            // Reduce the height of the node and force its overflow to scroll.
            newheight = nheight = cheight;
            node.setStyle('overflow', 'auto');
        }
        // If the node is below the top of the constraint AND
        //    the node is longer than the constraint allows.
        if (ny >= cy && ny + nheight > cy + cheight) {
            // Move it up.
            newtop = cy + cheight - nheight;
            try {
                boxshadow = node.getStyle('boxShadow').replace(/.*? (\d+)px \d+px$/, '$1');
                if (new RegExp(/^\d+$/).test(boxshadow) && newtop - cy > boxshadow) {
                    newtop -= boxshadow;
                }
            } catch (ex) {
                Y.log('Failed to determine box-shadow margin.', 'warn', ACTIONMENU.NAME);
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
 *
 * @static
 * @property instance
 * @type {ACTIONMENU}
 */
M.core.actionmenu.instance = null;

/**
 * Init function - will only ever create one instance of the actionmenu class.
 * @method init
 * @static
 * @param {Object} params
 */
M.core.actionmenu.init = M.core.actionmenu.init || function(params) {
    M.core.actionmenu.instance = M.core.actionmenu.instance || new ACTIONMENU(params);
};

/**
 * Registers a new DOM node with the action menu causing it to be enhanced if required.
 * @param node
 * @returns {boolean}
 */
M.core.actionmenu.newDOMNode = function(node) {
    if (M.core.actionmenu.instance === null) {
        return true;
    }
    node.all(SELECTOR.MENU).each(M.core.actionmenu.instance.enhance, M.core.actionmenu.instance);
};


}, '@VERSION@', {"requires": ["base", "event"]});
