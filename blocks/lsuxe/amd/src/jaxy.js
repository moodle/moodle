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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'block_lsuxe/notifications'],
    function($, Ajax, Noti) {
    'use strict';

    return {
        /**
         * Quick check to see if the data chunk is array or object.
         *
         * @param object or array
         * @return bool - If true then it's array or obj
         */
        isAorO: function(val) {
            return val instanceof Array || val instanceof Object ? true : false;
        },

        /**
         * AJAX method to access the external services for Cross Enrollment
         *
         * @param {object} The request arguments
         * Format to make calls is:
         *      'call': [the function name],
                'params': data you want to pass in JSON format,
                'class': [class name AND file name, should match]
         * @return {promise} Resolved with an array of the calendar events
         */
        XEAjax: function(data_chunk) {
            var promiseObj = new Promise(function(resolve, reject) {
                var send_this = [{
                    methodname: 'block_lsuxe_xeajax',
                    args: {
                        datachunk: data_chunk,
                    }
                }];
                Ajax.call(send_this)[0].then(function(response) {
                    resolve(JSON.parse(response.data));
                }).catch(function(ev) {
                    console.log("XEAjax() -> JAXY Fail :-(");
                    console.log("XEAjax() -> JAXY Fail going to reject: ", ev);
                    reject(ev);
                });
            });
            return promiseObj;
        },

        /**
         * AJAX method to access the remote Moodle instances.
         * Going to use default jQuery ajax, not Moodles, for more control.
         *
         * @param {object} The request arguments
         * Format to make calls is:
         *      type: GET or POST,
                data: {
                    wstoken: x
                    wsfunction: x
                    moodlewsrestformat: x
                },
                url: domain + '/webservice/rest/server.php',
         * @return {promise} Resolved with an array of the calendar events
         */
        XERemoteAjax: function(data_chunk) {
            if (data_chunk.url.substring(0, 4) == "http") {
                Noti.callNoti({
                    message: "URLs with a leading scheme (http, www) need to be removed in the Moodle's URL.",
                    type: 'error'
                });
                return;
            }

            var full_url = "https://"+data_chunk.url;
            var promiseObj = new Promise(function(resolve) {
                $.ajax({
                    type: data_chunk.type,
                    data: data_chunk.data,
                    url: full_url,
                }).done(function (response) {
                    // If token is incorrect Moodle will throw an exception.
                    if (response.hasOwnProperty('exception')) {
                        resolve({
                            'success': false,
                            'msg': response.message
                        });
                    } else {
                        // Need to handle the response. If the request is for Moodle Core
                        // then the response is an array or object (isAorO).
                        // otherwise it's a stringified JSON object.
                        // if (that.isAorO(response)) {
                        if (response instanceof Array || response instanceof Object) {
                            resolve(response);
                        } else {
                            resolve(JSON.parse(response.data));
                        }
                    }
                }).fail(function ( jqXHR, textStatus, errorThrown ) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    resolve({
                        'success': false,
                        'msg': "Could not connect to the server."
                    });
                });
            });
            return promiseObj;
        },
    };
});
