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
 * Error handling and normalisation of provided data.
 *
 * @module     core_grades/grades/grader/gradingpanel/normalise
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Normalise a resultset for consumption by the grader.
 *
 * @param {Object} result The result returned from a grading web service
 * @return {Object}
 */
export const normaliseResult = result => {
    return {
        result,
        failed: !!result.warnings.length,
        success: !result.warnings.length,
        error: null,
    };
};

/**
 * Return the resultset used to describe an invalid result.
 *
 * @return {Object}
 */
export const invalidResult = () => {
    return {
        success: false,
        failed: false,
        result: {},
        error: null,
    };
};

/**
 * Return the resultset used to describe a failed update.
 *
 * @param {Object} error
 * @return {Object}
 */
export const failedUpdate = error => {
    return {
        success: false,
        failed: true,
        result: {},
        error,
    };
};
