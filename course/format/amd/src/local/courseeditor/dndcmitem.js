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
 * Course index cm component.
 *
 * This component is used to control specific course modules interactions like drag and drop
 * in both course index and course content.
 *
 * @module     core_courseformat/local/courseeditor/dndcmitem
 * @class      core_courseformat/local/courseeditor/dndcmitem
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent, DragDrop} from 'core/reactive';

export default class extends BaseComponent {

    /**
     * Configure the component drag and drop.
     *
     * @param {number} cmid course module id
     */
    configDragDrop(cmid) {

        this.id = cmid;

        // Drag and drop is only available for components compatible course formats.
        if (this.reactive.isEditing && this.reactive.supportComponents) {
            // Init element drag and drop.
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
        this.dragdrop?.setDraggable(value);
    }

    // Drag and drop methods.

    /**
     * The element drop start hook.
     *
     * @param {Object} dropdata the dropdata
     */
    dragStart(dropdata) {
        this.reactive.dispatch('cmDrag', [dropdata.id], true);
    }

    /**
     * The element drop end hook.
     *
     * @param {Object} dropdata the dropdata
     */
    dragEnd(dropdata) {
        this.reactive.dispatch('cmDrag', [dropdata.id], false);
    }

    /**
     * Get the draggable data of this component.
     *
     * @returns {Object} exported course module drop data
     */
    getDraggableData() {
        const exporter = this.reactive.getExporter();
        return exporter.cmDraggableData(this.reactive.state, this.id);
    }

    /**
     * Validate if the drop data can be dropped over the component.
     *
     * @param {Object} dropdata the exported drop data.
     * @returns {boolean}
     */
    validateDropData(dropdata) {
        if (dropdata?.type !== 'cm') {
            return false;
        }
        // Prevent delegated sections loops.
        if (dropdata?.delegatesection === true) {
            const mycminfo = this.reactive.get('cm', this.id);
            const mysection = this.reactive.get('section', mycminfo.sectionid);
            if (mysection?.component !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Display the component dropzone.
     *
     * @param {Object} dropdata the accepted drop data
     */
    showDropZone(dropdata) {
        // If we are the next cmid of the dragged element we accept the drop because otherwise it
        // will get captured by the section. However, we won't trigger any mutation.
        if (dropdata.nextcmid != this.id && dropdata.id != this.id) {
            this.element.classList.add(this.classes.DROPUP);
        }
    }

    /**
     * Hide the component dropzone.
     */
    hideDropZone() {
        this.element.classList.remove(this.classes.DROPUP);
    }

    /**
     * Drop event handler.
     *
     * @param {Object} dropdata the accepted drop data
     * @param {Event} event the drop event
     */
    drop(dropdata, event) {
        // Call the move mutation if necessary.
        if (dropdata.id != this.id && dropdata.nextcmid != this.id) {
            const mutation = (event.altKey) ? 'cmDuplicate' : 'cmMove';
            this.reactive.dispatch(mutation, [dropdata.id], null, this.id);
        }
    }

}
