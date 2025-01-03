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
 * Controller for handling the show/hide prompt button and the associated textarea.
 *
 * @module      tiny_ai/controllers/file
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getDatamanager, getCurrentModalUniqId, getIttHandler} from 'tiny_ai/utils';
import Templates from 'core/templates';
import SELECTORS from 'tiny_ai/selectors';
import {errorAlert} from 'tiny_ai/utils';
import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import {getString} from 'core/str';


export default class {

    dropzone = null;
    dropzoneContentToResetTo = '';

    constructor(baseSelector) {
        this.baseElement = document.querySelector(baseSelector);
    }

    async init() {
        this.dropzone = this.baseElement.querySelector('[data-type="dropzone"]');
        const dropzone = this.dropzone;
        // Setting contentEditable to true makes the browser show a "paste" option in the context menu when
        // right-clicking the drop zone.
        dropzone.contentEditable = true;
        this.setDropzoneContent(dropzone.innerHTML);
        // Instantly focus the drop zone, so you can directly paste the image.
        dropzone.focus();

        const _this = this;
        // The drop zone has "contentEditable" enabled, so we have to take care of user input
        // and reset the content whenever a user tries to input something.
        dropzone.addEventListener('input', () => {
            if (dropzone.innerHTML !== _this.dropzoneContentToResetTo) {
                dropzone.innerHTML = _this.dropzoneContentToResetTo;
            }
        });
        dropzone.addEventListener('drop', async(event) => {
            event.preventDefault();

            if (event.dataTransfer.items) {
                // Use DataTransferItemList interface to access the file(s)
                const item = [...event.dataTransfer.items].shift();
                // If dropped item is no file, reject it.
                if (item.kind === 'file') {
                    await this.handleFile(item.getAsFile());
                }
            } else {
                // Use DataTransfer interface to access the file(s)
                await this.handleFile([...event.dataTransfer.files].shift());
            }
        });

        const datamanager = getDatamanager(getCurrentModalUniqId(this.baseElement));

        const handlePaste = async(event) => {
            // We have to be careful. We are registering this listener globally onto the modal dialog to catch all the
            // paste events. We have to ensure we do not interfere with pasting into text fields of other tools though.
            if (['describeimg', 'imagetotext'].includes(datamanager.getCurrentTool())) {
                event.preventDefault();
                const clipboardData = (event.clipboardData || window.clipboardData);
                if (clipboardData.files.length === 0) {
                    await errorAlert(BasedataHandler.getTinyAiString('error_nofileinclipboard_text'),
                        BasedataHandler.getTinyAiString('error_nofileinclipboard_title'));
                    return;
                }
                const file = clipboardData.files[0];
                this.handleFile(file);
            }
        };
        // Avoid re-adding event paste listener.
        document.querySelector(SELECTORS.modalDialog).removeEventListener('paste', handlePaste);
        document.querySelector(SELECTORS.modalDialog).addEventListener('paste', handlePaste);
        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.remove('tiny_ai_dropzone_filled');
            dropzone.classList.add('tiny_ai_dragover');
        });
        dropzone.addEventListener('dragleave', (event) => {
            event.preventDefault();
            dropzone.classList.remove('tiny_ai_dragover');
        });

        if (datamanager.getSelectionImg() !== null) {
            await this.handleFile(datamanager.getSelectionImg());
        }
    }

    async handleFile(file) {
        const reader = new FileReader();
        const _this = this;
        reader.addEventListener(
            'load',
            async() => {
                const currentModalUniqid = getCurrentModalUniqId(this.baseElement);
                const datamanager = getDatamanager(currentModalUniqid);
                const fileUploadedEvent = new CustomEvent('fileUploaded', {
                    detail: {
                        newFile: reader.result,
                    }
                });
                datamanager.getEventEmitterElement().dispatchEvent(fileUploadedEvent);
                const ittHandler = getIttHandler(currentModalUniqid);
                const allowedMimetypes = await ittHandler.getAllowedMimetypes();

                if (!allowedMimetypes.includes(file.type)) {
                    const errorTitle = await getString('error_unsupportedfiletype_title', 'tiny_ai');
                    const errorText = await getString('error_unsupportedfiletype_text', 'tiny_ai', allowedMimetypes.toString());
                    await errorAlert(errorText, errorTitle);
                    return;
                }

                const fileEntryTemplateContext = {
                    icon: file.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-image',
                    filename: file.name ? file.name : BasedataHandler.getTinyAiString('imagefromeditor'),
                };
                if (file.type.startsWith('image')) {
                    fileEntryTemplateContext.isImage = true;
                    fileEntryTemplateContext.dataurl = reader.result;
                }
                const {html, js} = await Templates.renderForPromise('tiny_ai/components/ai-file-list-entry',
                    fileEntryTemplateContext);
                _this.setDropzoneContent(html);
                // We probably have no JS, but let's be safe here.
                Templates.runTemplateJS(js);
                // There should be no tiny_ai_dragover class, just to be safe.
                _this.dropzone.classList.remove('tiny_ai_dragover');
                _this.dropzone.classList.add('tiny_ai_dropzone_filled');
            },
            false,
        );
        reader.readAsDataURL(file);
    }

    setDropzoneContent(html) {
        this.dropzone.innerHTML = html;
        // Keep track of the state.
        this.dropzoneContentToResetTo = html;
    }
}
