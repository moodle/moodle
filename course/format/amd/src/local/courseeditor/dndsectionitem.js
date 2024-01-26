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
 * Course index section title draggable component.
 *
 * This component is used to control specific course section interactions like drag and drop
 * in both course index and course content.
 *
 * @module     core_courseformat/local/courseeditor/dndsectionitem
 * @class      core_courseformat/local/courseeditor/dndsectionitem
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent, DragDrop} from 'core/reactive';

export default class extends BaseComponent {

    /**
     * Initial state ready method.
     *
     * @param {number} sectionid the section id
     * @param {Object} state the initial state
     * @param {Element} fullregion the complete section region to mark as dragged
     */
    configDragDrop(sectionid, state, fullregion) {

        this.id = sectionid;
        if (this.section === undefined) {
            this.section = state.section.get(this.id);
        }
        if (this.course === undefined) {
            this.course = state.course;
        }

        // Prevent topic zero from being draggable.
        if (this.section.number > 0) {
            this.getDraggableData = this._getDraggableData;
        }

        this.fullregion = fullregion;

        // Drag and drop is only available for components compatible course formats.
        if (this.reactive.isEditing && this.reactive.supportComponents) {
            // Init the dropzone.
            this.dragdrop = new DragDrop(this);
            // Save dropzone classes.
            this.classes = this.dragdrop.getClasses();
        }
    }

    /**
     * Remove all subcomponents dependencies.
     */
    destroy() {
        if (this.dragdrop !== undefined) {
            this.dragdrop.unregister();
        }
    }

    /**
     * Enable or disable the draggable property.
     *
     * @param {bool} value the new draggable value
     */
    setDraggable(value) {
        if (this.getDraggableData) {
            this.dragdrop?.setDraggable(value);
        }
    }

    // Drag and drop methods.

    /**
     * The element drop start hook.
     *
     * @param {Object} dropdata the dropdata
     */
    dragStart(dropdata) {
        this.reactive.dispatch('sectionDrag', [dropdata.id], true);
    }

    /**
     * The element end start hook.
     *
     * @param {Object} dropdata the dropdata
     */
    dragEnd(dropdata) {
        this.reactive.dispatch('sectionDrag', [dropdata.id], false);
    }

    /**
     * Get the draggable data of this component.
     *
     * @returns {Object} exported course module drop data
     */
    _getDraggableData() {
        const exporter = this.reactive.getExporter();
        return exporter.sectionDraggableData(this.reactive.state, this.id);
    }

    /**
     * Validate if the drop data can be dropped over the component.
     *
     * @param {Object} dropdata the exported drop data.
     * @returns {boolean}
     */
    validateDropData(dropdata) {
        // Course module validation.
        if (dropdata?.type === 'cm') {
            // Prevent content loops with subsections.
            if (this.section?.component && dropdata?.delegatesection === true) {
                return false;
            }
            // The first section element is already there so we can ignore it.
            const firstcmid = this.section?.cmlist[0];
            return dropdata.id !== firstcmid;
        }
        return false;
    }

    /**
     * Display the component dropzone.
     */
    showDropZone() {
        this.element.classList.add(this.classes.DROPZONE);
    }

    /**
     * Hide the component dropzone.
     */
    hideDropZone() {
        this.element.classList.remove(this.classes.DROPZONE);
    }

    /**
     * Drop event handler.
     *
     * @param {Object} dropdata the accepted drop data
     * @param {Event} event the drop event
     */
    drop(dropdata, event) {
        // Call the move mutation.
        if (dropdata.type == 'cm') {
            const mutation = (event.altKey) ? 'cmDuplicate' : 'cmMove';
            this.reactive.dispatch(mutation, [dropdata.id], this.id, this.section?.cmlist[0]);
        }
    }
}
