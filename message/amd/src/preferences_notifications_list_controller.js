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
 * Controls the preferences page
 *
 * @module     core_message/preferences_notifications_list_controller
 * @class      preferences_notifications_list_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    var SELECTORS = {
        PREFERENCE_ROW: '.preference-row',
        PROCESSOR: '[data-processor-name]',
        STATE_NONE: '[data-state="none"]',
        STATE_BOTH: '[data-state="both"]',
        STATE_LOGGED_IN: '[data-state="loggedin"]',
        STATE_LOGGED_OFF: '[data-state="loggedoff"]',
        STATE_INPUTS: '[data-state] input',
    };

    /**
     * Constructor for the Processor.
     *
     * @param element jQuery object root element of the processor
     * @return object Processor
     */
    var Processor = function(element) {
        this.root = $(element);
    };

    /**
     * Get the processor name.
     *
     * @method getName
     * @return string
     */
    Processor.prototype.getName = function() {
        return this.root.attr('data-processor-name');
    };

    /**
     * Check if the processor is enabled when the user is logged in.
     *
     * @method isLoggedInEnabled
     * @return bool
     */
    Processor.prototype.isLoggedInEnabled = function() {
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
     * @return bool
     */
    Processor.prototype.isLoggedOffEnabled = function() {
        var none = this.root.find(SELECTORS.STATE_NONE).find('input');

        if (none.prop('checked')) {
            return false;
        }

        var both = this.root.find(SELECTORS.STATE_BOTH).find('input');
        var loggedOff = this.root.find(SELECTORS.STATE_LOGGED_OFF).find('input');

        return loggedOff.prop('checked') || both.prop('checked');
    };

    /**
     * Constructor for the Preference.
     *
     * @param element jQuery object root element of the preference
     * @param int the current user id
     * @return object Preference
     */
    var Preference = function(element, userId) {
        this.root = $(element);
        this.userId = userId;
    };

    /**
     * Get the unique prefix key that identifies this user preference.
     *
     * @method getPreferenceKey
     * @return string
     */
    Preference.prototype.getPreferenceKey = function() {
        return this.root.attr('data-preference-key');
    };

    /**
     * Get the unique key for the logged in preference.
     *
     * @method getLoggedInPreferenceKey
     * @return string
     */
    Preference.prototype.getLoggedInPreferenceKey = function() {
        return this.getPreferenceKey() + '_loggedin';
    };

    /**
     * Get the unique key for the logged off preference.
     *
     * @method getLoggedOffPreferenceKey
     * @return string
     */
    Preference.prototype.getLoggedOffPreferenceKey = function() {
        return this.getPreferenceKey() + '_loggedoff';
    };

    /**
     * Get the list of Processors available for this preference.
     *
     * @method getProcessors
     * @return array
     */
    Preference.prototype.getProcessors = function() {
        return this.root.find(SELECTORS.PROCESSOR).map(function(index, element) {
            return new Processor($(element));
        });
    };

    /**
     * Flag the preference as loading.
     *
     * @method startLoading
     */
    Preference.prototype.startLoading = function() {
        this.root.addClass('loading');
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', true);
    };

    /**
     * Remove the loading flag for this preference.
     *
     * @method stopLoading
     */
    Preference.prototype.stopLoading = function() {
        this.root.removeClass('loading');
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', false);
    };

    /**
     * Check if the preference is loading.
     *
     * @method isLoading
     */
    Preference.prototype.isLoading = function() {
        return this.root.hasClass('loading');
    };

    /**
     * Persist the current state of the processors for this preference.
     *
     * @method save
     * @return promise
     */
    Preference.prototype.save = function() {
        if (this.isLoading()) {
            return $.Deferred();
        }

        this.startLoading();

        var loggedInValue = '';
        var loggedOffValue = '';

        this.getProcessors().each(function(index, processor) {
            if (processor.isLoggedInEnabled()) {
                if (loggedInValue === '') {
                    loggedInValue = processor.getName();
                } else {
                    loggedInValue += ',' + processor.getName();
                }
            }

            if (processor.isLoggedOffEnabled()) {
                if (loggedOffValue === '') {
                    loggedOffValue = processor.getName();
                } else {
                    loggedOffValue += ',' + processor.getName();
                }
            }
        });

        if (loggedInValue === '') {
            loggedInValue = 'none';
        }

        if (loggedOffValue === '') {
            loggedOffValue = 'none';
        }

        var args = {
            user: {
                preferences: [
                    {
                        type: this.getLoggedInPreferenceKey(),
                        value: loggedInValue,
                    },
                    {
                        type: this.getLoggedOffPreferenceKey(),
                        value: loggedOffValue,
                    },
                ],
            }
        };

        var request = {
            methodname: 'core_user_update_user',
            args: args,
        };

        return ajax.call([request])[0]
            .fail(notification.exception)
            .always(function() { this.stopLoading(); }.bind(this));
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

        this.root.on('change', function(e) {
            if (!this.isDisabled()) {
                var preferenceRow = $(e.target).closest(SELECTORS.PREFERENCE_ROW);
                var preference = new Preference(preferenceRow, this.userId);
                preference.save();
            }
        }.bind(this));

        $(document).on('messageprefs:disableall', function() {
            this.setDisabled();
        }.bind(this));

        $(document).on('messageprefs:enableall', function() {
            this.setEnabled();
        }.bind(this));
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
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', true);
    };

    /**
     * Enable all of the preferences.
     *
     * @method setEnabled
     */
    PreferencesController.prototype.setEnabled = function() {
        this.root.removeClass('disabled');
        this.root.find(SELECTORS.STATE_INPUTS).prop('disabled', false);
    };

    return PreferencesController;
});
