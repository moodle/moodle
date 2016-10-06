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
 * Contain the logic for a drawer.
 *
 * @package    theme_boost
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/custom_interaction_events', 'core/notification'],
     function($, CustomEvents, Notification) {

    var SELECTORS = {
        TOGGLE_REGION: '[data-region="drawer-toggle"]',
        TOGGLE_ACTION: '[data-action="toggle-drawer"]',
        TOGGLE_TARGET: 'aria-controls',
        TOGGLE_SIDE: 'left',
        BODY: 'body'
    };

    /**
     * Constructor for the Drawer.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var Drawer = function() {

        if (!$(SELECTORS.TOGGLE_REGION).length) {
            Notification.exception({message: 'Page is missing a drawer toggle region'});
        }
        if (!$(SELECTORS.TOGGLE_ACTION).length) {
            Notification.exception({message: 'Page is missing a drawer toggle link'});
        }
        $(SELECTORS.TOGGLE_REGION).each(function(index, ele) {
            var trigger = $(ele).find(SELECTORS.TOGGLE_ACTION);
            var hidden = trigger.attr('aria-expanded') == 'false';
            var side = trigger.attr('data-side');
            var body = $(SELECTORS.BODY);

            if (!hidden) {
                body.addClass('drawer-open-' + side);
                trigger.attr('aria-expanded', 'true');
            } else {
                trigger.attr('aria-expanded', 'false');
            }
        }.bind(this));

        this.registerEventListeners();
    };

    Drawer.prototype.closeAll = function() {
        $(SELECTORS.TOGGLE_REGION).each(function(index, ele) {
            var trigger = $(ele).find(SELECTORS.TOGGLE_ACTION);
            var side = trigger.attr('data-side');
            var body = $(SELECTORS.BODY);
            var drawerid = trigger.attr('aria-controls');
            var drawer = $(document.getElementById(drawerid));
            var preference = trigger.attr('data-preference');

            trigger.attr('aria-expanded', 'false');
            body.removeClass('drawer-open-' + side);
            drawer.attr('aria-hidden', 'true');
            drawer.addClass('closed');
            M.util.set_user_preference(preference, 'false');
        }.bind(this));
    };

    /**
     * Open / close the blocks drawer.
     *
     * @method toggleDrawer
     * @param {Event} e
     */
    Drawer.prototype.toggleDrawer = function(e) {
        var trigger = $(e.target).closest('[data-action=toggle-drawer]');
        var drawerid = trigger.attr('aria-controls');
        var drawer = $(document.getElementById(drawerid));
        var body = $(SELECTORS.BODY);
        var side = trigger.attr('data-side');
        var preference = trigger.attr('data-preference');

        body.addClass('drawer-ease');
        var open = trigger.attr('aria-expanded') == 'true';
        if (!open) {
            var small = $(document).width() < 512;
            if (small) {
                this.closeAll();
            }
            // Open.
            trigger.attr('aria-expanded', 'true');
            drawer.attr('aria-hidden', 'false');
            body.addClass('drawer-open-' + side);
            drawer.removeClass('closed');
            M.util.set_user_preference(preference, 'true');
        } else {
            // Close.
            body.removeClass('drawer-open-' + side);
            trigger.attr('aria-expanded', 'false');
            drawer.attr('aria-hidden', 'true');
            drawer.addClass('closed');
            M.util.set_user_preference(preference, 'false');
        }
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    Drawer.prototype.registerEventListeners = function() {
        var body = $(SELECTORS.BODY);

        CustomEvents.define(body, [CustomEvents.events.activate]);

        body.on(CustomEvents.events.activate, SELECTORS.TOGGLE_ACTION, function(e, data) {
            this.toggleDrawer(data.originalEvent);
            data.originalEvent.preventDefault();
        }.bind(this));
    };

    return {
        'init': function() {
            return new Drawer();
        }
    };
});
