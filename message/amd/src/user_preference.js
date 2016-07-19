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
 * Controls a user preference on the message preferences page.
 *
 * @module     core_message/user_preference
 * @class      user_preference
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
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
            return $.Deferred().resolve();
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

    return UserPreference;
});
