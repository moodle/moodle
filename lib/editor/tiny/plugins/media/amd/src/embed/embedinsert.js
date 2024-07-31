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
 * Tiny media plugin embed upload class.
 *
 * This handles the embed upload using url, drag-drop and repositories.
 *
 * @module      tiny_media/embed/embedinsert
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import {component} from "../common";
import {
    setPropertiesFromData,
    startMediaLoading,
    stopMediaLoading,
    showElements,
} from '../helpers';
import Selectors from "../selectors";
import Dropzone from 'core/dropzone';
import uploadFile from 'editor_tiny/uploader';

prefetchStrings('tiny_media', [
    'insertmedia',
    'addmediafilesdrop',
    'uploading',
    'loadingmedia',
]);

export class EmbedInsert {

    constructor(data) {
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Init the dropzone and lang strings.
     */
    init = async() => {
        const langStringKeys = [
            'insertmedia',
            'addmediafilesdrop',
            'uploading',
            'loadingmedia',
        ];
        const langStringValues = await getStrings([...langStringKeys].map((key) => ({key, component})));
        this.langStrings = Object.fromEntries(langStringKeys.map((key, index) => [key, langStringValues[index]]));
        this.currentModal.setTitle(this.langStrings.insertmedia);

        // Let's init the dropzone if canShowDropZone is true and mediaType is null.
        if (this.canShowDropZone && !this.mediaType) {
            const dropZoneEle = document.querySelector(Selectors.EMBED.elements.dropzoneContainer);
            const dropZone = new Dropzone(
                dropZoneEle,
                'audio/*,video/*',
                files => {
                    this.handleUploadedFile(files);
                }
            );

            dropZone.setLabel(this.langStrings.addmediafilesdrop);
            dropZone.init();
        }
    };

    /**
     * Updates the content of the loader icon.
     *
     * @param {HTMLElement} root - The root element containing the loader icon.
     * @param {object} langStrings - An object containing language strings.
     * @param {number|null} progress - The progress percentage (optional).
     * @returns {void}
     */
    updateLoaderIcon = (root, langStrings, progress = null) => {
        const loaderIcon = this.root.querySelector(Selectors.EMBED.elements.loaderIcon);
        if (loaderIcon && loaderIcon.classList.contains('d-none')) {
            showElements(Selectors.EMBED.elements.loaderIcon);
        }

        const loaderIconState = root.querySelector(Selectors.EMBED.elements.loaderIconContainer + ' div');
        loaderIconState.innerHTML = (progress !== null) ?
                               `${langStrings.uploading} ${Math.round(progress)}%` :
                               langStrings.loadingmedia;
    };

    /**
     * Handles media preview on file picker callback.
     *
     * @param {object} params Object of uploaded file
     */
    filePickerCallback = (params) => {
        if (params.url) {
            window.console.log(params.url);
            stopMediaLoading(this.root, 'EMBED');
        }
    };

    /**
     * Handles the uploaded file, initiates the upload process, and updates the UI during the upload.
     *
     * @param {FileList} files - The list of files to upload (usually from a file input field).
     * @returns {Promise<void>} A promise that resolves when the file is uploaded and processed.
     */
    handleUploadedFile = async(files) => {
        try {
            startMediaLoading(this.root, 'EMBED');
            const fileURL = await uploadFile(this.editor, 'media', files[0], files[0].name, (progress) => {
                this.updateLoaderIcon(this.root, this.langStrings, progress);
            });

            // Set the loader icon content to "loading" after the file upload completes.
            this.updateLoaderIcon(this.root, this.langStrings);
            this.filePickerCallback({url: fileURL});
        } catch (error) {
            // Handle the error.
            const urlWarningLabelEle = this.root.querySelector(Selectors.EMBED.elements.urlWarning);
            urlWarningLabelEle.innerHTML = error.error !== undefined ? error.error : error;
            showElements(Selectors.EMBED.elements.urlWarning, this.root);
            stopMediaLoading(this.root, 'EMBED');
        }
    };
}
