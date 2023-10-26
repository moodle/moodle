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
 * Javascript for preview.
 *
 * @module     qbank_preview/preview
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

/**
 * Set up the actions.
 *
 * @method init
 * @param {bool} redirect Redirect.
 * @param {string} url url to redirect.
 */
export const init = (redirect, url) => {
    if (!redirect) {
        let closeButton = document.getElementById('close-previewquestion-page');
        closeButton.onclick = () => {
            if (window.opener === null) {
                location.href = url;
            } else {
                window.close();
            }
        };
    }
    // Set up the form to be displayed.
    setupQuestionForm('responseform');
};

/**
 * Set up the form element to be displayed.
 *
 * @method setupQuestionForm
 * @param {string} formElement The form element.
 */
const setupQuestionForm = (formElement) => {
    let form = document.getElementById(formElement);
    if (form) {
        // Turning off browser autocomplete.
        autocompleteOff(form);
        // Stop a question form being submitted more than once.
        preventRepeatSubmission(form);
        // Removes any '.questionflagsavebutton's, since we have JavaScript to toggle.
        removeClass('.questionflagsavebutton', form);
        // Scroll to the position indicated by scrollpos= in the URL, if it is there.
        scrollToSavedPos(form);
    }
};

/**
 * Set the autocomplete off.
 *
 * @method autocompleteOff
 * @param {object} form The form element.
 */
const autocompleteOff = (form) => {
    form.setAttribute("autocomplete", "off");
};

/**
 * Event handler to stop a question form being submitted more than once.
 *
 * @method preventRepeatSubmission
 * @param {object} form The form element.
 */
const preventRepeatSubmission = (form) => {
    form.addEventListener("submit", function() {
        $(this).submit(function() {
            return false;
        });
        return true;
    });
};

/**
 *  Removes a class inside an element.
 *
 * @method removeClass
 * @param {string} classname Class name.
 * @param {object} form The form element.
 */
const removeClass = (classname, form) => {
    form.querySelectorAll(classname).forEach(e => e.remove());
};

/**
 *  If there is a parameter like scrollpos=123 in the URL, scroll to that saved position.
 *  (Note: Moodle 4.0 and above do NOT support Internet Explorer 11 and below.)
 *
 * @method scrollToSavedPos
 * @param {object} form The form element.
 */
const scrollToSavedPos = (form) => {
    let matches = window.location.href.match(/^.*[?&]scrollpos=(\d*)(?:&|$|#).*$/, '$1');
    if (matches) {
        // DOMContentLoaded is the effective one here. I am leaving the immediate call to
        // window.scrollTo in case it reduces flicker.
        window.scrollTo(0, matches[1]);
        form.addEventListener("DOMContentLoaded", () => {
            window.scrollTo(0, matches[1]);
        });
    }
};
