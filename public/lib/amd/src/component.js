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
 * Helpers for mounting ESM/React components into the DOM from AMD code.
 *
 * Each helper creates the container `<div>` expected by the MutationObserver
 * auto-initialiser in `core/reactAutoinit`:
 *
 * ```html
 * <div
 *   data-react-component="@moodle/lms/mod_book/viewer"
 *   data-react-props='{"title":"My Book"}'
 * ></div>
 * ```
 *
 * The auto-initialiser picks up the element and mounts the matching React
 * component automatically — no further initialisation call is needed.
 *
 * @example <caption>Append a component after existing page content</caption>
 * import {appendToDom} from 'core/component';
 *
 * // Mounts the viewer at the bottom of #region-main.
 * appendToDom(
 *     '@moodle/lms/mod_book/viewer',
 *     {bookid: 42, title: 'My Book'},
 *     document.getElementById('region-main')
 * );
 *
 * @example <caption>Prepend a component above existing page content</caption>
 * import {prependToDom} from 'core/component';
 *
 * // Inserts a notice banner at the very top of #region-main.
 * prependToDom(
 *     '@moodle/lms/mod_book/notice',
 *     {message: 'Chapter 1 of 12', variant: 'info'},
 *     document.getElementById('region-main')
 * );
 *
 * @example <caption>Mount a component on demand inside an event handler</caption>
 * import {appendToDom} from 'core/component';
 *
 * document.getElementById('show-feedback').addEventListener('click', (e) => {
 *     const panel = document.getElementById('feedback-panel');
 *     panel.innerHTML = '';
 *     appendToDom(
 *         '@moodle/lms/mod_assign/feedback_panel',
 *         {assignid: e.target.dataset.assignid, userid: e.target.dataset.userid},
 *         panel
 *     );
 * });
 *
 * @example <caption>Combine with core/import to inspect a module before mounting</caption>
 * import nativeImport from 'core/import';
 * import {appendToDom} from 'core/component';
 *
 * const specifier = '@moodle/lms/mod_book/viewer';
 * const mod = await nativeImport(specifier).catch(() => null);
 *
 * if (mod?.default) {
 *     appendToDom(specifier, {bookid: 42}, document.getElementById('region-main'));
 * }
 *
 * @module     core/component
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Build and return the container element for a React component.
 *
 * @param {string} moduleName The ESM specifier, e.g. `@moodle/lms/mod_book/viewer`.
 * @param {object} properties Props to pass to the component (must be JSON-serialisable).
 * @returns {HTMLDivElement}
 */
const createContainer = (moduleName, properties) => {
    const container = document.createElement('div');
    container.dataset.reactComponent = moduleName;
    container.dataset.reactProps = JSON.stringify(properties);
    return container;
};

/**
 * Create the React component container and append it to the given element.
 *
 * @param {string} moduleName The ESM specifier, e.g. `@moodle/lms/mod_book/viewer`.
 * @param {object} properties Props to pass to the component (must be JSON-serialisable).
 * @param {Element} appendTo The element to append the container to.
 */
export const appendToDom = (moduleName, properties, appendTo) => {
    appendTo.append(createContainer(moduleName, properties));
};

/**
 * Create the React component container and prepend it to the given element.
 *
 * @param {string} moduleName The ESM specifier, e.g. `@moodle/lms/mod_book/viewer`.
 * @param {object} properties Props to pass to the component (must be JSON-serialisable).
 * @param {Element} prependTo The element to prepend the container to.
 */
export const prependToDom = (moduleName, properties, prependTo) => {
    prependTo.prepend(createContainer(moduleName, properties));
};
