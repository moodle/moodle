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
 * Course index section title component.
 *
 * This component is used to control specific course section interactions like drag and drop.
 *
 * @module     core_courseformat/local/courseindex/sectiontitle
 * @class      core_courseformat/local/courseindex/sectiontitle
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DndSectionItem from 'core_courseformat/local/courseeditor/dndsectionitem';
import log from "core/log";

export default class Component extends DndSectionItem {

    /**
     * Constructor hook.
     *
     * @param {Object} descriptor
     */
    create(descriptor) {
        // Optional component name for debugging.
        this.name = 'courseindex_sectiontitle';

        this.id = descriptor.id;
        this.section = descriptor.section;
        this.course = descriptor.course;
        this.fullregion = descriptor.fullregion;

        // Prevent topic zero and delegated sections from being draggable.
        if (this.section.number > 0 && this.section.component === null) {
            this.getDraggableData = this._getDraggableData;
        }
    }

    /**
     * Static method to create a component instance form the mustahce template.
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
            selectors,
        });
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the initial state
     */
    stateReady(state) {
        this.configDragDrop(this.id, state, this.fullregion);
    }
}
