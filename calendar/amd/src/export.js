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
 * A javascript module to enhance the calendar export form.
 *
 * @module     core_calendar/export
 * @copyright  2021 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import 'core/copy_to_clipboard';

/**
 * Selectors for the calendar export page.
 *
 * @property {string} copyUrlId The element ID of the Copy URL button.
 */
const selectors = {
    copyUrlId: 'copyexporturl',
};

/**
 * Initialises the calendar export JS module.
 *
 * @method init
 */
export const init = () => {
    // Enable the copy URL button and focus on it.
    const copyUrl = document.getElementById(selectors.copyUrlId);
    copyUrl.removeAttribute('disabled');
    copyUrl.focus();
};
