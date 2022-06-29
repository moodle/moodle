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
 * Course section format component.
 *
 * @module     core_courseformat/local/content/section
 * @class      core_courseformat/local/content/section
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Header from 'core_courseformat/local/content/section/header';
import DndSection from 'core_courseformat/local/courseeditor/dndsection';

export default class extends DndSection {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'content_section';
        // Default query selectors.
        this.selectors = {
            SECTION_ITEM: `[data-for='section_title']`,
            CM: `[data-for="cmitem"]`,
            SECTIONINFO: `[data-for="sectioninfo"]`,
        };
        // Most classes will be loaded later by DndCmItem.
        this.classes = {
            LOCKED: 'editinprogress',
            HASDESCRIPTION: 'description',
        };

        // We need our id to watch specific events.
        this.id = this.element.dataset.id;
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the initial state
     */
    stateReady(state) {
        this.configState(state);
        // Drag and drop is only available for components compatible course formats.
        if (this.reactive.isEditing && this.reactive.supportComponents) {
            // Section zero and other formats sections may not have a title to drag.
            const sectionItem = this.getElement(this.selectors.SECTION_ITEM);
            if (sectionItem) {
                // Init the inner dragable element.
                const headerComponent = new Header({
                    ...this,
                    element: sectionItem,
                    fullregion: this.element,
                });
                this.configDragDrop(headerComponent);
            }
        }
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `section[${this.id}]:updated`, handler: this._refreshSection},
        ];
    }

    /**
     * Validate if the drop data can be dropped over the component.
     *
     * @param {Object} dropdata the exported drop data.
     * @returns {boolean}
     */
    validateDropData(dropdata) {
        // If the format uses one section per page sections dropping in the content is ignored.
       if (dropdata?.type === 'section' && this.reactive.sectionReturn != 0) {
            return false;
        }
        return super.validateDropData(dropdata);
    }

    /**
     * Get the last CM element of that section.
     *
     * @returns {element|null}
     */
    getLastCm() {
        const cms = this.getElements(this.selectors.CM);
        // DndUpload may add extra elements so :last-child selector cannot be used.
        if (!cms || cms.length === 0) {
            return null;
        }
        return cms[cms.length - 1];
    }

    /**
     * Update a course index section using the state information.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSection({element}) {
        // Update classes.
        this.element.classList.toggle(this.classes.DRAGGING, element.dragging ?? false);
        this.element.classList.toggle(this.classes.LOCKED, element.locked ?? false);
        this.locked = element.locked;
        // The description box classes depends on the section state.
        const sectioninfo = this.getElement(this.selectors.SECTIONINFO);
        if (sectioninfo) {
            sectioninfo.classList.toggle(this.classes.HASDESCRIPTION, element.hasrestrictions);
        }
    }
}
