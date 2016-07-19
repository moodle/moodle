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
 * Handle the disable all notifications user preference on the message
 * preferences page
 *
 * @module     core_message/disable_all_preference
 * @class      disable_all_preference
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery', 'core_message/user_preference'], function($, UserPreference) {
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

    return DisableAllPreference;
});
