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
 * Standard Ajax wrapper for Moodle. It calls the central Ajax script,
 * which can call any existing webservice using the current session.
 * In addition, it can batch multiple requests and return multiple responses.
 *
 * @module     core/ajax
 * @class      ajax
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/config'], function($, config) {

    /**
     * Success handler. Called when the ajax call succeeds. Checks each response and
     * resolves or rejects the deferred from that request.
     *
     * @method requestSuccess
     * @private
     * @param {Object[]} responses Array of responses containing error, exception and data attributes.
     */
    var requestSuccess = function(responses) {
        // Call each of the success handlers.
        var requests = this;
        var exception = null;
        var i = 0;
        var request;
        var response;

        for (i = 0; i < requests.length; i++) {
            request = requests[i];

            response = responses[i];
            // We may not have responses for all the requests.
            if (typeof response !== "undefined") {
                if (response.error === false) {
                    // Call the done handler if it was provided.
                    request.deferred.resolve(response.data);
                } else {
                    exception = response.exception;
                    break;
                }
            } else {
                // This is not an expected case.
                exception = new Error('missing response');
                break;
            }
        }
        // Something failed, reject the remaining promises.
        if (exception !== null) {
            for (; i < requests.length; i++) {
                request = requests[i];
                request.deferred.reject(exception);
            }
        }
    };

    /**
     * Fail handler. Called when the ajax call fails. Rejects all deferreds.
     *
     * @method requestFail
     * @private
     * @param {jqXHR} jqXHR The ajax object.
     * @param {string} textStatus The status string.
     */
    var requestFail = function(jqXHR, textStatus) {
        // Reject all the promises.
        var requests = this;

        var i = 0;
        for (i = 0; i < requests.length; i++) {
            var request = requests[i];

            request.deferred.reject(textStatus);
        }
    };

    return /** @alias module:core/ajax */ {
        // Public variables and functions.
        /**
         * Make a series of ajax requests and return all the responses.
         *
         * @method call
         * @param {Object[]} Array of requests with each containing methodname and args properties.
         *                   done and fail callbacks can be set for each element in the array, or the
         *                   can be attached to the promises returned by this function.
         * @param {Boolean} async Optional, defaults to true.
         *                  If false - this function will not return until the promises are resolved.
         * @param {Boolean} loginrequired Optional, defaults to true.
         *                  If false - this function will call the faster nologin ajax script - but
         *                  will fail unless all functions have been marked as 'loginrequired' => false
         *                  in services.php
         * @return {Promise[]} Array of promises that will be resolved when the ajax call returns.
         */
        call: function(requests, async, loginrequired) {
            var ajaxRequestData = [],
                i,
                promises = [];

            if (typeof loginrequired === "undefined") {
                loginrequired = true;
            }
            if (typeof async === "undefined") {
                async = true;
            }
            for (i = 0; i < requests.length; i++) {
                var request = requests[i];
                ajaxRequestData.push({
                    index: i,
                    methodname: request.methodname,
                    args: request.args
                });
                request.deferred = $.Deferred();
                promises.push(request.deferred.promise());
                // Allow setting done and fail handlers as arguments.
                // This is just a shortcut for the calling code.
                if (typeof request.done !== "undefined") {
                    request.deferred.done(request.done);
                }
                if (typeof request.fail !== "undefined") {
                    request.deferred.fail(request.fail);
                }
                request.index = i;
            }

            ajaxRequestData = JSON.stringify(ajaxRequestData);
            var settings = {
                type: 'POST',
                data: ajaxRequestData,
                context: requests,
                dataType: 'json',
                processData: false,
                async: async,
                contentType: "application/json"
            };

            var script = config.wwwroot + '/lib/ajax/service.php?sesskey=' + config.sesskey;
            if (!loginrequired) {
                script = config.wwwroot + '/lib/ajax/service-nologin.php?sesskey=' + config.sesskey;
            }

            // Jquery deprecated done and fail with async=false so we need to do this 2 ways.
            if (async) {
                $.ajax(script, settings)
                    .done(requestSuccess)
                    .fail(requestFail);
            } else {
                settings.success = requestSuccess;
                settings.error = requestFail;
                $.ajax(script, settings);
            }

            return promises;
        }
    };
});
