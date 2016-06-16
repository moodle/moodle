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
 * Handles the return params from the external registration page after it
 * redirects back to Moodle.
 *
 * See also: mod/lti/externalregistrationreturn.php
 *
 * @module     mod_lti/external_registration_return
 * @class      external_registration_return
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define([], function() {

    return /** @alias module:mod_lti/external_registration_return */ {

        /**
         * If this was rendered in an iframe then trigger the external registration
         * complete behaviour in the parent page and provide the params returned from
         * the external registration page.
         *
         * @param {String} message The registration message from the external registration page
         * @param {String} error The registration error message from the external registration page, if
         *                     there was an error.
         * @param {Integer} id The tool proxy id for the external registration.
         * @param {String} status Whether the external registration was successful or not.
         */
        init: function(message, error, id, status) {
            if (window.parent) {
                window.parent.triggerExternalRegistrationComplete({
                    message: message,
                    error: error,
                    id: id,
                    status: status
                });
            }
        }
    };
});
