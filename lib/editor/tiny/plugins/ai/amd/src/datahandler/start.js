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

import * as config from 'core/config';
import {getString, getStrings} from 'core/str';
import {constants} from 'tiny_ai/constants';
import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import BaseHandler from 'tiny_ai/datahandler/base';
import {getAiConfig} from 'local_ai_manager/config';
import {errorAlert, stripHtmlTags} from 'tiny_ai/utils';


/**
 * Tiny AI data handler for start page.
 *
 * @module      tiny_ai/datahandler/start
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class extends BaseHandler {

    stringKeys = [
        'error_limitreached',
        'error_pleaseconfirm',
        'error_purposenotconfigured',
        'error_tenantdisabled',
        'error_unavailable_noselection',
        'error_unavailable_selection',
        'error_userlocked',
        'error_usernotconfirmed'
    ];

    aiConfig = null;
    strings = new Map();

    async init() {
        this.aiConfig = await getAiConfig();
        // It's easier to fetch alle these strings before even if we do not use them
        // instead of making all functions async just because of getString returning a promise.
        const stringRequest = this.stringKeys.map(key => {
            return {key, component: 'local_ai_manager'};
        });

        const fetchedStrings = await getStrings(stringRequest);
        for (let i = 0; i < this.stringKeys.length; i++) {
            this.strings.set(this.stringKeys[i], fetchedStrings[i]);
        }
        const tinyNotAvailableString = await getString('error_tiny_ai_notavailable', 'tiny_ai');
        this.strings.set('error_editor_notavailable', tinyNotAvailableString);
        const confirmLink = document.createElement('a');
        confirmLink.href = `${config.wwwroot}/local/ai_manager/confirm_ai_usage.php`;
        confirmLink.innerText = this.strings.get('error_pleaseconfirm');
        confirmLink.target = '_blank';
        this.strings.set('combinedusernotconfirmederror', this.strings.get('error_usernotconfirmed') + ' ' + confirmLink.outerHTML);
    }

    getPurposeConfig(tool) {
        if (this.aiConfig === null) {
            throw new Error('Coding error: init function was not called before accessing this.getPurposeConfig!');
        }
        const toolPurpose = constants.toolPurposeMapping[tool];
        return this.aiConfig.purposes.filter(purpose => purpose.purpose === toolPurpose)[0];
    }

    isTinyAiDisabled() {
        if (!this.aiConfig.tenantenabled) {
            return this.strings.get('error_tenantdisabled');
        }
        if (!this.aiConfig.userconfirmed) {
            return this.strings.get('combinedusernotconfirmederror');
        }
        if (this.aiConfig.userlocked) {
            return this.strings.get('error_userlocked');
        }
        return '';
    }

    isToolDisabled(tool, mode) {
        if (this.isTinyAiDisabled()) {
            return this.isTinyAiDisabled();
        }
        const purposeInfo = this.getPurposeConfig(tool);
        if (!purposeInfo.isconfigured) {
            return this.strings.get('error_purposenotconfigured');
        }
        if (purposeInfo.limitreached) {
            return this.strings.get('error_limitreached');
        }

        if (mode === constants.modalModes.selection) {
            return ['audiogen', 'imggen']
                .includes(tool) ? this.strings.get('error_unavailable_noselection') : '';
        } else if (mode === constants.modalModes.general) {
            return ['summarize', 'translate', 'describe', 'tts']
                .includes(tool) ? this.strings.get('error_unavailable_selection') : '';
        }
        return '';
    }

    isToolHidden(tool) {
        const purposeInfo = this.getPurposeConfig(tool);
        // If the tenant is not allowed the plugin is being disabled completely, so we do not need
        // to check this case here.
        if (this.aiConfig.role === 'role_basic') {
            if (!this.aiConfig.tenantenabled) {
                return true;
            }
            if (!purposeInfo.isconfigured) {
                return true;
            }
        }
        return false;
    }

    async getTemplateContext(editorUtils) {
        const mode = editorUtils.getMode();
        let toolButtons = [];
        if (this.aiConfig.role === 'role_basic' && this.isTinyAiDisabled()) {
            await errorAlert(await getString('error_tiny_ai_notavailable', 'tiny_ai') + '<br/>'
                + this.isTinyAiDisabled());
            editorUtils.getModal().destroy();
        }

        if (!this.isToolHidden('summarize')) {
            toolButtons.push({
                toolname: 'summarize',
                tool: BasedataHandler.getTinyAiString('toolname_summarize'),
                description: BasedataHandler.getTinyAiString('toolname_summarize_extension'),
                customicon: true,
                iconname: 'shorten',
                disabled: this.isToolDisabled('summarize', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('summarize', mode)),
                action: 'loadsummarize'
            });
        }
        if (!this.isToolHidden('translate')) {
            toolButtons.push({
                toolname: 'translate',
                tool: BasedataHandler.getTinyAiString('toolname_translate'),
                description: BasedataHandler.getTinyAiString('toolname_translate_extension'),
                iconname: 'language',
                iconstyle: 'solid',
                disabled: this.isToolDisabled('translate', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('translate', mode)),
                action: 'loadtranslate'
            });
        }
        if (!this.isToolHidden('describe')) {
            toolButtons.push({
                toolname: 'describe',
                tool: BasedataHandler.getTinyAiString('toolname_describe'),
                description: BasedataHandler.getTinyAiString('toolname_describe_extension'),
                customicon: true,
                iconname: 'extend',
                disabled: this.isToolDisabled('describe', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('describe', mode)),
                action: 'loaddescribe'
            });
        }
        if (!this.isToolHidden('tts')) {
            toolButtons.push({
                toolname: 'tts',
                tool: BasedataHandler.getTinyAiString('toolname_tts'),
                description: BasedataHandler.getTinyAiString('toolname_tts_extension'),
                iconstyle: 'solid',
                iconname: 'microphone',
                disabled: this.isToolDisabled('tts', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('tts', mode)),
                action: 'loadtts'
            });
        }
        if (!this.isToolHidden('audiogen')) {
            toolButtons.push({
                toolname: 'audiogen',
                tool: BasedataHandler.getTinyAiString('toolname_audiogen'),
                iconstyle: 'solid',
                iconname: 'microphone',
                disabled: this.isToolDisabled('audiogen', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('audiogen', mode)),
                action: 'loadaudiogen'
            });
        }
        if (!this.isToolHidden('imggen')) {
            toolButtons.push({
                toolname: 'imggen',
                tool: BasedataHandler.getTinyAiString('toolname_imggen'),
                iconstyle: 'solid',
                iconname: 'image',
                disabled: this.isToolDisabled('imggen', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('imggen', mode)),
                action: 'loadimggen'
            });
        }
        if (!this.isToolHidden('describeimg')) {
            toolButtons.push({
                toolname: 'describeimg',
                tool: BasedataHandler.getTinyAiString('toolname_describeimg'),
                iconstyle: 'solid',
                iconname: 'file-image',
                disabled: this.isToolDisabled('describeimg', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('describeimg', mode)),
                action: 'loaddescribeimg'
            });
        }
        if (!this.isToolHidden('imagetotext')) {
            toolButtons.push({
                toolname: 'imagetotext',
                tool: BasedataHandler.getTinyAiString('toolname_imagetotext'),
                iconstyle: 'solid',
                iconname: 'signature',
                disabled: this.isToolDisabled('imagetotext', mode).length > 0,
                tooltip: stripHtmlTags(this.isToolDisabled('imagetotext', mode)),
                action: 'loadimagetotext'
            });
        }
        // We sort the not disabled tools to the top while keeping the groups "disabled tools" and "not disabled tools"
        // in the same order inside the groups.
        toolButtons.sort((a, b) => {
            if (a.disabled && !b.disabled) {
                return 1;
            } else if (b.disabled && !a.disabled) {
                return -1;
            } else {
                return 0;
            }
        });

        const templateContext = {
            showIcon: true,
            modalHeadline: BasedataHandler.getTinyAiString('mainselection_heading'),
            action: 'loadfreeprompt',
            modalButtons: toolButtons,
            freeprompthidden: true
        };

        Object.assign(templateContext, BasedataHandler.getInputContext());
        if (this.isTinyAiDisabled()) {
            templateContext.input[0].disabled = true;
            templateContext.input[0].hasError = true;
            templateContext.input[0].errorMessage = this.isTinyAiDisabled();
        }

        if (this.isToolDisabled('freeprompt', mode)) {
            templateContext.input[0].disabled = true;
        }
        return templateContext;
    }
}
