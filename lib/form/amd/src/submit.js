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

/** @type {number} ID for setInterval used when polling for download cookie */
let cookieListener = 0;

/** @type {Array} Array of buttons that need re-enabling if we get a download cookie */
const cookieListeningButtons = [];

/**
 * Listens in case a download cookie is provided.
 *
 * This function is used to detect file downloads. If there is a file download then we get a
 * beforeunload event, but the page is never unloaded and when the file download completes we
 * should re-enable the buttons. We detect this by watching for a specific cookie.
 *
 * PHP function \core_form\util::form_download_complete() can be used to send this cookie.
 *
 * @param {HTMLElement} button Button to re-enable
 */
const listenForDownloadCookie = (button) => {
    cookieListeningButtons.push(button);
    if (!cookieListener) {
        cookieListener = setInterval(() => {
            // Look for cookie.
            const parts = document.cookie.split(getCookieName() + '=');
            if (parts.length == 2) {
                // We found the cookie, so the file is ready. Expire the cookie and cancel polling.
                clearDownloadCookie();
                clearInterval(cookieListener);
                cookieListener = 0;

                // Re-enable all the buttons.
                cookieListeningButtons.forEach((button) => {
                    button.disabled = false;
                });
            }
        }, 500);
    }
};

/**
 * Gets a unique name for the download cookie.
 *
 * @returns {string} Cookie name
 */
const getCookieName = () => {
    return 'moodledownload_' + M.cfg.sesskey;
};

/**
 * Clears the download cookie if there is one.
 */
const clearDownloadCookie = () => {
    document.cookie = encodeURIComponent(getCookieName()) + '=deleted; expires=' + new Date(0).toUTCString();
};

/**
 * Initialises submit buttons.
 *
 * @param {String} elementId Form element
 */
export const init = (elementId) => {
    const button = document.getElementById(elementId);
    // If the form has double submit protection disabled, do nothing.
    if (button.form.dataset.doubleSubmitProtection === 'off') {
        return;
    }
    button.form.addEventListener('submit', function(event) {
        // Only disable it if the browser is really going to another page as a result of the
        // submit.
        const disableAction = function() {
            // If the submit was cancelled, or the button is already disabled, don't do anything.
            if (event.defaultPrevented || button.disabled) {
                return;
            }

            button.disabled = true;
            clearDownloadCookie();
            listenForDownloadCookie(button);
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
