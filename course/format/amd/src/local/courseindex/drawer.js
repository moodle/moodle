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
 * Course index drawer wrap.
 *
 * This component is mostly used to ensure all subcomponents find a parent
 * compoment with a reactive instance defined.
 *
 * @module     core_courseformat/local/courseindex/drawer
 * @class     core_courseformat/local/courseindex/drawer
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import log from "core/log";

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'courseindex-drawer';
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {element|string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        let element = document.querySelector(target);
        // TODO Remove this if condition as part of MDL-83851.
        if (!element) {
            log.debug('Init component with id is deprecated, use a query selector instead.');
            element = document.getElementById(target);
        }
        return new this({
            element,
            reactive: getCurrentCourseEditor(),
            selectors,
        });
    }
}
