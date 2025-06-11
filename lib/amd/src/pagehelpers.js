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
 * Page utility helpers.
 *
 * @module core/pagehelpers
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Maximum sizes for breakpoints. This needs to correspond with Bootstrap
 * Breakpoints
 *
 * @private
 */
const Sizes = {
    small: 576,
    medium: 991,
    large: 1400
};

const Selectors = {
    focusable: 'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])',
};

const Classes = {
    behatSite: 'behat-site',
};

/**
 * Check fi the current page is a Behat site.
 * @returns {boolean} true if the current page is a Behat site.
 */
export const isBehatSite = () => {
    return document.body.classList.contains(Classes.behatSite);
};

/**
 * Get the current body width.
 * @returns {number} the current body width.
 */
export const getCurrentWidth = () => {
    const DomRect = document.body.getBoundingClientRect();
    return DomRect.x + DomRect.width;
};

/**
 * Check if the user uses an extra small size browser.
 *
 * @returns {boolean} true if the body is smaller than sizes.small max size.
 */
export const isExtraSmall = () => {
    const browserWidth = getCurrentWidth();
    return browserWidth < Sizes.small;
};

/**
 * Check if the user uses a small size browser.
 *
 * @returns {boolean} true if the body is smaller than sizes.medium max size.
 */
export const isSmall = () => {
    const browserWidth = getCurrentWidth();
    return browserWidth < Sizes.medium;
};

/**
 * Check if the user uses a large size browser.
 *
 * @returns {boolean} true if the body is smaller than sizes.large max size.
 */
export const isLarge = () => {
    const browserWidth = getCurrentWidth();
    return browserWidth >= Sizes.large;
};

/**
 * Get the first focusable element inside a container.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @returns {HTMLElement|null}
 */
export const firstFocusableElement = (container) => {
    const containerElement = container || document;
    return containerElement.querySelector(Selectors.focusable);
};

/**
 * Get the last focusable element inside a container.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @returns {HTMLElement|null}
 */
export const lastFocusableElement = (container) => {
    const containerElement = container || document;
    const focusableElements = containerElement.querySelectorAll(Selectors.focusable);
    return focusableElements[focusableElements.length - 1] ?? null;
};

/**
 * Get all focusable elements inside a container.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @returns {HTMLElement[]}
 */
export const focusableElements = (container) => {
    const containerElement = container || document;
    return containerElement.querySelectorAll(Selectors.focusable);
};

/**
 * Get the previous focusable element in a container.
 * It uses the current focused element to know where to start the search.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @param {Boolean} [loopSelection] Whether to loop selection or not. Default to false.
 * @returns {HTMLElement|null}
 */
export const previousFocusableElement = (container, loopSelection) => {
    return getRelativeFocusableElement(container, loopSelection, -1);
};

/**
 * Get the next focusable element in a container.
 * It uses the current focused element to know where to start the search.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @param {Boolean} [loopSelection] Whether to loop selection or not. Default to false.
 * @returns {HTMLElement|null}
 */
export const nextFocusableElement = (container, loopSelection) => {
    return getRelativeFocusableElement(container, loopSelection, 1);
};

/**
 * Internal function to get the next or previous focusable element.
 * @param {HTMLElement} [container] Container to search in. Defaults to document.
 * @param {Boolean} [loopSelection] Whether to loop selection or not.
 * @param {Number} [direction] Direction to search in. 1 for next, -1 for previous.
 * @returns {HTMLElement|null}
 * @private
 */
const getRelativeFocusableElement = (container, loopSelection, direction) => {
    const focusedElement = document.activeElement;
    const focusables = [...focusableElements(container)];
    const focusedIndex = focusables.indexOf(focusedElement);

    if (focusedIndex === -1) {
        return null;
    }

    const newIndex = focusedIndex + direction;

    if (focusables[newIndex] !== undefined) {
        return focusables[newIndex];
    }
    if (loopSelection != true) {
        return null;
    }
    if (direction > 0) {
        return focusables[0] ?? null;
    }
    return focusables[focusables.length - 1] ?? null;
};
