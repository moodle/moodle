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
 * Module to handle toggle shared category AJAX requests
 *
 * @module      core_customfield/repository/toggle_shared
 * @copyright   2025 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Toggle shared category enabled
 *
 * @method
 * @param {Number} categoryId
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 * @param {Boolean} state
 * @return {Promise}
 */
export const toggleCategory = (categoryId, component, area, itemid, state) => {
    const request = {
        methodname: 'core_customfield_toggle_shared',
        args: {categoryid: categoryId, component: component, area: area, itemid: itemid, state: state}
    };

    return Ajax.call([request])[0];
};
