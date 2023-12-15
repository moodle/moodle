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
 * Sticky footer wrapper module.
 *
 * Themes are responsible for implementing the sticky footer. However,
 * modules can interact with the sticky footer using this module.
 *
 * @module     core/sticky-footer
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {dispatchEvent} from 'core/event_dispatcher';

let manager = {};

let enabled = false;

let initialized = false;

export const SELECTORS = {
    STICKYFOOTER: '.stickyfooter',
};

const CLASSES = {
    INVISIBLE: 'v-hidden',
};

export const eventTypes = {
    stickyFooterStateChanged: 'core/stickyfooter_state_changed',
};

/**
 * Enable sticky footer in the page.
 */
export const enableStickyFooter = () => {
    enabled = true;
    if (manager.enableStickyFooter === undefined) {
        document.querySelector(SELECTORS.STICKYFOOTER)?.classList.remove(CLASSES.INVISIBLE);
        return;
    }
    manager.enableStickyFooter();
    notifyStickyFooterStateChange(true);
};

/**
 * Disable sticky footer in the page.
 */
export const disableStickyFooter = () => {
    enabled = false;
    if (manager.disableStickyFooter === undefined) {
        document.querySelector(SELECTORS.STICKYFOOTER)?.classList.add(CLASSES.INVISIBLE);
        return;
    }
    manager.disableStickyFooter();
    notifyStickyFooterStateChange(false);
};

/**
 * Register the theme sticky footer methods.
 *
 * @param {Object} themeManager the manager object with all the needed methods.
 * @param {Function} themeManager.enableStickyFooter enable sticky footer method
 * @param {Function} themeManager.disableStickyFooter disable sticky footer method
 */
export const registerManager = (themeManager) => {
    manager = themeManager;
    if (enabled) {
        enableStickyFooter();
    }
};

/**
 * Initialize the module if the theme does not implement its own init.
 */
export const init = () => {
    if (initialized) {
        return;
    }
    initialized = true;

    const isDisabled = document.querySelector(SELECTORS.STICKYFOOTER)?.dataset.disable;
    if (isDisabled) {
        disableStickyFooter();
    } else {
        enableStickyFooter();
    }
};

/**
 * Dispatch an event to notify that the state (enabled/disabled) of the sticky footer has changed.
 *
 * @param {boolean} enabled Whether the sticky footer has been enabled or disabled.
 */
const notifyStickyFooterStateChange = (enabled) => {
    dispatchEvent(eventTypes.stickyFooterStateChanged, {enabled: enabled}, document, {cancelable: false});
};
