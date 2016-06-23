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
 * Controls the general settings on the message preferences page
 *
 * @module     message/preferences_general_settings_controller
 * @class      preferences_processors_controller
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    var SELECTORS = {
        SETTING: '[data-preference-key]',
    };

    /**
     * Constructor for the UserPreference.
     *
     * @param element jQuery object root element of the processor
     * @param int the current user id
     * @return object UserPreference
     */
    var UserPreference = function(element, userId) {
        this.root = $(element);
        this.userId = userId;
    };

    /**
     * Check if the preference is checked (enabled).
     *
     * @return bool
     */
    UserPreference.prototype.isChecked = function() {
        return this.root.find('input').prop('checked');
    };

    /**
     * Get the unique key that identifies this user preference.
     *
     * @method getPreferenceKey
     * @return string
     */
    UserPreference.prototype.getPreferenceKey = function() {
        return this.root.attr('data-preference-key');
    };

    /**
     * Flag the preference as loading.
     *
     * @method startLoading
     */
    UserPreference.prototype.startLoading = function() {
        this.root.addClass('loading');
        this.root.find('input').prop('disabled', true);
    };

    /**
     * Remove the loading flag for this preference.
     *
     * @method stopLoading
     */
    UserPreference.prototype.stopLoading = function() {
        this.root.removeClass('loading');
        this.root.find('input').prop('disabled', false);
    };

    /**
     * Check if the preference is loading.
     *
     * @method isLoading
     */
    UserPreference.prototype.isLoading = function() {
        return this.root.hasClass('loading');
    };

    /**
     * Generate the request arguments for the save function.
     *
     * @method getRequestArguments
     * @return object
     */
    UserPreference.prototype.getRequestArguments = function() {
        return {
            user: {
                preferences: [{
                    type: this.getPreferenceKey(),
                    value: this.isChecked() ? 1 : 0,
                }],
            }
        };
    };

    /**
     * Persist the user preference in the server.
     *
     * @method save
     * @return promise
     */
    UserPreference.prototype.save = function() {
        if (this.isLoading()) {
            return $.Deferred();
        }

        this.startLoading();

        var request = {
            methodname: 'core_user_update_user',
            args: this.getRequestArguments(),
        };

        return ajax.call([request])[0]
            .fail(notification.exception)
            .always(function() { this.stopLoading(); }.bind(this));
    };

    /**
     * Constructor for the DisableAlPreference. This is a special type
     * of UserPreference.
     *
     * Subclasses UserPreference.
     *
     * @param element jQuery object root element of the processor
     * @param int the current user id
     * @return object DisableAllPreference
     */
    var DisableAllPreference = function(element, userId) {
        UserPreference.call(this, element, userId);
    };

    /**
     * Clone the UserPreference prototype.
     */
    DisableAllPreference.prototype = Object.create(UserPreference.prototype);

    /**
     * Return the request arguments for the save function.
     *
     * Override UserPreference.prototype.getRequestArguments
     *
     * @method getRequestArguments
     * @return object
     */
    DisableAllPreference.prototype.getRequestArguments = function() {
        return {
            user: {
                emailstop: this.isChecked() ? 1 : 0,
            },
        };
    };

    /**
     * Persist the preference and fire relevant events after the
     * successfully saving.
     *
     * Override UserPreference.prototype.save
     *
     * @method save
     * @return promise
     */
    DisableAllPreference.prototype.save = function() {
        return UserPreference.prototype.save.call(this).done(function() {
            if (this.isChecked()) {
                $(document).trigger('messageprefs:disableall');
            } else {
                $(document).trigger('messageprefs:enableall');
            }
        }.bind(this));
    };

    /**
     * Constructor for the GeneralSettingsController.
     *
     * @param element jQuery object root element of the processor
     * @return object GeneralSettingsController
     */
    var GeneralSettingsController = function(element) {
        this.root = $(element);
        this.userId = this.root.attr('data-user-id');

        this.root.on('change', function(e) {
            var element = $(e.target).closest(SELECTORS.SETTING);
            var setting = this.createFromElement(element);
            setting.save();
        }.bind(this));
    };

    /**
     * Factory method to return the correct UserPreference instance
     * for the given jQuery element.
     *
     * @method save
     * @param object jQuery element
     * @return object UserPreference
     */
    GeneralSettingsController.prototype.createFromElement = function(element) {
        element = $(element);

        if (element.attr('data-preference-key') === "disableall") {
            return new DisableAllPreference(element, this.userId);
        } else {
            return new UserPreference(element, this.userId);
        }
    };

    return GeneralSettingsController;
});
