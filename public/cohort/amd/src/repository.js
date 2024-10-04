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
 * Module to handle cohort AJAX requests.
 *
 * @module      core_cohort/repository
 * @copyright   2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Delete single cohort.
 *
 * @param {Number} cohortid
 * @return {Promise}
 */
export const deleteCohort = cohortid => deleteCohorts([cohortid]);

/**
 * Delete multiple cohorts.
 *
 * @param {Number[]} cohortids
 * @return {Promise}
 */
export const deleteCohorts = cohortids => {
    const request = {
        methodname: 'core_cohort_delete_cohorts',
        args: {cohortids},
    };

    return Ajax.call([request])[0];
};
