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
import {getEmbedPermissions} from './options';
import {getFilePicker} from 'editor_tiny/options';
import {EmbedHandler} from './embed/embedhandler';
import {
    insertMediaTemplateContext,
    getSelectedMediaElement,
} from './embed/embedhelpers';
import {EmbedInsert} from './embed/embedinsert';

export default class MediaEmbed {
    editor = null;
    canShowFilePicker = false;
    canShowFilePickerPoster = false;
    canShowFilePickerTrack = false;

    constructor(editor) {
        const permissions = getEmbedPermissions(editor);
        const options = getFilePicker(editor, 'media');

        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker && (typeof getFilePicker(editor, 'media') !== 'undefined');
        this.canShowFilePickerPoster = permissions.filepicker && (typeof getFilePicker(editor, 'image') !== 'undefined');
        this.canShowFilePickerTrack = permissions.filepicker && (typeof getFilePicker(editor, 'subtitle') !== 'undefined');
        this.canShowDropZone = Object.values(options.repositories).some(repository => repository.type === 'upload');
        this.editor = editor;
    }

    async displayDialogue() {
        const [mediaType, selectedMedia] = getSelectedMediaElement(this.editor);
        this.mediaType = mediaType;
        this.selectedMedia = selectedMedia;
        this.currentModal = await EmbedModal.create();
        this.root = this.currentModal.getRoot()[0];

        if (this.selectedMedia) {
            // Preview the selected media.
            this.isUpdating = true;
            this.loadSelectedMedia();
        } else {
            const embedHandler = new EmbedHandler(this);
            embedHandler.loadTemplatePromise(insertMediaTemplateContext(this));
            embedHandler.registerEventListeners(this.currentModal);
        }
    }

    loadSelectedMedia = () => {
        let mediaSource = null;
        if (this.mediaType === 'link') {
            mediaSource = this.selectedMedia.href;
        } else {
            mediaSource = this.selectedMedia.querySelector('source').src;
        }

        // Load media preview.
        const embedInsert = new EmbedInsert(this);
        embedInsert.init();
        embedInsert.loadMediaPreview(mediaSource);
        (new EmbedHandler(this)).registerEventListeners(this.currentModal);
    };
}
