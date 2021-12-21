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
 * Wrapper to ensure that all Hugo example snippets have a "Copy to clipboard" button.
 *
 * @module     tool_componentlibrary/clipboardwrapper
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import 'core/copy_to_clipboard';
import selectors from 'tool_componentlibrary/selectors';
import Templates from 'core/templates';

let idCounter = 0;

/**
 * Initialise the clipboard button on all reusable code.
 *
 * @method
 */
export const clipboardWrapper = async() => {
    document.querySelectorAll(selectors.clipboardcontent).forEach(element => {
        if (!element.id) {
            element.id = `tool_componentlibrary_content-${idCounter++}`;
        }
        Templates.renderForPromise('tool_componentlibrary/clipboardbutton', {clipboardtarget: `#${element.id} code`})
        .then(({html, js}) => {
            Templates.prependNodeContents(element, html, js);
            return;
        })
        .catch();
    });
};
