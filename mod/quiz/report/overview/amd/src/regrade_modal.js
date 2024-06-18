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
 * Regrade modal form is used to regrade or dryrun the attempts and questions.
 *
 * @module quiz_overview/regrade_modal
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';
import Templates from 'core/templates';
import {getString} from 'core/str';
import Notification from 'core/notification';

/**
 * @type {Object} selectors used in this code.
 */
const SELECTORS = {
    'allQuestionsButton': '#regradeallquestions',
    'dryRunButton': '#dryrunbutton',
    'mainTableForm': '#attemptsform',
    'questionCheckbox': '[id^="regradeslot"]',
    'regradeAttemptsButtonId': 'regradeattempts',
    'regradeButton': '#regradebutton',
    'reportTableSelectedAttempts': '[id^="attemptid_"]:checked',
};

/**
 * Show the regrade modal.
 *
 * @param {Event} e click event that opened the modal.
 * @returns {Promise<void>}
 */
const showModal = async(e) => {
    e.preventDefault();
    try {
        let hiddenInputs = [];
        document.querySelectorAll(SELECTORS.mainTableForm + ' input[type=hidden]').forEach((hiddenInput) => {
            hiddenInputs.push({'name': hiddenInput.name, 'value': hiddenInput.value});
        });
        document.querySelectorAll(SELECTORS.reportTableSelectedAttempts).forEach((selectedAttempt) => {
            hiddenInputs.push({'name': selectedAttempt.name, 'value': selectedAttempt.value});
        });
        const modal = await Modal.create({
            title: getString('regrade', 'quiz_overview'),
            body: Templates.render('quiz_overview/regrade_modal_body', {
                'actionurl': document.querySelector(SELECTORS.mainTableForm).action,
                'hasselectedattempts': document.querySelector(SELECTORS.reportTableSelectedAttempts) !== null,
                'questions': JSON.parse(document.getElementById(SELECTORS.regradeAttemptsButtonId).dataset.slots),
                'hiddeninputs': hiddenInputs,
            }),
            isVerticallyCentered: true,
            removeOnClose: true,
            show: true,
        });
        modal.getRoot()[0].addEventListener('change', updateButtonStates);
        modal.getBodyPromise().then(updateButtonStates).catch(Notification.exception);

        // TODO MDL-82204 - there is not currently a good way to add a help icon to a modal overall, so we do it this way.
        modal.getTitlePromise().then((title) => {
            title.append(' ' + document.getElementById(SELECTORS.regradeAttemptsButtonId).dataset.helpIcon);
            // The next line is necessary to get a nice layout of the help icon.
            title[0].querySelector('a').classList.add('align-baseline');
            return title[0];
        }).catch(Notification.exception);
    } catch (ex) {
        await Notification.exception(ex);
    }
};

/**
 * Enables and disables controls when the selected options are changed.
 */
const updateButtonStates = () => {
    const allQuestionsButton = document.querySelector(SELECTORS.allQuestionsButton);

    // Question checkboxes enabled only if Selected questions is checked.
    document.querySelectorAll(SELECTORS.questionCheckbox).forEach((questionCheckbox) => {
        questionCheckbox.disabled = allQuestionsButton.checked;
    });

    // State of submit buttons.
    const isAnyQuestionSelected = document.querySelector(SELECTORS.questionCheckbox + ':checked') !== null;
    const canSubmit = allQuestionsButton.checked || isAnyQuestionSelected;
    document.querySelector(SELECTORS.regradeButton).disabled = !canSubmit;
    document.querySelector(SELECTORS.dryRunButton).disabled = !canSubmit;
};

/**
 * Initialize the regrade button to open the modal.
 */
export const init = () => {
    const regradeButton = document.getElementById(SELECTORS.regradeAttemptsButtonId);
    if (!regradeButton) {
        return;
    }

    regradeButton.addEventListener('click', showModal);
};
