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
 * Drag and drop helper component.
 *
 * This component is used to delegate drag and drop handling.
 *
 * To delegate the logic to this particular element the component should create a new instance
 * passing "this" as param. The component will use all the necessary callbacks and add all the
 * necessary listeners to the component element.
 *
 * Component attributes used by dragdrop module:
 * - element: the draggable or dropzone element.
 * - (optional) classes: object with alternative CSS classes
 * - (optional) fullregion: page element affeted by the elementy dragging. Use this attribute if
 *                          the draggable element affects a bigger region (for example a draggable
 *                          title).
 * - (optional) autoconfigDraggable: by default, the component will be draggable if it has a
 *                                   getDraggableData method. If this value is false draggable
 *                                  property must be defined using setDraggable method.
 * - (optional) relativeDrag: by default the drag image is located at point (0,0) relative to the
 *                            mouse position to prevent the mouse from covering it. If this attribute
 *                            is true the drag image will be located at the click offset.
 *
 * Methods the parent component should have for making it draggable:
 *
 * - getDraggableData(): Object|data
 *      Return the data that will be passed to any valid dropzone while it is dragged.
 *      If the component has this method, the dragdrop module will enable the dragging,
 *      this is the only required method for dragging.
 *      If at the dragging moment this method returns a false|null|undefined, the dragging
 *      actions won't be captured.
 *
 * - (optional) dragStart(Object dropdata, Event event): void
 * - (optional) dragEnd(Object dropdata, Event event): void
 *      Callbacks dragdrop will call when the element is dragged and getDraggableData
 *      return some data.
 *
 * Methods the parent component should have for enabling it as a dropzone:
 *
 * - validateDropData(Object dropdata): boolean
 *      If that method exists, the dragdrop module will automathically configure the element as dropzone.
 *      This method will return true if the dropdata is accepted. In case it returns false, no drag and
 *      drop event will be listened for this specific dragged dropdata.
 *
 * - (Optional) showDropZone(Object dropdata, Event event): void
 * - (Optional) hideDropZone(Object dropdata, Event event): void
 *      Methods called when a valid dragged data pass over the element.
 *
 * - (Optional) drop(Object dropdata, Event event): void
 *      Called when a valid dragged element is dropped over the element.
 *
 *      Note that none of this methods will be called if validateDropData
 *      returns a false value.
 *
 * This module will also add or remove several CSS classes from both dragged elements and dropzones.
 * See the "this.classes" in the create method for more details. In case the parent component wants
 * to use the same classes, it can use the getClasses method. On the other hand, if the parent
 * component has an alternative "classes" attribute, this will override the default drag and drop
 * classes.
 *
 * @module     core/local/reactive/dragdrop
 * @class      core/local/reactive/dragdrop
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseComponent from 'core/local/reactive/basecomponent';

// Map with the dragged element generate by an specific reactive applications.
// Potentially, any component can generate a draggable element to interact with other
// page elements. However, the dragged data is specific and could only interact with
// components of the same reactive instance.
let activeDropData = new Map();

// Drag & Drop API provides the final drop point and incremental movements but we can
// provide also starting points and displacements. Absolute displacements simplifies
// moving components with aboslute position around the page.
let dragStartPoint = {};

export default class extends BaseComponent {

    /**
     * Constructor hook.
     *
     * @param {BaseComponent} parent the parent component.
     */
    create(parent) {
        // Optional component name for debugging.
        this.name = `${parent.name ?? 'unkown'}_dragdrop`;

        // Default drag and drop classes.
        this.classes = Object.assign(
                {
                // This class indicate a dragging action is active at a page level.
                BODYDRAGGING: 'dragging',

                // Added when draggable and drop are ready.
                DRAGGABLEREADY: 'draggable',
                DROPREADY: 'dropready',

                // When a valid drag element is over the element.
                DRAGOVER: 'dragover',
                // When a the component is dragged.
                DRAGGING: 'dragging',

                // Dropzones classes names.
                DROPUP: 'drop-up',
                DROPDOWN: 'drop-down',
                DROPZONE: 'drop-zone',

                // Drag icon class.
                DRAGICON: 'dragicon',
            },
            parent?.classes ?? {}
        );

        // Add the affected region if any.
        this.fullregion = parent.fullregion;

        // Keep parent to execute drap and drop handlers.
        this.parent = parent;

        // Check if parent handle draggable manually.
        this.autoconfigDraggable = this.parent.draggable ?? true;

        // Drag image relative position.
        this.relativeDrag = this.parent.relativeDrag ?? false;

        // Sub HTML elements will trigger extra dragEnter and dragOver all the time.
        // To prevent that from affecting dropzones, we need to count the enters and leaves.
        this.entercount = 0;

        // Stores if the droparea is shown or not.
        this.dropzonevisible = false;

        // Stores if the mouse is over the element or not.
        this.ismouseover = false;
    }

    /**
     * Return the component drag and drop CSS classes.
     *
     * @returns {Object} the dragdrop css classes
     */
    getClasses() {
        return this.classes;
    }

    /**
     * Return the current drop-zone visible of the element.
     *
     * @returns {boolean} if the dropzone should be visible or not
     */
    isDropzoneVisible() {
        return this.dropzonevisible;
    }

    /**
     * Initial state ready method.
     *
     * This method will add all the necessary event listeners to the component depending on the
     * parent methods.
     *  - Add drop events to the element if the parent component has validateDropData method.
     *  - Configure the elements draggable if the parent component has getDraggableData method.
     */
    stateReady() {
        // Add drop events to the element if the parent component has dropable types.
        if (typeof this.parent.validateDropData === 'function') {
            this.element.classList.add(this.classes.DROPREADY);
            this.addEventListener(this.element, 'dragenter', this._dragEnter);
            this.addEventListener(this.element, 'dragleave', this._dragLeave);
            this.addEventListener(this.element, 'dragover', this._dragOver);
            this.addEventListener(this.element, 'drop', this._drop);
            this.addEventListener(this.element, 'mouseover', this._mouseOver);
            this.addEventListener(this.element, 'mouseleave', this._mouseLeave);
        }

        // Configure the elements draggable if the parent component has dragable data.
        if (this.autoconfigDraggable && typeof this.parent.getDraggableData === 'function') {
            this.setDraggable(true);
        }
    }

    /**
     * Enable or disable the draggable property.
     *
     * @param {bool} value the new draggable value
     */
    setDraggable(value) {
        if (typeof this.parent.getDraggableData !== 'function') {
            throw new Error(`Draggable components must have a getDraggableData method`);
        }
        this.element.setAttribute('draggable', value);
        if (value) {
            this.addEventListener(this.element, 'dragstart', this._dragStart);
            this.addEventListener(this.element, 'dragend', this._dragEnd);
            this.element.classList.add(this.classes.DRAGGABLEREADY);
        } else {
            this.removeEventListener(this.element, 'dragstart', this._dragStart);
            this.removeEventListener(this.element, 'dragend', this._dragEnd);
            this.element.classList.remove(this.classes.DRAGGABLEREADY);
        }
    }

    /**
     * Mouse over handle.
     */
    _mouseOver() {
        this.ismouseover = true;
    }

    /**
     * Mouse leave handler.
     */
    _mouseLeave() {
        this.ismouseover = false;
    }

    /**
     * Drag start event handler.
     *
     * This method will generate the current dropable data. This data is the one used to determine
     * if a droparea accepts the dropping or not.
     *
     * @param {Event} event the event.
     */
    _dragStart(event) {
        // Cancel dragging if any editable form element is focussed.
        if (document.activeElement.matches(`textarea, input`)) {
            event.preventDefault();
            return;
        }

        const dropdata = this.parent.getDraggableData();
        if (!dropdata) {
            return;
        }

        // Save the starting point.
        dragStartPoint = {
            pageX: event.pageX,
            pageY: event.pageY,
        };

        // If the drag event is accepted we prevent any other draggable element from interfiering.
        event.stopPropagation();

        // Save the drop data of the current reactive intance.
        activeDropData.set(this.reactive, dropdata);

        // Add some CSS classes to indicate the state.
        document.body.classList.add(this.classes.BODYDRAGGING);
        this.element.classList.add(this.classes.DRAGGING);
        this.fullregion?.classList.add(this.classes.DRAGGING);

        // Force the drag image. This makes the UX more consistent in case the
        // user dragged an internal element like a link or some other element.
        let dragImage = this.element;
        if (this.parent.setDragImage !== undefined) {
            const customImage = this.parent.setDragImage(dropdata, event);
            if (customImage) {
                dragImage = customImage;
            }
        }
        // Define the image position relative to the mouse.
        const position = {x: 0, y: 0};
        if (this.relativeDrag) {
            position.x = event.offsetX;
            position.y = event.offsetY;
        }
        event.dataTransfer.setDragImage(dragImage, position.x, position.y);
        event.dataTransfer.effectAllowed = 'copyMove';
        this._callParentMethod('dragStart', dropdata, event);
    }

    /**
     * Drag end event handler.
     *
     * @param {Event} event the event.
     */
    _dragEnd(event) {
        const dropdata = activeDropData.get(this.reactive);
        if (!dropdata) {
            return;
        }

        // Remove the current dropdata.
        activeDropData.delete(this.reactive);

        // Remove the dragging classes.
        document.body.classList.remove(this.classes.BODYDRAGGING);
        this.element.classList.remove(this.classes.DRAGGING);
        this.fullregion?.classList.remove(this.classes.DRAGGING);
        this.removeAllOverlays();

        // We add the total movement to the event in case the component
        // wants to move its absolute position.
        this._addEventTotalMovement(event);

        this._callParentMethod('dragEnd', dropdata, event);
    }

    /**
     * Drag enter event handler.
     *
     * The JS drag&drop API triggers several dragenter events on the same element because it bubbles the
     * child events as well. To prevent this form affecting the dropzones display, this methods use
     * "entercount" to determine if it's one extra child event or a valid one.
     *
     * @param {Event} event the event.
     */
    _dragEnter(event) {
        const dropdata = this._processEvent(event);
        if (dropdata) {
            this.entercount++;
            this.element.classList.add(this.classes.DRAGOVER);
            if (this.entercount == 1 && !this.dropzonevisible) {
                this.dropzonevisible = true;
                this.element.classList.add(this.classes.DRAGOVER);
                this._callParentMethod('showDropZone', dropdata, event);
            }
        }
    }

    /**
     * Drag over event handler.
     *
     * We only use dragover event when a draggable action starts inside a valid dropzone. In those cases
     * the API won't trigger any dragEnter because the dragged alement was already there. We use the
     * dropzonevisible to determine if the component needs to display the dropzones or not.
     *
     * @param {Event} event the event.
     */
    _dragOver(event) {
        const dropdata = this._processEvent(event);
        event.dataTransfer.dropEffect = (event.altKey) ? 'copy' : 'move';
        if (dropdata && !this.dropzonevisible) {
            this.dropzonevisible = true;
            this.element.classList.add(this.classes.DRAGOVER);
            this._callParentMethod('showDropZone', dropdata, event);
        }
    }

    /**
     * Drag over leave handler.
     *
     * The JS drag&drop API triggers several dragleave events on the same element because it bubbles the
     * child events as well. To prevent this form affecting the dropzones display, this methods use
     * "entercount" to determine if it's one extra child event or a valid one.
     *
     * @param {Event} event the event.
     */
    _dragLeave(event) {
        const dropdata = this._processEvent(event);
        if (dropdata) {
            this.entercount--;
            if (this.entercount <= 0 && this.dropzonevisible) {
                this.dropzonevisible = false;
                this.element.classList.remove(this.classes.DRAGOVER);
                this._callParentMethod('hideDropZone', dropdata, event);
            }
        }
    }

    /**
     * Drop event handler.
     *
     * This method will call both hideDropZones and drop methods on the parent component.
     *
     * @param {Event} event the event.
     */
    _drop(event) {
        const dropdata = this._processEvent(event);
        if (dropdata) {
            this.entercount = 0;
            if (this.dropzonevisible) {
                this.dropzonevisible = false;
                this._callParentMethod('hideDropZone', dropdata, event);
            }
            this.element.classList.remove(this.classes.DRAGOVER);
            this.removeAllOverlays();
            this._callParentMethod('drop', dropdata, event);
            // An accepted drop resets the initial position.
            // Save the starting point.
            dragStartPoint = {};
        }
    }

    /**
     * Process a drag and drop event and delegate logic to the parent component.
     *
     * @param {Event} event the drag and drop event
     * @return {Object|false} the dropdata or null if the event should not be processed
     */
    _processEvent(event) {
        const dropdata = this._getDropData(event);
        if (!dropdata) {
            return null;
        }
        if (this.parent.validateDropData(dropdata)) {
            // All accepted drag&drop event must prevent bubbling and defaults, otherwise
            // parent dragdrop instances could capture it by mistake.
            event.preventDefault();
            event.stopPropagation();
            this._addEventTotalMovement(event);
            return dropdata;
        }
        return null;
    }

    /**
     * Add the total amout of movement to a mouse event.
     *
     * @param {MouseEvent} event
     */
    _addEventTotalMovement(event) {
        if (dragStartPoint.pageX === undefined || event.pageX === undefined) {
            return;
        }
        event.fixedMovementX = event.pageX - dragStartPoint.pageX;
        event.fixedMovementY = event.pageY - dragStartPoint.pageY;
        event.initialPageX = dragStartPoint.pageX;
        event.initialPageY = dragStartPoint.pageY;
        // The element possible new top.
        const current = this.element.getBoundingClientRect();
        // Add the new position fixed position.
        event.newFixedTop = current.top + event.fixedMovementY;
        event.newFixedLeft = current.left + event.fixedMovementX;
        // The affected region possible new top.
        if (this.fullregion !== undefined) {
            const current = this.fullregion.getBoundingClientRect();
            event.newRegionFixedxTop = current.top + event.fixedMovementY;
            event.newRegionFixedxLeft = current.left + event.fixedMovementX;
        }
    }

    /**
     * Convenient method for calling parent component functions if present.
     *
     * @param {string} methodname the name of the method
     * @param {Object} dropdata the current drop data object
     * @param {Event} event the original event
     */
    _callParentMethod(methodname, dropdata, event) {
        if (typeof this.parent[methodname] === 'function') {
            this.parent[methodname](dropdata, event);
        }
    }

    /**
     * Get the current dropdata for a specific event.
     *
     * The browser can generate drag&drop events related to several user interactions:
     *  - Drag a page elements: this case is registered in the activeDropData map
     *  - Drag some HTML selections: ignored for now
     *  - Drag a file over the browser: file drag may appear in the future but for now they are ignored.
     *
     * @param {Event} event the original event.
     * @returns {Object|undefined} with the dragged data (or undefined if none)
     */
    _getDropData(event) {
        this._isOnlyFilesDragging = this._containsOnlyFiles(event);
        if (this._isOnlyFilesDragging) {
            // Check if the reactive instance can provide a files draggable data.
            if (this.reactive.getFilesDraggableData !== undefined && typeof this.reactive.getFilesDraggableData === 'function') {
                return this.reactive.getFilesDraggableData(event.dataTransfer);
            }
            return undefined;
        }
        return activeDropData.get(this.reactive);
    }

    /**
     * Check if the dragged event contains only files.
     *
     * Files dragging does not generate drop data because they came from outside the page and the component
     * must check it before validating the event.
     *
     * Some browsers like Firefox add extra types to file dragging. To discard the false positives
     * a double check is necessary.
     *
     * @param {Event} event the original event.
     * @returns {boolean} if the drag dataTransfers contains files.
     */
    _containsOnlyFiles(event) {
        if (!event.dataTransfer.types.includes('Files')) {
            return false;
        }
        return event.dataTransfer.types.every((type) => {
            return (type.toLowerCase() != 'text/uri-list'
                && type.toLowerCase() != 'text/html'
                && type.toLowerCase() != 'text/plain'
            );
        });
    }
}
