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
 * JavaScript required by the question engine.
 *
 * @module     core_question/question_engine
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as scrollManager from 'core/scroll_manager';
import * as formSubmit from 'core_form/submit';

/**
 * Initialise a question submit button. This saves the scroll position and
 * sets the fragment on the form submit URL so the page reloads in the right place.
 *
 * @param {string} button the id of the button in the HTML.
 */
export const initSubmitButton = button => {
    formSubmit.init(button);
    scrollManager.watchScrollButtonSaves();
};

/**
 * Initialise a form that contains questions printed using print_question.
 * This has the effect of:
 * 1. Turning off browser autocomlete.
 * 2. Stopping enter from submitting the form (or toggling the next flag) unless
 *    keyboard focus is on the submit button or the flag.
 * 3. Removes any '.questionflagsavebutton's, since we have JavaScript to toggle
 *    the flags using ajax.
 * 4. Scroll to the position indicated by scrollpos= in the URL, if it is there.
 * 5. Prevent the user from repeatedly submitting the form.
 *
 * @param {string} formSelector Selector to identify the form.
 */
export const initForm = (formSelector) => {
    const form = document.querySelector(formSelector);
    form.setAttribute('autocomplete', 'off');

    form.addEventListener('submit', preventRepeatSubmission);

    form.addEventListener('key', (event) => {
        if (event.keyCode !== 13) {
            return;
        }

        if (event.target.matches('a')) {
            return;
        }

        if (event.target.matches('input[type="submit"]')) {
            return;
        }

        if (event.target.matches('input[type=img]')) {
            return;
        }

        if (event.target.matches('textarea') || event.target.matches('[contenteditable=true]')) {
            return;
        }

        event.preventDefault();
    });

    const questionFlagSaveButtons = form.querySelectorAll('.questionflagsavebutton');
    [...questionFlagSaveButtons].forEach((node) => node.remove());

    // Note: The scrollToSavedPosition function tries to wait until the content has loaded before firing.
    scrollManager.scrollToSavedPosition();
};

/**
 * Event handler to stop a question form being submitted more than once.
 *
 * @param {object} event the form submit event.
 */
export const preventRepeatSubmission = (event) => {
    const form = event.target.closest('form');
    if (form.dataset.formSubmitted === '1') {
        event.preventDefault();
        return;
    }

    setTimeout(() => {
        [...form.querySelectorAll('input[type=submit]')].forEach((input) => input.setAttribute('disabled', true));
    });
    form.dataset.formSubmitted = '1';
};
