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
 * Controls the message preference page.
 *
 * @module     core_message/message_preferences
 * @class      message_preferences
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification',
        'core_message/message_notification_preference', 'core/custom_interaction_events'],
        function($, Ajax, Notification, MessageNotificationPreference, CustomEvents) {

    var SELECTORS = {
        PREFERENCE: '[data-state]',
        PREFERENCES_CONTAINER: '[data-region="preferences-container"]',
        CONTACTABLE_PRIVACY_CONTAINER: '[data-region="privacy-setting-container"]',
    };

    /**
     * Constructor for the MessagePreferences.
     *
     * @param {object} element The root element for the message preferences
     */
    var MessagePreferences = function(element) {
        this.root = $(element);
        this.userId = this.root.find(SELECTORS.PREFERENCES_CONTAINER).attr('data-user-id');

        this.registerEventListeners();
    };

    /**
     * Check if the preferences have been disabled on this page.
     *
     * @method preferencesDisabled
     * @return {bool}
     */
    MessagePreferences.prototype.preferencesDisabled = function() {
        return this.root.find(SELECTORS.PREFERENCES_CONTAINER).hasClass('disabled');
    };

    /**
     * Update the contactable privacy user preference in the DOM and
     * send a request to update on the server.
     *
     * @return {Promise}
     * @method saveContactablePrivacySetting
     */
    MessagePreferences.prototype.saveContactablePrivacySetting = function() {
        var container = this.root.find(SELECTORS.CONTACTABLE_PRIVACY_CONTAINER);
        var value = $("input[type='radio']:checked").val();

        if (container.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        container.addClass('loading');

        var request = {
            methodname: 'core_user_update_user_preferences',
            args: {
                userid: this.userId,
                preferences: [
                    {
                        type: container.attr('data-preference-key'),
                        value: value,
                    }
                ]
            }
        };

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .always(function() {
                container.removeClass('loading');
            });
    };

    /**
     * Create all of the event listeners for the message preferences page.
     *
     * @method registerEventListeners
     */
    MessagePreferences.prototype.registerEventListeners = function() {
        CustomEvents.define(this.root, [
            CustomEvents.events.activate
        ]);

        this.root.on('change', function(e) {
            // Add listener for privacy setting radio buttons change.
            if (e.target.name == 'message_blocknoncontacts') {
                this.saveContactablePrivacySetting();
            } else {
                // Add listener for processor preferences.
                if (!this.preferencesDisabled()) {
                    var preferencesContainer = $(e.target).closest(SELECTORS.PREFERENCES_CONTAINER);
                    var preferenceElement = $(e.target).closest(SELECTORS.PREFERENCE);
                    var messagePreference = new MessageNotificationPreference(preferencesContainer, this.userId);

                    preferenceElement.addClass('loading');
                    messagePreference.save().always(function() {
                        preferenceElement.removeClass('loading');
                    });
                }
            }
        }.bind(this));
    };

    return MessagePreferences;
});
