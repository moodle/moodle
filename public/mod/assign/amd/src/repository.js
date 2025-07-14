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
 * A repo for the search partial in the submissions page.
 *
 * @module    mod_assign/repository
 * @copyright 2024 Ilya Tregubov <ilyatregubov@proton.me>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';

/**
 * Given a course ID, we want to fetch the learners within this assignment.
 *
 * @method userFetch
 * @param {int} assignid ID of the assignment.
 * @param {int} groupid ID of the selected group.
 * @return {object} jQuery promise
 */
export const userFetch = (assignid, groupid) => {
    const request = {
        methodname: 'mod_assign_list_participants',
        args: {
            assignid: assignid,
            groupid: groupid,
            filter: '',
        },
    };
    return ajax.call([request])[0];
};
