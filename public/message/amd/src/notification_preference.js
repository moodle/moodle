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
 * @module     core_message/notification_preference
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification', 'core_message/notification_processor'],
        function($, Ajax, Notification, NotificationProcessor) {

    const SELECTORS = {
        PROCESSOR: '[data-processor-name]',
        STATE_INPUTS: '[data-state] input',
    };

    /**
     * Constructor for the Preference.
     *
     * @class
     * @param {object} element jQuery object root element of the preference
     * @param {int} userId The current user id
     */
    const NotificationPreference = function(element, userId) {
        this.root = $(element);
        this.userId = userId;
    };

    /**
     * Get the unique prefix key that identifies this user preference.
     *
     * @method getPreferenceKey
     * @return {string}
     */
    NotificationPreference.prototype.getPreferenceKey = function() {
        return this.root.attr('data-preference-key');
    };

    /**
     * Get the unique key for the enabled preference.
     *
     * @method getEnabledPreferenceKey
     * @return {string}
     */
    NotificationPreference.prototype.getEnabledPreferenceKey = function() {
        return this.getPreferenceKey() + '_enabled';
    };

    /**
     * Get the list of Processors available for this preference.
     *
     * @method getProcessors
     * @return {array}
     */
    NotificationPreference.prototype.getProcessors = function() {
        return this.root.find(SELECTORS.PROCESSOR).map(function(index, element) {
            return new NotificationProcessor($(element));
        });
    };

    /**
     * Flag the preference as loading.
     *
     * @method startLoading
     */
    NotificationPreference.prototype.startLoading = function() {
        this.root.addClass('loading');
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', true);
    };

    /**
     * Remove the loading flag for this preference.
     *
     * @method stopLoading
     */
    NotificationPreference.prototype.stopLoading = function() {
        this.root.removeClass('loading');
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', false);
    };

    /**
     * Check if the preference is loading.
     *
     * @method isLoading
     * @return {Boolean}
     */
    NotificationPreference.prototype.isLoading = function() {
        return this.root.hasClass('loading');
    };

    /**
     * Persist the current state of the processors for this preference.
     *
     * @method save
     * @return {object} jQuery promise
     */
    NotificationPreference.prototype.save = function() {
        if (this.isLoading()) {
            return $.Deferred().resolve();
        }

        this.startLoading();

        let enabledValue = '';

        this.getProcessors().each(function(index, processor) {
            if (processor.isEnabled()) {
                if (enabledValue === '') {
                    enabledValue = processor.getName();
                } else {
                    enabledValue += ',' + processor.getName();
                }
            }
        });

        if (enabledValue === '') {
            enabledValue = 'none';
        }

        const args = {
            userid: this.userId,
            preferences: [
                {
                    type: this.getEnabledPreferenceKey(),
                    value: enabledValue,
                }
            ],
        };

        const request = {
            methodname: 'core_user_update_user_preferences',
            args: args,
        };

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .always(function() {
                this.stopLoading();
            }.bind(this));
    };

    return NotificationPreference;
});
