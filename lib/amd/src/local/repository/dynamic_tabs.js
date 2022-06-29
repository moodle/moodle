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
 * Module to handle dynamic tabs AJAX requests
 *
 * @module      core/local/repository/dynamic_tabs
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Return tab content
 *
 * @param {String} tab
 * @param {String} jsondata
 * @return {Promise}
 */
export const getContent = (tab, jsondata) => {
    const request = {
        methodname: 'core_dynamic_tabs_get_content',
        args: {tab: tab, jsondata: jsondata}
    };

    return Ajax.call([request])[0];
};
