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
 * A repo for the search partial in the grader report.
 *
 * @module    gradereport_grader/search/repository
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';

/**
 * Given a course ID, we want to fetch the learners within this report.
 *
 * @method userFetch
 * @param {int} courseid ID of the course to fetch the users of.
 * @return {object} jQuery promise
 */
export const userFetch = (courseid) => {
    const request = {
        methodname: 'gradereport_grader_get_users_in_report',
        args: {
            courseid: courseid,
        },
    };
    return ajax.call([request])[0];
};
