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
 * Tiny Media plugin Embed class for Moodle.
 *
 * @module      tiny_media/embed
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import EmbedModal from './embedmodal';
import {getEmbedPermissions} from '../options';
import {getFilePicker, getContextId} from 'editor_tiny/options';
import {EmbedHandler} from './embedhandler';
import {insertMediaTemplateContext, getSelectedMediaElement} from './embedhelpers';
import {EmbedInsert} from './embedinsert';
import {startMediaLoading} from '../helpers';
import Selectors from "../selectors";

export default class MediaEmbed {
    editor = null;
    canShowFilePicker = false;
    canShowFilePickerTrack = false;
    canShowDropZone = false;

    constructor(editor) {
        const permissions = getEmbedPermissions(editor);
        const options = getFilePicker(editor, 'media');

        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker
            && (typeof options !== 'undefined')
            && Object.keys(options.repositories).length > 0;
        this.canShowFilePickerTrack = permissions.filepicker && (typeof getFilePicker(editor, 'subtitle') !== 'undefined');
        this.canShowDropZone = Object.values(options.repositories).some(repository => repository.type === 'upload');
        this.editor = editor;
        this.acceptedMediaTypes = options.accepted_types;
        this.contextId = getContextId(editor);

        // Image options.
        const imageOptions = getFilePicker(editor, 'image');
        this.acceptedImageTypes = imageOptions.accepted_types;
        this.canShowImageFilePicker = permissions.filepicker
            && (typeof imageOptions !== 'undefined')
            && Object.keys(imageOptions.repositories).length > 0;
    }

    /**
     * Displays media modal accordingly.
     */
    displayDialogue = async() => {
        const [mediaType, selectedMedia] = getSelectedMediaElement(this.editor);
        this.mediaType = mediaType;
        this.selectedMedia = selectedMedia;

        if (this.selectedMedia) {
            // Preview the selected media.
            this.isUpdating = true;
            this.loadSelectedMedia();
        } else {
            // Create media modal.
            await this.createMediaModal();
            // Load insert media modal.
            await this.loadInsertMediaModal();
        }
    };

    /**
     * Load insert media modal.
     */
    loadInsertMediaModal = async() => {
        const embedHandler = new EmbedHandler(this);
        embedHandler.loadTemplatePromise(insertMediaTemplateContext(this));
        await embedHandler.registerEventListeners();
    };

    /**
     * Create media modal.
     */
    createMediaModal = async() => {
        this.currentModal = await EmbedModal.create({
            large: true,
            templateContext: {elementid: this.editor.getElement().id},
        });
        this.modalRoot = this.currentModal.getRoot();
        this.root = this.modalRoot[0];
    };

    /**
     * Load media preview based on the selected media.
     */
    loadSelectedMedia = async() => {
        let mediaSource = null;
        if (['video', 'audio'].includes(this.mediaType)) {
            mediaSource = this.selectedMedia.querySelector('source').src;
            // If the selected media has more than one sources, it has main source and alternative sources.
            const sources = this.selectedMedia.querySelectorAll('source');
            if (sources.length > 1) {
                let alternativeSources = [];
                Object.keys(sources).forEach(function(source) {
                    alternativeSources.push(sources[source].src);
                });
                this.alternativeSources = alternativeSources; // Used to later check if the embedded media has alternative sources.
            }
        } else if (this.selectedMedia.classList.contains(Selectors.EMBED.externalMediaProvider)) {
            mediaSource = this.selectedMedia.href;
            this.mediaType = 'link';
        }

        // Load media preview.
        if (this.mediaType) {
            // Create media modal.
            await this.createMediaModal();
            // Start the spinner.
            startMediaLoading(this.root, Selectors.EMBED.type);

            const embedInsert = new EmbedInsert(this);
            embedInsert.init();
            embedInsert.loadMediaPreview(mediaSource);
            await (new EmbedHandler(this)).registerEventListeners();
        } else {
            // Create media modal.
            await this.createMediaModal();
            // Load insert media modal.
            this.loadInsertMediaModal();
        }
    };
}
