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
 * Provides an interface for a tool type in the Moodle server.
 *
 * @module     mod_lti/tool_type
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['core/ajax', 'core/notification'], function(ajax, notification) {
    return {
        /**
         * Get a list of tool types from Moodle for the given
         * search args.
         *
         * See also:
         * mod/lti/classes/external.php get_tool_types_parameters()
         *
         * @method query
         * @public
         * @param {Object} args Search parameters
         * @return {Promise} jQuery Deferred object
         */
        query: function(args) {
            var request = {
                methodname: 'mod_lti_get_tool_types',
                args: args || {}
            };

            var promise = ajax.call([request])[0];

            promise.fail(notification.exception);

            return promise;
        },

        /**
         * Create a tool type in Moodle.
         *
         * The promise will fail if the URL is not a cartridge, so you must handle the fail result.
         *
         * See also:
         * mod/lti/classes/external.php create_tool_type_parameters()
         *
         * @method create
         * @public
         * @param {Object} args Tool type properties
         * @return {Promise} jQuery Deferred object
         */
        create: function(args) {
            var request = {
                methodname: 'mod_lti_create_tool_type',
                args: args
            };

            var promise = ajax.call([request])[0];

            return promise;
        },

        /**
         * Update a tool type in Moodle.
         *
         * See also:
         * mod/lti/classes/external.php update_tool_type_parameters()
         *
         * @method update
         * @public
         * @param {Object} args Tool type properties
         * @return {Promise} jQuery Deferred object
         */
        update: function(args) {
            var request = {
                methodname: 'mod_lti_update_tool_type',
                args: args
            };

            var promise = ajax.call([request])[0];

            promise.fail(notification.exception);

            return promise;
        },

        /**
         * Delete a tool type from Moodle.
         *
         * @method delete
         * @public
         * @param {Integer} id Tool type ID
         * @return {Promise} jQuery Deferred object
         */
        'delete': function(id) {
            var request = {
                methodname: 'mod_lti_delete_tool_type',
                args: {
                    id: id
                }
            };

            var promise = ajax.call([request])[0];

            promise.fail(notification.exception);

            return promise;
        },

        /**
         * Get a list of tool types from Moodle for the given
         * tool proxy id.
         *
         * @method query
         * @public
         * @param {Integer} id Tool type ID
         * @return {Promise} jQuery Deferred object
         */
        getFromToolProxyId: function(id) {
            return this.query({toolproxyid: id});
        },

        /**
         * Check if the given URL is a cartridge URL.
         *
         * The promise will fail if the URL is unreachable, so you must handle the fail result.
         *
         * @method isCartridge
         * @public
         * @param {String} url
         * @return {Promise} jQuery Deferred object
         */
        isCartridge: function(url) {
            var request = {
                methodname: 'mod_lti_is_cartridge',
                args: {
                    url: url
                }
            };

            var promise = ajax.call([request])[0];

            return promise;
        },

        /**
         * Tool type constants.
         */
        constants: {
            state: {
                configured: 1,
                pending: 2,
                rejected: 3
            },
        }
    };
});
