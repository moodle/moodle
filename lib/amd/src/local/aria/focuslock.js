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
 * Tab locking system.
 *
 * This is based on code and examples provided in the ARIA specification.
 * https://www.w3.org/TR/wai-aria-practices/examples/dialog-modal/dialog.html
 *
 * @module     core/local/aria/focuslock
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Selectors from './selectors';

const lockRegionStack = [];
const initialFocusElementStack = [];
const finalFocusElementStack = [];

let lastFocus = null;
let ignoreFocusChanges = false;
let isLocked = false;

/**
 * The lock handler.
 *
 * This is the item that does a majority of the work.
 * The overall logic from this comes from the examles in the WCAG guidelines.
 *
 * The general idea is that if the focus is not held within by an Element within the lock region, then we replace focus
 * on the first element in the lock region. If the first element is the element previously selected prior to the
 * user-initiated focus change, then instead jump to the last element in the lock region.
 *
 * This gives us a solution which supports focus locking of any kind, which loops in both directions, and which
 * prevents the lock from escaping the modal entirely.
 *
 * @method
 * @param {Event} event The event from the focus change
 */
const lockHandler = event => {
    if (ignoreFocusChanges) {
        // The focus change was made by an internal call to set focus.
        return;
    }

    // Find the current lock region.
    let lockRegion = getCurrentLockRegion();
    while (lockRegion) {
        if (document.contains(lockRegion)) {
            break;
        }

        // The lock region does not exist.
        // Perhaps it was removed without being untrapped.
        untrapFocus();
        lockRegion = getCurrentLockRegion();
    }
    if (!lockRegion) {
        return;
    }

    if (lockRegion.contains(event.target)) {
        lastFocus = event.target;
    } else {
        focusFirstDescendant();
        if (lastFocus == document.activeElement) {
            focusLastDescendant();
        }
        lastFocus = document.activeElement;
    }
};

/**
 * Focus the first descendant of the current lock region.
 *
 * @method
 * @returns {Bool} Whether a node was focused
 */
const focusFirstDescendant = () => {
    const lockRegion = getCurrentLockRegion();

    // Grab all elements in the lock region and attempt to focus each element until one is focused.
    // We can capture most of this in the query selector, but some cases may still reject focus.
    // For example, a disabled text area cannot be focused, and it becomes difficult to provide a decent query selector
    // to capture this.
    // The use of Array.some just ensures that we stop as soon as we have a successful focus.
    const focusableElements = Array.from(lockRegion.querySelectorAll(Selectors.elements.focusable));

    // The lock region itself may be focusable. This is particularly true on Moodle's older dialogues.
    // We must include it in the calculation of descendants to ensure that looping works correctly.
    focusableElements.unshift(lockRegion);
    return focusableElements.some(focusableElement => attemptFocus(focusableElement));
};

/**
 * Focus the last descendant of the current lock region.
 *
 * @method
 * @returns {Bool} Whether a node was focused
 */
const focusLastDescendant = () => {
    const lockRegion = getCurrentLockRegion();

    // Grab all elements in the lock region, reverse them, and attempt to focus each element until one is focused.
    // We can capture most of this in the query selector, but some cases may still reject focus.
    // For example, a disabled text area cannot be focused, and it becomes difficult to provide a decent query selector
    // to capture this.
    // The use of Array.some just ensures that we stop as soon as we have a successful focus.
    const focusableElements = Array.from(lockRegion.querySelectorAll(Selectors.elements.focusable)).reverse();

    // The lock region itself may be focusable. This is particularly true on Moodle's older dialogues.
    // We must include it in the calculation of descendants to ensure that looping works correctly.
    focusableElements.push(lockRegion);
    return focusableElements.some(focusableElement => attemptFocus(focusableElement));
};

/**
 * Check whether the supplied focusTarget is actually focusable.
 * There are cases where a normally focusable element can reject focus.
 *
 * Note: This example is a wholesale copy of the WCAG example.
 *
 * @method
 * @param {HTMLElement} focusTarget
 * @returns {Bool}
 */
const isFocusable = focusTarget => {
    if (focusTarget.tabIndex > 0 || (focusTarget.tabIndex === 0 && focusTarget.getAttribute('tabIndex') !== null)) {
        return true;
    }

    if (focusTarget.disabled) {
        return false;
    }

    switch (focusTarget.nodeName) {
        case 'A':
            return !!focusTarget.href && focusTarget.rel != 'ignore';
        case 'INPUT':
            return focusTarget.type != 'hidden' && focusTarget.type != 'file';
        case 'BUTTON':
        case 'SELECT':
        case 'TEXTAREA':
            return true;
        default:
            return false;
    }
};

/**
 * Attempt to focus the supplied focusTarget.
 *
 * Note: This example is a heavily inspired by the WCAG example.
 *
 * @method
 * @param {HTMLElement} focusTarget
 * @returns {Bool} Whether focus was successful o rnot.
 */
const attemptFocus = focusTarget => {
    if (!isFocusable(focusTarget)) {
        return false;
    }

    // The ignoreFocusChanges variable prevents the focus event handler from interfering and entering a fight with itself.
    ignoreFocusChanges = true;

    try {
        focusTarget.focus();
    } catch (e) {
        // Ignore failures. We will just try to focus the next element in the list.
    }

    ignoreFocusChanges = false;

    // If focus was successful the activeElement will be the one we focused.
    return (document.activeElement === focusTarget);
};

/**
 * Get the current lock region from the top of the stack.
 *
 * @method
 * @returns {HTMLElement}
 */
const getCurrentLockRegion = () => {
    return lockRegionStack[lockRegionStack.length - 1];
};

/**
 * Add a new lock region to the stack.
 *
 * @method
 * @param {HTMLElement} newLockRegion
 */
const addLockRegionToStack = newLockRegion => {
    if (newLockRegion === getCurrentLockRegion()) {
        return;
    }

    lockRegionStack.push(newLockRegion);
    const currentLockRegion = getCurrentLockRegion();

    // Append an empty div which can be focused just outside of the item locked.
    // This locks tab focus to within the tab region, and does not allow it to extend back into the window by
    // guaranteeing the existence of a tabable item after the lock region which can be focused but which will be caught
    // by the handler.
    const element = document.createElement('div');
    element.tabIndex = 0;
    element.style.position = 'fixed';
    element.style.top = 0;
    element.style.left = 0;

    const initialNode = element.cloneNode();
    currentLockRegion.parentNode.insertBefore(initialNode, currentLockRegion);
    initialFocusElementStack.push(initialNode);

    const finalNode = element.cloneNode();
    currentLockRegion.parentNode.insertBefore(finalNode, currentLockRegion.nextSibling);
    finalFocusElementStack.push(finalNode);
};

/**
 * Remove the top lock region from the stack.
 *
 * @method
 */
const removeLastLockRegionFromStack = () => {
    // Take the top element off the stack, and replce the current lockRegion value.
    lockRegionStack.pop();

    const finalNode = finalFocusElementStack.pop();
    if (finalNode) {
        // The final focus element may have been removed if it was part of a parent item.
        finalNode.remove();
    }

    const initialNode = initialFocusElementStack.pop();
    if (initialNode) {
        // The initial focus element may have been removed if it was part of a parent item.
        initialNode.remove();
    }
};

/**
 * Whether any region is left in the stack.
 *
 * @return {Bool}
 */
const hasTrappedRegionsInStack = () => {
    return !!lockRegionStack.length;
};

/**
 * Start trapping the focus and lock it to the specified newLockRegion.
 *
 * @method
 * @param {HTMLElement} newLockRegion The container to lock focus to
 */
export const trapFocus = newLockRegion => {
    // Update the lock region stack.
    // This allows us to support nesting.
    addLockRegionToStack(newLockRegion);

    if (!isLocked) {
        // Add the focus handler.
        document.addEventListener('focus', lockHandler, true);
    }

    // Attempt to focus on the first item in the lock region.
    if (!focusFirstDescendant()) {
        const currentLockRegion = getCurrentLockRegion();

        // No focusable descendants found in the region yet.
        // This can happen when the region is locked before content is generated.
        // Focus on the region itself for now.
        const originalRegionTabIndex = currentLockRegion.tabIndex;
        currentLockRegion.tabIndex = 0;
        attemptFocus(currentLockRegion);
        currentLockRegion.tabIndex = originalRegionTabIndex;
    }

    // Keep track of the last item focused.
    lastFocus = document.activeElement;

    isLocked = true;
};

/**
 * Stop trapping the focus.
 *
 * @method
 */
export const untrapFocus = () => {
    // Remove the top region from the stack.
    removeLastLockRegionFromStack();

    if (hasTrappedRegionsInStack()) {
        // The focus manager still has items in the stack.
        return;
    }

    document.removeEventListener('focus', lockHandler, true);

    lastFocus = null;
    ignoreFocusChanges = false;
    isLocked = false;
};
