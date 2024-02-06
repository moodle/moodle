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
 * Tiny Media plugin Image class for Moodle.
 *
 * @module      tiny_media/image
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ImageModal from './imagemodal';
import {getImagePermissions} from './options';
import {getFilePicker} from 'editor_tiny/options';
import {ImageInsert} from 'tiny_media/imageinsert';
import {
    bodyImageInsert,
    footerImageInsert,
} from 'tiny_media/imagehelpers';

export default class MediaImage {
    canShowFilePicker = false;
    editor = null;
    currentModal = null;
    /**
     * @type {HTMLElement|null} The root element.
     */
    root = null;

    constructor(editor) {
        const permissions = getImagePermissions(editor);
        const options = getFilePicker(editor, 'image');
        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker
            && (typeof options !== 'undefined')
            && Object.keys(options.repositories).length > 0;
        // Indicates whether the drop zone area can be shown.
        this.canShowDropZone = Object.values(options.repositories).some(repository => repository.type === 'upload');

        this.editor = editor;
    }

    async displayDialogue() {
        this.currentModal = await ImageModal.create();
        this.root = this.currentModal.getRoot()[0];
        this.loadInsertImage();
    }

    /**
     * Displays an insert image view asynchronously.
     *
     * @returns {Promise<void>}
     */
    loadInsertImage = async function() {
        const templateContext = {
            elementid: this.editor.id,
            showfilepicker: this.canShowFilePicker,
            showdropzone: this.canShowDropZone,
        };

        Promise.all([bodyImageInsert(templateContext, this.root), footerImageInsert(templateContext, this.root)])
            .then(() => {
                const imageinsert = new ImageInsert(
                    this.root,
                    this.editor,
                    this.currentModal,
                    this.canShowFilePicker,
                    this.canShowDropZone,
                );
                imageinsert.init();
                return;
            })
            .catch(error => {
                window.console.log(error);
            });
    };
}
