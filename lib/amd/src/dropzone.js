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
 * JavaScript to handle dropzone.
 *
 * @module     core/dropzone
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.4
 */

import {getString} from 'core/str';
import Log from 'core/log';
import {prefetchString} from 'core/prefetch';
import Templates from 'core/templates';

/**
 * A dropzone.
 *
 * @class core/dropzone
 */
const DropZone = class {

    /**
     * The element to render the dropzone.
     * @type {Element}
     */
    dropZoneElement;

    /**
     * The file types that are allowed to be uploaded.
     * @type {String}
     */
    fileTypes;

    /**
     * The function to call when a file is dropped.
     * @type {CallableFunction}
     */
    callback;

    /**
     * The label to display in the dropzone.
     * @type {string}
     */
    dropZoneLabel = '';

    /**
     * Constructor.
     *
     * @param {Element} dropZoneElement The element to render the dropzone.
     * @param {String} fileTypes The file types that are allowed to be uploaded. Example: image/*
     * @param {CallableFunction} callback The function to call when a file is dropped.
     */
    constructor(dropZoneElement, fileTypes, callback) {
        prefetchString('core', 'addfilesdrop');
        this.dropZoneElement = dropZoneElement;
        this.fileTypes = fileTypes;
        this.callback = callback;
    }

    /**
     * Initialise the dropzone.
     *
     * @returns {DropZone}
     */
    init() {
        this.dropZoneElement.addEventListener('dragover', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        this.dropZoneElement.addEventListener('dragleave', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.remove('dragover');
        });
        this.dropZoneElement.addEventListener('drop', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.remove('dragover');
            this.callback(e.dataTransfer.files);
        });
        this.dropZoneElement.addEventListener('click', (e) => {
            const targetDropZoneElement = e.target.closest('.dropzone-visually-hidden-focusable') ||
                this.getDropZoneContainerFromEvent(e);
            if (!targetDropZoneElement) {
                return;
            }
            this.getFileElementFromEvent(e).click();
        });
        this.dropZoneElement.addEventListener('change', (e) => {
            const fileInput = this.getFileElementFromEvent(e);
            if (fileInput) {
                e.preventDefault();
                this.callback(fileInput.files);
            }
        });
        this.renderDropZone(this.dropZoneElement, this.fileTypes);
        Log.info('Dropzone has been initialized!');
        return this;
    }

    /**
     * Get the dropzone.
     *
     * @param {Event} e The event.
     * @returns {HTMLElement|bool}
     */
    getDropZoneFromEvent(e) {
        return e.target.closest('.dropzone');
    }

    /**
     * Get the dropzone container.
     *
     * @param {Event} e The event.
     * @returns {HTMLElement|bool}
     */
    getDropZoneContainerFromEvent(e) {
        return e.target.closest('.dropzone-container');
    }

    /**
     * Get the file element.
     *
     * @param {Event} e The event.
     * @returns {HTMLElement|bool}
     */
    getFileElementFromEvent(e) {
        return e.target.closest('.dropzone-container').querySelector('.drop-zone-fileinput');
    }

    /**
     * Set the label to display in the dropzone.
     *
     * @param {String} label The label to display in the dropzone.
     */
    setLabel(label) {
        this.dropZoneLabel = label;
    }

    /**
     * Get the label to display in the dropzone.
     *
     * @return {String} The label to display in the dropzone.
     */
    getLabel() {
        return this.dropZoneLabel;
    }

    /**
     * Render the dropzone.
     *
     * @param {Element} dropZoneElement The element to render the dropzone.
     * @param {String} fileTypes The file types that are allowed to be uploaded.
     * @returns {Promise}
     */
    async renderDropZone(dropZoneElement, fileTypes) {
        if (!this.getLabel()) {
            // Use the default one.
            this.setLabel(await getString('addfilesdrop', 'core'));
        }
        const dropZoneLabel = this.getLabel();
        dropZoneElement.innerHTML = await Templates.render('core/dropzone', {
            label: dropZoneLabel,
            filetypes: fileTypes,
        });
    }
};

export default DropZone;
