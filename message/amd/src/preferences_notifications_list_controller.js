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
 * @since      3.2
 */
define(['jquery', 'core/custom_interaction_events', 'core_message/notification_preference',
        'core_message/notification_processor_settings'],
        function($, CustomEvents, NotificationPreference, NotificationProcessorSettings) {

    var SELECTORS = {
        PREFERENCE: '[data-state]',
        PREFERENCE_ROW: '.preference-row',
        PREFERENCE_INPUT: '[data-state] input',
        PROCESSOR_SETTING: '[data-processor-setting]',
    };

    /**
     * Constructor for the PreferencesController.
     *
     * @param element jQuery object root element of the preference
     * @return object PreferencesController
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
     * @return bool
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

    PreferencesController.prototype.registerEventListeners = function() {
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

        this.root.on(CustomEvents.events.activate, SELECTORS.PROCESSOR_SETTING, function(e) {
            var element = $(e.target).closest(SELECTORS.PROCESSOR_SETTING);
            var processorSettings = new NotificationProcessorSettings(element);
            processorSettings.show();
        });

        $(document).on('messageprefs:disableall', function() {
            this.setDisabled();
        }.bind(this));

        $(document).on('messageprefs:enableall', function() {
            this.setEnabled();
        }.bind(this));
    };

    return PreferencesController;
});
