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
 * Scroll manager is a class that help with saving the scroll positing when you
 * click on an action icon, and then when the page is reloaded after processing
 * the action, it scrolls you to exactly where you were. This is much nicer for
 * the user.
 *
 * To use this in your code, you need to ensure that:
 * 1. The button that triggers the action has to have a click event handler that
 *    calls saveScrollPos()
 * 2. After doing the processing, the redirect() function will add 'mdlscrollto'
 *    parameter into the redirect url automatically.
 * 3. Finally, on the page that is reloaded (which should be the same as the one
 *    the user started on) you need to call scrollToSavedPosition()
 *    on page load.
 *
 * @module     core/scroll_manager
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @property {HTMLElement} scrollingElement the current scrolling element. */
let scrollingElement = null;

/**
 * Is the element scrollable?
 *
 * @param {HTMLElement} element Element.
 * @returns {boolean}
 */
const isScrollable = (element) => {
    // Check if the element has scrollable content.
    const hasScrollableContent = element.scrollHeight > element.clientHeight;

    // If 'overflow-y' is set to hidden, the scroll bar is't show.
    const elementOverflow = window.getComputedStyle(element).overflowY;
    const isOverflowHidden = elementOverflow.indexOf('hidden') !== -1;

    return hasScrollableContent && !isOverflowHidden;
};

/**
 * Get the scrolling element.
 *
 * @returns {HTMLElement}
 */
const getScrollingElement = () => {
    if (scrollingElement === null) {
        const page = document.getElementById('page');
        if (isScrollable(page)) {
            scrollingElement = page;
        } else {
            scrollingElement = document.scrollingElement;
        }
    }

    return scrollingElement;
};

/**
 * Get current scroll position.
 *
 * @returns {Number} Scroll position.
 */
const getScrollPos = () => {
    const scrollingElement = getScrollingElement();

    return scrollingElement.scrollTop;
};

/**
 * Get the scroll position for this form.
 *
 * @param {HTMLFormElement} form
 * @returns {HTMLInputElement}
 */
const getScrollPositionElement = (form) => {
    const element = form.querySelector('input[name=mdlscrollto]');
    if (element) {
        return element;
    }

    const scrollPos = document.createElement('input');
    scrollPos.type = 'hidden';
    scrollPos.name = 'mdlscrollto';
    form.appendChild(scrollPos);

    return scrollPos;
};

/**
 * In the form that contains the element, set the value of the form field with
 * name mdlscrollto to the current scroll position. If there is no element with
 * that name, it creates a hidden form field with that name within the form.
 *
 * @param {string} elementId The element in the form.
 */
export const saveScrollPos = (elementId) => {
    const element = document.getElementById(elementId);
    const form = element.closest('form');
    if (!form) {
        return;
    }

    saveScrollPositionToForm(form);
};

/**
 * Init event handlers for all links with data-savescrollposition=true.
 * Set the value to the closest form.
 */
export const watchScrollButtonSaves = () => {
    document.addEventListener('click', (e) => {
        const button = e.target.closest('[data-savescrollposition="true"]');
        if (button) {
            saveScrollPositionToForm(button.form);
        }
    });
};

/**
 * Save the position to form.
 *
 * @param {Object} form The form is saved scroll position.
 */
export const saveScrollPositionToForm = (form) => {
    getScrollPositionElement(form).value = getScrollPos();
};

/**
 * Init event handlers for all links with data-save-scroll=true.
 * Handle to add mdlscrollto parameter to link using js when we click on the link.
 *
 */
export const initLinksScrollPos = () => {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[data-save-scroll=true]');
        if (!link) {
            return;
        }

        e.preventDefault();
        const url = new URL(e.target.href);
        url.searchParams.set('mdlscrollto', getScrollPos());
        window.location = url;
    });
};

/**
 * If there is a parameter like mdlscrollto=123 in the URL, scroll to that saved position.
 */
export const scrollToSavedPosition = () => {
    const url = new URL(window.location.href);
    if (!url.searchParams.has('mdlscrollto')) {
        return;
    }

    const scrollPosition = url.searchParams.get('mdlscrollto');

    // Event onDOMReady is the effective one here. I am leaving the immediate call to
    // window.scrollTo in case it reduces flicker.
    const scrollingElement = getScrollingElement();
    scrollingElement.scrollTo(0, scrollPosition);
    document.addEventListener('DOMContentLoaded', () => {
        scrollingElement.scrollTo(0, scrollPosition);
    });
};
