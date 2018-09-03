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
 * Javascript used to save the user's tab preference.
 *
 * @package    block_myoverview
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/custom_interaction_events',
    'core/notification'], function($, Ajax, CustomEvents, Notification) {

    /**
     * Registers an event that saves the user's tab preference when switching between them.
     *
     * @param {object} root The container element
     */
    var registerEventListeners = function(root) {
        CustomEvents.define(root, [CustomEvents.events.activate]);
        root.on(CustomEvents.events.activate, "[data-toggle='tab']", function(e) {
            var tabname = $(e.currentTarget).data('tabname');
            // Bootstrap does not change the URL when using BS tabs, so need to do this here.
            // Also check to make sure the browser supports the history API.
            if (typeof window.history.pushState === "function") {
                window.history.pushState(null, null, '?myoverviewtab=' + tabname);
            }
            var request = {
                methodname: 'core_user_update_user_preferences',
                args: {
                    preferences: [
                        {
                            type: 'block_myoverview_last_tab',
                            value: tabname
                        }
                    ]
                }
            };

            Ajax.call([request])[0]
                .fail(Notification.exception);
        });
    };

    return {
        registerEventListeners: registerEventListeners
    };
});
