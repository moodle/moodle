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
    const SELECTORS = {
        STATE_INPUTS: '.preference-state input.notification_enabled'
    };

    /**
     * Constructor for the notification processor.
     *
     * @class
     * @param {object} element jQuery object root element of the processor
     */
    const NotificationProcessor = function(element) {
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
    NotificationProcessor.prototype.isEnabled = function() {
        const enabled = this.root.find(SELECTORS.STATE_INPUTS);

        return enabled.prop('checked');
    };

    return NotificationProcessor;
});
