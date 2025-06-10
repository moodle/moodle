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

define(['jquery', 'core/ajax', 'block_lsuxe/notifications', 'block_lsuxe/xe_lib'],
    function($, Ajax, Noti, XELib) {
    'use strict';
    return {
        /**
         * Make an ajax call to the destination server and get groups.
         * This is currently not being used but may in the future?
         *
         * @param {Object} all the options to use jquery's $.ajax function (not moodle's)
         * @return {Promise}
         */
         getGroups: function (c, v) {
            var params = {
                'type': 'GET',
                'url': sessionStorage.getItem("currentUrl") + '/webservice/rest/server.php',
                'data': {
                    'wstoken': sessionStorage.getItem("currentToken"),
                    'wsfunction': 'core_group_get_course_user_groups',
                    'courseid': v,
                    'moodlewsrestformat': 'json'
                }
            };
            return XELib.jaxyRemotePromise(params);
        },

        processSelected: function (params) {
            var course_shortname = "",
                course_value = "";

            // Handle multiple select results
            if (params.selector.length > 1) {
                // TODO: process multiple selects.
                return;
            } else if (params.selector.length == 1) {
                course_shortname = params.selector[0].label;
                course_value = (params.selector[0].value).split("__")[0];
                this.getGroups(course_shortname, course_value);
                // Here a function can be called to make whatever changes AFTER something
                // has been selected.
            } else {
                // Nothing selected.
                return;
            }
        },

        /**
         * This is a mod that worked but not used. If to be used in the future then
         * in lib/amd/src/form-autocomplete.js approx ln. 743 need to inject a call to onSelected
         * when either mouse is clicked or enter key is hit (ln 870)
         * See the file for details.
         * lib/amd/src/form-cohort-selector.js also needs to add the onSelected method.
         * See the file for details.
         *
         * This is currently not being used but may in the future?
         *
         * @param {Object} all the options to use jquery's $.ajax function (not moodle's)
         * @return {Promise}
         */
        onSelected: function (selector, data) {
            this.processSelected({'selector': selector, 'data': data});
        },

        /**
         * Make an ajax call to the destination server and get courses.
         *
         * @param {Object} all the options to use jquery's $.ajax function (not moodle's)
         * @return {Promise}
         */
         getCourses: function (params) {
            var params = {
                'type': 'GET',
                'url': sessionStorage.getItem("currentUrl") + '/webservice/rest/server.php',
                'data': {
                    'wstoken': sessionStorage.getItem("currentToken"),
                    'wsfunction': 'core_course_search_courses',
                    "criterianame": 'search',
                    'criteriavalue': $('.xe_dest_sn_wrap input').val(),
                    'moodlewsrestformat': 'json'
                }
            };
            return XELib.jaxyRemotePromise(params);
         },

        /**
         * Process the results for auto complete elements. To keep the course id
         * and name they have been concatenated as the value.
         *
         * @param {String} selector The selector of the auto complete element.
         * @param {Array} results An array or results.
         * @return {Array} New array of results.
         */
        processResults: function(selector, results) {
            if ('success' in results && results.success == false) {
                if (results.msg == "Could not connect to the server.") {
                    // Don't show more than one notice, it's annoying.
                    Noti.callNoti({
                        message: results.msg,
                        type: 'error'
                    });
                    return;
                }
            } else if (results.courses.length > 0) {

                var options = [];
                $.each(results.courses, function(index, data) {
                    options.push({
                        value: data.id + '__' + data.shortname,
                        label: data.shortname
                    });
                });
                return options;
            }

            Noti.callNoti({
                message: "Sorry, can't seem to reach anything at the moment, please contact the Moodle Dev team.",
                type: 'error'
            });
        },

        /**
         * This is using a subclass of Moodle's autocomplete function in:
         * classes/form/groupform_autocomplete.php
         *
         * @param {String} selector The selector of the auto complete element.
         * @param {String} query The query string.
         * @param {Function} callback A callback function receiving an array of results.
         */
        /* eslint-disable promise/no-callback-in-promise */
        transport: function(selector, query, callback) {
            this.getCourses().then(callback).catch();
            // this calls processResults when done.
        }
    };

});
