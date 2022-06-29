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
 * Module to handle filter AJAX requests
 *
 * @module      core_reportbuilder/local/repository/filters
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Reset all filters for given report
 *
 * @method
 * @param {Number} reportId
 * @return {Promise}
 */
export const resetFilters = reportId => {
    const request = {
        methodname: 'core_reportbuilder_filters_reset',
        args: {reportid: reportId}
    };

    return Ajax.call([request])[0];
};

/**
 * Add a filter to the given report
 *
 * @param {Number} reportId
 * @param {String} uniqueIdentifier
 * @return {Promise}
 */
export const addFilter = (reportId, uniqueIdentifier) => {
    const request = {
        methodname: 'core_reportbuilder_filters_add',
        args: {reportid: reportId, uniqueidentifier: uniqueIdentifier}
    };

    return Ajax.call([request])[0];
};

/**
 * Remove filter from given report
 *
 * @param {Number} reportId
 * @param {Number} filterId
 * @return {Promise}
 */
export const deleteFilter = (reportId, filterId) => {
    const request = {
        methodname: 'core_reportbuilder_filters_delete',
        args: {reportid: reportId, filterid: filterId}
    };

    return Ajax.call([request])[0];
};

/**
 * Reorder a filter in a given report
 *
 * @param {Number} reportId
 * @param {Number} filterId
 * @param {Number} position
 * @return {Promise}
 */
export const reorderFilter = (reportId, filterId, position) => {
    const request = {
        methodname: 'core_reportbuilder_filters_reorder',
        args: {reportid: reportId, filterid: filterId, position: position}
    };

    return Ajax.call([request])[0];
};
