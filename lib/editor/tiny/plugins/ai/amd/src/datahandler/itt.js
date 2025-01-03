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
import {getDatamanager} from 'tiny_ai/utils';

/**
 * Tiny AI data handler for image to text.
 *
 * @module      tiny_ai/datahandler/itt
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    ittOptions = null;

    async loadIttOptions() {
        if (this.ittOptions === null) {
            const fetchedOptions = await AiConfig.getPurposeOptions('itt');
            this.ittOptions = JSON.parse(fetchedOptions.options);
        }
    }

    async getAllowedMimetypes() {
        await this.loadIttOptions();
        return this.ittOptions.allowedmimetypes;
    }

    getOptions() {
        const options = {};
        const datamanager = getDatamanager(this.uniqid);
        options.image = datamanager.getCurrentFile();
        return options;
    }

    /**
     * Get the prompt.
     *
     * @param {string} tool the tool to generate the prompt for, can be 'describeimage' and 'imagetotext'
     */
    getPrompt(tool) {
        return BasedataHandler.getTinyAiString(tool + '_baseprompt');
    }

    /**
     * Get the rendering context.
     *
     * @param {string} tool the tool to generate the context for, can be 'describeimage' and 'imagetotext'
     */
    async getTemplateContext(tool) {
        const context = {
            modalHeadline: BasedataHandler.getTinyAiString(tool + '_headline'),
            showIcon: true,
            tool: tool,
            textareatype: 'prompt',
            placeholder: BasedataHandler.getTinyAiString(tool + '_placeholder'),
            insertimagedescription: BasedataHandler.getTinyAiString('imagetotext_insertimage')
        };

        Object.assign(context, BasedataHandler.getShowPromptButtonContext());

        Object.assign(context, BasedataHandler.getBackAndGenerateButtonContext());
        return context;
    }
}
