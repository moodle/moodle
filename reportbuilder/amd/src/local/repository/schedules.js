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
 * Module to handle schedule AJAX requests
 *
 * @module      core_reportbuilder/local/repository/schedules
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Delete schedule
 *
 * @method
 * @param {Number} reportId
 * @param {Number} scheduleId
 * @return {Promise}
 */
export const deleteSchedule = (reportId, scheduleId) => {
    const request = {
        methodname: 'core_reportbuilder_schedules_delete',
        args: {reportid: reportId, scheduleid: scheduleId}
    };

    return Ajax.call([request])[0];
};

/**
 * Send schedule
 *
 * @method
 * @param {Number} reportId
 * @param {Number} scheduleId
 * @return {Promise}
 */
export const sendSchedule = (reportId, scheduleId) => {
    const request = {
        methodname: 'core_reportbuilder_schedules_send',
        args: {reportid: reportId, scheduleid: scheduleId}
    };

    return Ajax.call([request])[0];
};

/**
 * Toggle schedule enabled
 *
 * @method
 * @param {Number} reportId
 * @param {Number} scheduleId
 * @param {Boolean} scheduleEnabled
 * @return {Promise}
 */
export const toggleSchedule = (reportId, scheduleId, scheduleEnabled) => {
    const request = {
        methodname: 'core_reportbuilder_schedules_toggle',
        args: {reportid: reportId, scheduleid: scheduleId, enabled: scheduleEnabled}
    };

    return Ajax.call([request])[0];
};
