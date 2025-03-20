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
 * Tiny media plugin embed thumbnail upload class.
 *
 * This handles the embed thumbnail upload using drag-drop.
 *
 * @module      tiny_media/embed/embedthumbnailinsert
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from '../selectors';
import Dropzone from 'core/dropzone';
import uploadFile from 'editor_tiny/uploader';
import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import {component} from "../common";
import {
    showElements,
    startMediaLoading,
    stopMediaLoading,
    setPropertiesFromData,
    body,
    footer,
} from '../helpers';
import {EmbedThumbnailPreview} from './embedthumbnailpreview';
import {EmbedHandler} from './embedhandler';
import {displayFilepicker} from 'editor_tiny/utils';

prefetchStrings(component, [
    'insertmediathumbnail',
    'uploading',
    'loadingembedthumbnail',
    'addmediathumbnaildrop',
]);

export class EmbedThumbnailInsert {

    constructor(data) {
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Init the dropzone and lang strings.
     *
     * @param {object} mediaData Object of selected media data
     */
    init = async(mediaData) => {
        this.mediaData = mediaData; // Current selected media data passed from embedPreview.
        const langStringKeys = [
            'insertmediathumbnail',
            'uploading',
            'loadingembedthumbnail',
            'addmediathumbnaildrop',
        ];
        const langStringvalues = await getStrings([...langStringKeys].map((key) => ({key, component})));
        this.langStrings = Object.fromEntries(langStringKeys.map((key, index) => [key, langStringvalues[index]]));
        this.currentModal.uploadThumbnailModal.setTitle(this.langStrings.insertmediathumbnail);

        // Let's init the dropzone if canShowDropZone is true.
        if (this.canShowDropZone) {
            const dropZoneEle = document.querySelector(Selectors.EMBED.elements.dropzoneContainer);
            const dropZone = new Dropzone(
                dropZoneEle,
                this.acceptedImageTypes,
                files => {
                    this.handleUploadedFile(files);
                }
            );
            dropZone.setLabel(this.langStrings.addmediathumbnaildrop);
            dropZone.init();
        }

        this.registerInsertMediaThumbnailEvents(this.thumbnailModalRoot);
    };

    /**
     * Load and display a preview thumbnail based on the provided URL, and handles thumbnail loading events.
     *
     * @param {string} url - The URL of the thumbnail to load and display.
     */
    loadPreviewThumbnail = (url) => {
        this.media.poster = url;

        let templateContext = {
            bodyTemplate: Selectors.EMBED.template.body.mediaThumbnailBody,
            footerTemplate: Selectors.EMBED.template.footer.mediaThumbnailFooter,
            selector: Selectors.EMBED.type,
        };

        Promise.all([body(templateContext, this.thumbnailModalRoot), footer(templateContext, this.thumbnailModalRoot)])
            .then(() => {
                const mediaThumbnail = new EmbedThumbnailPreview(this);
                mediaThumbnail.init(this.mediaData);
                return;
            })
            .catch(error => {
                window.console.log(error);
            });
    };

    /**
     * Handles media preview on file picker callback.
     *
     * @param {object} params Object of uploaded file
     */
    filePickerCallback = (params) => {
        if (params.url) {
            this.loadPreviewThumbnail(params.url);
        }
    };

    /**
     * Updates the content of the loader icon.
     *
     * @param {HTMLElement} root - The root element containing the loader icon.
     * @param {object} langStrings - An object containing language strings.
     * @param {number|null} progress - The progress percentage (optional).
     */
    updateLoaderIcon = (root, langStrings, progress = null) => {
        const loaderIconState = root.querySelector(Selectors.EMBED.elements.loaderIconContainer + ' div');
        loaderIconState.innerHTML = (progress !== null) ?
                               `${langStrings.uploading} ${Math.round(progress)}%` :
                               langStrings.loadingembedthumbnail;
    };

    /**
     * Handles changes in the media URL input field and loads a preview of the media if the URL has changed.
     */
    urlChanged() {
        const url = this.thumbnailModalRoot.querySelector(Selectors.EMBED.elements.fromUrl).value;
        if (url && url !== this.currentUrl) {
            this.loadPreviewThumbnail(url);
        }
    }

    /**
     * Handles the uploaded file, initiates the upload process, and updates the UI during the upload.
     *
     * @param {FileList} files - The list of files to upload (usually from a file input field).
     * @returns {Promise<void>} A promise that resolves when the file is uploaded and processed.
     */
    handleUploadedFile = async(files) => {
        try {
            startMediaLoading(this.thumbnailModalRoot, Selectors.EMBED.type);
            const fileURL = await uploadFile(this.editor, 'image', files[0], files[0].name, (progress) => {
                this.updateLoaderIcon(this.thumbnailModalRoot, this.langStrings, progress);
            });

            // Set the loader icon content to "loading" after the file upload completes.
            this.updateLoaderIcon(this.thumbnailModalRoot, this.langStrings);
            this.filePickerCallback({url: fileURL});
        } catch (error) {
            // Handle the error.
            const urlWarningLabelEle = this.thumbnailModalRoot.querySelector(Selectors.EMBED.elements.urlWarning);
            urlWarningLabelEle.innerHTML = error.error !== undefined ? error.error : error;
            showElements(Selectors.EMBED.elements.urlWarning, this.thumbnailModalRoot);
            stopMediaLoading(this.thumbnailModalRoot, Selectors.EMBED.type);
        }
    };

    /**
     * Registers events for insert thumbnail modal.
     *
     * @param {HTMLElement} root - The root element containing the loader icon.
     */
    registerInsertMediaThumbnailEvents = (root) => {
        const urlEle = root.querySelector(Selectors.EMBED.elements.fromUrl);
        if (urlEle) {
            urlEle.addEventListener('input', () => {
                (new EmbedHandler(this)).toggleUrlButton(urlEle, this.thumbnailModalRoot);
            });
        }

        // Handles add media url.
        const addUrlEle = root.querySelector(Selectors.EMBED.actions.addUrl);
        if (addUrlEle) {
            addUrlEle.addEventListener('click', () => {
                startMediaLoading(this.thumbnailModalRoot, Selectors.EMBED.type);
                this.urlChanged();
            });
        }

        // Handle repository browsing.
        const imageBrowser = root.querySelector(Selectors.IMAGE.actions.imageBrowser);
        if (imageBrowser) {
            imageBrowser.addEventListener('click', async(e) => {
                e.preventDefault();
                const params = await displayFilepicker(this.editor, 'image');
                this.filePickerCallback(params);
            });
        }
    };
}
