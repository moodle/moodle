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
 * Controls the mdl popover element.
 *
 * See template: core/mdl_popover
 *
 * @module     core/mdl_popover_controller
 * @class      mdl_popover_controller
 * @package    core
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/str', 'core/custom_interaction_events'],
        function($, str, customEvents) {

    var SELECTORS = {
        CONTENT: '.mdl-popover-content',
        CONTENT_CONTAINER: '.mdl-popover-content-container',
        MENU_CONTAINER: '.mdl-popover-container',
        MENU_TOGGLE: '.mdl-popover-toggle',
    };

    /**
     * Constructor for the MdlPopoverController.
     *
     * @param element jQuery object root element of the popover
     * @return object MdlPopoverController
     */
    var MdlPopoverController = function(element) {
        this.root = $(element);
        this.content = this.root.find(SELECTORS.CONTENT);
        this.contentContainer = this.root.find(SELECTORS.CONTENT_CONTAINER);
        this.menuContainer = this.root.find(SELECTORS.MENU_CONTAINER);
        this.menuToggle = this.root.find(SELECTORS.MENU_TOGGLE);
        this.isLoading = false;
    };

    /**
     * The collection of events triggered by this controller.
     */
    MdlPopoverController.prototype.events = function() {
        return {
            menuOpened: 'mdlpopover:menuopened',
            menuClosed: 'mdlpopover:menuclosed',
            startLoading: 'mdlpopover:startLoading',
            stopLoading: 'mdlpopover:stopLoading',
        };
    };

    /**
     * Return the container element for the content element.
     *
     * @method getContentContainer
     * @return jQuery object
     */
    MdlPopoverController.prototype.getContentContainer = function() {
        return this.contentContainer;
    };

    /**
     * Return the content element.
     *
     * @method getContent
     * @return jQuery object
     */
    MdlPopoverController.prototype.getContent = function() {
        return this.content;
    };

    /**
     * Checks if the popover is displayed.
     *
     * @method isMenuOpen
     * @return bool
     */
    MdlPopoverController.prototype.isMenuOpen = function() {
        return !this.root.hasClass('collapsed');
    };

    /**
     * Toggle the visibility of the popover.
     *
     * @method toggleMenu
     */
    MdlPopoverController.prototype.toggleMenu = function() {
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
    MdlPopoverController.prototype.closeMenu = function() {
        // We're already closed.
        if (!this.isMenuOpen()) {
            return;
        }

        this.root.addClass('collapsed');
        this.menuContainer.attr('aria-expanded', 'false');
        this.menuContainer.attr('aria-hidden', 'true');
        this.updateButtonAriaLabel();
        this.root.trigger(this.events().menuClosed);
    };

    /**
     * Show the popover.
     *
     * Note: This triggers the menuOpened event.
     *
     * @method openMenu
     */
    MdlPopoverController.prototype.openMenu = function() {
        // We're already open.
        if (this.isMenuOpen()) {
            return;
        }

        this.root.removeClass('collapsed');
        this.menuContainer.attr('aria-expanded', 'true');
        this.menuContainer.attr('aria-hidden', 'false');
        this.updateButtonAriaLabel();
        this.root.trigger(this.events().menuOpened);
    };

    /**
     * Set the appropriate aria label on the popover toggle.
     *
     * @method updateButtonAriaLabel
     */
    MdlPopoverController.prototype.updateButtonAriaLabel = function() {
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
    MdlPopoverController.prototype.startLoading = function() {
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
    MdlPopoverController.prototype.stopLoading = function() {
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
    MdlPopoverController.prototype.focusMenuToggle = function() {
        this.menuToggle.focus();
    };

    /**
     * Return the currently focused content item.
     *
     * @method getContentItemWithFocus
     * @return jQuery object
     */
    MdlPopoverController.prototype.getContentItemWithFocus = function() {
        var currentFocus = $(document.activeElement);
        var items = this.getContent().children();
        var currentItem = items.filter(currentFocus);

        if (!currentItem.length) {
            currentItem = items.has(currentFocus);
        }

        return currentItem;
    };

    /**
     * Set focus on the first content item in the list.
     *
     * @method focusFirstContentItem
     */
    MdlPopoverController.prototype.focusFirstContentItem = function() {
        this.getContent().children().first().focus();
    };

    /**
     * Set focus on the last content item in the list.
     *
     * @method focusLastContentItem
     */
    MdlPopoverController.prototype.focusLastContentItem = function() {
        this.getContent().children().last().focus();
    };

    /**
     * Set focus on the content item after the item that currently has focus
     * in the list.
     *
     * @method focusNextContentItem
     */
    MdlPopoverController.prototype.focusNextContentItem = function() {
        var currentItem = this.getContentItemWithFocus();

        if (currentItem.length && currentItem.next()) {
            currentItem.next().focus();
        }
    };

    /**
     * Set focus on the content item preceding the item that currently has focus
     * in the list.
     *
     * @method focusPreviousContentItem
     */
    MdlPopoverController.prototype.focusPreviousContentItem = function() {
        var currentItem = this.getContentItemWithFocus();

        if (currentItem.length && currentItem.prev()) {
            currentItem.prev().focus();
        }
    };

    /**
     * Register the minimal amount of listeners for the popover to function.
     *
     * @method registerBaseEventListeners
     */
    MdlPopoverController.prototype.registerBaseEventListeners = function() {
        customEvents.define(this.root, [
            customEvents.events.activate,
            customEvents.events.escape,
        ]);

        // Toggle the popover visibility on activation (click/enter/space) of the toggle button.
        this.root.on(customEvents.events.activate, SELECTORS.MENU_TOGGLE, function() {
            this.toggleMenu();
        }.bind(this));

        // Close the popover if escape is pressed.
        this.root.on(customEvents.events.escape, function() {
            this.closeMenu();
            this.focusMenuToggle();
        }.bind(this));

        // Close the popover if any other part of the page is clicked.
        $('html').click(function(e) {
            var target = $(e.target);
            if (!this.root.is(target) && !this.root.has(target).length) {
                this.closeMenu();
            }
        }.bind(this));

        customEvents.define(this.getContentContainer(), [
            customEvents.events.scrollBottom
        ]);
    };

    /**
     * Set up the event listeners for keyboard navigating a list of content items.
     *
     * @method registerListNavigationEventListeners
     */
    MdlPopoverController.prototype.registerListNavigationEventListeners = function() {
        customEvents.define(this.root, [
            customEvents.events.down,
            customEvents.events.up,
            customEvents.events.home,
            customEvents.events.end,
        ]);

        // If the down arrow is pressed then open the menu and focus the first content
        // item or focus the next content item if the menu is open.
        this.root.on(customEvents.events.down, function() {
            if (!this.isMenuOpen()) {
                this.openMenu();
                this.focusFirstContentItem();
            } else {
                this.focusNextContentItem();
            }
        }.bind(this));

        // Shift focus to the previous content item if the up key is pressed.
        this.root.on(customEvents.events.up, function() {
            this.focusPreviousContentItem();
        }.bind(this));

        // Jump focus to the first content item if the home key is pressed.
        this.root.on(customEvents.events.home, function() {
            this.focusFirstContentItem();
        }.bind(this));

        // Jump focus to the last content item if the end key is pressed.
        this.root.on(customEvents.events.end, function() {
            this.focusLastContentItem();
        }.bind(this));
    };

    return MdlPopoverController;
});
