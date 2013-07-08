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
        MENU : '[data-enhance=moodle-core-actionmenu]',
        MENUCONTENT : '[data-rel=menu-content]',
        TOGGLE : '.toggle-display'
    },
    ACTIONMENU;

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
        Y.all(SELECTOR.MENU).each(function() {
            var menucontent = this.one(SELECTOR.MENUCONTENT);
            menucontent.set('aria-hidden', true);
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
        if (!e.target.test('.secondary') && !e.target.ancestor('.secondary')) {
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
        var ownerSelector = menu.getData('owner'),
            menuContent = menu.one(SELECTOR.MENUCONTENT);
        this.owner = (ownerSelector) ? menu.ancestor(ownerSelector) : null;
        this.dialogue = menu;
        menu.addClass('show');
        this.owner.addClass(CSS.MENUSHOWN);
        this.constrain(menuContent.set('aria-hidden', true));
        return true;
    },

    /**
     * Constrains the node to its the page width.
     *
     * @method constrain
     * @param {Node} node
     */
    constrain : function(node) {
        var x = node.getX(),
            nodewidth = node.get('offsetWidth'),
            winwidth = node.get('winWidth'),
            newwidth = null,
            newleft = null;

        if (x < 0) {
            x = 0;
            newleft = 0;
        } else if (x > winwidth) {
            x = winwidth;
            newleft = winwidth;
        }

        if (x + nodewidth > winwidth) {
            if (nodewidth > winwidth) {
                newleft = 0;
                newwidth = winwidth;
            } else {
                newleft = winwidth - nodewidth;
            }
        }

        if (newleft !== null) {
            node.setX(newleft);
        }
        if (newwidth !== null) {
            node.setStyle('width', newwidth.toString() + 'px');
        }
    }
};

Y.extend(ACTIONMENU, Y.Base, ACTIONMENU.prototype, {
    NAME : 'moodle-core-actionmenu',
    ATTRS : {
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
