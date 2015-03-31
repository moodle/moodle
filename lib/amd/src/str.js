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
 * Fetch and render language strings.
 * Hooks into the old M.str global - but can also fetch missing strings on the fly.
 *
 * @module     core/str
 * @class      str
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/ajax'], function($, ajax) {


    return /** @alias module:core/str */ {
        // Public variables and functions.
        /**
         * Return a promise object that will be resolved into a string eventually (maybe immediately).
         *
         * @method get_string
         * @param {string} key The language string key
         * @param {string} component The language string component
         * @param {string} param The param for variable expansion in the string.
         * @param {string} lang The users language - if not passed it is deduced.
         * @return {Promise}
         */
        get_string: function(key, component, param, lang) {
            var deferred = $.Deferred();

            var request = this.get_strings([{
                key: key,
                component: component,
                param: param,
                lang: lang
            }]);

            request.done(function(results) {
                deferred.resolve(results[0]);
            }).fail(function(ex) {
                deferred.reject(ex);
            });

            return deferred.promise();
        },

        /**
         * Make a batch request to load a set of strings
         *
         * @method get_strings
         * @param {Object[]} requests Array of { key: key, component: component, param: param, lang: lang };
         *                                      See get_string for more info on these args.
         * @return {Promise}
         */
        get_strings: function(requests) {

            var deferred = $.Deferred();
            var results = [];
            var i = 0;
            var missing = false;
            var request;

            for (i = 0; i < requests.length; i++) {
                request = requests[i];
                if (typeof M.str[request.component] === "undefined" ||
                        typeof M.str[request.component][request.key] === "undefined") {
                    missing = true;
                }
            }

            if (!missing) {
                // We have all the strings already.
                for (i = 0; i < requests.length; i++) {
                    request = requests[i];

                    results[i] = M.util.get_string(request.key, request.component, request.param);
                }
                deferred.resolve(results);
            } else {
                // Something is missing, we might as well load them all.
                var ajaxrequests = [];

                for (i = 0; i < requests.length; i++) {
                    request = requests[i];

                    if (typeof request.lang === "undefined") {
                        request.lang = $('html').attr('lang');
                    }
                    ajaxrequests.push({
                        methodname: 'core_get_string',
                        args: {
                            stringid: request.key,
                            component: request.component,
                            lang: request.lang,
                            stringparams: []
                        }
                    });
                }

                var deferreds = ajax.call(ajaxrequests);
                $.when.apply(null, deferreds).done(
                    function() {
                        // Turn the list of arguments (unknown length) into a real array.
                        var i = 0;
                        for (i = 0; i < arguments.length; i++) {
                            request = requests[i];
                            // Cache all the string templates.
                            if (typeof M.str[request.component] === "undefined") {
                                M.str[request.component] = [];
                            }
                            M.str[request.component][request.key] = arguments[i];
                            // And set the results.
                            results[i] = M.util.get_string(request.key, request.component, request.param).trim();
                        }
                        deferred.resolve(results);
                    }
                ).fail(
                    function(ex) {
                        deferred.reject(ex);
                    }
                );
            }

            return deferred.promise();
        }
    };
});
