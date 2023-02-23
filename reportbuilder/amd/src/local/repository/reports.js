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
 * Module to handle report AJAX requests
 *
 * @module      core_reportbuilder/local/repository/reports
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Delete given report
 *
 * @param {Number} reportId
 * @return {Promise}
 */
export const deleteReport = reportId => {
    const request = {
        methodname: 'core_reportbuilder_reports_delete',
        args: {reportid: reportId}
    };

    return Ajax.call([request])[0];
};

/**
 * Get report content
 *
 * @param {Number} reportId
 * @param {Boolean} editMode
 * @param {Number} [pageSize=0]
 * @return {Promise}
 */
export const getReport = (reportId, editMode, pageSize = 0) => {
    const request = {
        methodname: 'core_reportbuilder_reports_get',
        args: {reportid: reportId, editmode: editMode, pagesize: pageSize}
    };

    return Ajax.call([request])[0];
};
