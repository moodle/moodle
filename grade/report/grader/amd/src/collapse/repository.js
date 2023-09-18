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
 * A repo for the collapsing in the grader report.
 *
 * @module    gradereport_grader/collapse/repository
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';

/**
 * Fetch all the information on gradeitems we'll need in the column collapser.
 *
 * @method gradeItems
 * @param {Number} courseid What course to fetch the gradeitems for
 * @return {object} jQuery promise
 */
export const gradeItems = (courseid) => {
    const request = {
        methodname: 'core_grades_get_gradeitems',
        args: {
            courseid: courseid,
        },
    };
    return ajax.call([request])[0];
};
