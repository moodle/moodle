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

import Log from 'core/log';
import Templates from 'core/templates';

/**
 * A dropzone.
 *
 * @class core/dropzone
 */
const DropZone = class {

    /**
     * Constructor.
     *
     * @param {Element} dropZoneElement The element to render the dropzone.
     * @param {String} fileTypes The file types that are allowed to be uploaded. Example: image/*
     * @param {CallableFunction} callback The function to call when a file is dropped.
     */
    constructor(dropZoneElement, fileTypes, callback) {
        this.init(dropZoneElement, fileTypes, callback);
    }

    /**
     * Initialise the dropzone.
     *
     * @param {Element} dropZoneElement The element to render the dropzone.
     * @param {String} fileTypes The file types that are allowed to be uploaded. Example: image/*
     * @param {CallableFunction} callback The function to call when a file is dropped.
     * @returns {DropZone}
     */
    init(dropZoneElement, fileTypes, callback) {
        dropZoneElement.addEventListener('dragover', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZoneElement.addEventListener('dragleave', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.remove('dragover');
        });
        dropZoneElement.addEventListener('drop', (e) => {
            const dropZone = this.getDropZoneFromEvent(e);
            if (!dropZone) {
                return;
            }
            e.preventDefault();
            dropZone.classList.remove('dragover');
            callback(e.dataTransfer.files);
        });
        dropZoneElement.addEventListener('click', (e) => {
            const dropZoneContainer = this.getDropZoneContainerFromEvent(e);
            if (!dropZoneContainer) {
                return;
            }
            this.getFileElementFromEvent(e).click();
        });
        dropZoneElement.addEventListener('click', (e) => {
            const dropZoneLabel = e.target.closest('.dropzone-sr-only-focusable');
            if (!dropZoneLabel) {
                return;
            }
            this.getFileElementFromEvent(e).click();
        });
        dropZoneElement.addEventListener('change', (e) => {
            const fileInput = this.getFileElementFromEvent(e);
            if (fileInput) {
                e.preventDefault();
                callback(fileInput.files);
            }
        });
        this.renderDropZone(dropZoneElement, fileTypes);
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
     * Render the dropzone.
     *
     * @param {Element} dropZoneElement The element to render the dropzone.
     * @param {String} fileTypes The file types that are allowed to be uploaded.
     * @returns {Promise}
     */
    async renderDropZone(dropZoneElement, fileTypes) {
        dropZoneElement.innerHTML = await Templates.render('core/dropzone', {
            fileTypes,
        });
    }
};

export default DropZone;
