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
 * Tiny AI generate text.
 *
 * @module      tiny_aiplacement/generatetext
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import TextModal from './textmodal';
import Ajax from 'core/ajax';
import {getString} from 'core/str';
import Templates from 'core/templates';
import AIHelper from 'core_ai/helper';
import {getContextId} from './options';
import TinyAiTextMarker from './textmark';
import GenerateBase from './generatebase';

export default class GenerateText extends GenerateBase {
    SELECTORS = {
        GENERATEBUTTON: () => `[id="${this.editor.id}_tiny_aiplacement_generatebutton"]`,
        PROMPTAREA: () => `[id="${this.editor.id}_tiny_aiplacement_textprompt"]`,
        TEXTCONTAINER: () => `[id="${this.editor.id}_tiny_aiplacement_generate_text"]`,
        GENERATEDRESPONSE: () => `[id="${this.editor.id}_tiny_aiplacement_textresponse"]`,
        INSERTBTN: '[data-action="inserter"]',
        BACKTBTN: '[data-action="back"]',
    };

    getModalClass() {
        return TextModal;
    }

    /**
     * Handle click events within the text modal.
     *
     * @param {Event} e - The click event object.
     * @param {HTMLElement} root - The root element of the modal.
     */
    handleContentModalClick(e, root) {
        const actions = {
            generate: () => this.handleSubmit(root, e.target),
            inserter: () => this.handleInsert(root, e.target),
            cancel: () => this.modalObject.destroy(),
            back: () => {
                this.modalObject.destroy();
                this.displayContentModal();
            },
        };

        const actionKey = Object.keys(actions).find(key => e.target.closest(`[data-action="${key}"]`));
        if (actionKey) {
            e.preventDefault();
            actions[actionKey]();
        }
    }

    /**
     * Set up the prompt area in the modal, adding necessary event listeners.
     *
     * @param {HTMLElement} root - The root element of the modal.
     */
    setupPromptArea(root) {
        const generateBtn = root.querySelector(this.SELECTORS.GENERATEBUTTON());
        const promptArea = root.querySelector(this.SELECTORS.PROMPTAREA());

        promptArea.addEventListener('input', () => {
            generateBtn.disabled = promptArea.value.trim() === '';
        });
    }

    /**
     * Handle the submit action.
     *
     * @param {Object} root The root element of the modal.
     * @param {Object} submitBtn The submit button element.
     */
    async handleSubmit(root, submitBtn) {
        await this.displayLoading(root, submitBtn);

        const requestArgs = this.getRequestArgs(root);
        const request = {
            methodname: 'aiplacement_editor_generate_text',
            args: requestArgs
        };

        try {
            this.responseObj = await Ajax.call([request])[0];
            if (this.responseObj.error) {
                this.handleGenerationError(root, submitBtn, '');
            } else {
                await this.displayGeneratedText(root);
                await this.hideLoading(root, submitBtn);
                // Focus the container for accessibility.
                const textDisplayContainer = root.querySelector(this.SELECTORS.TEXTCONTAINER());
                textDisplayContainer.focus();
            }
        } catch (error) {
            this.handleGenerationError(root, submitBtn, '');
        }
    }

    /**
     * Handle the insert action.
     *
     * @param {Object} root The root element of the modal.
     * @param {HTMLElement} submitBtn - The submit button element.
     */
    async handleInsert(root, submitBtn) {
        await this.displayLoading(root, submitBtn);

        // Update the generated response with the content from the form.
        // In case the user has edited the response.
        const generatedResponseDiv = root.querySelector(this.SELECTORS.GENERATEDRESPONSE());

        // Wrap the edited sections in the response with tags.
        // This is so we can differentiate between the edited sections and the generated content.
        const wrappedEditedResponse = await TinyAiTextMarker.wrapEditedSections(
            this.responseObj.generatedcontent,
            generatedResponseDiv.value)
        ;

        // Replace double line breaks with <br> and with </p><p> for paragraphs and removed any markdown.
        this.responseObj.editedtext = AIHelper.formatResponse(wrappedEditedResponse);

        // Generate the HTML for the response.
        const formattedResponse = await Templates.render('tiny_aiplacement/textinsert', this.responseObj);

        // Insert the response into the editor.
        this.editor.insertContent(formattedResponse);
        this.editor.execCommand('mceRepaint');
        this.editor.windowManager.close();

        // Close the modal and return to the editor.
        this.modalObject.hide();
    }

    /**
     * Handle a generation error.
     *
     * @param {Object} root The root element of the modal.
     * @param {Object} submitBtn The submit button element.
     * @param {String} errorMessage The error message to display.
     */
    async handleGenerationError(root, submitBtn, errorMessage = '') {
        if (!errorMessage) {
            // Get the default error message.
            errorMessage = await getString('errorgeneral', 'tiny_aiplacement');
        }
        this.modalObject.setBody(await Templates.render('tiny_aiplacement/modalbodyerror', {'errorMessage': errorMessage}));
        const backBtn = root.querySelector(this.SELECTORS.BACKTBTN);
        const generateBtn = root.querySelector(this.SELECTORS.GENERATEBUTTON());
        backBtn.classList.remove('hidden');
        generateBtn.classList.add('hidden');
        await this.hideLoading(root, submitBtn);
        // Focus the back button for accessibility.
        backBtn.focus();
    }

    /**
     * Display the generated text in the modal.
     *
     * @param {HTMLElement} root - The root element of the modal.
     */
    async displayGeneratedText(root) {
        const textDisplayContainer = root.querySelector(this.SELECTORS.TEXTCONTAINER());
        const insertBtn = root.querySelector(this.SELECTORS.INSERTBTN);

        // Render the textarea template and insert it into the modal.
        textDisplayContainer.innerHTML = await Templates.render('tiny_aiplacement/textarea', {
            elementid: this.editor.id,
            text: this.responseObj.generatedcontent,
        });
        const textareaElement = root.querySelector(this.SELECTORS.GENERATEDRESPONSE());

        return new Promise((resolve, reject) => {
            if (textareaElement.textLength > 0) {
                insertBtn.classList.remove('hidden');
                textareaElement.focus();
                resolve();
            } else {
                reject();
            }
        });
    }

    /**
     * Get the request args for the generated text.
     *
     * @param {Object} root The root element of the modal.
     */
    getRequestArgs(root) {
        const contextId = getContextId(this.editor);
        const promptText = root.querySelector(this.SELECTORS.PROMPTAREA()).value;

        return {
            contextid: contextId,
            prompttext: promptText
        };
    }
}
