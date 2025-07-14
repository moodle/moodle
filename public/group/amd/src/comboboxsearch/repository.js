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
 * A repo for the comboboxsearch group type.
 *
 * @module    core_group/comboboxsearch/repository
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from "core/ajax";

/**
 * Given a course ID and optionally a module ID, we want to fetch the groups, so we may fetch their users.
 *
 * @method groupFetch
 * @param {int} courseid ID of the course to fetch the groups of.
 * @param {int|null} cmid ID of the course module initiating the group search (optional).
 * @return {object} jQuery promise
 */
export const groupFetch = (courseid, cmid = null) => {
    const request = {
        methodname: 'core_group_get_groups_for_selector',
        args: {
            courseid: courseid,
            cmid: cmid,
        },
    };
    return ajax.call([request])[0];
};
