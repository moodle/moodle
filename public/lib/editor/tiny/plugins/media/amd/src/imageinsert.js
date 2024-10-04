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
 * Tiny media plugin image insertion class for Moodle.
 *
 * @module      tiny_media/imageinsert
 * @copyright   2024 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from './selectors';
import Dropzone from 'core/dropzone';
import uploadFile from 'editor_tiny/uploader';
import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import {component} from "./common";
import {getFilePicker} from 'editor_tiny/options';
import {displayFilepicker} from 'editor_tiny/utils';
import {ImageDetails} from 'tiny_media/imagedetails';
import {
    body,
    footer,
    hideElements,
    showElements,
    isValidUrl,
} from './helpers';
import {MAX_LENGTH_ALT} from './imagehelpers';

prefetchStrings('tiny_media', [
    'insertimage',
    'enterurl',
    'enterurlor',
    'imageurlrequired',
    'uploading',
    'loading',
    'addfilesdrop',
    'sizecustom_help',
]);

export class ImageInsert {

    constructor(
        root,
        editor,
        currentModal,
        canShowFilePicker,
        canShowDropZone,
    ) {
        this.root = root;
        this.editor = editor;
        this.currentModal = currentModal;
        this.canShowFilePicker = canShowFilePicker;
        this.canShowDropZone = canShowDropZone;
    }

    init = async function() {
        // Get the localization lang strings and turn them into object.
        const langStringKeys = [
            'insertimage',
            'enterurl',
            'enterurlor',
            'imageurlrequired',
            'uploading',
            'loading',
            'addfilesdrop',
            'sizecustom_help',
        ];
        const langStringvalues = await getStrings([...langStringKeys].map((key) => ({key, component})));

        // Convert array to object.
        this.langStrings = Object.fromEntries(langStringKeys.map((key, index) => [key, langStringvalues[index]]));
        this.currentModal.setTitle(this.langStrings.insertimage);
        if (this.canShowDropZone) {
            const dropZoneEle = document.querySelector(Selectors.IMAGE.elements.dropzoneContainer);

            // Accepted types can be either a string or an array.
            let acceptedTypes = getFilePicker(this.editor, 'image').accepted_types;
            if (Array.isArray(acceptedTypes)) {
                acceptedTypes = acceptedTypes.join(',');
            }

            const dropZone = new Dropzone(
                dropZoneEle,
                acceptedTypes,
                files => {
                    this.handleUploadedFile(files);
                }
            );
            dropZone.setLabel(this.langStrings.addfilesdrop);
            dropZone.init();
        }
        await this.registerEventListeners();
    };

    /**
     * Enables or disables the URL-related buttons in the footer based on the current URL and input value.
     */
    toggleUrlButton() {
        const urlInput = this.root.querySelector(Selectors.IMAGE.elements.url);
        const url = urlInput.value;
        const addUrl = this.root.querySelector(Selectors.IMAGE.actions.addUrl);
        addUrl.disabled = !(url !== "" && isValidUrl(url));
    }

    /**
     * Handles changes in the image URL input field and loads a preview of the image if the URL has changed.
     */
    urlChanged() {
        hideElements(Selectors.IMAGE.elements.urlWarning, this.root);
        const input = this.root.querySelector(Selectors.IMAGE.elements.url);
        if (input.value && input.value !== this.currentUrl) {
            this.loadPreviewImage(input.value);
        }
    }

    /**
     * Loads and displays a preview image based on the provided URL, and handles image loading events.
     *
     * @param {string} url - The URL of the image to load and display.
     */
    loadPreviewImage = function(url) {
        this.startImageLoading();
        this.currentUrl = url;
        const image = new Image();
        image.src = url;
        image.addEventListener('error', () => {
            const urlWarningLabelEle = this.root.querySelector(Selectors.IMAGE.elements.urlWarning);
            urlWarningLabelEle.innerHTML = this.langStrings.imageurlrequired;
            showElements(Selectors.IMAGE.elements.urlWarning, this.root);
            this.currentUrl = "";
            this.stopImageLoading();
        });

        image.addEventListener('load', () => {
            let templateContext = {};
            templateContext.sizecustomhelpicon = {text: this.langStrings.sizecustom_help};
            templateContext.bodyTemplate = Selectors.IMAGE.template.body.insertImageDetailsBody;
            templateContext.footerTemplate = Selectors.IMAGE.template.footer.insertImageDetailsFooter;
            templateContext.selector = Selectors.IMAGE.type;
            templateContext.maxlengthalt = MAX_LENGTH_ALT;

            Promise.all([body(templateContext, this.root), footer(templateContext, this.root)])
                .then(() => {
                    const imagedetails = new ImageDetails(
                        this.root,
                        this.editor,
                        this.currentModal,
                        this.canShowFilePicker,
                        this.canShowDropZone,
                        this.currentUrl,
                        image,
                    );
                    imagedetails.init();
                    return;
                }).then(() => {
                    this.stopImageLoading();
                    return;
                })
                .catch(error => {
                    window.console.log(error);
                });
        });
    };

    /**
     * Displays the upload loader and disables UI elements while loading a file.
     */
    startImageLoading() {
        showElements(Selectors.IMAGE.elements.loaderIcon, this.root);
        const elementsToHide = [
            Selectors.IMAGE.elements.insertImage,
            Selectors.IMAGE.elements.urlWarning,
            Selectors.IMAGE.elements.modalFooter,
        ];
        hideElements(elementsToHide, this.root);
    }

    /**
     * Displays the upload loader and disables UI elements while loading a file.
     */
    stopImageLoading() {
        hideElements(Selectors.IMAGE.elements.loaderIcon, this.root);
        const elementsToShow = [
            Selectors.IMAGE.elements.insertImage,
            Selectors.IMAGE.elements.modalFooter,
        ];
        showElements(elementsToShow, this.root);
    }

    filePickerCallback(params) {
        if (params.url) {
            this.loadPreviewImage(params.url);
        }
    }

    /**
     * Updates the content of the loader icon.
     *
     * @param {HTMLElement} root - The root element containing the loader icon.
     * @param {object} langStrings - An object containing language strings.
     * @param {number|null} progress - The progress percentage (optional).
     * @returns {void}
     */
    updateLoaderIcon = (root, langStrings, progress = null) => {
        const loaderIcon = root.querySelector(Selectors.IMAGE.elements.loaderIconContainer + ' div');
        loaderIcon.innerHTML = progress !== null ? `${langStrings.uploading} ${Math.round(progress)}%` : langStrings.loading;
    };

    /**
     * Handles the uploaded file, initiates the upload process, and updates the UI during the upload.
     *
     * @param {FileList} files - The list of files to upload (usually from a file input field).
     * @returns {Promise<void>} A promise that resolves when the file is uploaded and processed.
     */
    handleUploadedFile = async(files) => {
        try {
            this.startImageLoading();
            const fileURL = await uploadFile(this.editor, 'image', files[0], files[0].name, (progress) => {
                this.updateLoaderIcon(this.root, this.langStrings, progress);
            });
            // Set the loader icon content to "loading" after the file upload completes.
            this.updateLoaderIcon(this.root, this.langStrings);
            this.filePickerCallback({url: fileURL});
        } catch (error) {
            // Handle the error.
            const urlWarningLabelEle = this.root.querySelector(Selectors.IMAGE.elements.urlWarning);
            urlWarningLabelEle.innerHTML = error.error !== undefined ? error.error : error;
            showElements(Selectors.IMAGE.elements.urlWarning, this.root);
            this.stopImageLoading();
        }
    };

    registerEventListeners() {
        this.root.addEventListener('click', async(e) => {
            const addUrlEle = e.target.closest(Selectors.IMAGE.actions.addUrl);
            if (addUrlEle) {
                this.urlChanged();
            }

            const imageBrowserAction = e.target.closest(Selectors.IMAGE.actions.imageBrowser);
            if (imageBrowserAction && this.canShowFilePicker) {
                e.preventDefault();
                const params = await displayFilepicker(this.editor, 'image');
                this.filePickerCallback(params);
            }
        });

        this.root.addEventListener('input', (e) => {
            const urlEle = e.target.closest(Selectors.IMAGE.elements.url);
            if (urlEle) {
                this.toggleUrlButton();
            }
        });

        const fileInput = this.root.querySelector(Selectors.IMAGE.elements.fileInput);
        if (fileInput) {
            fileInput.addEventListener('change', () => {
                this.handleUploadedFile(fileInput.files);
            });
        }
    }
}
