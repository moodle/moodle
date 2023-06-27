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
 * Represents the notification processor (e.g. email, popup, jabber)
 *
 * @module     core_message/notification_processor
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    var SELECTORS = {
        STATE_NONE: '[data-state="none"]',
        STATE_BOTH: '[data-state="both"]',
        STATE_LOGGED_IN: '[data-state="loggedin"]',
        STATE_LOGGED_OFF: '[data-state="loggedoff"]',
    };

    /**
     * Constructor for the notification processor.
     *
     * @class
     * @param {object} element jQuery object root element of the processor
     */
    var NotificationProcessor = function(element) {
        this.root = $(element);
    };

    /**
     * Get the processor name.
     *
     * @method getName
     * @return {string}
     */
    NotificationProcessor.prototype.getName = function() {
        return this.root.attr('data-processor-name');
    };

    /**
     * Check if the processor is enabled when the user is logged in.
     *
     * @method isLoggedInEnabled
     * @return {bool}
     */
    NotificationProcessor.prototype.isLoggedInEnabled = function() {
        var none = this.root.find(SELECTORS.STATE_NONE).find('input');

        if (none.prop('checked')) {
            return false;
        }

        var both = this.root.find(SELECTORS.STATE_BOTH).find('input');
        var loggedIn = this.root.find(SELECTORS.STATE_LOGGED_IN).find('input');

        return loggedIn.prop('checked') || both.prop('checked');
    };

    /**
     * Check if the processor is enabled when the user is logged out.
     *
     * @method isLoggedOffEnabled
     * @return {bool}
     */
    NotificationProcessor.prototype.isLoggedOffEnabled = function() {
        var none = this.root.find(SELECTORS.STATE_NONE).find('input');

        if (none.prop('checked')) {
            return false;
        }

        var both = this.root.find(SELECTORS.STATE_BOTH).find('input');
        var loggedOff = this.root.find(SELECTORS.STATE_LOGGED_OFF).find('input');

        return loggedOff.prop('checked') || both.prop('checked');
    };

    return NotificationProcessor;
});
