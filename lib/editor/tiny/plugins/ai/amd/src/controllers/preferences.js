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
 * Controller for the main selection.
 *
 * @module      tiny_ai/controllers/preferences
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {constants} from 'tiny_ai/constants';
import SELECTORS from 'tiny_ai/selectors';
import BaseController from 'tiny_ai/controllers/base';
import {getSummarizeHandler, getTranslateHandler, getTtsHandler, getImggenHandler, getIttHandler} from 'tiny_ai/utils';

export default class extends BaseController {

    async init() {
        const modalFooter = document.querySelector(SELECTORS.modalFooter);
        const backButton = modalFooter.querySelector('[data-action="back"]');
        const generateButton = modalFooter.querySelector('[data-action="generate"]');

        const [summarizeHandler, translateHandler, ttsHandler, imggenHandler, ittHandler] = [
            getSummarizeHandler(this.uniqid),
            getTranslateHandler(this.uniqid),
            getTtsHandler(this.uniqid),
            getImggenHandler(this.uniqid),
            getIttHandler(this.uniqid)
        ];

        switch (this.datamanager.getCurrentTool()) {
            case 'summarize':
            case 'describe': {
                summarizeHandler.setTool(this.datamanager.getCurrentTool());
                const maxWordCountElement = this.baseElement.querySelector('[data-preference="maxWordCount"]');
                const languageTypeElement = this.baseElement.querySelector('[data-preference="languageType"]');
                summarizeHandler.setMaxWordCount(maxWordCountElement.querySelector('[data-dropdown="select"]').dataset.value);
                summarizeHandler.setLanguageType(languageTypeElement.querySelector('[data-dropdown="select"]').dataset.value);
                const currentPromptSummarize = await summarizeHandler.getPrompt(this.datamanager.getSelectionText());
                this.datamanager.setCurrentPrompt(currentPromptSummarize);
                maxWordCountElement.addEventListener('dropdownSelectionUpdated', async(event) => {
                    summarizeHandler.setMaxWordCount(event.detail.newValue);
                    const currentPrompt = await summarizeHandler.getPrompt(this.datamanager.getSelectionText());
                    this.datamanager.setCurrentPrompt(currentPrompt);
                });
                languageTypeElement.addEventListener('dropdownSelectionUpdated', async(event) => {
                    summarizeHandler.setLanguageType(event.detail.newValue);
                    const currentPrompt = await summarizeHandler.getPrompt(this.datamanager.getSelectionText());
                    this.datamanager.setCurrentPrompt(currentPrompt);
                });
                break;
            }
            case 'translate': {
                const targetLanguageElement = this.baseElement.querySelector('[data-preference="targetLanguage"]');
                translateHandler.setTargetLanguage(targetLanguageElement.querySelector('[data-dropdown="select"]').dataset.value);
                const currentPromptTranslate = await translateHandler.getPrompt(this.datamanager.getSelectionText());
                this.datamanager.setCurrentPrompt(currentPromptTranslate);
                targetLanguageElement.addEventListener('dropdownSelectionUpdated', async(event) => {
                    translateHandler.setTargetLanguage(event.detail.newValue);
                    const currentPromptTranslate = await translateHandler.getPrompt(this.datamanager.getSelectionText());
                    this.datamanager.setCurrentPrompt(currentPromptTranslate);
                });
                break;
            }
            case 'tts':
            case 'audiogen': {
                const ttsTargetLanguageElement = this.baseElement.querySelector('[data-preference="targetLanguage"]');
                const voiceElement = this.baseElement.querySelector('[data-preference="voice"]');
                const genderElement = this.baseElement.querySelector('[data-preference="gender"]');
                if (ttsTargetLanguageElement) {
                    ttsHandler.setTargetLanguage(ttsTargetLanguageElement.querySelector('[data-dropdown="select"]').dataset.value);
                    ttsTargetLanguageElement.addEventListener('dropdownSelectionUpdated', event => {
                        ttsHandler.setTargetLanguage(event.detail.newValue);
                        this.datamanager.setCurrentOptions(ttsHandler.getOptions());
                    });
                }
                if (voiceElement) {
                    ttsHandler.setVoice(voiceElement.querySelector('[data-dropdown="select"]').dataset.value);
                    voiceElement.addEventListener('dropdownSelectionUpdated', event => {
                        ttsHandler.setVoice(event.detail.newValue);
                        this.datamanager.setCurrentOptions(ttsHandler.getOptions());
                    });
                }
                if (genderElement) {
                    ttsHandler.setGender(genderElement.querySelector('[data-dropdown="select"]').dataset.value);
                    genderElement.addEventListener('dropdownSelectionUpdated', event => {
                        ttsHandler.setGender(event.detail.newValue);
                        this.datamanager.setCurrentOptions(ttsHandler.getOptions());
                    });
                }
                this.datamanager.setCurrentPrompt(ttsHandler.getPrompt(this.datamanager.getCurrentTool(),
                    this.datamanager.getSelectionText()));
                this.datamanager.setCurrentOptions(ttsHandler.getOptions());
                break;
            }
            case 'imggen': {
                const sizesElement = this.baseElement.querySelector('[data-preference="sizes"]');

                if (sizesElement) {
                    imggenHandler.setSize(sizesElement.querySelector('[data-dropdown="select"]').dataset.value);
                    sizesElement.addEventListener('dropdownSelectionUpdated', event => {
                        imggenHandler.setSize(event.detail.newValue);
                        this.datamanager.setCurrentOptions(imggenHandler.getOptions());
                    });
                }
                this.datamanager.setCurrentPrompt('');
                this.datamanager.setCurrentOptions(imggenHandler.getOptions());
                break;
            }
            case 'describeimg':
            case 'imagetotext': {
                const fileUploadArea = this.baseElement.querySelector('[data-preference="fileupload"]');
                if (fileUploadArea) {
                    this.datamanager.getEventEmitterElement().addEventListener('fileUploaded', async(event) => {
                        this.datamanager.setCurrentFile(event.detail.newFile);
                        this.datamanager.setCurrentOptions(ittHandler.getOptions());
                    });
                }
                this.datamanager.setCurrentPrompt(ittHandler.getPrompt(this.datamanager.getCurrentTool()));
                this.datamanager.setCurrentFile(null);
                break;
            }
        }

        if (backButton) {
            backButton.addEventListener('click', async() => {
                await this.renderer.renderStart(constants.modalModes.selection);
            });
        }

        if (generateButton) {
            generateButton.addEventListener('click', async() => {
                const result = await this.generateAiAnswer();
                if (result === null) {
                    return;
                }
                await this.renderer.renderSuggestion();
            });
        }
    }
}
