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
 * Event handling for the edit random question form.
 *
 * Dynamically saves the new filter condition before navigating back to the quiz question list.
 *
 * @module     mod_quiz/update_random_question_filter_condition
 * @author      2022 <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';
import Notification from 'core/notification';

export const init = () => {
    const SELECTORS = {
        QUESTION_BANK_CONTAINER: '#questionbank_container',
        FORM_ELEMENT: '#update_filter_condition_form',
        UPDATE_BUTTON: '[name="update"]',
        CANCEL_BUTTON: '[name="cancel"]',
        MESSAGE_INPUT: '[name="message"]',
        FILTER_CONDITION_ELEMENT: '[data-filtercondition]',
    };

    const questionBank = document.querySelector(SELECTORS.QUESTION_BANK_CONTAINER);
    const form = document.querySelector(SELECTORS.FORM_ELEMENT);
    const updateButton = form.querySelector(SELECTORS.UPDATE_BUTTON);

    updateButton.addEventListener("click", async(e) => {
        e.preventDefault();
        const request = {
            methodname: 'mod_quiz_update_filter_condition',
            args: {
                cmid: form.dataset?.cmid,
                slotid: form.dataset?.slotid,
                filtercondition: questionBank.querySelector(SELECTORS.FILTER_CONDITION_ELEMENT).dataset?.filtercondition,
            }
        };
        try {
            const response = await ajax.call([request])[0];
            const messageInput = form.querySelector(SELECTORS.MESSAGE_INPUT);
            messageInput.value = response.message;
            form.submit();
        } catch (e) {
            Notification.exception(e);
        }
    });

};
