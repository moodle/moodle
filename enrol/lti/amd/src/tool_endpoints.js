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
 * Module supporting the dynamic and manual registration URLs in the tool registration admin setting.
 *
 * @module     enrol_lti/tool_endpoints
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import 'core/copy_to_clipboard';

/**
 * DOM Selectors.
 * @type {{URL_VALUE: string}}
 */
const SELECTORS = {
    URL_VALUE: '[id^="lti_tool_endpoint_url_"]',
};

/**
 * Focus handler for the registration URL field, enabling auto select of text on click.
 *
 * @param {Event} event a click event.
 */
const focusURLHandler = (event) => {
    const triggerElement = event.target.closest(SELECTORS.URL_VALUE);
    if (triggerElement === null) {
        return;
    }
    event.preventDefault();

    triggerElement.select();
};

/**
 * Initialise the tool registration page, attaching handlers, etc.
 */
export const init = () => {
    // Event delegation supporting the select on focus behaviour (with text selection permitted on subsequent clicks).
    document.addEventListener('focusin', focusURLHandler);
};
