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
 * Course index section component.
 *
 * This component is used to control specific course section interactions like drag and drop
 * in both course index and course content.
 *
 * @module     core_courseformat/local/courseeditor/dndsection
 * @class      core_courseformat/local/courseeditor/dndsection
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent, DragDrop} from 'core/reactive';
import {getString} from 'core/str';
import {prefetchStrings} from 'core/prefetch';
import Templates from 'core/templates';

// Load global strings.
prefetchStrings('core', ['addfilehere']);

export default class extends BaseComponent {

    /**
     * Save some values form the state.
     *
     * @param {Object} state the current state
     */
    configState(state) {
        this.id = this.element.dataset.id;
        this.section = state.section.get(this.id);
        this.course = state.course;
    }

    /**
     * Register state values and the drag and drop subcomponent.
     *
     * @param {BaseComponent} sectionitem section item component
     */
    configDragDrop(sectionitem) {
        // Drag and drop is only available for components compatible course formats.
        if (this.reactive.isEditing && this.reactive.supportComponents) {
            // Init the inner dragable element.
            this.sectionitem = sectionitem;
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
        if (this.sectionitem !== undefined) {
            this.sectionitem.unregister();
        }
        if (this.dragdrop !== undefined) {
            this.dragdrop.unregister();
        }
    }

    /**
     * Get the last CM element of that section.
     *
     * @returns {element|null} the las course module element of the section.
     */
    getLastCm() {
        return null;
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
     * The element drop end hook.
     *
     * @param {Object} dropdata the dropdata
     */
    dragEnd(dropdata) {
        this.reactive.dispatch('sectionDrag', [dropdata.id], false);
    }

    /**
     * Validate if the drop data can be dropped over the component.
     *
     * @param {Object} dropdata the exported drop data.
     * @returns {boolean}
     */
    validateDropData(dropdata) {
        // We accept files.
        if (dropdata?.type === 'files') {
            return true;
        }
        // We accept any course module unless it can form a subsection loop.
        if (dropdata?.type === 'cm') {
            if (this.section?.component && dropdata?.delegatesection === true) {
                return false;
            }
            return true;
        }
        // We accept any section but yourself and the next one.
        if (dropdata?.type === 'section') {
            return dropdata?.id != this.id && dropdata?.number != this.section.number + 1;
        }
        return false;
    }

    /**
     * Display the component dropzone.
     *
     * @param {Object} dropdata the accepted drop data
     */
    showDropZone(dropdata) {
        if (dropdata.type == 'files') {
            this.addOverlay({
                content: getString('addfilehere', 'core'),
                icon: Templates.renderPix('t/download', 'core'),
            }).then(() => {
                // Check if we still need the file dropzone.
                if (!this.dragdrop?.isDropzoneVisible()) {
                    this.removeOverlay();
                }
                return;
            }).catch((error) => {
                throw error;
            });
        }
        if (dropdata.type == 'cm') {
            this.getLastCm()?.classList.add(this.classes.DROPDOWN);
        }
        if (dropdata.type == 'section') {
            this.element.classList.remove(this.classes.DROPUP);
            this.element.classList.add(this.classes.DROPDOWN);
        }
    }

    /**
     * Hide the component dropzone.
     */
    hideDropZone() {
        this.getLastCm()?.classList.remove(this.classes.DROPDOWN);
        this.element.classList.remove(this.classes.DROPUP);
        this.element.classList.remove(this.classes.DROPDOWN);
        this.removeOverlay();
    }

    /**
     * Drop event handler.
     *
     * @param {Object} dropdata the accepted drop data
     * @param {Event} event the drop event
     */
    drop(dropdata, event) {
        // File handling.
        if (dropdata.type == 'files') {
            this.reactive.uploadFiles(
                this.section.id,
                this.section.number,
                dropdata.files
            );
            return;
        }
        // Call the move mutation.
        if (dropdata.type == 'cm') {
            const mutation = (event.altKey) ? 'cmDuplicate' : 'cmMove';
            this.reactive.dispatch(mutation, [dropdata.id], this.id);
        }
        if (dropdata.type == 'section') {
            this.reactive.dispatch('sectionMoveAfter', [dropdata.id], this.id);
        }
    }
}
