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
 * Check API webservice repository
 *
 * @module core/check
 * @author Matthew Hilton <matthewhilton@catalyst-au.net>
 * @copyright Catalyst IT, 2023
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

/**
 * Call check_get_result webservice function
 *
 * @param {String} adminTreeId Id of the admin_setting that called this webservice. Used to retrieve the check registered to it.
 * @param {String} settingName Setting name (used to find it's parent)
 * @param {Boolean} includeDetails If details should be included in the response
 */
export const getCheckResult = (adminTreeId, settingName, includeDetails) => fetchMany([{
    methodname: 'core_check_get_result_admintree',
    args: {
        admintreeid: adminTreeId,
        settingname: settingName,
        includedetails: includeDetails,
    },
}])[0];

