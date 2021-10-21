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
 * Module to handle condition AJAX requests
 *
 * @module      core_reportbuilder/local/repository/conditions
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Reset all conditions for given report
 *
 * @param {Number} reportId
 * @return {Promise}
 */
export const resetConditions = reportId => {
    const request = {
        methodname: 'core_reportbuilder_conditions_reset',
        args: {reportid: reportId}
    };

    return Ajax.call([request])[0];
};

/**
 * Add condition to given report
 *
 * @param {Number} reportId
 * @param {String} uniqueIdentifier
 * @return {Promise}
 */
export const addCondition = (reportId, uniqueIdentifier) => {
    const request = {
        methodname: 'core_reportbuilder_conditions_add',
        args: {reportid: reportId, uniqueidentifier: uniqueIdentifier}
    };

    return Ajax.call([request])[0];
};

/**
 * Remove condition from given report
 *
 * @param {Number} reportId
 * @param {Number} conditionId
 * @return {Promise}
 */
export const deleteCondition = (reportId, conditionId) => {
    const request = {
        methodname: 'core_reportbuilder_conditions_delete',
        args: {reportid: reportId, conditionid: conditionId}
    };

    return Ajax.call([request])[0];
};

/**
 * Reorder a condition in a given report
 *
 * @param {Number} reportId
 * @param {Number} conditionId
 * @param {Number} position
 * @return {Promise}
 */
export const reorderCondition = (reportId, conditionId, position) => {
    const request = {
        methodname: 'core_reportbuilder_conditions_reorder',
        args: {reportid: reportId, conditionid: conditionId, position: position}
    };

    return Ajax.call([request])[0];
};
