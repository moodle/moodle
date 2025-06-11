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
 * Check API result functions
 *
 * @module core/check
 * @author Matthew Hilton <matthewhilton@catalyst-au.net>
 * @copyright Catalyst IT, 2023
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getCheckResult} from './repository';
import {getString} from 'core/str';
import * as Templates from 'core/templates';

/**
 * Get the result of a check and replace a given DOM element with the result.
 *
 * @method getAndRender
 * @param {String} domSelector A CSS selector for a dom element to replace the the HTML for.
 * @param {String} adminTreeId Id of the admin_setting that called this webservice. Used to retrieve the check registered to it.
 * @param {String} settingName Name of setting (used to find the parent node in the admin tree)
 * @param {Boolean} includeDetails If true, details will be included in the check.
 * By default only the status and the summary is returned.
 */
export async function getAndRender(domSelector, adminTreeId, settingName, includeDetails) {
    const element = document.querySelector(domSelector);

    if (!element) {
        window.console.error('Check selector not found');
        return;
    }

    try {
        const result = await getCheckResult(adminTreeId, settingName, includeDetails);
        const decoded = new DOMParser().parseFromString(result.html, "text/html").documentElement.textContent;
        element.innerHTML = decoded;
    } catch (e) {
        window.console.error(e);

        // Render error as a red notification.
        element.innerHTML = await Templates.render('core/notification', {
            iserror: true,
            closebutton: false,
            announce: 0,
            extraclasses: '',
            message: await getString('checkerror', 'core', adminTreeId)
        });
    }
}
