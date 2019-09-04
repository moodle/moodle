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
 * A javascript module to handle course ajax actions.
 *
 * @module     core_course/repository
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function($, Ajax) {

    /**
     * Get the list of courses that the logged in user is enrolled in for a given
     * timeline classification.
     *
     * @param {string} classification past, inprogress, or future
     * @param {int} limit Only return this many results
     * @param {int} offset Skip this many results from the start of the result set
     * @param {string} sort Column to sort by and direction, e.g. 'shortname asc'
     * @return {object} jQuery promise resolved with courses.
     */
    var getEnrolledCoursesByTimelineClassification = function(classification, limit, offset, sort) {
        var args = {
            classification: classification
        };

        if (typeof limit !== 'undefined') {
            args.limit = limit;
        }

        if (typeof offset !== 'undefined') {
            args.offset = offset;
        }

        if (typeof sort !== 'undefined') {
            args.sort = sort;
        }

        var request = {
            methodname: 'core_course_get_enrolled_courses_by_timeline_classification',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get the list of courses that the user has most recently accessed.
     *
     * @method getLastAccessedCourses
     * @param {int} userid User from which the courses will be obtained
     * @param {int} limit Only return this many results
     * @param {int} offset Skip this many results from the start of the result set
     * @param {string} sort Column to sort by and direction, e.g. 'shortname asc'
     * @return {promise} Resolved with an array of courses
     */
    var getLastAccessedCourses = function(userid, limit, offset, sort) {
        var args = {};

        if (typeof userid !== 'undefined') {
            args.userid = userid;
        }

        if (typeof limit !== 'undefined') {
            args.limit = limit;
        }

        if (typeof offset !== 'undefined') {
            args.offset = offset;
        }

        if (typeof sort !== 'undefined') {
            args.sort = sort;
        }

        var request = {
            methodname: 'core_course_get_recent_courses',
            args: args
        };

        return Ajax.call([request])[0];
    };

    return {
        getEnrolledCoursesByTimelineClassification: getEnrolledCoursesByTimelineClassification,
        getLastAccessedCourses: getLastAccessedCourses
    };
});
