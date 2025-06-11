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
 * JS module for toggling the sensitive input visibility (e.g. passwords, keys).
 *
 * @module     core/togglesensitive
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {isExtraSmall} from 'core/pagehelpers';
import Templates from 'core/templates';
import Pending from 'core/pending';
import Prefetch from 'core/prefetch';
import Notification from 'core/notification';
import {notifyFieldStructureChanged} from 'core_form/events';

const SELECTORS = {
    BUTTON: '.toggle-sensitive-btn',
    ICON: '.toggle-sensitive-btn .icon',
};

const PIX = {
    EYE: 't/hide',
    EYE_SLASH: 't/show',
};

let sensitiveElementId;
let smallScreensOnly;

/**
 * Entrypoint of the js.
 *
 * @method init
 * @param {String} elementId Form button element.
 * @param {boolean} isSmallScreensOnly Is this for small screens only?
 */
export const init = (elementId, isSmallScreensOnly = false) => {
    const sensitiveInput = document.getElementById(elementId);
    if (sensitiveInput === null) {
        // Exit early if invalid element id passed.
        return;
    }
    sensitiveElementId = elementId;
    smallScreensOnly = isSmallScreensOnly;
    Prefetch.prefetchTemplate('core/form_input_toggle_sensitive');
    // Render the sensitive input with a toggle button.
    renderSensitiveToggle(sensitiveInput);
    // Register event listeners.
    registerListenerEvents();
};

/**
 * Render the new input html with toggle button and update the incoming html.
 *
 * @method renderSensitiveToggle
 * @param {HTMLElement} sensitiveInput HTML element for the sensitive input.
 */
const renderSensitiveToggle = (sensitiveInput) => {
    Templates.render(
        'core/form_input_toggle_sensitive',
        {
            smallscreensonly: smallScreensOnly,
            sensitiveinput: sensitiveInput.outerHTML,
        }
    ).then((html) => {
        sensitiveInput.outerHTML = html;
        // Dispatch the event indicating the sensitive input has changed.
        notifyFieldStructureChanged(sensitiveInput.id);
        return;
    }).catch(Notification.exception);
};

/**
 * Register event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    // Toggle the sensitive input visibility when interacting with the toggle button.
    document.addEventListener('click', handleButtonInteraction);
    // For small screens only, hide all sensitive inputs when the screen is enlarged.
    if (smallScreensOnly) {
        window.addEventListener('resize', handleScreenResizing);
    }
};

/**
 * Handle events trigger by interacting with the toggle button.
 *
 * @method handleButtonInteraction
 * @param {Event} event The button event.
 */
const handleButtonInteraction = (event) => {
    const toggleButton = event.target.closest(SELECTORS.BUTTON);
    if (toggleButton) {
        const sensitiveInput = document.getElementById(sensitiveElementId);
        if (sensitiveInput) {
            toggleSensitiveVisibility(sensitiveInput, toggleButton);
        }
    }
};

/**
 * Handle events trigger by resizing the screen.
 *
 * @method handleScreenResizing
 */
const handleScreenResizing = () => {
    if (!isExtraSmall()) {
        const sensitiveInput = document.getElementById(sensitiveElementId);
        if (sensitiveInput) {
            const toggleButton = sensitiveInput.parentNode.querySelector(SELECTORS.BUTTON);
            if (toggleButton) {
                toggleSensitiveVisibility(sensitiveInput, toggleButton, true);
            }
        }
    }
};

/**
 * Toggle the sensitive input visibility and its associated icon.
 *
 * @method toggleSensitiveVisibility
 * @param {HTMLInputElement} sensitiveInput The sensitive input element.
 * @param {HTMLElement} toggleButton The toggle button.
 * @param {boolean} force Force the input back to password type.
 */
const toggleSensitiveVisibility = (sensitiveInput, toggleButton, force = false) => {
    const pendingPromise = new Pending('core/togglesensitive:toggle');
    let type;
    let icon;
    if (force === true) {
        type = 'password';
        icon = PIX.EYE;
    } else {
        type = sensitiveInput.getAttribute('type') === 'password' ? 'text' : 'password';
        icon = sensitiveInput.getAttribute('type') === 'password' ? PIX.EYE_SLASH : PIX.EYE;
    }
    sensitiveInput.setAttribute('type', type);
    Templates.renderPix(icon, 'core').then((icon) => {
        toggleButton.innerHTML = icon;
        pendingPromise.resolve();
        return;
    }).catch(Notification.exception);
};
