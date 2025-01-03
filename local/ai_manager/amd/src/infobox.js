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
 * Module handling the form submission of the statistics tables of local_ai_manager.
 *
 * @module     local_ai_manager/infobox
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Templates from 'core/templates';
import * as config from 'core/config';

/**
 * Inserts the infobox into the beginning of element with the given selector.
 *
 * Also triggers a confirmation modal the first time it is being rendered by a component.
 *
 * @param {string} component The component name from which this is being called
 * @param {int} userId id of the user
 * @param {string} selector the id of the element to insert the infobox
 * @param {string[]} purposes the purposes which are being used
 */
export const renderInfoBox = async(component, userId, selector, purposes = []) => {
    const targetElement = document.querySelector(selector);
    const aiInfoUrl = new URL(config.wwwroot + '/local/ai_manager/ai_info.php');
    purposes.forEach(purpose => {
        aiInfoUrl.searchParams.append('purposes[]', purpose);
    });
    const templateContext = {
        'aiinfourl': aiInfoUrl
    };
    const {html, js} = await Templates.renderForPromise('local_ai_manager/infobox', templateContext);
    Templates.prependNodeContents(targetElement, html, js);
};
