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
 * Switching between the light and dark colour modes.
 *
 * The mode is applied straight away by updating the data-bs-theme attribute of the html tag, which is what every
 * Bootstrap colour mode override keys off. The "auto" mode is resolved against the colour scheme reported by the
 * device; the listener keeping it up to date lives in the script added to the page head, so that dark mode users do
 * not get a flash of the light theme before this module has loaded.
 *
 * @module     theme_boost/colourmode
 * @copyright  2026 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {setUserPreference} from 'core_user/repository';

const PREFERENCE = 'theme_boost_colourmode';

const SELECTORS = {
    MENU: '[data-region="colourmode-menu"]',
    OPTION: '[data-action="set-colourmode"]',
    CURRENTICON: '[data-region="colourmode-current-icon"]',
    CURRENTLABEL: '[data-region="colourmode-current-label"]',
};

const MODES = {
    LIGHT: 'light',
    DARK: 'dark',
    AUTO: 'auto',
};

let registered = false;

/**
 * Work out which colour mode should actually be rendered for the given mode.
 *
 * @param {String} mode One of the MODES values.
 * @returns {String} Either MODES.LIGHT or MODES.DARK.
 */
const resolveMode = (mode) => {
    if (mode !== MODES.AUTO) {
        return mode;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? MODES.DARK : MODES.LIGHT;
};

/**
 * Apply a colour mode to the page and remember it for the next page load.
 *
 * @param {String} mode One of the MODES values.
 */
const applyMode = (mode) => {
    const root = document.documentElement;
    root.setAttribute('data-colourmode', mode);
    root.setAttribute('data-bs-theme', resolveMode(mode));

    document.querySelectorAll(SELECTORS.MENU).forEach((menu) => {
        menu.querySelectorAll(SELECTORS.OPTION).forEach((option) => {
            const isactive = option.dataset.colourmode === mode;
            option.setAttribute('aria-checked', isactive ? 'true' : 'false');
            option.classList.toggle('active', isactive);
            if (!isactive) {
                return;
            }

            const icon = menu.querySelector(SELECTORS.CURRENTICON);
            if (icon) {
                icon.className = `icon fa ${option.dataset.icon} fa-fw m-0`;
            }

            const label = menu.querySelector(SELECTORS.CURRENTLABEL);
            if (label) {
                label.textContent = option.dataset.togglelabel;
            }
        });
    });

    setUserPreference(PREFERENCE, mode);
};

/**
 * Register the colour mode switcher.
 */
export const init = () => {
    if (registered) {
        return;
    }
    registered = true;

    document.addEventListener('click', (e) => {
        const option = e.target.closest(SELECTORS.OPTION);
        if (!option) {
            return;
        }

        e.preventDefault();
        applyMode(option.dataset.colourmode);
    });
};
