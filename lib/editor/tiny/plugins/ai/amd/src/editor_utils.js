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
 * Editor instance specific utils.
 *
 * @module      tiny_ai/editor_utils
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import AiModal from 'tiny_ai/modal';
import ModalEvents from 'core/modal_events';
import {getUserId} from 'tiny_ai/options';
import {constants} from 'tiny_ai/constants';
import {selectionbarSource, toolbarSource, menubarSource} from 'tiny_ai/common';
import {getDraftItemId as getDraftItemIdTinyCore, getContextId as getContextItemIdTinyCore} from 'editor_tiny/options';
import {getRenderer, getDatamanager} from 'tiny_ai/utils';

export default class {

    uniqid = null;
    userId = null;
    modal = null;
    mode = null;
    editor = null;

    constructor(uniqid, editor) {
        this.uniqid = uniqid;
        this.editor = editor;
        this.userId = getUserId(editor);
    }

    /**
     * Shows and handles the dialog.
     *
     * @param {string} source the different sources from where the modal is being created, defined in common module
     */
    async displayDialogue(source) {
        if (source === selectionbarSource || this.editor.selection.getContent().length > 0) {
            this.mode = constants.modalModes.selection;
        } else if (source === toolbarSource || source === menubarSource) {
            this.mode = constants.modalModes.general;
        }

        // We initially render the modal without content, because we need to rerender it anyway.
        this.modal = await AiModal.create({
            templateContext: {
                classes: 'tiny_ai-modal--dialog',
                headerclasses: 'tiny_ai-modal--header'
            }
        });
        this.modal.show();
        const renderer = getRenderer(this.uniqid);

        getDatamanager(this.uniqid).setSelectionImg(null);
        if (this.mode === constants.modalModes.selection) {
            const selectedEditorContentHtml = this.editor.selection.getContent({format: 'html'});
            const parser = new DOMParser();
            const editorDom = parser.parseFromString(selectedEditorContentHtml, 'text/html');
            const images = editorDom.querySelectorAll('img');

            if (images.length > 0 && images[0].src) {
                // If there are more than one we just use the first one.
                const image = images[0];
                // This should work for both external and data urls.
                const fetchResult = await fetch(image.src);
                const data = await fetchResult.blob();
                getDatamanager(this.uniqid).setSelectionImg(data);
            }
            getDatamanager(this.uniqid).setSelection(this.editor.selection.getContent());
        }
        // Unfortunately, the modal will not execute any JS code in the template, so we need to rerender the modal as a whole again.
        await renderer.renderStart();
        this.modal.getRoot().on(ModalEvents.outsideClick, event => {
            event.preventDefault();
        });
    }


    insertAfterContent(textToInsert) {
        this.editor.setContent(this.editor.getContent() + '<p>' + textToInsert + '</p>');
    }

    /**
     * Replaces a selected text with the given replacement.
     *
     * In case nothing is selected, it will be inserted at the current caret position.
     *
     * @param {strings} textReplacement the text by which the current selection will be replaced or which will be inserted
     *  at the caret (if no selection), can be HTML code
     */
    replaceSelection(textReplacement) {
        this.editor.selection.setContent(textReplacement);
    }

    getDraftItemId() {
        return getDraftItemIdTinyCore(this.editor);
    }

    getContextId() {
        return getContextItemIdTinyCore(this.editor);
    }

    getMode() {
        return this.mode;
    }

    getModal() {
        return this.modal;
    }

    getUserId() {
        return this.userId;
    }

}
