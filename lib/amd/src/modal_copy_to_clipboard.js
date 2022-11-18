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
 * Contain the logic for the copy to clipboard modal, i.e. the modal contains a
 * readonly input text field, that contains a value. Clicking on the single
 * button "Copy to clipboard" in the footer, puts the content of the input
 * text field into the clipboard and closes the modal.
 *
 * Usage:
 * ModalCopyToClipboard.create(string:<stringToCopy>, string:<modalTitle>|null);
 *
 * @module     core/modal_copy_to_clipboard
 * @copyright  2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';
import ModalRegistry from 'core/modal_registry';
import ModalFactory from 'core/modal_factory';
import 'core/copy_to_clipboard';

export default class CopyToClipboardModal extends Modal {
    static TYPE = 'core/copytoclipboard';
    static TEMPLATE = 'core/modal_copytoclipboard';

    constructor(...config) {
        // Override the constructor to set the removeOnClose property, and show the modal.
        super(...config);
        this.setRemoveOnClose(true);
    }

    /**
     * Set up all the event handling for the modal.
     * This is an override of the parent method, adding an event listener to close upon the action.
     *
     * @param {array} args
     */
    registerEventListeners(...args) {
        super.registerEventListeners(...args);

        this.getRoot().get(0).addEventListener('click', (e) => {
            if (!e.target.closest('[data-action="copytoclipboard"]')) {
                return;
            }

            if (!this.getRoot().get(0).contains(e.target)) {
                return;
            }

            // Note: We must call destroy() here, because the copy-to-clipboard action listens on the document,
            // which will be processed after this event listener has been processed.
            // By placing this in a setTimeout we move its processing to after the event loop has finished.
            setTimeout(this.destroy.bind(this));
        });
    }

    /**
     * Create a new instance of the Modal. Set the text that is being copied. By default, the text is put into the
     * value of an input readonly field. If useTextArea is set to true, the text is rendered in a textarea element.
     * The optional title argument is for the modal title. If not set, the generic string "copy to clipboard" is used.
     *
     * @param {Object} data used in the template
     * @param {string} data.text the text to copy to the clipboard
     * @param {boolean} data.useTextArea when the text to copy is displayed in a textarea, default is input
     * @param {string|null} title
     * @returns {Promise<void>}
     */
    static async create(
        {
            text,
            useTextArea = false,
        } = {},
        title
    ) {
        if (!ModalRegistry.get(this.TYPE)) {
            ModalRegistry.register(this.TYPE, this, this.TEMPLATE);
        }

        const modal = await ModalFactory.create({
            type: this.TYPE,
            templateContext: {
                text: text,
                useTextArea: useTextArea,
            },
        });
        if (title) {
            modal.setTitle(title);
        }
        modal.show();
    }
}