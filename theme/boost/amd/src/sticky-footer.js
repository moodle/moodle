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
 * Sticky footer module.
 *
 * @module     theme_boost/sticky-footer
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import {registerManager, init as defaultInit} from 'core/sticky-footer';

const SELECTORS = {
    STICKYFOOTER: '.stickyfooter',
    PAGE: '#page',
};

const CLASSES = {
    HASSTICKYFOOTER: 'hasstickyfooter',
};

let initialized = false;

let previousScrollPosition = 0;

let enabled = false;

/**
 * Scroll handler.
 * @package
 */
const scrollSpy = () => {
    if (!enabled) {
        return;
    }
    // Ignore scroll if page size is not small.
    if (document.body.clientWidth >= 768) {
        return;
    }
    // Detect if scroll is going down.
    let scrollPosition = window.scrollY;
    if (scrollPosition > previousScrollPosition) {
        hideStickyFooter();
    } else {
        showStickyFooter();
    }
    previousScrollPosition = scrollPosition;
};

/**
 * Return if the sticky footer must be enabled by default or not.
 * @returns {Boolean} true if the sticky footer is enabled automatic.
 */
const isDisabledByDefault = () => {
    const footer = document.querySelector(SELECTORS.STICKYFOOTER);
    if (!footer) {
        return false;
    }
    return !!footer.dataset.disable;
};

/**
 * Show the sticky footer in the page.
 */
const showStickyFooter = () => {
    // We need some seconds to make sure the CSS animation is ready.
    const pendingPromise = new Pending('theme_boost/sticky-footer:enabling');
    const footer = document.querySelector(SELECTORS.STICKYFOOTER);
    const page = document.querySelector(SELECTORS.PAGE);
    if (footer && page) {
        document.body.classList.add(CLASSES.HASSTICKYFOOTER);
        page.classList.add(CLASSES.HASSTICKYFOOTER);
    }
    setTimeout(() => pendingPromise.resolve(), 1000);
};

/**
 * Hide the sticky footer in the page.
 */
const hideStickyFooter = () => {
    document.body.classList.remove(CLASSES.HASSTICKYFOOTER);
    const page = document.querySelector(SELECTORS.PAGE);
    page?.classList.remove(CLASSES.HASSTICKYFOOTER);
};

/**
 * Enable sticky footer in the page.
 */
export const enableStickyFooter = () => {
    enabled = true;
    showStickyFooter();
};

/**
 * Disable sticky footer in the page.
 */
export const disableStickyFooter = () => {
    enabled = false;
    hideStickyFooter();
};

/**
 * Initialize the module.
 */
export const init = () => {
    // Prevent sticky footer in behat.
    if (initialized || document.body.classList.contains('behat-site')) {
        defaultInit();
        return;
    }
    initialized = true;
    if (!isDisabledByDefault()) {
        enableStickyFooter();
    }

    document.addEventListener("scroll", scrollSpy);

    registerManager({
        enableStickyFooter,
        disableStickyFooter,
    });
};
