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
 * Modal focus trap — CISC 3650 Group 5 accessibility fix
 *
 * Issue #4 | CRITICAL | WCAG 2.1.2 (A) — Modal dialogs do not trap focus
 *
 * PROBLEM:
 *   When a modal dialog opens, pressing Tab cycles through ALL focusable
 *   elements on the page — including the background content behind the
 *   modal. Keyboard users can inadvertently interact with hidden/obscured
 *   content, and screen reader users lose context of the modal.
 *
 *   WCAG 2.1.2 requires that when a component "traps" keyboard focus, the
 *   user can always move focus away using only the keyboard (e.g. Escape).
 *   The corollary: when a modal is open, Tab MUST be contained within it
 *   so the user isn't lost in background content.
 *
 * FIX:
 *   This AMD module:
 *     1. Finds all focusable elements inside the active modal
 *     2. Intercepts Tab and Shift+Tab to loop within those elements
 *     3. Listens for Escape to close the modal and restore focus to the
 *        element that triggered it (so keyboard users don't lose their place)
 *     4. Moves focus to the first focusable element when the modal opens
 *     5. Cleans up event listeners when the modal closes
 *
 * USAGE:
 *   This module is imported and initialised by core/modal.js.
 *   No direct calls needed — it hooks into Moodle's existing modal events.
 *
 * @module     core/modal_focus_trap
 * @copyright  2026 CISC 3650 Group 5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    'use strict';

    /**
     * CSS selector for all elements that can receive keyboard focus.
     * This matches the WHATWG "focusable area" definition.
     */
    var FOCUSABLE_SELECTOR = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])',
        '[contenteditable="true"]',
        'details > summary'
    ].join(', ');

    /**
     * The element that had focus before the modal was opened.
     * Restored when the modal closes so keyboard users don't lose their place.
     * @type {Element|null}
     */
    var triggerElement = null;

    /**
     * The bound keydown handler — stored so it can be removed on modal close.
     * @type {Function|null}
     */
    var keydownHandler = null;

    /**
     * Return all currently focusable elements within a container.
     *
     * @param  {Element} container  The modal root element
     * @return {Element[]}          Array of focusable elements in DOM order
     */
    var getFocusableElements = function(container) {
        var elements = Array.from(container.querySelectorAll(FOCUSABLE_SELECTOR));
        // Filter out elements that are hidden (display:none / visibility:hidden)
        return elements.filter(function(el) {
            return el.offsetParent !== null || el === document.activeElement;
        });
    };

    /**
     * Handle keydown events while the focus trap is active.
     *
     * Tab        → move to next focusable element; wrap to first if at end
     * Shift+Tab  → move to previous; wrap to last if at start
     * Escape     → close the modal (calls modal's hide method if available)
     *
     * @param {KeyboardEvent} e
     * @param {Element}       modalRoot  The modal container element
     * @param {Object}        modalObj   Moodle modal JS object (optional)
     */
    var handleKeydown = function(e, modalRoot, modalObj) {
        var focusable = getFocusableElements(modalRoot);

        if (!focusable.length) {
            return;
        }

        var first = focusable[0];
        var last  = focusable[focusable.length - 1];

        if (e.key === 'Tab' || e.keyCode === 9) {
            if (e.shiftKey) {
                // Shift+Tab: if focus is on the first element, wrap to last
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab: if focus is on the last element, wrap to first
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }

        if (e.key === 'Escape' || e.keyCode === 27) {
            // Let the modal's own Escape handler run (if it has one),
            // otherwise call hide() directly
            if (modalObj && typeof modalObj.hide === 'function') {
                modalObj.hide();
            } else {
                // Fallback: trigger Bootstrap modal hide
                var bsModal = modalRoot.closest('[data-region="modal-container"]');
                if (bsModal) {
                    require(['jquery'], function($) {
                        $(bsModal).modal('hide');
                    });
                }
            }
        }
    };

    /**
     * Activate the focus trap on a modal.
     *
     * Call this after the modal's DOM is visible. It:
     *   - Saves the currently focused element (to restore on close)
     *   - Moves focus to the modal's first focusable element
     *   - Attaches the keydown handler
     *
     * @param {Element} modalRoot  The modal container element (role="dialog")
     * @param {Object}  modalObj   The Moodle modal JS object (optional)
     */
    var activate = function(modalRoot, modalObj) {
        // Remember where focus was before the modal opened
        triggerElement = document.activeElement;

        // Move initial focus into the modal
        var focusable = getFocusableElements(modalRoot);
        if (focusable.length) {
            // Prefer an explicit autofocus element; fall back to first focusable
            var autoFocus = modalRoot.querySelector('[autofocus]');
            (autoFocus || focusable[0]).focus();
        } else {
            // If no focusable children, make the modal itself focusable
            // so keyboard users can at least reach it and press Escape
            modalRoot.setAttribute('tabindex', '-1');
            modalRoot.focus();
        }

        // Attach the keydown trap
        keydownHandler = function(e) {
            handleKeydown(e, modalRoot, modalObj);
        };
        document.addEventListener('keydown', keydownHandler);
    };

    /**
     * Deactivate the focus trap.
     *
     * Call this when the modal is hidden. It:
     *   - Removes the keydown handler
     *   - Restores focus to the element that triggered the modal
     *
     * @param {Element} [modalRoot]  The modal root (used for cleanup if needed)
     */
    var deactivate = function(modalRoot) {
        if (keydownHandler) {
            document.removeEventListener('keydown', keydownHandler);
            keydownHandler = null;
        }

        // Restore focus to the trigger element
        if (triggerElement && typeof triggerElement.focus === 'function') {
            triggerElement.focus();
        }
        triggerElement = null;

        // Remove any tabindex we added to the modal root itself
        if (modalRoot && modalRoot.getAttribute('tabindex') === '-1') {
            var hadOriginalTabindex = modalRoot.dataset.originalTabindex;
            if (!hadOriginalTabindex) {
                modalRoot.removeAttribute('tabindex');
            }
        }
    };

    return {
        activate:   activate,
        deactivate: deactivate
    };
});
