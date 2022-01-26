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
 * Module to handle column sorting AJAX requests
 *
 * @module      core_reportbuilder/local/repository/sorting
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Retrieve column sorting
 *
 * @param {Number} reportId
 * @return {Promise}
 */
export const getColumnSorting = reportId => {
    const request = {
        methodname: 'core_reportbuilder_columns_sort_get',
        args: {reportid: reportId}
    };

    return Ajax.call([request])[0];
};

/**
 * Re-order sort column position
 *
 * @param {Number} reportId
 * @param {Number} columnId
 * @param {Number} position
 * @return {Promise}
 */
export const reorderColumnSorting = (reportId, columnId, position) => {
    const request = {
        methodname: 'core_reportbuilder_columns_sort_reorder',
        args: {reportid: reportId, columnid: columnId, position: position}
    };

    return Ajax.call([request])[0];
};

/**
 * Enables/disabled sorting on column
 *
 * @param {Number} reportId
 * @param {Number} columnId
 * @param {Boolean} enabled
 * @param {Number} direction
 * @return {Promise}
 */
export const toggleColumnSorting = (reportId, columnId, enabled, direction) => {
    const request = {
        methodname: 'core_reportbuilder_columns_sort_toggle',
        args: {reportid: reportId, columnid: columnId, enabled: enabled, direction: direction}
    };

    return Ajax.call([request])[0];
};
