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
 * Repository for simple direct grading panel.
 *
 * @module     core_grades/grades/grader/gradingpanel/repository
 * @package    core_grades
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {call as fetchMany} from 'core/ajax';
import {normaliseResult} from './normalise';

export const fetchGrade = type => (component, contextid, itemname, gradeduserid) => {
    return fetchMany([{
        methodname: `core_grades_grader_gradingpanel_${type}_fetch`,
        args: {
            component,
            contextid,
            itemname,
            gradeduserid,
        },
    }])[0];
};

export const saveGrade = type => async(component, contextid, itemname, gradeduserid, notifyUser, formdata) => {
    return normaliseResult(await fetchMany([{
        methodname: `core_grades_grader_gradingpanel_${type}_store`,
        args: {
            component,
            contextid,
            itemname,
            gradeduserid,
            notifyuser: notifyUser,
            formdata,
        },
    }])[0]);
};
