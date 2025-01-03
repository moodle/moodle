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
 * Tiny AI utils library.
 *
 * @module      tiny_ai/renderer
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {renderInfoBox} from 'local_ai_manager/infobox';
import {renderUserQuota} from 'local_ai_manager/userquota';
import * as BasedataHandler from 'tiny_ai/datahandler/basedata';
import Templates from 'core/templates';
import $ from 'jquery';
import {
    getEditorUtils,
    getDatamanager,
    getImggenHandler,
    getOptimizeHandler,
    getStartHandler,
    getSummarizeHandler,
    getTranslateHandler,
    getTtsHandler,
    getIttHandler
} from 'tiny_ai/utils';

export default class {

    uniqid = null;
    datamanager = null;
    editorUtils = null;

    constructor(uniqid) {
        this.uniqid = uniqid;
        this.datamanager = getDatamanager(uniqid);
        this.editorUtils = getEditorUtils(uniqid);
    }

    async renderStart() {
        this.datamanager.reset();
        const templateContext = await getStartHandler(this.uniqid).getTemplateContext(getEditorUtils(this.uniqid));
        await this.renderModalContent('moodle-modal-body-start', 'moodle-modal-footer-info', templateContext);
    }


    async renderSummarize() {
        const templateContext = getSummarizeHandler(this.uniqid).getTemplateContext('summarize');
        await this.renderModalContent('moodle-modal-body-preferences', 'moodle-modal-footer-generate', templateContext);
    }


    async renderTranslate() {
        const templateContext = getTranslateHandler(this.uniqid).getTemplateContext();
        await this.renderModalContent('moodle-modal-body-preferences', 'moodle-modal-footer-generate', templateContext);
    }

    async renderDescribe() {
        const templateContext = getSummarizeHandler(this.uniqid).getTemplateContext('describe');
        await this.renderModalContent('moodle-modal-body-preferences', 'moodle-modal-footer-generate', templateContext);
    }

    async renderTts() {
        const templateContext = await getTtsHandler(this.uniqid).getTemplateContext('tts');
        await this.renderModalContent('moodle-modal-body-preferences', 'moodle-modal-footer-generate', templateContext);
    }

    async renderAudiogen() {
        const templateContext = await getTtsHandler(this.uniqid).getTemplateContext('audiogen');
        await this.renderModalContent('moodle-modal-body-mediageneration', 'moodle-modal-footer-generate', templateContext);
    }


    async renderImggen() {
        const templateContext = await getImggenHandler(this.uniqid).getTemplateContext();
        await this.renderModalContent('moodle-modal-body-mediageneration', 'moodle-modal-footer-generate', templateContext);
    }

    async renderDescribeimg() {
        const templateContext = await getIttHandler(this.uniqid).getTemplateContext('describeimg');
        await this.renderModalContent('moodle-modal-body-itt', 'moodle-modal-footer-generate', templateContext);
    }

    async renderImagetotext() {
        const templateContext = await getIttHandler(this.uniqid).getTemplateContext('imagetotext');
        await this.renderModalContent('moodle-modal-body-itt', 'moodle-modal-footer-generate', templateContext);
    }

    async renderLoading() {
        const templateContext = {};
        templateContext.modalHeadline = BasedataHandler.getTinyAiString('aigenerating');
        await this.renderModalContent('moodle-modal-body-loading', 'moodle-modal-footer-empty', templateContext);
    }


    async renderSuggestion() {
        const templateContext = {};
        templateContext.modalHeadline = BasedataHandler.getTinyAiString('aisuggestion');
        // TODO Eventually do not use the same rendering in the suggestion like in the course, or just leave it because we
        //  consider it beautiful
        templateContext.resultText = this.renderAiResultForEditor();

        Object.assign(templateContext, BasedataHandler.getReplaceButtonsContext(this.editorUtils.getMode()));
        await this.renderModalContent('moodle-modal-body-suggestion', 'moodle-modal-footer-replace', templateContext);
    }

    async renderOptimizePrompt() {
        const templateContext = getOptimizeHandler(this.uniqid).getTemplateContext();
        await this.renderModalContent('moodle-modal-body-optimize', 'moodle-modal-footer-generate', templateContext);
    }


    async renderDismiss() {
        const templateContext = {
            modalHeadline: '',
            centeredHeadline: BasedataHandler.getTinyAiString('dismisssuggestion'),
            showIcon: false,
            buttons: [
                {
                    hasText: true,
                    buttonText: BasedataHandler.getTinyAiString('cancel'),
                    iconLeft: false,
                    iconRight: false,
                    primary: false,
                    secondary: true,
                    tertiary: false,
                    action: 'canceldismiss'
                },
                {
                    hasText: true,
                    buttonText: BasedataHandler.getTinyAiString('dismiss'),
                    iconLeft: false,
                    iconRight: false,
                    primary: true,
                    secondary: false,
                    tertiary: false,
                    action: 'dismiss'
                }
            ]
        };
        await this.renderModalContent('moodle-modal-body-dismiss', 'moodle-modal-footer-empty', templateContext);
    }


    renderAiResultForEditor() {
        let html;
        switch (this.datamanager.getCurrentTool()) {
            case 'tts':
            case 'audiogen': {
                const audioPlayer = document.createElement('audio');
                audioPlayer.controls = 'controls';
                audioPlayer.src = this.datamanager.getCurrentAiResult();
                audioPlayer.type = 'audio/mpeg';
                html = audioPlayer.outerHTML;
                break;
            }
            case 'imggen': {
                const img = document.createElement('img');
                img.src = this.datamanager.getCurrentAiResult();
                img.classList.add('mw-100');
                html = img.outerHTML;
                break;
            }
            default: {
                html = this.datamanager.getCurrentAiResult();
            }
        }
        return html;
    }

    /**
     * Re-renders the content auf the modal once it has been created.
     *
     * @param {string} bodyComponentTemplate the name of the body template to use (without the prefix 'tiny_ai/components/')
     * @param {string} footerComponentTemplate the name of the footer template to use (without the prefix 'tiny_ai/components/')
     * @param {object} templateContext the template context being used for all partial templates
     * @returns {Promise<void>} the async promise
     */
    async renderModalContent(bodyComponentTemplate, footerComponentTemplate, templateContext) {
        templateContext.tinyinstanceuniqid = this.uniqid;
        const modal = getEditorUtils(this.uniqid).getModal();
        // Remove all eventually remaining tooltips before rendering a new view.
        document.querySelectorAll('button[data-action]').forEach(button => {
            $(button).tooltip('hide');
        });
        const result = await Promise.all([
            Templates.renderForPromise('tiny_ai/components/moodle-modal-header-title', templateContext),
            Templates.renderForPromise('tiny_ai/components/' + bodyComponentTemplate, templateContext),
            Templates.renderForPromise('tiny_ai/components/' + footerComponentTemplate, templateContext)
        ]);
        if (templateContext.hasOwnProperty('modalHeadline')) {
            // If there is no headline specified, we keep the old one.
            modal.setTitle(result[0].html);
        }
        // Hide all eventually still existing tooltips first, because they show on 'hover' and
        // 'focus'. So we need to remove them before removing the corresponding buttons from the DOM.
        // Boostrap 4 still using jQuery for tooltips, so we need jQuery here.
        document.querySelectorAll('button[data-action]').forEach(button => {
            $(button).tooltip('hide');
        });
        modal.setBody(result[1].html);
        modal.setFooter(result[2].html);
        result.forEach((item) => {
            Templates.runTemplateJS(item.js);
        });
        modal.getRoot().attr('data-tiny_ai_uniqid', this.uniqid);
        await this.insertInfoBox();
        await this.insertUserQuotaBox();
        document.querySelectorAll('button[data-action]').forEach(button => {
            button.addEventListener('click', event => {
                $(event.target).closest('button[data-action]').tooltip('hide');
            });
        });
    }

    async insertInfoBox() {
        const infoBoxSelector = '[data-rendertarget="infobox"]';
        if (document.querySelector(infoBoxSelector)) {
            await renderInfoBox('tiny_ai', getEditorUtils(this.uniqid).getUserId(), infoBoxSelector,
                ['singleprompt', 'translate', 'tts', 'imggen', 'itt']);
        }
    }

    async insertUserQuotaBox() {
        const usageBoxSelector = '[data-rendertarget="usageinfo"]';
        if (document.querySelector(usageBoxSelector)) {
            await renderUserQuota(usageBoxSelector, ['singleprompt', 'translate', 'tts', 'imggen', 'itt']);
        }
    }
}
