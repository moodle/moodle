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
 * Controls the preferences for the list of notification types on the
 * message preference page
 *
 * @module     core_message/preferences_notifications_list_controller
 * @class      preferences_notifications_list_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/ajax',
        'core/notification',
        'core/custom_interaction_events',
        'core_message/notification_preference',
        'core_message/notification_processor_settings',
        'core/modal_factory',
        ],
        function(
          $,
          Ajax,
          Notification,
          CustomEvents,
          NotificationPreference,
          NotificationProcessorSettings,
          ModalFactory
        ) {

    var SELECTORS = {
        DISABLE_NOTIFICATIONS: '[data-region="disable-notification-container"] [data-disable-notifications]',
        DISABLE_NOTIFICATIONS_CONTAINER: '[data-region="disable-notification-container"]',
        PREFERENCE: '[data-state]',
        PREFERENCE_ROW: '[data-region="preference-row"]',
        PREFERENCE_INPUT: '[data-state] input',
        PROCESSOR_SETTING: '[data-processor-setting]',
    };

    /**
     * Constructor for the PreferencesController.
     *
     * @param {object} element jQuery object root element of the preference
     */
    var PreferencesController = function(element) {
        this.root = $(element);
        this.userId = this.root.attr('data-user-id');

        this.registerEventListeners();
    };

    /**
     * Check if the preferences are all disabled.
     *
     * @method isDisabled
     * @return {bool}
     */
    PreferencesController.prototype.isDisabled = function() {
        return this.root.hasClass('disabled');
    };

    /**
     * Disable all of the preferences.
     *
     * @method setDisabled
     */
    PreferencesController.prototype.setDisabled = function() {
        this.root.addClass('disabled');
        this.root.find(SELECTORS.PREFERENCE_INPUT).prop('disabled', true);
    };

    /**
     * Enable all of the preferences.
     *
     * @method setEnabled
     */
    PreferencesController.prototype.setEnabled = function() {
        this.root.removeClass('disabled');
        this.root.find(SELECTORS.PREFERENCE_INPUT).prop('disabled', false);
    };

    /**
      * Update the disable all notifications user property in the DOM and
      * send a request to update on the server.
      *
      * @method toggleDisableAllStatus
      * @return {Promise}
      */
    PreferencesController.prototype.toggleDisableAllStatus = function() {
        var checkbox = $(SELECTORS.DISABLE_NOTIFICATIONS);
        var container = $(SELECTORS.DISABLE_NOTIFICATIONS_CONTAINER);
        var ischecked = checkbox.prop('checked');

        if (container.hasClass('loading')) {
            return $.Deferred().resolve();
        }

        container.addClass('loading');

        var request = {
            methodname: 'core_user_update_user_preferences',
            args: {
                userid: this.userId,
                emailstop: ischecked ? 1 : 0,
            }
        };

        return Ajax.call([request])[0]
            .done(function() {
                if (ischecked) {
                    this.setDisabled();
                } else {
                    this.setEnabled();
                }
            }.bind(this))
            .always(function() {
                container.removeClass('loading');
            })
            .fail(Notification.exception);
    };

    /**
      * Set up all of the event listeners for the PreferencesController.
      *
      * @method registerEventListeners
      */
    PreferencesController.prototype.registerEventListeners = function() {
        var disabledNotificationsElement = $(SELECTORS.DISABLE_NOTIFICATIONS);

        CustomEvents.define(this.root, [
            CustomEvents.events.activate,
        ]);

        this.root.on('change', function(e) {
            if (!this.isDisabled()) {
                var preferenceElement = $(e.target).closest(SELECTORS.PREFERENCE);
                var preferenceRow = $(e.target).closest(SELECTORS.PREFERENCE_ROW);
                var preference = new NotificationPreference(preferenceRow, this.userId);

                preferenceElement.addClass('loading');
                preference.save().always(function() {
                    preferenceElement.removeClass('loading');
                });
            }
        }.bind(this));

        var eventFormPromise = ModalFactory.create({
            type: NotificationProcessorSettings.TYPE,
        });

        this.root.on(CustomEvents.events.activate, SELECTORS.PROCESSOR_SETTING, function(e) {
            var element = $(e.target).closest(SELECTORS.PROCESSOR_SETTING);

            e.preventDefault();
            eventFormPromise.then(function(modal) {
                // Configure modal with element settings.
                modal.setUserId($(element).attr('data-user-id'));
                modal.setName($(element).attr('data-name'));
                modal.setContextId($(element).attr('data-context-id'));
                modal.setElement(element);
                modal.show();

                e.stopImmediatePropagation();
                return;
            }).fail(Notification.exception);
        });

        CustomEvents.define(disabledNotificationsElement, [
            CustomEvents.events.activate
        ]);

        disabledNotificationsElement.on(CustomEvents.events.activate, function() {
            this.toggleDisableAllStatus();
        }.bind(this));
    };

    return PreferencesController;
});
