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
 * Controls the preference for an individual notification type on the
 * message preference page.
 *
 * @module     core_message/message_notification_preference
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core_message/notification_preference'],
        function($, NotificationPreference) {

    var SELECTORS = {
        PREFERENCE_KEY: '[data-preference-key]',
    };

    /**
     * Constructor for the Preference.
     *
     * @class
     * @param {object} element jQuery object root element of the preference
     * @param {int} userId The current user id
     */
    var MessageNotificationPreference = function(element, userId) {
        NotificationPreference.call(this, element, userId);
    };

    /**
     * Clone the parent prototype.
     */
    MessageNotificationPreference.prototype = Object.create(NotificationPreference.prototype);

    /**
     * Set constructor.
     */
    MessageNotificationPreference.prototype.constructor = MessageNotificationPreference;

    /**
     * Get the unique prefix key that identifies this user preference.
     *
     * @method getPreferenceKey
     * @return {string}
     */
    MessageNotificationPreference.prototype.getPreferenceKey = function() {
        return this.root.find(SELECTORS.PREFERENCE_KEY).attr('data-preference-key');
    };

    return MessageNotificationPreference;
});
