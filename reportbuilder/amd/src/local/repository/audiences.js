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
 * Module to handle audiences AJAX requests
 *
 * @module      core_reportbuilder/local/repository/audiences
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Remove audience from given report
 *
 * @param {Number} reportId
 * @param {Number} instanceId
 * @return {Promise}
 */
export const deleteAudience = (reportId, instanceId) => {
    const request = {
        methodname: 'core_reportbuilder_audiences_delete',
        args: {reportid: reportId, instanceid: instanceId}
    };

    return Ajax.call([request])[0];
};
