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
 * Course index placeholder replacer.
 *
 * @module     core_courseformat/local/courseindex/placeholder
 * @class      core_courseformat/local/courseindex/placeholder
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import Templates from 'core/templates';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';

export default class Component extends BaseComponent {

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {element|string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new Component({
            element: document.getElementById(target),
            reactive: getCurrentCourseEditor(),
            selectors,
        });
    }

    /**
     * Initial state ready method.
     *
     * This stateReady to be async because it loads the real courseindex.
     *
     * @param {object} state the initial state
     */
    async stateReady(state) {
        // Collect section information from the state.
        const exporter = this.reactive.getExporter();
        const data = exporter.course(state);

        try {
            // To render an HTML into our component we just use the regular Templates module.
            const {html, js} = await Templates.renderForPromise(
                'core_courseformat/local/courseindex/courseindex',
                data,
            );
            Templates.replaceNode(this.element, html, js);
        } catch (error) {
            throw error;
        }
    }
}
