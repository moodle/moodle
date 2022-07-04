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
 * A javascript module to handle submission confirmation for quiz.
 *
 * @module mod_quiz/submission_confirmation
 * @copyright 2022 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 4.1
 */

import Notification from 'core/notification';
import Prefetch from 'core/prefetch';
import {get_string as getString} from 'core/str';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';

const SELECTOR = {
    attemptSubmitButton: '.path-mod-quiz .btn-finishattempt button',
    attemptSubmitForm: 'form#frm-finishattempt',
};

/**
 * Register events for attempt submit button.
 */
const registerEventListeners = () => {
    const submitAction = document.querySelector(SELECTOR.attemptSubmitButton);
    if (submitAction) {
        submitAction.addEventListener('click', e => {
            e.preventDefault();
            Modal.create({
                type: Modal.types.SAVE_CANCEL,
                title: getString('confirmation', 'admin'),
                body: getString('confirmclose', 'quiz'),
                buttons: {
                    save: getString('submitallandfinish', 'quiz')
                },
            }).then(modal => {
                modal.show();
                return modal;
            }).then(modal => {
                modal.getRoot().on(ModalEvents.save, () => {
                    const attemptForm = submitAction.closest(SELECTOR.attemptSubmitForm);
                    attemptForm.submit();
                });
                return modal;
            }).catch(Notification.exception);
        });
    }
};

/**
 * Initialises.
 */
export const init = () => {
    Prefetch.prefetchStrings('core_admin', ['confirmation']);
    Prefetch.prefetchStrings('quiz', ['confirmclose', 'submitallandfinish']);
    registerEventListeners();
};
