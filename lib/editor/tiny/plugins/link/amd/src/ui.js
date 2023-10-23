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
 * Tiny Link UI.
 *
 * @module      tiny_link/ui
 * @copyright   2023 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getFilePicker} from 'editor_tiny/options';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import {displayFilepicker} from 'editor_tiny/utils';
import LinkModal from 'tiny_link/modal';
import {getPermissions} from "tiny_link/options";
import {setLink, getCurrentLinkData, unSetLink} from "tiny_link/link";
import Selectors from 'tiny_link/selectors';

/**
 * Handle action.
 *
 * @param {TinyMCE} editor
 * @param {boolean} unlink
 */
export const handleAction = (editor, unlink = false) => {
    if (!unlink) {
        displayDialogue(editor);
    } else {
        unSetLink(editor);
    }
};

/**
 * Display the link dialogue.
 *
 * @param {TinyMCE} editor
 * @returns {Promise<void>}
 */
const displayDialogue = async(editor) => {
    const modalPromises = await ModalFactory.create({
        type: LinkModal.TYPE,
        templateContext: getTemplateContext(editor),
        large: false,
    });

    modalPromises.show();
    const $root = await modalPromises.getRoot();
    const root = $root[0];
    const currentForm = root.querySelector('form');

    $root.on(ModalEvents.hidden, () => {
        modalPromises.destroy();
    });

    root.addEventListener('click', (e) => {
        const submitAction = e.target.closest(Selectors.actions.submit);
        const linkBrowserAction = e.target.closest(Selectors.actions.linkBrowser);
        if (submitAction) {
            e.preventDefault();
            setLink(currentForm, editor);
            modalPromises.destroy();
        }
        if (linkBrowserAction) {
            e.preventDefault();
            displayFilepicker(editor, 'link').then((params) => {
                filePickerCallback(params, currentForm, editor);
                return modalPromises.destroy();
            }).catch();
        }
    });

    const linkTitle = root.querySelector(Selectors.elements.urlText);
    const linkUrl = root.querySelector(Selectors.elements.urlEntry);
    linkTitle.addEventListener('change', () => {
        if (linkTitle.value.length > 0) {
            linkTitle.dataset.useLinkAsText = 'false';
        } else {
            linkTitle.dataset.useLinkAsText = 'true';
            linkTitle.value = linkUrl.value;
        }
    });

    linkUrl.addEventListener('keyup', () => {
        updateTextToDisplay(currentForm);
    });
};

/**
 * Get template context.
 *
 * @param {TinyMCE} editor
 * @returns {Object}
 */
const getTemplateContext = (editor) => {
    const data = getCurrentLinkData(editor);

    return Object.assign({}, {
        elementid: editor.id,
        showfilepicker: getPermissions(editor).filepicker &&
            (typeof getFilePicker(editor, 'link') !== 'undefined'),
        isupdating: Object.keys(data).length > 0,
    }, data);
};

/**
 * Update the dialogue after a link was selected in the File Picker.
 *
 * @param {Object} params
 * @param {Element} currentForm
 * @param {TinyMCE} editor
 */
const filePickerCallback = (params, currentForm, editor) => {
    if (params.url) {
        const inputUrl = currentForm.querySelector(Selectors.elements.urlEntry);
        inputUrl.value = params.url;
        setLink(currentForm, editor);
    }
};

/**
 * Update the text to display if the user does not provide the custom text.
 *
 * @param {Element} currentForm
 */
const updateTextToDisplay = (currentForm) => {
    const urlEntry = currentForm.querySelector(Selectors.elements.urlEntry);
    const urlText = currentForm.querySelector(Selectors.elements.urlText);
    if (urlText.dataset.useLinkAsText === 'true') {
        urlText.value = urlEntry.value;
    }
};
