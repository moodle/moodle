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
 * Controls the popover region element.
 *
 * See template: core/popover_region
 *
 * @module     core/popover_region_controller
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/str', 'core/custom_interaction_events'],
        function($, str, customEvents) {

    var SELECTORS = {
        CONTENT: '.popover-region-content',
        CONTENT_CONTAINER: '.popover-region-content-container',
        MENU_CONTAINER: '.popover-region-container',
        MENU_TOGGLE: '.popover-region-toggle',
        CAN_RECEIVE_FOCUS: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
    };

    /**
     * Constructor for the PopoverRegionController.
     *
     * @param {jQuery} element object root element of the popover
     */
    var PopoverRegionController = function(element) {
        this.root = $(element);
        this.content = this.root.find(SELECTORS.CONTENT);
        this.contentContainer = this.root.find(SELECTORS.CONTENT_CONTAINER);
        this.menuContainer = this.root.find(SELECTORS.MENU_CONTAINER);
        this.menuToggle = this.root.find(SELECTORS.MENU_TOGGLE);
        this.isLoading = false;
        this.promises = {
            closeHandlers: $.Deferred(),
            navigationHandlers: $.Deferred(),
        };

        // Core event listeners to open and close.
        this.registerBaseEventListeners();
    };

    /**
     * The collection of events triggered by this controller.
     *
     * @returns {object}
     */
    PopoverRegionController.prototype.events = function() {
        return {
            menuOpened: 'popoverregion:menuopened',
            menuClosed: 'popoverregion:menuclosed',
            startLoading: 'popoverregion:startLoading',
            stopLoading: 'popoverregion:stopLoading',
        };
    };

    /**
     * Return the container element for the content element.
     *
     * @method getContentContainer
     * @return {jQuery} object
     */
    PopoverRegionController.prototype.getContentContainer = function() {
        return this.contentContainer;
    };

    /**
     * Return the content element.
     *
     * @method getContent
     * @return {jQuery} object
     */
    PopoverRegionController.prototype.getContent = function() {
        return this.content;
    };

    /**
     * Checks if the popover is displayed.
     *
     * @method isMenuOpen
     * @return {bool}
     */
    PopoverRegionController.prototype.isMenuOpen = function() {
        return !this.root.hasClass('collapsed');
    };

    /**
     * Toggle the visibility of the popover.
     *
     * @method toggleMenu
     */
    PopoverRegionController.prototype.toggleMenu = function() {
        if (this.isMenuOpen()) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    };

    /**
     * Hide the popover.
     *
     * Note: This triggers the menuClosed event.
     *
     * @method closeMenu
     */
    PopoverRegionController.prototype.closeMenu = function() {
        // We're already closed.
        if (!this.isMenuOpen()) {
            return;
        }

        this.root.addClass('collapsed');
        this.menuToggle.attr('aria-expanded', 'false');
        this.menuContainer.attr('aria-hidden', 'true');
        this.updateButtonAriaLabel();
        this.updateFocusItemTabIndex();
        this.root.trigger(this.events().menuClosed);
    };

    /**
     * Show the popover.
     *
     * Note: This triggers the menuOpened event.
     *
     * @method openMenu
     */
    PopoverRegionController.prototype.openMenu = function() {
        // We're already open.
        if (this.isMenuOpen()) {
            return;
        }

        this.root.removeClass('collapsed');
        this.menuToggle.attr('aria-expanded', 'true');
        this.menuContainer.attr('aria-hidden', 'false');
        this.updateButtonAriaLabel();
        this.updateFocusItemTabIndex();
        // Resolve the promises to allow the handlers to be added
        // to the DOM, if they have been requested.
        this.promises.closeHandlers.resolve();
        this.promises.navigationHandlers.resolve();
        this.root.trigger(this.events().menuOpened);
    };

    /**
     * Set the appropriate aria label on the popover toggle.
     *
     * @method updateButtonAriaLabel
     */
    PopoverRegionController.prototype.updateButtonAriaLabel = function() {
        if (this.isMenuOpen()) {
            str.get_string('hidepopoverwindow').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        } else {
            str.get_string('showpopoverwindow').done(function(string) {
                this.menuToggle.attr('aria-label', string);
            }.bind(this));
        }
    };

    /**
     * Set the loading state on this popover.
     *
     * Note: This triggers the startLoading event.
     *
     * @method startLoading
     */
    PopoverRegionController.prototype.startLoading = function() {
        this.isLoading = true;
        this.getContentContainer().addClass('loading');
        this.getContentContainer().attr('aria-busy', 'true');
        this.root.trigger(this.events().startLoading);
    };

    /**
     * Undo the loading state on this popover.
     *
     * Note: This triggers the stopLoading event.
     *
     * @method stopLoading
     */
    PopoverRegionController.prototype.stopLoading = function() {
        this.isLoading = false;
        this.getContentContainer().removeClass('loading');
        this.getContentContainer().attr('aria-busy', 'false');
        this.root.trigger(this.events().stopLoading);
    };

    /**
     * Sets the focus on the menu toggle.
     *
     * @method focusMenuToggle
     */
    PopoverRegionController.prototype.focusMenuToggle = function() {
        this.menuToggle.focus();
    };

    /**
     * Check if a content item has focus.
     *
     * @method contentItemHasFocus
     * @return {bool}
     */
    PopoverRegionController.prototype.contentItemHasFocus = function() {
        return this.getContentItemWithFocus().length > 0;
    };

    /**
     * Return the currently focused content item.
     *
     * @method getContentItemWithFocus
     * @return {jQuery} object
     */
    PopoverRegionController.prototype.getContentItemWithFocus = function() {
        var currentFocus = $(document.activeElement);
        var items = this.getContent().children();
        var currentItem = items.filter(currentFocus);

        if (!currentItem.length) {
            currentItem = items.has(currentFocus);
        }

        return currentItem;
    };

    /**
     * Focus the given content item or the first focusable element within
     * the content item.
     *
     * @method focusContentItem
     * @param {object} item The content item jQuery element
     */
    PopoverRegionController.prototype.focusContentItem = function(item) {
        if (item.is(SELECTORS.CAN_RECEIVE_FOCUS)) {
            item.focus();
        } else {
            item.find(SELECTORS.CAN_RECEIVE_FOCUS).first().focus();
        }
    };

    /**
     * Set focus on the first content item in the list.
     *
     * @method focusFirstContentItem
     */
    PopoverRegionController.prototype.focusFirstContentItem = function() {
        this.focusContentItem(this.getContent().children().first());
    };

    /**
     * Set focus on the last content item in the list.
     *
     * @method focusLastContentItem
     */
    PopoverRegionController.prototype.focusLastContentItem = function() {
        this.focusContentItem(this.getContent().children().last());
    };

    /**
     * Set focus on the content item after the item that currently has focus
     * in the list.
     *
     * @method focusNextContentItem
     */
    PopoverRegionController.prototype.focusNextContentItem = function() {
        var currentItem = this.getContentItemWithFocus();

        if (currentItem.length && currentItem.next()) {
            this.focusContentItem(currentItem.next());
        }
    };

    /**
     * Set focus on the content item preceding the item that currently has focus
     * in the list.
     *
     * @method focusPreviousContentItem
     */
    PopoverRegionController.prototype.focusPreviousContentItem = function() {
        var currentItem = this.getContentItemWithFocus();

        if (currentItem.length && currentItem.prev()) {
            this.focusContentItem(currentItem.prev());
        }
    };

    /**
     * Register the minimal amount of listeners for the popover to function.
     *
     * @method registerBaseEventListeners
     */
    PopoverRegionController.prototype.registerBaseEventListeners = function() {
        customEvents.define(this.root, [
            customEvents.events.activate,
            customEvents.events.escape,
        ]);

        // Toggle the popover visibility on activation (click/enter/space) of the toggle button.
        this.root.on(customEvents.events.activate, SELECTORS.MENU_TOGGLE, function() {
            this.toggleMenu();
        }.bind(this));

        // Delay the binding of these handlers until the region has been opened.
        this.promises.closeHandlers.done(function() {
            // Close the popover if escape is pressed.
            this.root.on(customEvents.events.escape, function() {
                this.closeMenu();
                this.focusMenuToggle();
            }.bind(this));

            // Close the popover if any other part of the page is clicked.
            document.addEventListener('click', (e) => {
                const target = e.target;
                // Check if the click is outside the root element.
                if (!this.root.is(target) && !this.root.has(target).length) {
                    this.closeMenu();
                }
            }, true); // `true` makes it a capture phase event listener.

            customEvents.define(this.getContentContainer(), [
                customEvents.events.scrollBottom
            ]);
        }.bind(this));
    };

    /**
     * Set up the event listeners for keyboard navigating a list of content items.
     *
     * @method registerListNavigationEventListeners
     */
    PopoverRegionController.prototype.registerListNavigationEventListeners = function() {
        customEvents.define(this.root, [
            customEvents.events.down
        ]);

        // If the down arrow is pressed then open the menu and focus the first content
        // item or focus the next content item if the menu is open.
        this.root.on(customEvents.events.down, function(e, data) {
            if (!this.isMenuOpen()) {
                this.openMenu();
                this.focusFirstContentItem();
            } else {
                if (this.contentItemHasFocus()) {
                    this.focusNextContentItem();
                } else {
                    this.focusFirstContentItem();
                }
            }

            data.originalEvent.preventDefault();
        }.bind(this));

        // Delay the binding of these handlers until the region has been opened.
        this.promises.navigationHandlers.done(function() {
            customEvents.define(this.root, [
                customEvents.events.up,
                customEvents.events.home,
                customEvents.events.end,
            ]);

            // Shift focus to the previous content item if the up key is pressed.
            this.root.on(customEvents.events.up, function(e, data) {
                this.focusPreviousContentItem();
                data.originalEvent.preventDefault();
            }.bind(this));

            // Jump focus to the first content item if the home key is pressed.
            this.root.on(customEvents.events.home, function(e, data) {
                this.focusFirstContentItem();
                data.originalEvent.preventDefault();
            }.bind(this));

            // Jump focus to the last content item if the end key is pressed.
            this.root.on(customEvents.events.end, function(e, data) {
                this.focusLastContentItem();
                data.originalEvent.preventDefault();
            }.bind(this));
        }.bind(this));
    };

    /**
     * Set the appropriate tabindex attribute on the popover toggle.
     *
     * @method updateFocusItemTabIndex
     */
    PopoverRegionController.prototype.updateFocusItemTabIndex = function() {
        if (this.isMenuOpen()) {
            this.menuContainer.find(SELECTORS.CAN_RECEIVE_FOCUS).removeAttr('tabindex');
        } else {
            this.menuContainer.find(SELECTORS.CAN_RECEIVE_FOCUS).attr('tabindex', '-1');
        }
    };

    return PopoverRegionController;
});
