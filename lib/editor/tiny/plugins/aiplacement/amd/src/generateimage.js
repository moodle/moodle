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
 * Tiny AI generate images.
 *
 * @module      tiny_aiplacement/generateimage
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ImageModal from 'tiny_aiplacement/imagemodal';
import Ajax from 'core/ajax';
import {getString} from 'core/str';
import Templates from 'core/templates';
import AiMediaImage from './mediaimage';
import {getContextId} from 'tiny_aiplacement/options';
import GenerateBase from 'tiny_aiplacement/generatebase';

export default class GenerateImage extends GenerateBase {
    SELECTORS = {
        GENERATEBUTTON: () => `[id="${this.editor.id}_tiny_aiplacement_generatebutton"]`,
        PROMPTAREA: () => `[id="${this.editor.id}_tiny_aiplacement_imageprompt"]`,
        IMAGECONTAINER: () => `[id="${this.editor.id}_tiny_aiplacement_generate_image"]`,
        GENERATEBTN: '[data-action="generate"]',
        INSERTBTN: '[data-action="inserter"]',
        BACKTBTN: '[data-action="back"]',
        GENERATEDIMAGE: () => `[id="${this.editor.id}_tiny_generated_image"]`,
    };

    imageURL = null;

    getModalClass() {
        return ImageModal;
    }

    /**
     * Handle click events within the image modal.
     *
     * @param {Event} e - The click event object.
     * @param {HTMLElement} root - The root element of the modal.
     */
    handleContentModalClick(e, root) {
        const actions = {
            generate: () => this.handleSubmit(root, e.target),
            inserter: () => this.handleInsert(),
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

        const displayArgs = this.getDisplayArgs(root);
        const request = {
            methodname: 'aiplacement_editor_generate_image',
            args: displayArgs
        };

        try {
            this.responseObj = await Ajax.call([request])[0];
            if (this.responseObj.error) {
                this.handleGenerationError(root, submitBtn, this.responseObj.error, this.responseObj.errormessage);
            } else {
                await this.displayGeneratedImage(root);
                await this.hideLoading(root, submitBtn);
                // Focus the container for accessibility.
                const imageDisplayContainer = root.querySelector(this.SELECTORS.IMAGECONTAINER());
                imageDisplayContainer.focus();
            }
        } catch (error) {
            this.handleGenerationError(root, submitBtn);
        }
    }

    /**
     * Handle the insert action.
     *
     */
    async handleInsert() {
        // Use the revised prompt for the image alt text if it is available in the response.
        const revisedPrompt = this.responseObj.revisedprompt;
        const altTextToUse = revisedPrompt ? revisedPrompt : this.promptText;
        const mediaImage = new AiMediaImage(this.editor, this.imageURL, altTextToUse);
        await mediaImage.displayDialogue();
        this.modalObject.destroy();
    }

    /**
     * Handle a generation error.
     *
     * @param {Object} root The root element of the modal.
     * @param {Object} submitBtn The submit button element.
     * @param {String} error The error name to display.
     * @param {String} errorMessage The error message to display.
     */
    async handleGenerationError(root, submitBtn, error = '', errorMessage = '') {
        if (!error) {
            // Get the default error message.
            error = await getString('error:defaultname', 'core_ai');
            errorMessage = await getString('error:defaultmessage', 'core_ai');
        }
        const backBtn = root.querySelector(this.SELECTORS.BACKTBTN);
        const generateBtn = root.querySelector(this.SELECTORS.GENERATEBUTTON());
        backBtn.classList.remove('hidden');
        generateBtn.classList.add('hidden');
        await this.hideLoading(root, submitBtn);
        this.modalObject.setBody(Templates.render('tiny_aiplacement/modalbodyerror',
            {'error': error, 'errorMessage': errorMessage}));
        // Focus the back button for accessibility.
        backBtn.focus();
    }

    /**
     * Display the generated image in the modal.
     *
     * @param {HTMLElement} root - The root element of the modal.
     */
    async displayGeneratedImage(root) {
        const imageDisplayContainer = root.querySelector(this.SELECTORS.IMAGECONTAINER());
        const insertBtn = root.querySelector(this.SELECTORS.INSERTBTN);
        // Set the draft URL as it's used elsewhere.
        this.imageURL = this.responseObj.drafturl;

        // Render the image template and insert it into the modal.
        imageDisplayContainer.innerHTML = await Templates.render('tiny_aiplacement/image', {
            url: this.responseObj.drafturl,
            elementid: this.editor.id,
            alt: this.promptText,
        });
        const imagElement = root.querySelector(this.SELECTORS.GENERATEDIMAGE());

        return new Promise((resolve, reject) => {
            imagElement.onload = () => {
                insertBtn.classList.remove('hidden');
                imagElement.focus();
                resolve(); // Resolve the promise when the image is loaded.
            };
            imagElement.onerror = (error) => {
                reject(error); // Reject the promise if there is an error loading the image.
            };
        });
    }

    /**
     * Get the display args for the image.
     *
     * @param {Object} root The root element of the modal.
     */
    getDisplayArgs(root) {
        const contextId = getContextId(this.editor);
        const promptText = root.querySelector(this.SELECTORS.PROMPTAREA()).value;
        this.promptText = promptText;

        const aspectRatio = this.getSelectedRadioValue('aspect-ratio', 'square');
        const imageQuality = this.getSelectedRadioValue('quality', 'standard');

        return {
            contextid: contextId,
            prompttext: promptText,
            aspectratio: aspectRatio,
            quality: imageQuality,
            numimages: 1
        };
    }

    /**
     * Get the value of the selected radio button.
     *
     * @param {String} radioName The name of the radio button group.
     * @param {String} defaultValue The default value of the radio button.
     */
    getSelectedRadioValue(radioName, defaultValue = null) {
        const radios = document.getElementsByName(radioName);
        for (const radio of radios) {
            if (radio.checked) {
                return radio.value;
            }
        }
        return defaultValue;
    }
}
