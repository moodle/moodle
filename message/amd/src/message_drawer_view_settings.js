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
 * Controls the settings page in the message drawer.
 *
 * @module     core_message/message_drawer_view_settings
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/notification',
    'core/str',
    'core/pubsub',
    'core_message/message_repository',
    'core/custom_interaction_events',
    'core_message/message_drawer_events'
],
function(
    $,
    Notification,
    Str,
    PubSub,
    Repository,
    CustomEvents,
    MessageDrawerEvents
) {

    var SELECTORS = {
        SETTINGS: '[data-region="settings"]',
        PREFERENCE_CONTROL: '[data-region="preference-control"]',
        PRIVACY_PREFERENCE: '[data-preference="blocknoncontacts"] input[type="radio"]',
        EMAIL_ENABLED_PREFERENCE: '[data-preference="emailnotifications"] input[type="checkbox"]',
        ENTER_TO_SEND_PREFERENCE: '[data-preference="entertosend"] input[type="checkbox"]',
    };

    var PREFERENCES_EMAIL = {
        'message_provider_moodle_instantmessage_loggedoff': {
            type: 'emailnotifications',
            enabled: 'email',
            disabled: 'none'
        },
        'message_provider_moodle_instantmessage_loggedin': {
            type: 'emailnotifications',
            enabled: 'email',
            disabled: 'none'
        }
    };

    /**
     * Create all of the event listeners for the message preferences page.
     *
     * @method registerEventListeners
     * @param {Object} body The settings body element.
     * @param {Number} loggedInUserId The logged in user id.
     */
    var registerEventListeners = function(body, loggedInUserId) {
        var settingsContainer = body.find(SELECTORS.SETTINGS);

        CustomEvents.define(settingsContainer, [
            CustomEvents.events.activate
        ]);

        settingsContainer.on(CustomEvents.events.activate, SELECTORS.EMAIL_ENABLED_PREFERENCE, function(e) {
                var checkbox = $(e.target);
                var setting = checkbox.closest(SELECTORS.PREFERENCE_CONTROL);
                var type = setting.attr('data-preference');
                var isEnabled = checkbox.prop('checked');
                var preferences = Object.keys(PREFERENCES_EMAIL).reduce(function(carry, preference) {
                    var config = PREFERENCES_EMAIL[preference];

                    if (config.type === type) {
                        carry.push({
                            type: preference,
                            value: isEnabled ? config.enabled : config.disabled
                        });
                    }

                    return carry;
                }, []);

                Repository.savePreferences(loggedInUserId, preferences)
                    .then(function() {
                        PubSub.publish(MessageDrawerEvents.PREFERENCES_UPDATED, preferences);
                        return;
                    })
                    .catch(Notification.exception);
            }
        );

        settingsContainer.on(CustomEvents.events.activate, SELECTORS.PRIVACY_PREFERENCE, function(e) {
                var newValue = $(e.target).val();
                var preferences = [
                    {
                        type: 'message_blocknoncontacts',
                        value: newValue
                    }
                ];

                Repository.savePreferences(loggedInUserId, preferences)
                    .then(function() {
                        PubSub.publish(MessageDrawerEvents.PREFERENCES_UPDATED, preferences);
                        return;
                    })
                    .catch(Notification.exception);
            }
        );

        settingsContainer.on(CustomEvents.events.activate, SELECTORS.ENTER_TO_SEND_PREFERENCE, function(e) {
                var newValue = $(e.target).prop('checked');
                var preferences = [
                    {
                        type: 'message_entertosend',
                        value: newValue
                    }
                ];

                Repository.savePreferences(loggedInUserId, preferences)
                    .then(function() {
                        PubSub.publish(MessageDrawerEvents.PREFERENCES_UPDATED, preferences);
                        return;
                    })
                    .catch(Notification.exception);
            }
        );
    };

    /**
     * Initialise the settings page by adding event listeners to
     * the checkboxes.
     *
     * @param {Object} header The settings header element.
     * @param {Object} body The settings body element.
     * @param {Number} loggedInUserId The logged in user id.
     * @return {Object} jQuery promise
     */
    var show = function(header, body, loggedInUserId) {
        if (!body.attr('data-init')) {
            registerEventListeners(body, loggedInUserId);
            body.attr('data-init', true);
        }

        return $.Deferred().resolve().promise();
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @return {Object} jQuery promise
     */
    var description = function() {
        return Str.get_string('messagedrawerviewsettings', 'core_message');
    };

    return {
        show: show,
        description: description,
    };
});
