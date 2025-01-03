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

import * as AiConfig from 'local_ai_manager/config';
import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import BaseHandler from 'tiny_ai/datahandler/base';
import Config from 'core/config';

/**
 * Tiny AI data manager.
 *
 * @module      tiny_ai/datahandler/tts
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    ttsOptions = null;

    targetLanguage = null;
    voice = null;
    gender = null;

    async getTargetLanguageOptions() {
        await this.loadTtsOptions();
        return this.ttsOptions.languages;
    }

    async getVoiceOptions() {
        await this.loadTtsOptions();
        return this.ttsOptions.voices;
    }

    async getGenderOptions() {
        await this.loadTtsOptions();
        return this.ttsOptions.gender;
    }

    setTargetLanguage(targetLanguage) {
        this.targetLanguage = targetLanguage;
    }

    setVoice(voice) {
        this.voice = voice;
    }

    setGender(gender) {
        this.gender = gender;
    }

    getOptions() {
        if (this.targetLanguage === null && this.voice === null && this.gender === null) {
            return {};
        }
        const options = {};
        if (this.targetLanguage) {
            options.languages = [this.targetLanguage];
        }
        if (this.voice) {
            options.voices = [this.voice];
        }
        if (this.gender) {
            options.gender = [this.gender];
        }
        return options;
    }

    getPrompt(currentTool, selectionText) {
        // This handler handles both 'tts' and 'audiogen' tool types which basically are pretty much the same,
        // but not exactly.
        return currentTool === 'tts' ? selectionText : '';
    }

    async loadTtsOptions() {
        if (this.ttsOptions === null) {
            const fetchedOptions = await AiConfig.getPurposeOptions('tts');
            this.ttsOptions = JSON.parse(fetchedOptions.options);
        }
    }

    /**
     * Get the rendering context.
     *
     * @param {string} tool the tool to generate the context for, can be 'tts' and 'audiogen'
     */
    async getTemplateContext(tool) {
        const context = {
            modalHeadline: BasedataHandler.getTinyAiString(tool + '_headline'),
            showIcon: true,
            tool: tool,
        };

        const modalDropdowns = [];

        const targetLanguageOptions = await this.getTargetLanguageOptions();
        if (targetLanguageOptions !== null && Object.keys(targetLanguageOptions).length > 0) {
            const targetLanguageDropdownContext = {};
            targetLanguageDropdownContext.preference = 'targetLanguage';
            let indexOfLanguageOption = 0;
            const matchingEntry = targetLanguageOptions.map(entry => entry.key.startsWith(Config.language));

            if (matchingEntry.length > 0) {
                // Language keys are of the form de-DE, so we check, if current user's language starts with same language code.
                indexOfLanguageOption = targetLanguageOptions.findIndex(value => value.key.startsWith(Config.language));
            }
            targetLanguageDropdownContext.dropdownDefault = targetLanguageOptions[indexOfLanguageOption].displayname;
            targetLanguageDropdownContext.dropdownDefaultValue = targetLanguageOptions[indexOfLanguageOption].key;
            targetLanguageDropdownContext.dropdownDescription = BasedataHandler.getTinyAiString('targetlanguage');
            const targetLanguageDropdownOptions = [];
            targetLanguageOptions.forEach(option => {
                targetLanguageDropdownOptions.push({
                    optionValue: option.key,
                    optionLabel: option.displayname,
                });
            });
            targetLanguageDropdownContext.dropdownOptions = targetLanguageDropdownOptions;
            modalDropdowns.push(targetLanguageDropdownContext);
        }

        const voiceOptions = await this.getVoiceOptions();
        if (voiceOptions !== null && Object.keys(voiceOptions).length > 0) {
            const voiceDropdownContext = {};
            voiceDropdownContext.preference = 'voice';
            voiceDropdownContext.dropdownDefault = voiceOptions[0].displayname;
            voiceDropdownContext.dropdownDefaultValue = voiceOptions[0].key;
            voiceDropdownContext.dropdownDescription = BasedataHandler.getTinyAiString('voice');
            const voiceDropdownOptions = [];
            voiceOptions.forEach(option => {
                voiceDropdownOptions.push({
                    optionValue: option.key,
                    optionLabel: option.displayname,
                });
            });
            voiceDropdownContext.dropdownOptions = voiceDropdownOptions;
            modalDropdowns.push(voiceDropdownContext);
        }

        const genderOptions = await this.getGenderOptions();
        if (genderOptions !== null && Object.keys(genderOptions).length > 0) {
            const genderDropdownContext = {};
            genderDropdownContext.preference = 'gender';
            genderDropdownContext.dropdownDefault = genderOptions[0].displayname;
            genderDropdownContext.dropdownDefaultValue = genderOptions[0].key;
            genderDropdownContext.dropdownDescription = BasedataHandler.getTinyAiString('gender');
            const genderDropdownOptions = [];
            genderOptions.forEach(option => {
                genderDropdownOptions.push({
                    optionValue: option.key,
                    optionLabel: option.displayname,
                });
            });
            genderDropdownContext.dropdownOptions = genderDropdownOptions;
            modalDropdowns.push(genderDropdownContext);
        }
        if (tool === 'audiogen') {
            // In the audiogen view the dropdowns are at the bottom, so we need to make the dropdowns dropup instead of dropdown.
            modalDropdowns.forEach(dropdownContext => {
                dropdownContext.dropup = true;
            });
        }

        Object.assign(context, {
            modalDropdowns: modalDropdowns
        });

        Object.assign(context, BasedataHandler.getShowPromptButtonContext());

        if (tool === 'audiogen') {
            // Overwrite some prompt textarea specific attributes.
            context.collapsed = false;
            context.placeholder = BasedataHandler.getTinyAiString('audiogen_placeholder');
        }
        Object.assign(context, BasedataHandler.getBackAndGenerateButtonContext());
        return context;
    }
}
