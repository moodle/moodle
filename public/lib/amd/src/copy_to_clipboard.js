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
 * A JavaScript module that enhances a button and text container to support copy-to-clipboard functionality.
 *
 * This module needs to be loaded by pages/templates/modules that require this functionality.
 *
 * To enable copy-to-clipboard functionality, we need a trigger element (usually a button) and a copy target element
 * (e.g. a div, span, text input, or text area).
 *
 * In the trigger element, we need to declare the <code>data-action="copytoclipboard"</code> attribute and set the
 * <code>data-clipboard-target</code> attribute which is the CSS selector that points to the target element that contains the text
 * to be copied.
 *
 * When the text is successfully copied to the clipboard, a toast message that indicates that the copy operation was a success
 * will be shown. This success message can be customised by setting the <code>data-clipboard-success-message</code> attribute in the
 * trigger element.
 *
 * @module     core/copy_to_clipboard
 * @copyright  2021 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Markup for the trigger and target elements</caption>
 * <input type="text" id="textinputtocopy" class="form-control" value="Copy me!" readonly />
 * <button id="copybutton" data-action="copytoclipboard" data-clipboard-target="#textinputtocopy"
 *         data-clipboard-success-message="Success!" class="btn btn-secondary">
 *     Copy to clipboard
 * </button>
 */
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import {prefetchStrings} from 'core/prefetch';

/**
 * Add event listeners to trigger elements through event delegation.
 *
 * @private
 */
const addEventListeners = () => {
    document.addEventListener('click', e => {
        const copyButton = e.target.closest('[data-action="copytoclipboard"]');
        if (!copyButton) {
            return;
        }

        if (!copyButton.dataset.clipboardTarget) {
            return;
        }

        const copyTarget = document.querySelector(copyButton.dataset.clipboardTarget);
        if (!copyTarget) {
            return;
        }

        // This is a copy target and there is content.
        // Prevent the default action.
        e.preventDefault();

        // We have a copy target - great. Let's copy its content.
        const textToCopy = getTextFromContainer(copyTarget);
        if (!textToCopy) {
            displayFailureToast();
            return;
        }

        if (navigator.clipboard) {
            navigator.clipboard.writeText(textToCopy)
                .then(() => displaySuccessToast(copyButton)).catch();

            return;
        }

        // The clipboard API is not available.
        // This may happen when the page is not served over SSL.
        // Try to fall back to document.execCommand() approach of copying the text.
        // WARNING: This is deprecated functionality that may get dropped at anytime by browsers.

        if (copyTarget instanceof HTMLInputElement || copyTarget instanceof HTMLTextAreaElement) {
            // Focus and select the text in the target element.
            // If the execCommand fails, at least the user can readily copy the text.
            copyTarget.focus();

            if (copyNodeContentToClipboard(copyButton, copyTarget)) {
                // If the copy was successful then focus back on the copy button.
                copyButton.focus();
            }
        } else {
            // This copyTarget is not an input, or text area so cannot be used with the execCommand('copy') command.
            // To work around this we create a new textarea and copy that.
            // This textarea must be part of the DOM and must be visible.
            // We (ab)use the visually-hidden tag to ensure that it is considered visible to the browser, whilst being
            // hidden from view by the user.
            const copyRegion = document.createElement('textarea');
            copyRegion.value = textToCopy;
            copyRegion.classList.add('visually-hidden');
            document.body.appendChild(copyRegion);

            copyNodeContentToClipboard(copyButton, copyRegion);

            // After copying, remove the temporary element and move focus back to the triggering button.
            copyRegion.remove();
            copyButton.focus();
        }
    });
};

/**
 * Copy the content of the selected element to the clipboard, and display a notifiction if successful.
 *
 * @param {HTMLElement} copyButton
 * @param {HTMLElement} copyTarget
 * @returns {boolean}
 * @private
 */
const copyNodeContentToClipboard = (copyButton, copyTarget) => {
    copyTarget.select();

    // Try to copy the text from the target element.
    if (document.execCommand('copy')) {
        displaySuccessToast(copyButton);
        return true;
    }

    displayFailureToast();
    return false;
};

/**
 * Displays a toast containing the success message.
 *
 * @param {HTMLElement} copyButton The element that copies the text from the container.
 * @returns {Promise<void>}
 * @private
 */
const displaySuccessToast = copyButton => getSuccessText(copyButton)
    .then(successMessage => addToast(successMessage, {}));

/**
 * Displays a toast containing the failure message.
 *
 * @returns {Promise<void>}
 * @private
 */
const displayFailureToast = () => getFailureText()
    .then(message => addToast(message, {type: 'warning'}));

/**
 * Fetches the failure message to show to the user.
 *
 * @returns {Promise}
 * @private
 */
const getFailureText = () => getString('unabletocopytoclipboard', 'core');

/**
 * Fetches the success message to show to the user.
 *
 * @param {HTMLElement} copyButton The element that copies the text from the container. This may contain the custom success message
 * via its data-clipboard-success-message attribute.
 * @returns {Promise|*}
 * @private
 */
const getSuccessText = copyButton => {
    if (copyButton.dataset.clipboardSuccessMessage) {
        return Promise.resolve(copyButton.dataset.clipboardSuccessMessage);
    }

    return getString('textcopiedtoclipboard', 'core');
};

/**
 * Fetches the text to be copied from the container.
 *
 * @param {HTMLElement} container The element containing the text to be copied.
 * @returns {null|string}
 * @private
 */
const getTextFromContainer = container => {
    if (container.value) {
        // For containers which are form elements (e.g. text area, text input), get the element's value.
        return container.value;
    } else if (container.innerText) {
        // For other elements, try to use the innerText attribute.
        return container.innerText;
    }

    return null;
};

let loaded = false;
if (!loaded) {
    prefetchStrings('core', [
        'textcopiedtoclipboard',
        'unabletocopytoclipboard',
    ]);

    // Add event listeners.
    addEventListeners();
    loaded = true;
}
