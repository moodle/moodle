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
 * @module      tiny_ai/controllers/start
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BaseController from 'tiny_ai/controllers/base';
import {getStartHandler} from 'tiny_ai/utils';
import {errorAlert} from 'tiny_ai/utils';
// We unfortunately need jquery for tooltip handling.
import $ from 'jquery';

export default class extends BaseController {

    async init() {
        if (!this.baseElement) {
            // In rare cases (display error messages etc.) we do not have a correct modal, so there is nothing to do here.
            return;
        }
        const summarizeButton = this.baseElement.querySelector('[data-action="loadsummarize"]');
        const translateButton = this.baseElement.querySelector('[data-action="loadtranslate"]');
        const describeButton = this.baseElement.querySelector('[data-action="loaddescribe"]');
        const ttsButton = this.baseElement.querySelector('[data-action="loadtts"]');
        const audiogenButton = this.baseElement.querySelector('[data-action="loadaudiogen"]');
        const imggenButton = this.baseElement.querySelector('[data-action="loadimggen"]');
        const freePromptButton = this.baseElement.querySelector('[data-action="loadfreeprompt"]');
        const describeimgButton = this.baseElement.querySelector('[data-action="loaddescribeimg"]');
        const imagetotextButton = this.baseElement.querySelector('[data-action="loadimagetotext"]');

        const startHandler = getStartHandler(this.uniqid);

        if (!(await startHandler.isTinyAiDisabled())) {
            if (window.matchMedia("(pointer: coarse)").matches) {
                // If we have a touch device, we need to manually trigger the tooltips by touching the cards.
                document.querySelectorAll('.tiny_ai-card-button.disabled').forEach(button => {
                    button.parentElement.addEventListener(
                        'click', async() => {
                            $(button).tooltip('toggle');
                        });
                });
            }
        }

        if (summarizeButton) {
            summarizeButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('summarize');
                await this.renderer.renderSummarize();
            });
        }
        if (translateButton) {
            translateButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('translate');
                await this.renderer.renderTranslate();
            });
        }
        if (describeButton) {
            describeButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('describe');
                await this.renderer.renderDescribe();
            });
        }
        if (ttsButton) {
            ttsButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('tts');
                await this.renderer.renderTts();
            });
        }
        if (audiogenButton) {
            audiogenButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('audiogen');
                await this.renderer.renderAudiogen();
            });
        }
        if (imggenButton) {
            imggenButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('imggen');
                await this.renderer.renderImggen();
            });
        }
        if (describeimgButton) {
            describeimgButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('describeimg');
                await this.renderer.renderDescribeimg();
            });
        }
        if (imagetotextButton) {
            imagetotextButton.addEventListener('click', async() => {
                this.datamanager.setCurrentTool('imagetotext');
                await this.renderer.renderImagetotext();
            });
        }
        if (freePromptButton) {
            if (!freePromptButton.classList.contains('disabled')) {
                freePromptButton.addEventListener('click', async() => {
                    this.datamanager.setCurrentTool('freeprompt');
                    this.datamanager.setCurrentPrompt(this.baseElement.querySelector('[data-type="freepromptinput"]').value);
                    const result = await this.generateAiAnswer();
                    if (result === null) {
                        return;
                    }
                    await this.renderer.renderSuggestion();
                });
            } else {
                if (!(await startHandler.isTinyAiDisabled())) {
                    freePromptButton.addEventListener('click', async() => {
                        await errorAlert(startHandler.isToolDisabled('freeprompt', this.editorUtils.getMode()));
                    });
                }
            }
        }
    }
}
