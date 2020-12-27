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
    'core/templates',
    'core_message/message_repository',
    'core/custom_interaction_events',
    'core_message/message_drawer_events'
],
function(
    $,
    Notification,
    Str,
    PubSub,
    Templates,
    Repository,
    CustomEvents,
    MessageDrawerEvents
) {

    var SELECTORS = {
        CHECKBOX: 'input[type="checkbox"]',
        SETTINGS: '[data-region="settings"]',
        PRIVACY_PREFERENCE: '[data-preference="blocknoncontacts"] input[type="radio"]',
        NOTIFICATIONS_PREFERENCE: '[data-preference="notifications"] input[type="checkbox"]',
        ENTER_TO_SEND_PREFERENCE: '[data-preference="entertosend"] input[type="checkbox"]',
        NOTIFICATION_PREFERENCES_CONTAINER: '[data-region="notification-preference-container"]',
        CONTENT_CONTAINER: '[data-region="content-container"]',
        PLACEHOLDER_CONTAINER: '[data-region="placeholder-container"]'
    };

    var TEMPLATES = {
        NOTIFICATION_PREFERENCES: 'core_message/message_drawer_view_settings_body_content_notification_preferences'
    };

    var NOTIFICATION_PREFERENCES_KEY = 'message_provider_moodle_instantmessage';

    /**
     * Select the correct radio button in the DOM for the privacy preference.
     *
     * @param {Object} body The settings body element.
     * @param {Number} value Which radio button should be set
     */
    var setPrivacyPreference = function(body, value) {
        var inputs = body.find(SELECTORS.PRIVACY_PREFERENCE);
        inputs.each(function(index, input) {
            input = $(input);
            if (input.val() == value) {
                input.prop('checked', true);
            } else {
                input.prop('checked', false);
            }
        });
    };

    /**
     * Set the "enter to send" checkbox to the correct value in the DOM.
     *
     * @param {Object} body The settings body element.
     * @param {Bool} value Whether enter to send is enabled or disabled.
     */
    var setEnterToSend = function(body, value) {
        var checkbox = body.find(SELECTORS.ENTER_TO_SEND_PREFERENCE);

        if (value) {
            checkbox.prop('checked', true);
        } else {
            checkbox.prop('checked', false);
        }
    };

    /**
     * Send a request to the server to save the given preferences. Also publish
     * a preferences updated event for the rest of the message drawer to
     * subscribe to.
     *
     * @param {Number} loggedInUserId The logged in user id.
     * @param {Array} preferences The preferences to set.
     * @return {Object} jQuery promise
     */
    var savePreferences = function(loggedInUserId, preferences) {
        return Repository.savePreferences(loggedInUserId, preferences)
            .then(function() {
                PubSub.publish(MessageDrawerEvents.PREFERENCES_UPDATED, preferences);
                return;
            })
            .catch(Notification.exception);
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

        settingsContainer.on(CustomEvents.events.activate, SELECTORS.NOTIFICATIONS_PREFERENCE, function(e) {
            var container = $(e.target).closest(SELECTORS.NOTIFICATION_PREFERENCES_CONTAINER);
            var checkboxes = container.find(SELECTORS.CHECKBOX);
            if (!checkboxes.length) {
                return;
            }
            // The preference value is all of the enabled processors, comma separated, so let's
            // see which ones are enabled.
            var values = checkboxes.toArray().reduce(function(carry, checkbox) {
                checkbox = $(checkbox);
                if (checkbox.prop('checked')) {
                    carry.push(checkbox.attr('data-name'));
                }

                return carry;
            }, []);
            var newValue = values.length ? values.join(',') : 'none';
            var preferences = [
                {
                    type: 'message_provider_moodle_instantmessage_loggedoff',
                    value: newValue
                },
                {
                    type: 'message_provider_moodle_instantmessage_loggedin',
                    value: newValue
                }
            ];

            savePreferences(loggedInUserId, preferences);
        });

        settingsContainer.on('change', SELECTORS.PRIVACY_PREFERENCE, function(e) {
            var newValue = $(e.target).val();
            var preferences = [
                {
                    type: 'message_blocknoncontacts',
                    value: newValue
                }
            ];

            savePreferences(loggedInUserId, preferences);
        });

        settingsContainer.on(CustomEvents.events.activate, SELECTORS.ENTER_TO_SEND_PREFERENCE, function(e) {
            var newValue = $(e.target).prop('checked');
            var preferences = [
                {
                    type: 'message_entertosend',
                    value: newValue
                }
            ];

            savePreferences(loggedInUserId, preferences);
        });
    };

    /**
     * Initialise the module by loading the user's messaging preferences from the server and
     * rendering them in the settings page.
     *
     * Moodle may have many (or no) message processors enabled to notify the user when they
     * receive messages. We need to dynamically build the settings page based on which processors
     * are configured for the user.
     *
     * @param {Object} body The settings body element.
     * @param {Number} loggedInUserId The logged in user id.
     */
    var init = function(body, loggedInUserId) {
        // Load the message preferences from the server.
        Repository.getUserMessagePreferences(loggedInUserId)
            .then(function(response) {
                // Set the values of the stright forward preferences.
                setPrivacyPreference(body, response.blocknoncontacts);
                setEnterToSend(body, response.entertosend);

                // Parse the list of other preferences into a more usable format.
                var notificationProcessors = [];
                if (response.preferences.components.length) {
                    response.preferences.components.forEach(function(component) {
                        if (component.notifications.length) {
                            // Filter down to just the notification processors that work on instant
                            // messaging. We don't care about another other ones.
                            var notificationPreferences = component.notifications.filter(function(notification) {
                                return notification.preferencekey == NOTIFICATION_PREFERENCES_KEY;
                            });

                            if (notificationPreferences.length) {
                                // Messaging only has one config at the moment which is for notifications
                                // on personal messages.
                                var configuration = component.notifications[0];
                                notificationProcessors = configuration.processors.map(function(processor) {
                                    // Consider the the processor enabled if either preference is set. This is
                                    // for backwards compatibility. Going forward they will be treated as one
                                    // setting.
                                    var checked = processor.loggedin.checked || processor.loggedoff.checked;
                                    return {
                                        displayname: processor.displayname,
                                        name: processor.name,
                                        checked: checked,
                                        // The admin can force processors to be enabled at a site level so
                                        // we need to check if this processor has been locked by the admin.
                                        locked: processor.locked,
                                        lockedmessage: processor.lockedmessage || null,
                                    };
                                });
                            }
                        }
                    });
                }

                var container = body.find(SELECTORS.NOTIFICATION_PREFERENCES_CONTAINER);
                if (notificationProcessors.length) {
                    // We have processors (i.e. email, mobile, jabber) to show.
                    container.removeClass('hidden');
                    // Render the processor options.
                    return Templates.render(TEMPLATES.NOTIFICATION_PREFERENCES, {processors: notificationProcessors})
                        .then(function(html) {
                            container.append(html);
                            return html;
                        });
                } else {
                    return true;
                }
            })
            .then(function() {
                // We're done loading so hide the loading placeholder and show the settings.
                body.find(SELECTORS.CONTENT_CONTAINER).removeClass('hidden');
                body.find(SELECTORS.PLACEHOLDER_CONTAINER).addClass('hidden');
                // Register the event listers for if the user wants to change the preferences.
                registerEventListeners(body, loggedInUserId);
                return;
            })
            .catch(Notification.exception);
    };

    /**
     * Initialise the settings page by adding event listeners to
     * the checkboxes.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} header The settings header element.
     * @param {Object} body The settings body element.
     * @param {Object} footer The footer body element.
     * @param {Number} loggedInUserId The logged in user id.
     * @return {Object} jQuery promise
     */
    var show = function(namespace, header, body, footer, loggedInUserId) {
        if (!body.attr('data-init')) {
            init(body, loggedInUserId);
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
