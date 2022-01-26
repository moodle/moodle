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
 * Javascript module to control the template editor.
 *
 * @module      mod_data/templateseditor
 * @copyright   2021 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import {confirm as confirmDialogue} from 'core/notification';
import {relativeUrl} from 'core/url';

/**
 * Template editor constants.
 */
const selectors = {
    toggleTemplateEditor: 'input[name="useeditor"]',
};

/**
 * Register event listeners for the module.
 *
 * @param {int} d The database ID
 * @param {string} mode The template mode
 */
const registerEventListeners = (d, mode) => {
    const toggleTemplateEditor = document.querySelector(selectors.toggleTemplateEditor);

    toggleTemplateEditor.addEventListener('click', async(event) => {
        event.preventDefault();
        // Whether the event action attempts to enable or disable the template editor.
        const enableTemplateEditor = event.target.checked;

        if (enableTemplateEditor) {
            // Display a confirmation dialog before enabling the template editor.
            confirmDialogue(
                getString('confirmation', 'admin'),
                getString('enabletemplateeditorcheck', 'mod_data'),
                getString('yes', 'core'),
                getString('no', 'core'),
                () => {
                    window.location = relativeUrl('/mod/data/templates.php', {d: d, mode: mode, useeditor: true});
                }
            );
        } else {
            window.location = relativeUrl('/mod/data/templates.php', {d: d, mode: mode, useeditor: false});
        }
    });
};

/**
 * Initialize the module.
 *
 * @param {int} d The database ID
 * @param {string} mode The template mode
 */
export const init = (d, mode) => {
    registerEventListeners(d, mode);
};
