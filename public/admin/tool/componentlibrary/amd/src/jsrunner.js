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
 * Run the JS required for example code to work in the library.
 *
 * @module     tool_componentlibrary/jsrunner
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import selectors from 'tool_componentlibrary/selectors';

/**
 * The Hugo shortcodes changes the JavaScript in markdownfiles from
 * the Moodle mustache {{js}} code... {{/js}} syntax into a div with
 * attribute data-action='runjs'. See hugo/site/layouts/shortcodes/example.html.
 * This code fetches and runs the JavaScript content.
 *
 * @method
 */
export const jsRunner = () => {
    const compLib = document.querySelector(selectors.componentlibrary);
    compLib.querySelectorAll(selectors.jscode).forEach(runjs => {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.innerHTML = runjs.textContent;
        document.head.appendChild(script);
    });
};
