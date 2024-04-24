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
 * A repo for the search user in the report.
 *
 * @module    core/searchwidget/repository
 * @copyright 2024 The Open University.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';

/**
 * Given params, we want to fetch the students within report.
 *
 * @method userFetch
 * @param {object} params ID of the course to fetch the users of.
 * @param {string} serviceName Service name for each request.
 * @return {object} jQuery promise
 */
export const userFetch = (params, serviceName) => {
    const request = {
        methodname: serviceName,
        args: params,
    };
    return ajax.call([request])[0];
};
