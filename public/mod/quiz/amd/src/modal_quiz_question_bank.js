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
 * Contain the logic for the question bank modal.
 *
 * @module     mod_quiz/modal_quiz_question_bank
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from './add_question_modal';
import * as Fragment from 'core/fragment';
import * as FormChangeChecker from 'core_form/changechecker';
import * as ModalEvents from 'core/modal_events';
import * as Notification from 'core/notification';
import BankSwitcher from 'core_question/bank_switcher';

const SELECTORS = {
    ADD_TO_QUIZ_CONTAINER: 'td.addtoquizaction',
    ANCHOR: 'a[href]',
    PREVIEW_CONTAINER: 'td.previewquestionaction',
    ADD_QUESTIONS_FORM: 'form#questionsubmit',
    SORTERS: '.sorters',
    SWITCH_TO_OTHER_BANK: 'button[data-action="switch-question-bank"]',
    NEW_BANKMOD_ID: 'data-newmodid',
    BANK_SEARCH: '#searchbanks',
    GO_BACK_BUTTON: 'button[data-action="go-back"]',
    ADD_ON_PAGE_FORM_ELEMENT: 'input[name="addonpage"]',
    CMID_FORM_ELEMENT: 'form#questionsubmit input[name="cmid"]',
};

export default class ModalQuizQuestionBank extends Modal {
    static TYPE = 'mod_quiz-quiz-question-bank';

    /**
     * Create the question bank modal.
     *
     * @param {Number} contextId Current module context id.
     * @param {Number} bankCmId Current question bank course module id.
     * @param {Number} quizCmId Current quiz course module id.
     * @param {Number} courseId Current course id, required for switching banks.
     */
    static init(contextId, bankCmId, quizCmId, courseId) {
        const selector = '.menu [data-action="questionbank"]';
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest(selector);
            if (!trigger) {
                return;
            }
            e.preventDefault();

            ModalQuizQuestionBank.create({
                contextId,
                quizCmId,
                bankCmId,
                title: trigger.dataset.header,
                addOnPage: trigger.dataset.addonpage,
                templateContext: {
                    hidden: true,
                },
                large: true,
                courseId,
            });
        });
    }

    /**
     * Override the parent show function.
     *
     * Reload the body contents when the modal is shown. The current
     * window URL is used to inform the new content that should be
     * displayed.
     *
     * @method show
     * @return {void}
     */
    show() {
        this.reloadBodyContent(window.location.search);
        return super.show(this);
    }

    /**
     * Replaces the current body contents with a new version of the question
     * bank.
     *
     * The contents of the question bank are generated using the provided
     * query string.
     *
     * @method reloadBodyContent
     * @param {string} querystring URL encoded string.
     */
    reloadBodyContent(querystring) {
        // Load the question bank fragment to be displayed in the modal and hide the 'go back' button.
        this.hideFooter();
        this.setTitle(this.originalTitle);
        this.setBody(Fragment.loadFragment(
            'mod_quiz',
            'quiz_question_bank',
            this.getContextId(),
            {
                querystring,
                quizcmid: this.quizCmId,
                bankcmid: this.bankCmId,
            }
        ));
    }

    /**
     * Update the URL of the anchor element that the user clicked on to make
     * sure that the question is added to the correct page.
     *
     * @method handleAddToQuizEvent
     * @param {event} e A JavaScript event
     * @param {object} anchorElement The anchor element that was triggered
     */
    handleAddToQuizEvent(e, anchorElement) {
        // If the user clicks the plus icon to add the question to the page
        // directly then we need to intercept the click in order to adjust the
        // href and include the correct add on page id and cmid before the page is
        // redirected.
        const href = new URL(anchorElement.getAttribute('href'));
        href.searchParams.set('addonpage', this.getAddOnPageId());
        href.searchParams.set('cmid', this.quizCmId);
        anchorElement.setAttribute('href', href);
    }

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        this.getModal().on('submit', SELECTORS.ADD_QUESTIONS_FORM, (e) => {
            // If the user clicks on the "Add selected questions to the quiz" button to add some questions to the page
            // then we need to intercept the submit in order to include the correct "add on page id"
            // and the quizmod id before the form is submitted.
            const formElement = e.currentTarget;
            document.querySelector(SELECTORS.ADD_ON_PAGE_FORM_ELEMENT).setAttribute('value', this.getAddOnPageId());

            // We also need to set the form cmid & action as the quiz modid as this could be coming from a module that isn't a quiz.
            document.querySelector(SELECTORS.CMID_FORM_ELEMENT).setAttribute('value', this.quizCmId);
            const actionUrl = new URL(formElement.getAttribute('action'));
            actionUrl.searchParams.set('cmid', this.quizCmId);
            formElement.setAttribute('action', actionUrl.toString());
        });

        const switcher = new BankSwitcher();

        this.getModal().on('click', SELECTORS.SWITCH_TO_OTHER_BANK, () => {
            try {
                switcher.show(this, this.courseId, this.getContextId(), this.bankCmId, this.quizCmId);
            } catch (ex) {
                Notification.exception(ex);
            }
        });

        this.getModal().get(0).addEventListener('bankSwitched', (e) => {
            const bankCmId = e.detail.cmid;
            if (bankCmId > 0) {
                // We need to clear the filter as we are about to reload the content.
                const url = new URL(location.href);
                url.searchParams.delete('filter');
                history.pushState({}, '', url);

                this.bankCmId = bankCmId;
                this.reloadBodyContent(window.location.search);
            }
        });

        this.getModal().on('click', SELECTORS.ANCHOR, (e) => {
            const anchorElement = e.currentTarget;

            // If the anchor element was the add to quiz link.
            if (anchorElement.closest(SELECTORS.ADD_TO_QUIZ_CONTAINER)) {
                this.handleAddToQuizEvent(e, anchorElement);
                return;
            }

            e.preventDefault();
        });

        // Disable the form change checker when the body is rendered.
        this.getRoot().on(ModalEvents.bodyRendered, () => {
            // Make sure the form change checker is disabled otherwise it'll stop the user from navigating away from the
            // page once the modal is hidden.
            FormChangeChecker.disableAllChecks();
        });
    }
}

ModalQuizQuestionBank.registerModalType();
