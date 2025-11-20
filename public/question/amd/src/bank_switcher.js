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

import {get_string as getString} from 'core/str';
import Fetch from 'core/fetch';
import Templates from 'core/templates';
import AutoComplete from 'core/form-autocomplete';

/**
 * Bank switcher module for switching between question banks.
 *
 * The BankSwitcher class is used to display the bank switching UI in an existing Modal. This can either be a new Modal
 * created for the purpose, or an existing modal, in which case it will replace the existing title, body and footer.
 *
 * When a new bank is selected, it will emit a custom `bankSwitched` event on the modal's DOM element,
 * with the cmid of the selected bank in `event.details.cmid`. For example:
 *
 * const modal = Modal.create(...);
 * const switcher = new BankSwitcher();
 * switcher.show(modal, courseId, cmId);
 * modal.getModal().get(0).addEventListener('bankSwitched', (e) => window.console.log(e.details.cmid));
 *
 * @module     core_question/bank_switcher
 * @copyright  2025 Catalyst IT Europe Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    ANCHOR: 'a[data-newmodid]',
    AUTOCOMPLETE_SELECTION: '.search-banks .form-autocomplete-selection',
    BANK_SEARCH: '#searchbanks',
    BANK_SWITCHER: '.bank-switcher',
    GO_BACK_BUTTON: 'button[data-action="go-back"]',
    MODAL: 'div[data-region="modal"]',
};

export default class BankSwitcher {
    /**
     * Show the bank switcher.
     *
     * This will replace the content of the given modal with the bank switcher, and emit a bankChanged event on the modal
     * when a bank is selected (either a new bank, or the current one again).
     *
     * @param {Modal} modal The modal to display the switcher within.
     * @param {Number} courseId The course ID to display banks from.
     * @param {Number} contextId The context the bank switcher is being displayed in, for text filters.
     * @param {Number} currentCmId The cmid of the currently selected question bank.
     * @param {Number} activityCmId The cmid of activity we are currently in (if any).
     * @return {Promise<void>} Resolves once the switcher has finished displaying.
     */
    async show(modal, courseId, contextId, currentCmId = null, activityCmId = null) {
        if (!courseId) {
            throw new Error('courseId is required by the bank switcher.');
        }
        modal.setTitle(getString('switcherselectbank', 'core_question'));

        if (currentCmId) {
            // Create a 'Go back' button and set it in the footer.
            const el = document.createElement('button');
            el.classList.add('btn', 'btn-primary');
            el.textContent = await getString('switchergoback', 'core_question');
            el.setAttribute('data-action', 'go-back');
            el.setAttribute('value', currentCmId);
            el.addEventListener('click', this.clickListener);
            modal.setFooter(el);
        }

        const params = {
            course: courseId,
            includeshared: true,
            includerecent: true,
        };
        if (activityCmId) {
            params.currentmodule = activityCmId;
        }
        const banksResponse = await Fetch.performGet('question', 'banks', {params});
        const {banks} = await banksResponse.json();
        const templateContext = {
            contextid: contextId,
            hasactivitybank: false,
            hascoursesharedbanks: false,
            coursesharedbanks: [],
            hasrecentlyviewedbanks: false,
            recentlyviewedbanks: [],
        };
        banks.forEach((bank) => {
            if (bank.current) {
                templateContext.hasactivitybank = true;
                templateContext.activitybank = {
                    name: bank.name,
                    cmid: bank.modid,
                };
                return;
            }
            if (bank.recent) {
                templateContext.hasrecentlyviewedbanks = true;
                templateContext.recentlyviewedbanks.push({
                    coursenamebankname: bank.coursenamebankname,
                    modid: bank.modid,
                });
                return;
            }
            templateContext.hascoursesharedbanks = true;
            templateContext.coursesharedbanks.push({
                name: bank.name,
                modid: bank.modid,
            });
        });

        modal.setBody(
            Templates.render('core_question/switch_question_bank', templateContext)
        );
        const placeholder = await getString('switchersearchbyname', 'core_question');
        await modal.getBodyPromise();
        await AutoComplete.enhance(
            SELECTORS.BANK_SEARCH,
            false,
            'core_question/question_banks_datasource',
            placeholder,
            false,
            true,
            '',
            true
        );

        const modalBody = modal.getBody()[0];
        // Hide the selection element as we don't need it.
        modalBody.querySelector(SELECTORS.AUTOCOMPLETE_SELECTION)?.classList.add('d-none');

        const switcherElement = modalBody.querySelector(SELECTORS.BANK_SWITCHER);
        switcherElement.addEventListener('click', this.clickListener);
        switcherElement.addEventListener('change', this.changeListener);
    }

    clickListener(e) {
        const goBack = e.target.closest(SELECTORS.GO_BACK_BUTTON);
        let cmid;
        if (goBack) {
            cmid = goBack.value;
        } else {
            const anchor = e.target.closest(SELECTORS.ANCHOR);
            if (!anchor) {
                return;
            }
            cmid = anchor.dataset.newmodid;
        }
        e.preventDefault();
        const modalElement = e.target.closest(SELECTORS.MODAL);
        const bankSwitched = new CustomEvent('bankSwitched', {detail: {cmid}});
        modalElement.dispatchEvent(bankSwitched);
    }

    changeListener(e) {
        const search = e.target.closest(SELECTORS.BANK_SEARCH);
        if (!search) {
            return;
        }
        const bankCmId = search.value;
        if (bankCmId > 0) {
            const modalElement = e.target.closest(SELECTORS.MODAL);
            const bankSwitched = new CustomEvent('bankSwitched', {detail: {cmid: bankCmId}});
            modalElement.dispatchEvent(bankSwitched);
        }
    }
}
