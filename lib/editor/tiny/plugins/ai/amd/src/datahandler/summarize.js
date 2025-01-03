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

import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import BaseHandler from 'tiny_ai/datahandler/base';
import {getString} from 'core/str';

/**
 * Tiny AI data manager.
 *
 * @module      tiny_ai/datahandler/summarize
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    currentTool = null;

    getLanguageTypeOptions() {
        return {
            nospeciallanguage: BasedataHandler.getTinyAiString('keeplanguagetype'),
            simplelanguage: BasedataHandler.getTinyAiString('simplelanguage'),
            technicallanguage: BasedataHandler.getTinyAiString('technicallanguage'),
        };
    }

    getMaxWordCountOptions() {
        return {
            '0': BasedataHandler.getTinyAiString('nomaxwordcount'),
            '10': '10',
            '20': '20',
            '50': '50',
            '100': '100',
            '200': '200',
            '300': '300'
        };
    }

    languageType = null;
    maxWordCount = 0;

    setMaxWordCount(maxWordCount) {
        this.maxWordCount = maxWordCount;
    }

    setLanguageType(languageType) {
        this.languageType = languageType;
    }

    async getPrompt(selectionText) {
        let prompt = '';
        if (this.currentTool === 'summarize') {
            prompt += BasedataHandler.getTinyAiString('summarize_baseprompt');
        } else if (this.currentTool === 'describe') {
            prompt += BasedataHandler.getTinyAiString('describe_baseprompt');
        }
        if (parseInt(this.maxWordCount) === 0 && this.languageType === 'nospeciallanguage') {
            return prompt + ': ' + selectionText;
        } else {
            prompt += '. ';
            if (parseInt(this.maxWordCount) !== 0) {
                prompt += ' ';
                prompt += await getString('maxwordcount_prompt', 'tiny_ai', this.maxWordCount);
                prompt += '.';
            }
            if (this.languageType !== 'nospeciallanguage') {
                prompt += ' ';
                prompt += await getString('languagetype_prompt', 'tiny_ai', this.getLanguageTypeOptions()[this.languageType]);
                prompt += '.';
            }
            prompt += '\n';
            prompt += BasedataHandler.getTinyAiString('texttouse') + ': ' + selectionText;
            return prompt;
        }
    }

    setTool(currentTool) {
        this.currentTool = currentTool;
    }

    /**
     * Return the template context.
     *
     * @param {string} tool the tool to generate the context for, can be 'summarize' or 'describe'
     */
    getTemplateContext(tool) {
        const
            context = {
                modalHeadline: BasedataHandler.getTinyAiString(tool + '_headline'),
                showIcon: true,
                tool: tool,
            };
        Object
            .assign(context, BasedataHandler

                .getShowPromptButtonContext()
            )
        ;
        Object
            .assign(context, BasedataHandler

                .getBackAndGenerateButtonContext()
            )
        ;

        const maxWordCountDropdownContext = {};
        maxWordCountDropdownContext.preference = 'maxWordCount';
        maxWordCountDropdownContext.dropdownDefault = Object.values(this.getMaxWordCountOptions())[0];
        maxWordCountDropdownContext.dropdownDefaultValue = Object.keys(this.getMaxWordCountOptions())[0];
        maxWordCountDropdownContext.dropdownDescription = 'MAXIMALE WORTANZAHL';
        const maxWordCountDropdownOptions = [];

        for (const [key, value] of Object.entries(this.getMaxWordCountOptions())) {
            maxWordCountDropdownOptions.push({
                optionValue: key,
                optionLabel: value,
            });
        }

        delete maxWordCountDropdownOptions[Object.keys(this.getLanguageTypeOptions())[0]];
        maxWordCountDropdownContext.dropdownOptions = maxWordCountDropdownOptions;

        const languageTypeDropdownContext = {};
        languageTypeDropdownContext.preference = 'languageType';
        languageTypeDropdownContext.dropdownDefault = Object.values(this.getLanguageTypeOptions())[0];
        languageTypeDropdownContext.dropdownDefaultValue = Object.keys(this.getLanguageTypeOptions())[0];
        languageTypeDropdownContext.dropdownDescription = 'ART DER SPRACHE';
        const languageTypeDropdownOptions = [];
        for (const [key, value] of Object.entries(this.getLanguageTypeOptions())) {
            languageTypeDropdownOptions.push({
                optionValue: key,
                optionLabel: value,
            });
        }
        delete languageTypeDropdownOptions[Object.keys(this.getLanguageTypeOptions)[0]];
        languageTypeDropdownContext.dropdownOptions = languageTypeDropdownOptions;


        Object.assign(context, {
            modalDropdowns: [
                maxWordCountDropdownContext,
                languageTypeDropdownContext,
            ]
        });

        return context;
    }
}
