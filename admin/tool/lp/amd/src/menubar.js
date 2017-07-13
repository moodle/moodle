// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Aria menubar functionality. Enhances a simple nested list structure into a full aria widget.
 * Based on the open ajax example: http://oaa-accessibility.org/example/26/
 *
 * @module     tool_lp/menubar
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /** @property {boolean}  Flag to indicate if we have already registered a click event handler for the document. */
    var documentClickHandlerRegistered = false;

    /** @property {boolean} Flag to indicate whether there's an active, open menu. */
    var menuActive = false;

    /**
     * Close all open submenus anywhere in the page (there should only ever be one open at a time).
     *
     * @method closeAllSubMenus
     */
    var closeAllSubMenus = function() {
        $('.tool-lp-menu .tool-lp-sub-menu').attr('aria-hidden', 'true');
        // Every menu's closed at this point, so set the menu active flag to false.
        menuActive = false;
    };

    /**
     * Constructor
     *
     * @param {$} menuRoot Jquery collection matching the root of the menu.
     * @param {Function[]} handlers, called when a menu item is chosen.
     */
    var Menubar = function(menuRoot, handlers) {
        // Setup private class variables.
        this.menuRoot = menuRoot;
        this.handlers = handlers;
        this.rootMenus = this.menuRoot.children('li');
        this.subMenus = this.rootMenus.children('ul');
        this.subMenuItems = this.subMenus.children('li');
        this.allItems = this.rootMenus.add(this.subMenuItems);
        this.activeItem = null;
        this.isChildOpen = false;

        this.keys = {
            tab:    9,
            enter:  13,
            esc:    27,
            space:  32,
            left:   37,
            up:     38,
            right:  39,
            down:   40
        };

        this.addAriaAttributes();
        // Add the event listeners.
        this.addEventListeners();
    };

    /**
     * Open a submenu, first it closes all other sub-menus and sets the open direction.
     * @method openSubMenu
     * @param {Node} menu
     */
    Menubar.prototype.openSubMenu = function(menu) {
        this.setOpenDirection();
        closeAllSubMenus();
        menu.attr('aria-hidden', 'false');
        // Set menu active flag to true when a menu is opened.
        menuActive = true;
    };


    /**
     * Bind the event listeners to the DOM
     * @method addEventListeners
     */
    Menubar.prototype.addEventListeners = function() {
        var currentThis = this;

        // When clicking outside the menubar.
        if (documentClickHandlerRegistered === false) {
            $(document).click(function() {
                // Check if a menu is opened.
                if (menuActive) {
                    // Close menu.
                    closeAllSubMenus();
                }
            });
            // Set this flag to true so that we won't need to add a document click handler for the other Menubar instances.
            documentClickHandlerRegistered = true;
        }

        // Hovers.
        this.subMenuItems.mouseenter(function() {
            $(this).addClass('menu-hover');
            return true;
        });

        this.subMenuItems.mouseout(function() {
            $(this).removeClass('menu-hover');
            return true;
        });

        // Mouse listeners.
        this.allItems.click(function(e) {
            return currentThis.handleClick($(this), e);
        });

        // Key listeners.
        this.allItems.keydown(function(e) {
            return currentThis.handleKeyDown($(this), e);
        });

        this.allItems.focus(function() {
            return currentThis.handleFocus($(this));
        });

        this.allItems.blur(function() {
            return currentThis.handleBlur($(this));
        });
    };

    /**
     * Process click events for the top menus.
     *
     * @method handleClick
     * @param {Object} item is the jquery object of the item firing the event
     * @param {Event} e is the associated event object
     * @return {boolean} Returns false
     */
    Menubar.prototype.handleClick = function(item, e) {
        e.stopPropagation();

        var parentUL = item.parent();

        if (parentUL.is('.tool-lp-menu')) {
            // Toggle the child menu open/closed.
            if (item.children('ul').first().attr('aria-hidden') == 'true') {
                this.openSubMenu(item.children('ul').first());
            } else {
                item.children('ul').first().attr('aria-hidden', 'true');
            }
        } else {
            // Remove hover and focus styling.
            this.allItems.removeClass('menu-hover menu-focus');

            // Clear the active item.
            this.activeItem = null;

            // Close the menu.
            this.menuRoot.find('ul').not('.root-level').attr('aria-hidden', 'true');
            // Follow any link, or call the click handlers.
            var anchor = item.find('a').first();
            var clickEvent = new $.Event('click');
            clickEvent.target = anchor;
            var eventHandled = false;
            if (this.handlers) {
                $.each(this.handlers, function(selector, handler) {
                    if (eventHandled) {
                        return;
                    }
                    if (item.find(selector).length > 0) {
                        var callable = $.proxy(handler, anchor);
                        // False means stop propogatting events.
                        eventHandled = (callable(clickEvent) === false) || clickEvent.isDefaultPrevented();
                    }
                });
            }
            // If we didn't find a handler, and the HREF is # that probably means that
            // we are handling it from somewhere else. Let's just do nothing in that case.
            if (!eventHandled && anchor.attr('href') !== '#') {
                window.location.href = anchor.attr('href');
            }
        }
        return false;
    };

    /*
     * Process focus events for the menu.
     *
     * @method handleFocus
     * @param {Object} item is the jquery object of the item firing the event
     * @return boolean Returns false
     */
    Menubar.prototype.handleFocus = function(item) {

        // If activeItem is null, we are getting focus from outside the menu. Store
        // the item that triggered the event.
        if (this.activeItem === null) {
            this.activeItem = item;
        } else if (item[0] != this.activeItem[0]) {
            return true;
        }

        // Get the set of jquery objects for all the parent items of the active item.
        var parentItems = this.activeItem.parentsUntil('ul.tool-lp-menu').filter('li');

        // Remove focus styling from all other menu items.
        this.allItems.removeClass('menu-focus');

        // Add focus styling to the active item.
        this.activeItem.addClass('menu-focus');

        // Add focus styling to all parent items.
        parentItems.addClass('menu-focus');

        // If the bChildOpen flag has been set, open the active item's child menu (if applicable).
        if (this.isChildOpen === true) {

            var itemUL = item.parent();

            // If the itemUL is a root-level menu and item is a parent item,
            // show the child menu.
            if (itemUL.is('.tool-lp-menu') && (item.attr('aria-haspopup') == 'true')) {
                this.openSubMenu(item.children('ul').first());
            }
        }

        return true;
    };

    /*
     * Process blur events for the menu.
     *
     * @method handleBlur
     * @param {Object} item is the jquery object of the item firing the event
     * @return boolean Returns false
     */
    Menubar.prototype.handleBlur = function(item) {
        item.removeClass('menu-focus');

        return true;
    };

    /*
     * Determine if the menu should open to the left, or the right,
     * based on the screen size and menu position.
     * @method setOpenDirection
     */
    Menubar.prototype.setOpenDirection = function() {
        var pos = this.menuRoot.offset();
        var isRTL = $(document.body).hasClass('dir-rtl');
        var openLeft = true;
        var heightmenuRoot = this.rootMenus.outerHeight();
        var widthmenuRoot = this.rootMenus.outerWidth();
        // Sometimes the menuMinWidth is not enough to figure out if menu exceeds the window width.
        // So we have to calculate the real menu width.
        var subMenuContainer = this.rootMenus.find('ul.tool-lp-sub-menu');

        // Reset margins.
        subMenuContainer.css('margin-right', '');
        subMenuContainer.css('margin-left', '');
        subMenuContainer.css('margin-top', '');

        subMenuContainer.attr('aria-hidden', false);
        var menuRealWidth = subMenuContainer.outerWidth(),
            menuRealHeight = subMenuContainer.outerHeight();

        var margintop = null,
            marginright = null,
            marginleft = null;
        var top = pos.top - $(window).scrollTop();
        // Top is the same for RTL and LTR.
        if (top + menuRealHeight > $(window).height()) {
            margintop = menuRealHeight + heightmenuRoot;
            subMenuContainer.css('margin-top', '-' + margintop + 'px');
        }

        if (isRTL) {
            if (pos.left - menuRealWidth < 0) {
                marginright = menuRealWidth - widthmenuRoot;
                subMenuContainer.css('margin-right', '-' + marginright + 'px');
            }
        } else {
            if (pos.left + menuRealWidth > $(window).width()) {
                marginleft = menuRealWidth - widthmenuRoot;
                subMenuContainer.css('margin-left', '-' + marginleft + 'px');
            }
        }

        if (openLeft) {
            this.menuRoot.addClass('tool-lp-menu-open-left');
        } else {
            this.menuRoot.removeClass('tool-lp-menu-open-left');
        }

    };

    /*
     * Process keyDown events for the menu.
     *
     * @method handleKeyDown
     * @param {Object} item is the jquery object of the item firing the event
     * @param {Event} e is the associated event object
     * @return boolean Returns false if consuming the event
     */
    Menubar.prototype.handleKeyDown = function(item, e) {

        if (e.altKey || e.ctrlKey) {
            // Modifier key pressed: Do not process.
            return true;
        }

        switch (e.keyCode) {
            case this.keys.tab: {

                // Hide all menu items and update their aria attributes.
                this.menuRoot.find('ul').attr('aria-hidden', 'true');

                // Remove focus styling from all menu items.
                this.allItems.removeClass('menu-focus');

                this.activeItem = null;

                this.isChildOpen = false;

                break;
            }
            case this.keys.esc: {
                var itemUL = item.parent();

                if (itemUL.is('.tool-lp-menu')) {
                    // Hide the child menu and update the aria attributes.
                    item.children('ul').first().attr('aria-hidden', 'true');
                } else {

                    // Move up one level.
                    this.activeItem = itemUL.parent();

                    // Reset the isChildOpen flag.
                    this.isChildOpen = false;

                    // Set focus on the new item.
                    this.activeItem.focus();

                    // Hide the active menu and update the aria attributes.
                    itemUL.attr('aria-hidden', 'true');
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.enter:
            case this.keys.space: {
                // Trigger click handler.
                return this.handleClick(item, e);
            }

            case this.keys.left: {

                this.activeItem = this.moveToPrevious(item);

                this.activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.right: {

                this.activeItem = this.moveToNext(item);

                this.activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.up: {

                this.activeItem = this.moveUp(item);

                this.activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.down: {

                this.activeItem = this.moveDown(item);

                this.activeItem.focus();

                e.stopPropagation();
                return false;
            }
        }

        return true;

    };


    /**
     * Move to the next menu level.
     * This will be either the next root-level menu or the child of a menu parent. If
     * at the root level and the active item is the last in the menu, this function will loop
     * to the first menu item.
     *
     * If the menu is a horizontal menu, the first child element of the newly selected menu will
     * be selected
     *
     * @method moveToNext
     * @param {Object} item is the active menu item
     * @return {Object} Returns the item to move to. Returns item is no move is possible
     */
    Menubar.prototype.moveToNext = function(item) {
        // Item's containing menu.
        var itemUL = item.parent();

        // The items in the currently active menu.
        var menuItems = itemUL.children('li');

        // The number of items in the active menu.
        var menuNum = menuItems.length;
        // The items index in its menu.
        var menuIndex = menuItems.index(item);
        var newItem = null;
        var childMenu = null;

        if (itemUL.is('.tool-lp-menu')) {
            // This is the root level move to next sibling. This will require closing
            // the current child menu and opening the new one.

            if (menuIndex < menuNum - 1) {
                // Not the last root menu.
                newItem = item.next();
            } else { // Wrap to first item.
                newItem = menuItems.first();
            }

            // Close the current child menu (if applicable).
            if (item.attr('aria-haspopup') == 'true') {

                childMenu = item.children('ul').first();

                if (childMenu.attr('aria-hidden') == 'false') {
                    // Update the child menu's aria-hidden attribute.
                    childMenu.attr('aria-hidden', 'true');
                    this.isChildOpen = true;
                }
            }

            // Remove the focus styling from the current menu.
            item.removeClass('menu-focus');

            // Open the new child menu (if applicable).
            if ((newItem.attr('aria-haspopup') === 'true') && (this.isChildOpen === true)) {

                childMenu = newItem.children('ul').first();

                // Update the child's aria-hidden attribute.
                this.openSubMenu(childMenu);
            }
        } else {
            // This is not the root level. If there is a child menu to be moved into, do that;
            // otherwise, move to the next root-level menu if there is one.
            if (item.attr('aria-haspopup') == 'true') {

                childMenu = item.children('ul').first();

                newItem = childMenu.children('li').first();

                // Show the child menu and update its aria attributes.
                this.openSubMenu(childMenu);
            } else {
                // At deepest level, move to the next root-level menu.

                var parentMenus = null;
                var rootItem = null;

                // Get list of all parent menus for item, up to the root level.
                parentMenus = item.parentsUntil('ul.tool-lp-menu').filter('ul').not('.tool-lp-menu');

                // Hide the current menu and update its aria attributes accordingly.
                parentMenus.attr('aria-hidden', 'true');

                // Remove the focus styling from the active menu.
                parentMenus.find('li').removeClass('menu-focus');
                parentMenus.last().parent().removeClass('menu-focus');

                // The containing root for the menu.
                rootItem = parentMenus.last().parent();

                menuIndex = this.rootMenus.index(rootItem);

                // If this is not the last root menu item, move to the next one.
                if (menuIndex < this.rootMenus.length - 1) {
                    newItem = rootItem.next();
                } else {
                    // Loop.
                    newItem = this.rootMenus.first();
                }

                // Add the focus styling to the new menu.
                newItem.addClass('menu-focus');

                if (newItem.attr('aria-haspopup') == 'true') {
                    childMenu = newItem.children('ul').first();

                    newItem = childMenu.children('li').first();

                    // Show the child menu and update it's aria attributes.
                    this.openSubMenu(childMenu);
                    this.isChildOpen = true;
                }
            }
        }

        return newItem;
    };

    /**
     * Member function to move to the previous menu level.
     * This will be either the previous root-level menu or the child of a menu parent. If
     * at the root level and the active item is the first in the menu, this function will loop
     * to the last menu item.
     *
     * If the menu is a horizontal menu, the first child element of the newly selected menu will
     * be selected
     *
     * @method moveToPrevious
     * @param {Object} item is the active menu item
     * @return {Object} Returns the item to move to. Returns item is no move is possible
     */
    Menubar.prototype.moveToPrevious = function(item) {
        // Item's containing menu.
        var itemUL = item.parent();
        // The items in the currently active menu.
        var menuItems = itemUL.children('li');
        // The items index in its menu.
        var menuIndex = menuItems.index(item);
        var newItem = null;
        var childMenu = null;

        if (itemUL.is('.tool-lp-menu')) {
            // This is the root level move to previous sibling. This will require closing
            // the current child menu and opening the new one.

            if (menuIndex > 0) {
                // Not the first root menu.
                newItem = item.prev();
            } else {
                // Wrap to last item.
                newItem = menuItems.last();
            }

            // Close the current child menu (if applicable).
            if (item.attr('aria-haspopup') == 'true') {
                childMenu = item.children('ul').first();

                if (childMenu.attr('aria-hidden') == 'false') {
                    // Update the child menu's aria-hidden attribute.
                    childMenu.attr('aria-hidden', 'true');
                    this.isChildOpen = true;
                }
            }

            // Remove the focus styling from the current menu.
            item.removeClass('menu-focus');

            // Open the new child menu (if applicable).
            if ((newItem.attr('aria-haspopup') === 'true') && (this.isChildOpen === true)) {

                childMenu = newItem.children('ul').first();

                // Update the child's aria-hidden attribute.
                this.openSubMenu(childMenu);

            }
        } else {
            // This is not the root level. If there is a parent menu that is not the
            // root menu, move up one level; otherwise, move to first item of the previous
            // root menu.

            var parentLI = itemUL.parent();
            var parentUL = parentLI.parent();

            // If this is a vertical menu or is not the first child menu
            // of the root-level menu, move up one level.
            if (!parentUL.is('.tool-lp-menu')) {

                newItem = itemUL.parent();

                // Hide the active menu and update aria-hidden.
                itemUL.attr('aria-hidden', 'true');

                // Remove the focus highlight from the item.
                item.removeClass('menu-focus');

            } else {
                // Move to previous root-level menu.

                // Hide the current menu and update the aria attributes accordingly.
                itemUL.attr('aria-hidden', 'true');

                // Remove the focus styling from the active menu.
                item.removeClass('menu-focus');
                parentLI.removeClass('menu-focus');

                menuIndex = this.rootMenus.index(parentLI);

                if (menuIndex > 0) {
                    // Move to the previous root-level menu.
                    newItem = parentLI.prev();
                } else {
                    // Loop to last root-level menu.
                    newItem = this.rootMenus.last();
                }

                // Add the focus styling to the new menu.
                newItem.addClass('menu-focus');

                if (newItem.attr('aria-haspopup') == 'true') {
                    childMenu = newItem.children('ul').first();

                    // Show the child menu and update it's aria attributes.
                    this.openSubMenu(childMenu);
                    this.isChildOpen = true;

                    newItem = childMenu.children('li').first();
                }
            }
        }

        return newItem;
    };

    /**
     * Member function to select the next item in a menu.
     * If the active item is the last in the menu, this function will loop to the
     * first menu item.
     *
     * @method moveDown
     * @param {Object} item is the active menu item
     * @param {String} startChr is the character to attempt to match against the beginning of the
     *                          menu item titles. If found, focus moves to the next menu item beginning with that character.
     * @return {Object} Returns the item to move to. Returns item is no move is possible
     */
    Menubar.prototype.moveDown = function(item, startChr) {
        // Item's containing menu.
        var itemUL = item.parent();
        // The items in the currently active menu.
        var menuItems = itemUL.children('li').not('.separator');
        // The number of items in the active menu.
        var menuNum = menuItems.length;
        // The items index in its menu.
        var menuIndex = menuItems.index(item);
        var newItem = null;
        var newItemUL = null;

        if (itemUL.is('.tool-lp-menu')) {
            // This is the root level menu.

            if (item.attr('aria-haspopup') != 'true') {
                // No child menu to move to.
                return item;
            }

            // Move to the first item in the child menu.
            newItemUL = item.children('ul').first();
            newItem = newItemUL.children('li').first();

            // Make sure the child menu is visible.
            this.openSubMenu(newItemUL);

            return newItem;
        }

        // If $item is not the last item in its menu, move to the next item. If startChr is specified, move
        // to the next item with a title that begins with that character.
        if (startChr) {
            var match = false;
            var curNdx = menuIndex + 1;

            // Check if the active item was the last one on the list.
            if (curNdx == menuNum) {
                curNdx = 0;
            }

            // Iterate through the menu items (starting from the current item and wrapping) until a match is found
            // or the loop returns to the current menu item.
            while (curNdx != menuIndex) {

                var titleChr = menuItems.eq(curNdx).html().charAt(0);

                if (titleChr.toLowerCase() == startChr) {
                    match = true;
                    break;
                }

                curNdx = curNdx + 1;

                if (curNdx == menuNum) {
                    // Reached the end of the list, start again at the beginning.
                    curNdx = 0;
                }
            }

            if (match === true) {
                newItem = menuItems.eq(curNdx);

                // Remove the focus styling from the current item.
                item.removeClass('menu-focus');

                return newItem;
            } else {
                return item;
            }
        } else {
            if (menuIndex < menuNum - 1) {
                newItem = menuItems.eq(menuIndex + 1);
            } else {
                newItem = menuItems.first();
            }
        }

        // Remove the focus styling from the current item.
        item.removeClass('menu-focus');

        return newItem;
    };

    /**
     * Function moveUp() is a member function to select the previous item in a menu.
     * If the active item is the first in the menu, this function will loop to the
     * last menu item.
     *
     * @method moveUp
     * @param {Object} item is the active menu item
     * @return {Object} Returns the item to move to. Returns item is no move is possible
     */
    Menubar.prototype.moveUp = function(item) {
        // Item's containing menu.
        var itemUL = item.parent();
        // The items in the currently active menu.
        var menuItems = itemUL.children('li').not('.separator');
        // The items index in its menu.
        var menuIndex = menuItems.index(item);
        var newItem = null;

        if (itemUL.is('.tool-lp-menu')) {
            // This is the root level menu.
            // Nothing to do.
            return item;
        }

        // If item is not the first item in its menu, move to the previous item.
        if (menuIndex > 0) {
            newItem = menuItems.eq(menuIndex - 1);
        } else {
            // Loop to top of menu.
            newItem = menuItems.last();
        }

        // Remove the focus styling from the current item.
        item.removeClass('menu-focus');

        return newItem;
    };

    /**
     * Enhance the dom with aria attributes.
     * @method addAriaAttributes
     */
    Menubar.prototype.addAriaAttributes = function() {
        this.menuRoot.attr('role', 'menubar');
        this.rootMenus.attr('role', 'menuitem');
        this.rootMenus.attr('tabindex', '0');
        this.rootMenus.attr('aria-haspopup', 'true');
        this.subMenus.attr('role', 'menu');
        this.subMenus.attr('aria-hidden', 'true');
        this.subMenuItems.attr('role', 'menuitem');
        this.subMenuItems.attr('tabindex', '-1');

        // For CSS styling and effects.
        this.menuRoot.addClass('tool-lp-menu');
        this.allItems.addClass('tool-lp-menu-item');
        this.rootMenus.addClass('tool-lp-root-menu');
        this.subMenus.addClass('tool-lp-sub-menu');
        this.subMenuItems.addClass('dropdown-item');
    };

    return /** @alias module:tool_lp/menubar */ {
        /**
         * Create a menu bar object for every node matching the selector.
         *
         * The expected DOM structure is shown below.
         * <ul> <- This is the target of the selector parameter.
         *   <li> <- This is repeated for each top level menu.
         *      Text <- This is the text for the top level menu.
         *      <ul> <- This is a list of the entries in this top level menu.
         *         <li> <- This is repeated for each menu entry.
         *            <a href="someurl">Choice 1</a> <- The anchor for the menu.
         *         </li>
         *      </ul>
         *   </li>
         * </ul>
         *
         * @method enhance
         * @param {String} selector - The selector for the outer most menu node.
         * @param {Function} handler - Javascript handler for when a menu item was chosen. If the
         *                             handler returns true (or does not exist), the
         *                             menu will look for an anchor with a link to follow.
         *                             For example, if the menu entry has a "data-action" attribute
         *                             and we want to call a javascript function when that entry is chosen,
         *                             we could pass a list of handlers like this:
         *                             { "[data-action='add']" : callAddFunction }
         */
        enhance: function(selector, handler) {
            $(selector).each(function(index, element) {
                var menuRoot = $(element);
                // Don't enhance the same menu twice.
                if (menuRoot.data("menubarEnhanced") !== true) {
                    (new Menubar(menuRoot, handler));
                    menuRoot.data("menubarEnhanced", true);
                }
            });
        },

        /**
         * Handy function to close all open menus anywhere on the page.
         * @method closeAll
         */
        closeAll: closeAllSubMenus
    };
});
