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

import Ajax from 'core/ajax';

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
const getEnrolledCoursesByTimelineClassification = (classification, limit, offset, sort) => {
    const args = {
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

    const request = {
        methodname: 'core_course_get_enrolled_courses_by_timeline_classification',
        args: args
    };

    return Ajax.call([request])[0];
};

/**
 * Get a list of courses that the logged in user is enrolled in, where they have at least one action event,
 * for a given timeline classification.
 *
 * @param {string} classification past, inprogress, or future
 * @param {int} limit The maximum number of courses to return
 * @param {int} offset Skip this many results from the start of the result set
 * @param {string} sort Column to sort by and direction, e.g. 'shortname asc'
 * @param {string} searchValue Optional text search value
 * @param {int} eventsFrom Optional start timestamp (inclusive) that the course should have event(s) in
 * @param {int} eventsTo Optional end timestamp (inclusive) that the course should have event(s) in
 * @return {object} jQuery promise resolved with courses.
 */
 const getEnrolledCoursesWithEventsByTimelineClassification = (classification, limit = 0, offset = 0, sort = null,
        searchValue = null, eventsFrom = null, eventsTo = null) => {

    const args = {
        classification: classification,
        limit: limit,
        offset: offset,
        sort: sort,
        eventsfrom: eventsFrom,
        eventsto: eventsTo,
        searchvalue: searchValue,
    };

    const request = {
        methodname: 'core_course_get_enrolled_courses_with_action_events_by_timeline_classification',
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
const getLastAccessedCourses = (userid, limit, offset, sort) => {
    const args = {};

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

    const request = {
        methodname: 'core_course_get_recent_courses',
        args: args
    };

    return Ajax.call([request])[0];
};

/**
 * Get the list of users enrolled in this cmid.
 *
 * @param {Number} cmid Course Module from which the users will be obtained
 * @param {Number} groupID Group ID from which the users will be obtained
 * @returns {Promise} Promise containing a list of users
 */
const getEnrolledUsersFromCourseModuleID = (cmid, groupID) => {
    var request = {
        methodname: 'core_course_get_enrolled_users_by_cmid',
        args: {
            cmid: cmid,
            groupid: groupID,
        },
    };

    return Ajax.call([request])[0];
};

/**
 * Toggle the completion state of an activity with manual completion.
 *
 * @param {Number} cmid The course module ID.
 * @param {Boolean} completed Whether to set as complete or not.
 * @returns {object} jQuery promise
 */
const toggleManualCompletion = (cmid, completed) => {
    const request = {
        methodname: 'core_completion_update_activity_completion_status_manually',
        args: {
            cmid,
            completed,
        }
    };
    return Ajax.call([request])[0];
};

export default {
    getEnrolledCoursesByTimelineClassification,
    getLastAccessedCourses,
    getUsersFromCourseModuleID: getEnrolledUsersFromCourseModuleID,
    toggleManualCompletion,
    getEnrolledCoursesWithEventsByTimelineClassification,
};
