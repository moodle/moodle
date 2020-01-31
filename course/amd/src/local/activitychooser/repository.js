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
 *
 * @module     core_course/repository
 * @package    core_course
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ajax from 'core/ajax';

/**
 * Fetch all the information on modules we'll need in the activity chooser.
 *
 * @method activityModules
 * @param {Number} courseid What course to fetch the modules for
 * @return {object} jQuery promise
 */
export const activityModules = (courseid) => {
    const request = {
        methodname: 'core_course_get_course_content_items',
        args: {
            courseid: courseid,
        },
    };
    return ajax.call([request])[0];
};
