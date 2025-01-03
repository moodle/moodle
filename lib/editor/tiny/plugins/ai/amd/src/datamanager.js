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

import {getEditorUtils} from 'tiny_ai/utils';

/**
 * Tiny AI data manager.
 *
 * @module      tiny_ai/datamanager
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class DataManager {

    uniqid = null;

    constructor(uniqid) {
        this.uniqid = uniqid;
        this.eventEmitterElement = document.createElement('div');
    }

    currentTool = null;
    currentAiResult = null;
    prompt = null;
    file = null;
    options = null;
    selection = null;
    selectionImg = null;

    getDefaultOptions() {
        const defaultOptions = {
            component: 'tiny_ai',
            itemid: getEditorUtils(this.uniqid).getDraftItemId(),
            contextid: getEditorUtils(this.uniqid).getContextId()
        };
        if (['tts', 'audiogen'].includes(this.getCurrentTool())) {
            defaultOptions.filename = 'audio_' + Math.random().toString(16).slice(2) + '.mp3';
        } else if (this.getCurrentTool() === 'imggen') {
            defaultOptions.filename = 'img_' + Math.random().toString(16).slice(2) + '.png';
        }
        return defaultOptions;
    }

    setCurrentTool(currentTool) {
        this.currentTool = currentTool;
    }

    getCurrentTool() {
        return this.currentTool;
    }

    setCurrentPrompt(prompt) {
        this.prompt = prompt;
        const promptUpdatedEvent = new CustomEvent('promptUpdated', {
            detail: {
                newPrompt: prompt
            }
        });
        this.eventEmitterElement.dispatchEvent(promptUpdatedEvent);
    }

    getCurrentPrompt() {
        return this.prompt;

    }

    setCurrentFile(file) {
        this.file = file;
    }

    getCurrentFile() {
        return this.file;

    }

    getSelection() {
        return this.selection;
    }

    getSelectionText() {
        const span = document.createElement('span');
        span.innerHTML = this.selection;
        return span.textContent;
    }

    setSelection(selection) {
        this.selection = selection;
    }

    getSelectionImg() {
        return this.selectionImg;
    }

    setSelectionImg(image) {
        this.selectionImg = image;
    }

    getEventEmitterElement() {
        return this.eventEmitterElement;
    }

    setCurrentAiResult(aiResult) {
        this.currentAiResult = aiResult;
    }

    getCurrentAiResult() {
        return this.currentAiResult;
    }

    setCurrentOptions(options) {
        this.options = options;
    }

    getCurrentOptions() {
        const optionsToReturn = this.options === null ? {} : this.options;
        Object.assign(optionsToReturn, this.getDefaultOptions());
        return optionsToReturn;
    }

    reset() {
        this.setCurrentPrompt('');
        this.setCurrentOptions(null);
        this.setCurrentTool(null);
        this.setCurrentAiResult(null);
        this.setCurrentFile(null);
    }
}
