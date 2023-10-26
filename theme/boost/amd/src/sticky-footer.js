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

const SELECTORS = {
    STICKYFOOTER: '.stickyfooter',
    PAGE: '#page',
};

const CLASSES = {
    HASSTICKYFOOTER: 'hasstickyfooter',
};

let initialized = false;

let previousScrollPosition = 0;

/**
 * Return the current page scroll position.
 * @package
 * @returns {number} the current scroll position
 */
const getScrollPosition = () => {
    const page = document.querySelector(SELECTORS.PAGE);
    if (page) {
        return page.scrollTop;
    }
    return window.pageYOffset;
};

/**
 * Scroll handler.
 * @package
 */
const scrollSpy = () => {
    // Ignore scroll if page size is not small.
    if (document.body.clientWidth >= 768) {
        return;
    }
    // Detect if scroll is going down.
    let scrollPosition = getScrollPosition();
    if (scrollPosition > previousScrollPosition) {
        disableStickyFooter();
    } else {
        enableStickyFooter();
    }
    previousScrollPosition = scrollPosition;
};

/**
 * Enable sticky footer in the page.
 */
export const enableStickyFooter = () => {
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
 * Disable sticky footer in the page.
 */
export const disableStickyFooter = () => {
    document.body.classList.remove(CLASSES.HASSTICKYFOOTER);
    const page = document.querySelector(SELECTORS.PAGE);
    page?.classList.remove(CLASSES.HASSTICKYFOOTER);
};

/**
 * Initialize the module.
 */
export const init = () => {
    // Prevent sticky footer in behat.
    if (initialized || document.body.classList.contains('behat-site')) {
        return;
    }
    initialized = true;
    enableStickyFooter();
    const content = document.querySelector(SELECTORS.PAGE) ?? document.body;
    content.addEventListener("scroll", scrollSpy);
};
