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
import * as BasedataHandler from "./basedata";
import BaseHandler from 'tiny_ai/datahandler/base';

/**
 * Tiny AI data manager.
 *
 * @module      tiny_ai/datahandler/imggen
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    imggenOptions = null;

    size = null;

    async getSizesOptions() {
        await this.loadImggenOptions();
        return this.imggenOptions.sizes;
    }

    setSize(size) {
        this.size = size;
    }

    getOptions() {
        if (this.size === null) {
            return {};
        }
        const options = {};
        if (this.size) {
            options.sizes = [this.size];
        }
        return options;
    }

    async loadImggenOptions() {
        if (this.imggenOptions === null) {
            const fetchedOptions = await AiConfig.getPurposeOptions('imggen');
            this.imggenOptions = JSON.parse(fetchedOptions.options);
        }
    }

    async getTemplateContext() {
        const context = {
            modalHeadline: BasedataHandler.getTinyAiString('imggen_headline'),
            showIcon: true,
            tool: 'imggen',
            textareatype: 'prompt',
            placeholder: BasedataHandler.getTinyAiString('imggen_placeholder'),
        };

        const modalDropdowns = [];

        const sizesOptions = await this.getSizesOptions();
        if (sizesOptions !== null && Object.keys(sizesOptions).length > 0) {
            const sizesDropdownContext = {};
            sizesDropdownContext.preference = 'sizes';
            sizesDropdownContext.dropdownDefault = sizesOptions[0].displayname;
            sizesDropdownContext.dropdownDefaultValue = sizesOptions[0].key;
            sizesDropdownContext.dropdownDescription = BasedataHandler.getTinyAiString('size');
            const sizesDropdownOptions = [];
            sizesOptions.forEach(option => {
                sizesDropdownOptions.push({
                    optionValue: option.key,
                    optionLabel: option.displayname,
                });
            });
            sizesDropdownContext.dropdownOptions = sizesDropdownOptions;
            modalDropdowns.push(sizesDropdownContext);
        }
        // In the imggen view the dropdowns are at the bottom, so we need to make the dropdowns dropup instead of dropdown.
        // We only have one here of course, but in case we will have more options, we use a forEach.
        modalDropdowns.forEach(dropdownContext => {
            dropdownContext.dropup = true;
        });

        Object.assign(context, {
            modalDropdowns: modalDropdowns
        });
        Object.assign(context, BasedataHandler.getBackAndGenerateButtonContext());
        return context;
    }
}
