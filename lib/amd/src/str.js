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
define(['jquery', 'core/ajax', 'core/localstorage'], function($, ajax, storage) {

    var promiseCache = [];

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
            var request = this.get_strings([{
                key: key,
                component: component,
                param: param,
                lang: lang
            }]);

            return request.then(function(results) {
                return results[0];
            });
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

            // Try from local storage. If it's there - put it in M.str and resolve it.

            for (i = 0; i < requests.length; i++) {
                request = requests[i];
                if (typeof request.lang === "undefined") {
                    request.lang = $('html').attr('lang').replace(/-/g, '_');
                }
                request.cacheKey = 'core_str/' + request.key + '/' + request.component + '/' + request.lang;
                if (typeof M.str[request.component] === "undefined" ||
                        typeof M.str[request.component][request.key] === "undefined") {
                    // Try and revive it from local storage.
                    var cached = storage.get(request.cacheKey);
                    if (cached) {
                        if (typeof M.str[request.component] === "undefined") {
                            M.str[request.component] = [];
                        }
                        M.str[request.component][request.key] = cached;
                    } else {
                        // It's really not here.
                        missing = true;
                    }
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
                var ajaxrequests = [];
                var fetchpromises = [];

                // Done handler for ajax call. Must be bound to the current fetchpromise. We do this
                // to avoid creating a function in a loop.
                var doneFunc = function(str) {
                    this.resolve(str);
                };

                var failFunc = function(reason) {
                    this.reject(reason);
                };

                for (i = 0; i < requests.length; i++) {
                    request = requests[i];

                    // If we ever fetched this string with a promise, reuse it.
                    if (typeof promiseCache[request.cacheKey] !== 'undefined') {
                        fetchpromises.push(promiseCache[request.cacheKey]);
                    } else {
                        // Add this to the list we need to really fetch.
                        var fetchpromise = $.Deferred();

                        ajaxrequests.push({
                            methodname: 'core_get_string',
                            args: {
                                stringid: request.key,
                                component: request.component,
                                lang: request.lang,
                                stringparams: []
                            },
                            done: doneFunc.bind(fetchpromise),
                            fail: failFunc.bind(fetchpromise)
                        });

                        promiseCache[request.cacheKey] = fetchpromise.promise();
                        fetchpromises.push(promiseCache[request.cacheKey]);
                    }
                }

                // Everything might already be queued so we need to check if we have real ajax requests to run.
                if (ajaxrequests.length > 0) {
                    ajax.call(ajaxrequests, true, false);
                }

                $.when.apply(null, fetchpromises).done(
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
                            storage.set('core_str/' + request.key + '/' + request.component + '/' + request.lang, arguments[i]);
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
