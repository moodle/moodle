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
        MENUBAR: '[role="menubar"]',
        MENUITEM: '[role="menuitem"]',
        MENUCONTENT : '.menu[data-rel=menu-content]',
        MENUCONTENTCHILD: 'li a',
        MENUCHILD: '.menu li a',
        TOGGLE : '.toggle-display',
        KEEPOPEN: '[data-keepopen="1"]',
        MENUBARITEMS: [
            '[role="menubar"] > [role="menuitem"]',
            '[role="menubar"] > [role="presentation"] > [role="menuitem"]'
        ],
        MENUITEMS: [
            '> [role="menuitem"]',
            '> [role="presentation"] > [role="menuitem"]'
        ]
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
 * @extends Base
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
     *
     * @property owner
     * @type Node
     * @default null
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
     * The set of menu nodes.
     *
     * @property menuChildren
     * @type NodeList
     * @protected
     */
    menuChildren: null,

    /**
     * The first menu item.
     *
     * @property firstMenuChild
     * @type Node
     * @protected
     */
    firstMenuChild: null,

    /**
     * The last menu item.
     *
     * @property lastMenuChild
     * @type Node
     * @protected
     */
    lastMenuChild: null,

    /**
     * Called during the initialisation process of the object.
     *
     * @method initializer
     */
    initializer : function() {
        Y.log('Initialising the action menu manager', 'debug', ACTIONMENU.NAME);
        Y.all(SELECTOR.MENU).each(this.enhance, this);
        BODY.delegate('key', this.moveMenuItem, 'down:37,39', SELECTOR.MENUBARITEMS.join(','), this);

        BODY.delegate('click', this.toggleMenu, SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);
        BODY.delegate('key', this.showIfHidden, 'down:enter,38,40', SELECTOR.MENU + ' ' + SELECTOR.TOGGLE, this);

        // Ensure that we toggle on menuitems when the spacebar is pressed.
        BODY.delegate('key', function(e) {
            e.currentTarget.simulate('click');
            e.preventDefault();
        }, 'down:32', SELECTOR.MENUBARITEMS.join(','));
    },

    /**
     * Enhances a menu adding aria attributes and flagging it as functional.
     *
     * @method enhance
     * @param {Node} menu
     * @return boolean
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
     * Handle movement between menu items in a menubar.
     *
     * @method moveMenuItem
     * @param {EventFacade} e The event generating the move request
     * @chainable
     */
    moveMenuItem: function(e) {
        var nextFocus,
            menuitem = e.target.ancestor(SELECTOR.MENUITEM, true);

        if (e.keyCode === 37) {
            nextFocus = this.getMenuItem(menuitem, true);
        } else if (e.keyCode === 39) {
            nextFocus = this.getMenuItem(menuitem);
        }

        if (nextFocus) {
            nextFocus.focus();
        }
        return this;
    },

    /**
     * Get the next menuitem in a menubar.
     *
     * @method getMenuItem
     * @param {Node} currentItem The currently focused item in the menubar
     * @param {Boolean} [previous=false] Move backwards in the menubar instead of forwards
     * @return {Node|null} The next item, or null if none was found
     */
    getMenuItem: function(currentItem, previous) {
        var menubar = currentItem.ancestor(SELECTOR.MENUBAR),
            menuitems,
            next;

        if (!menubar) {
            return null;
        }

        menuitems = menubar.all(SELECTOR.MENUITEMS.join(','));

        if (!menuitems) {
            return null;
        }

        var childCount = menuitems.size();

        if (childCount === 1) {
            // Only one item, exit now because we should already be on it.
            return null;
        }

        // Determine the next child.
        var index = 0,
            direction = 1,
            checkCount = 0;

        // Work out the index of the currently selected item.
        for (index = 0; index < childCount; index++) {
            if (menuitems.item(index) === currentItem) {
                break;
            }
        }

        // Check that the menu item was found - otherwise return null.
        if (menuitems.item(index) !== currentItem) {
            return null;
        }

        // Reverse the direction if we want the previous item.
        if (previous) {
            direction = -1;
        }

        do {
            // Update the index in the direction of travel.
            index += direction;

            next = menuitems.item(index);

            // Check that we don't loop multiple times.
            checkCount++;
        } while (next && next.hasAttribute('hidden'));

        return next;
    },

    /**
     * Hides the menu if it is visible.
     * @param {EventFacade} e
     * @method hideMenu
     */
    hideMenu : function(e) {
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
            if (!e || e.type != 'click') {
                // We needed to test !e to retain backwards compatiablity if the event is not passed.
                this.menulink.focus();
            }
            this.menulink = null;
        }
    },

    showIfHidden: function(e) {
        var menu = e.target.ancestor(SELECTOR.MENU),
            menuvisible = (menu.hasClass('show'));

        if (!menuvisible) {
            e.preventDefault();
            this.showMenu(e, menu);
        }
        return this;
    },

    /**
     * Toggles the display of the menu.
     * @method toggleMenu
     * @param {EventFacade} e
     */
    toggleMenu : function(e) {
        var menu = e.target.ancestor(SELECTOR.MENU),
            menuvisible = (menu.hasClass('show'));

        // Prevent event propagation as it will trigger the hideIfOutside event handler in certain situations.
        e.halt(true);
        this.hideMenu(e);
        if (menuvisible) {
            // The menu was visible and the user has clicked to toggle it again.
            return;
        }
        this.showMenu(e, menu);
    },

    /**
     * Handle keyboard events when the menu is open. We respond to:
     * * escape (exit)
     * * tab (move to next menu item)
     * * up/down (move to previous/next menu item)
     *
     * @method handleKeyboardEvent
     * @param {EventFacade} e The key event
     */
    handleKeyboardEvent: function(e) {
        var next;
        var markEventHandled = function(e) {
            e.preventDefault();
            e.stopPropagation();
        };

        // Handle when the menu is still selected.
        if (e.currentTarget.ancestor(SELECTOR.TOGGLE, true)) {
            if ((e.keyCode === 40 || (e.keyCode === 9 && !e.shiftKey)) && this.firstMenuChild) {
                this.firstMenuChild.focus();
                markEventHandled(e);
            } else if (e.keyCode === 38 && this.lastMenuChild) {
                this.lastMenuChild.focus();
                markEventHandled(e);
            } else if (e.keyCode === 9 && e.shiftKey) {
                this.hideMenu(e);
                markEventHandled(e);
            }
            return this;
        }

        if (e.keyCode === 27) {
            // The escape key was pressed so close the menu.
            this.hideMenu(e);
            markEventHandled(e);

        } else if (e.keyCode === 32) {
            // The space bar was pressed. Trigger a click.
            markEventHandled(e);
            e.currentTarget.simulate('click');
        } else if (e.keyCode === 9) {
            // The tab key was pressed. Tab moves forwards, Shift + Tab moves backwards through the menu options.
            // We only override the Shift + Tab on the first option, and Tab on the last option to change where the
            // focus is moved to.
            if (e.target === this.firstMenuChild && e.shiftKey) {
                this.hideMenu(e);
                markEventHandled(e);
            } else if (e.target === this.lastMenuChild && !e.shiftKey) {
                if (this.hideMenu(e)) {
                    // Determine the next selector and focus on it.
                    next = this.menulink.next(SELECTOR.CAN_RECEIVE_FOCUS_SELECTOR);
                    if (next) {
                        next.focus();
                        markEventHandled(e);
                    }
                }
            }

        } else if (e.keyCode === 38 || e.keyCode === 40) {
            // The up (38) or down (40) key was pushed.
            // On cursor moves we loops through the menu rather than exiting it as in the tab behaviour.
            var found = false,
                index = 0,
                direction = 1,
                checkCount = 0;

            // Determine which menu item is currently selected.
            while (!found && index < this.menuChildren.size()) {
                if (this.menuChildren.item(index) === e.currentTarget) {
                    found = true;
                } else {
                    index++;
                }
            }

            if (!found) {
                Y.log("Unable to find this menu item in the list of menu children", 'debug', 'moodle-core-actionmenu');
                return;
            }

            if (e.keyCode === 38) {
                // Moving up so reverse the direction.
                direction = -1;
            }

            // Try to find the next
            do {
                index += direction;
                if (index < 0) {
                    index = this.menuChildren.size() - 1;
                } else if (index >= this.menuChildren.size()) {
                    // Handle wrapping.
                    index = 0;
                }
                next = this.menuChildren.item(index);

                // Add a counter to ensure we don't get stuck in a loop if there's only one visible menu item.
                checkCount++;
            } while (checkCount < this.menuChildren.size() && next !== e.currentTarget && next.hasClass('hidden'));

            if (next) {
                next.focus();
                markEventHandled(e);
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
        if (!e.target.ancestor(SELECTOR.MENUCONTENT, true)) {
            this.hideMenu(e);
        }
    },

    /**
     * Displays the menu with the given content and alignment.
     *
     * @method showMenu
     * @param {EventFacade} e
     * @param {Node} menu
     * @return M.core.dialogue
     */
    showMenu : function(e, menu) {
        Y.log('Displaying an action menu', 'debug', ACTIONMENU.NAME);
        var ownerselector = menu.getData('owner'),
            menucontent = menu.one(SELECTOR.MENUCONTENT);
        this.owner = (ownerselector) ? menu.ancestor(ownerselector) : null;
        this.dialogue = menu;
        menu.addClass('show');
        if (this.owner) {
            this.owner.addClass(CSS.MENUSHOWN);
            this.menulink = this.owner.one(SELECTOR.TOGGLE);
        } else {
            this.menulink = e.target.ancestor(SELECTOR.TOGGLE, true);
        }
        this.constrain(menucontent.set('aria-hidden', false));

        this.menuChildren = this.dialogue.all(SELECTOR.MENUCHILD);
        if (this.menuChildren) {
            this.firstMenuChild = this.menuChildren.item(0);
            this.lastMenuChild  = this.menuChildren.item(this.menuChildren.size() - 1);

            this.firstMenuChild.focus();
        }

        // Close the menu if the user presses escape.
        this.events.push(BODY.on('key', this.hideMenu, 'esc', this));

        // Close the menu if the user clicks outside the menu.
        this.events.push(BODY.on('click', this.hideIfOutside, this));

        // Close the menu if the user focuses outside the menu.
        this.events.push(BODY.delegate('focus', this.hideIfOutside, '*', this));

        // Check keyboard changes.
        this.events.push(
            menu.delegate('key', this.handleKeyboardEvent,
                          'down:9, 27, 38, 40, 32', SELECTOR.MENUCHILD + ', ' + SELECTOR.TOGGLE, this)
            );

        // Close the menu after a button was pushed.
        this.events.push(menu.delegate('click', function(e) {
            if (e.currentTarget.test(SELECTOR.KEEPOPEN)) {
                return;
            }
            this.hideMenu(e);
        }, SELECTOR.MENUCHILD, this));

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

M.core = M.core || {};
M.core.actionmenu = M.core.actionmenu || {};

/**
 *
 * @static
 * @property M.core.actionmenu.instance
 * @type {ACTIONMENU}
 */
M.core.actionmenu.instance = null;

/**
 * Init function - will only ever create one instance of the actionmenu class.
 *
 * @method M.core.actionmenu.init
 * @static
 * @param {Object} params
 */
M.core.actionmenu.init = M.core.actionmenu.init || function(params) {
    M.core.actionmenu.instance = M.core.actionmenu.instance || new ACTIONMENU(params);
};

/**
 * Registers a new DOM node with the action menu causing it to be enhanced if required.
 *
 * @method M.core.actionmenu.newDOMNode
 * @param node
 * @return {boolean}
 */
M.core.actionmenu.newDOMNode = function(node) {
    if (M.core.actionmenu.instance === null) {
        return true;
    }
    node.all(SELECTOR.MENU).each(M.core.actionmenu.instance.enhance, M.core.actionmenu.instance);
};


}, '@VERSION@', {"requires": ["base", "event", "node-event-simulate"]});
