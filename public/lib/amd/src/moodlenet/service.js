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
 * A javascript module to handle MoodleNet ajax actions.
 *
 * @module     core/moodlenet/service
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.2
 */

import Ajax from 'core/ajax';

/**
 * Get the activity information by course module id.
 *
 * @param {Integer} cmId The course module id.
 * @return {promise}
 */
export const getActivityInformation = (cmId) => {
    const request = {
        methodname: 'core_moodlenet_get_share_info_activity',
        args: {
            cmid: cmId
        }
    };

    return Ajax.call([request])[0];
};


/**
 * Get the course information by course module id.
 *
 * @param {Integer} courseID The course id.
 * @return {promise}
 */
export const getCourseInformation = (courseID) => {
    const request = {
        methodname: 'core_moodlenet_get_shared_course_info',
        args: {
            courseid: courseID
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Send the course to MoodleNet.
 *
 * @param {Integer} issuerId The OAuth 2 issuer ID.
 * @param {Integer} courseId The course ID.
 * @param {Integer} shareFormat The share format.
 * @return {promise}
 */
export const sendCourse = (issuerId, courseId, shareFormat) => {
    const request = {
        methodname: 'core_moodlenet_send_course',
        args: {
            issuerid: issuerId,
            courseid: courseId,
            shareformat: shareFormat,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Send the activity to Moodlenet.
 *
 * @param {Integer} issuerId The OAuth 2 issuer ID.
 * @param {Integer} cmId The course module ID.
 * @param {Integer} shareFormat The share format.
 * @return {promise}
 */
export const sendActivity = (issuerId, cmId, shareFormat) => {
    const request = {
        methodname: 'core_moodlenet_send_activity',
        args: {
            issuerid: issuerId,
            cmid: cmId,
            shareformat: shareFormat,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Send the selected activities in a course to MoodleNet.
 *
 * @param {Integer} issuerId The OAuth 2 issuer ID.
 * @param {Integer} courseId The course ID.
 * @param {array} selectedCmIds Course module IDs in the course.
 * @param {Integer} shareFormat The share format.
 * @return {promise}
 */
export const sendPartialCourse = (issuerId, courseId, selectedCmIds, shareFormat) => {
    const request = {
        methodname: 'core_moodlenet_send_course',
        args: {
            issuerid: issuerId,
            courseid: courseId,
            shareformat: shareFormat,
            cmids: selectedCmIds,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Check if the user is already authorized with MoodleNet.
 *
 * @param {Integer} issuerId The OAuth 2 issuer ID.
 * @param {Integer} courseId The course ID.
 * @return {promise}
 */
export const authorizationCheck = (issuerId, courseId) => {
    const request = {
        methodname: 'core_moodlenet_auth_check',
        args: {
            issuerid: issuerId,
            courseid: courseId,
        }
    };

    return Ajax.call([request])[0];
};
