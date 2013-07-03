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
        Y.all(SELECTOR.MENU).each(function() {
            var menucontent = this.one(SELECTOR.MENUCONTENT);
            if (menucontent.hasChildNodes()) {
                this.setAttribute('data-enhanced', '1');
                this.setData('menucontent', menucontent);
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
            this.dialogue.hide();
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
        this.showMenu(e.target.ancestor(SELECTOR.MENU), align = this.calculateAlign(e.target));
        this.events.push(BODY.on('key', this.hideMenu, 'esc', this));
    },

    /**
     * Calculates the alignment point for the menu.
     * @param {Node} node
     * @returns {{node: *, points: Array}}
     */
    calculateAlign : function(node) {
        var points = [Y.WidgetPositionAlign.TR, Y.WidgetPositionAlign.BR];
        if (BODY.hasClass('dir-rtl')) {
            points = [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL];
        }
        return {node: node, points : points};
    },

    /**
     * Displays the menu with the given content and alignment.
     * @param {Node} bodyContent
     * @param Array align
     * @returns {M.core.dialogue|dialogue}
     */
    showMenu : function(menu, align) {
        var bodyContent = menu.getData('menucontent'),
            ownerSelector = menu.getData('owner');
        this.owner = (ownerSelector) ? menu.ancestor(ownerSelector) : null;
        if (!this.dialogue) {
            this.dialogue = new M.core.dialogue({
                visible : false,
                bodyContent : bodyContent,
                align : align,
                lightbox : false,
                closeButton : false,
                additionalBaseClass : 'action-menu-dialogue',
                width : 'auto',
                constrain : BODY,
                hideOn : [
                    {
                        eventName: 'clickoutside'
                    }
                ]
            });
            this.dialogue.after('visibleChange', this.flagOwner, this);
            this.flagOwner({attrName: 'visible', newVal: true});
        } else {
            if (this.dialogue.get('visible')) {
                this.dialogue.hide();
            }
            this.dialogue.set('bodyContent', bodyContent);
            this.dialogue.set('align', align);
            this.dialogue.show();
        }
        return this.dialogue;
    },

    /**
     * Flags the owner appropriately when the visibility of the dialogue changes.
     * @param {EventFacade} e
     */
    flagOwner : function(e) {
        if (this.owner && e.attrName === 'visible') {
            if (e.newVal === true) {
                this.owner.addClass(CSS.MENUSHOWN);
            } else {
                this.owner.removeClass(CSS.MENUSHOWN);
                this.owner = null;
            }
        }
    }
};

Y.extend(ACTIONMENU, Y.Base, ACTIONMENU.prototype, {
    NAME : 'moodle-core-actionmenu',
    ATTRS : {
        /**
         * A selector to use to get the owner of the menu if there is one.
         * @attribute owner
         * @type String
         */
        owner : {
            value : null
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


}, '@VERSION@', {"requires": ["base", "event", "moodle-core-notification"]});
