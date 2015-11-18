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
 * Provides an interface for a tool proxy in the Moodle server.
 *
 * @module     mod_lti/tool_proxy
 * @class      tool_proxy
 * @package    core
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['core/ajax', 'core/notification'], function(ajax, notification) {
    return /** @alias module:mod_lti/tool_proxy */ {
        /**
         * Delete a tool proxy from Moodle.
         *
         * @method delete
         * @public
         * @param int Tool proxy ID
         * @return object jQuery deferred object
         */
        delete: function(id) {
            var request = {
                methodname: 'mod_lti_delete_tool_proxy',
                args: {
                    id: id
                }
            };

            var promise = ajax.call([request])[0];

            promise.fail(notification.exception);

            return promise;
        },

        /**
         * Create a tool proxy in Moodle.
         *
         * See mod/lti/classes/external.php create_tool_proxy_parameters
         *
         * @method create
         * @public
         * @param object Tool proxy properties
         * @return object jQuery deferred object
         */
        create: function(args) {
            var request = {
                methodname: 'mod_lti_create_tool_proxy',
                args: args
            };

            var promise = ajax.call([request])[0];

            promise.fail(notification.exception);

            return promise;
        }
    };
});
