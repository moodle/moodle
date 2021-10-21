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
 * Module to handle column AJAX requests
 *
 * @module      core_reportbuilder/local/repository/columns
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Add column to given report
 *
 * @param {Number} reportId
 * @param {String} uniqueIdentifier
 * @return {Promise}
 */
export const addColumn = (reportId, uniqueIdentifier) => {
    const request = {
        methodname: 'core_reportbuilder_columns_add',
        args: {reportid: reportId, uniqueidentifier: uniqueIdentifier}
    };

    return Ajax.call([request])[0];
};

/**
 * Remove column from given report
 *
 * @param {Number} reportId
 * @param {Number} columnId
 * @return {Promise}
 */
export const deleteColumn = (reportId, columnId) => {
    const request = {
        methodname: 'core_reportbuilder_columns_delete',
        args: {reportid: reportId, columnid: columnId}
    };

    return Ajax.call([request])[0];
};

/**
 * Re-order column within a report
 *
 * @param {Number} reportId
 * @param {Number} columnId
 * @param {Number} position
 * @return {Promise}
 */
export const reorderColumn = (reportId, columnId, position) => {
    const request = {
        methodname: 'core_reportbuilder_columns_reorder',
        args: {reportid: reportId, columnid: columnId, position: position}
    };

    return Ajax.call([request])[0];
};
