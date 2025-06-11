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
 * Blocks drawer module.
 * Drawer related logic.
 *
 * @module     theme_snap/blocks_drawer
 * @copyright  2025 Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Drawers from 'theme_boost/drawers';

const SELECTORS = {
    DRAWER_OPEN_BUTTON: '.drawer-toggler.drawer-right-toggle > button',
    BLOCK_SETTINGS: '.block_settings',
    DRAWER: '.drawer',
};

/**
 * Setup event listeners.
 */
const setupEventListeners = () => {
    document.addEventListener(Drawers.eventTypes.drawerShow, e => {
        const originalPreventOverlap = e.detail.drawerInstance.preventOverlap;
        e.detail.drawerInstance.preventOverlap = () => {
            if (document.querySelector(SELECTORS.DRAWER_OPEN_BUTTON) != document.activeElement) {
                originalPreventOverlap.call(document.activeElement);
            }
        };
    }, true);
    document.addEventListener('focusin', (e) => {
        if (document.querySelector(SELECTORS.DRAWER).contains(document.activeElement) ||
            document.activeElement.tagName === 'BODY' ||
            document.activeElement.tagName === 'BUTTON') {
            e.stopPropagation();
        }
    }, true);
    document.addEventListener('focusout', (e) => {
        if (document.querySelector(SELECTORS.DRAWER).contains(document.activeElement) ||
            document.activeElement.tagName === 'BODY' ||
            document.activeElement.tagName === 'BUTTON') {
            e.stopPropagation();
        }
    }, true);
};

/**
 * Reposition the settings block above the drawer.
 * Note: This is because the settings block is handled outside the drawer.
 */
const repositionSettingsBlock = () => {
    const settingsBlock = document.querySelector(SELECTORS.BLOCK_SETTINGS);
    if (settingsBlock) {
        settingsBlock.parentNode.removeChild(settingsBlock);
        const drawer = document.querySelector(SELECTORS.DRAWER);
        drawer.parentNode.insertBefore(settingsBlock, drawer);
    }
};

export const init = () => {
    setupEventListeners();
    repositionSettingsBlock();
};
