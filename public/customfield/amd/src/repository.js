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
 * Module to handle custom fields AJAX requests.
 *
 * @module      core_customfield/repository
 * @copyright   2026 Yerai Rodríguez <yerai.rodriguez@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import Ajax from 'core/ajax';

/**
 * Convert an entity custom field category to a shared category.
 *
 * @param {Number} categoryId
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 * @return {Promise}
 */
export const convertCategory = (categoryId, component, area, itemid) => {
    const request = {
        methodname: 'core_customfield_convert_category',
        args: {categoryid: categoryId, component: component, area: area, itemid: itemid}
    };

    return Ajax.call([request])[0];
};

/**
 * Reload custom fields list.
 *
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 * @return {Promise}
 */
export const reloadTemplate = (component, area, itemid) => {
    const request = {
        methodname: 'core_customfield_reload_template',
        args: {component: component, area: area, itemid: itemid}
    };

    return Ajax.call([request])[0];
};

/**
 * Toggle shared category enabled.
 *
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
