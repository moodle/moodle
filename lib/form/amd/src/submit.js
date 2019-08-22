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
 * Submit button JavaScript. All submit buttons will be automatically disabled once the form is
 * submitted, unless that submission results in an error/cancelling the submit.
 *
 * @module core_form/submit
 * @package core_form
 * @copyright 2019 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 3.8
 */

/**
 * Initialises submit buttons.
 *
 * @param {String} elementId Form element
 */
export const init = (elementId) => {
    const button = document.getElementById(elementId);
    button.form.addEventListener('submit', function() {
        // Only disable it if the browser is really going to another page as a result of the
        // submit.
        const disableAction = function() {
            button.disabled = true;
        };
        window.addEventListener('beforeunload', disableAction);
        // If there is no beforeunload event as a result of this form submit, then the form
        // submit must have been cancelled, so don't disable the button if the page is
        // unloaded later.
        setTimeout(function() {
            window.removeEventListener('beforeunload', disableAction);
        }, 0);
    }, false);
};
