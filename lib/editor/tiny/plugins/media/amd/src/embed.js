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

import {getString} from 'core/str';
import * as ModalEvents from 'core/modal_events';
import EmbedModal from './embedmodal';
import {getEmbedPermissions} from './options';
import {getFilePicker} from 'editor_tiny/options';

export default class MediaEmbed {
    editor = null;
    canShowFilePicker = false;
    canShowFilePickerPoster = false;
    canShowFilePickerTrack = false;

    constructor(editor) {
        const permissions = getEmbedPermissions(editor);

        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker && (typeof getFilePicker(editor, 'media') !== 'undefined');
        this.canShowFilePickerPoster = permissions.filepicker && (typeof getFilePicker(editor, 'image') !== 'undefined');
        this.canShowFilePickerTrack = permissions.filepicker && (typeof getFilePicker(editor, 'subtitle') !== 'undefined');

        this.editor = editor;
    }

    async getTemplateContext() {
        return {
            elementid: this.editor.getElement().id,
            showfilepicker: this.canShowFilePicker,
            showfilepickerposter: this.canShowFilePickerPoster,
            showfilepickertrack: this.canShowFilePickerTrack,
        };
    }

    async displayDialogue() {
        this.currentModal = await EmbedModal.create({
            title: getString('createmedia', 'tiny_media'),
            templateContext: await this.getTemplateContext(),
        });

        await this.registerEventListeners(this.currentModal);
    }

    async registerEventListeners(modal) {
        await modal.getBody();
        const $root = modal.getRoot();

        $root.on(ModalEvents.hidden, () => {
            this.currentModal.destroy();
        });
    }
}
