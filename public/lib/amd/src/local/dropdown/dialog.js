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
 * Dropdown status JS controls.
 *
 * @module      core/local/dropdown/dialog
 * @copyright   2023 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Dropdown from 'theme_boost/bootstrap/dropdown';
import {
    firstFocusableElement,
    lastFocusableElement,
    previousFocusableElement,
    nextFocusableElement,
} from 'core/pagehelpers';
import Pending from 'core/pending';
import EventHandler from 'theme_boost/bootstrap/dom/event-handler';

const Selectors = {
    dropdownButton: '[data-for="dropdowndialog_button"]',
    dropdownDialog: '[data-for="dropdowndialog_dialog"]',
};

/**
 * Dropdown dialog class.
 * @private
 */
export class DropdownDialog {
    /**
     * Constructor.
     * @param {HTMLElement} element The element to initialize.
     */
    constructor(element) {
        this.element = element;
        this.button = element.querySelector(Selectors.dropdownButton);
        this.panel = element.querySelector(Selectors.dropdownDialog);
    }

    /**
     * Initialize the subpanel element.
     *
     * This method adds the event listeners to the subpanel and the position classes.
     */
    init() {
        if (this.element.dataset.dropdownDialogInitialized) {
            return;
        }

        // Use the Bootstrap key handler for the Dropdown button key handler.
        // This will avoid Boostrap Dropdown handler to prevent the propagation to the dialog.
        // Menu Item events.
        const dialogButtonSelector = `#${this.element.id} ${Selectors.dropdownButton}`;
        EventHandler.on(document, 'keydown', dialogButtonSelector, this._buttonKeyHandler.bind(this));
        // Subpanel content events.
        const dialogSelector = `#${this.element.id} ${Selectors.dropdownDialog}`;
        EventHandler.on(document, 'keydown', dialogSelector, this._contentKeyHandler.bind(this));

        this.element.dataset.dropdownDialogInitialized = true;
    }

    /**
     * Dropdown button key handler.
     * @param {Event} event
     * @private
     */
    _buttonKeyHandler(event) {
        if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
            event.stopPropagation();
            event.preventDefault();
            this.setVisible(false);
            return;
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            event.stopPropagation();
            event.preventDefault();
            this.setVisible(true);
            this._focusPanelContent();
        }
    }

    /**
     * Sub panel content key handler.
     * @param {Event} event
     * @private
     */
    _contentKeyHandler(event) {
        let newFocus = null;

        if (event.key === 'End') {
            newFocus = lastFocusableElement(this.panel);
        }
        if (event.key === 'Home') {
            newFocus = firstFocusableElement(this.panel);
        }
        if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
            newFocus = previousFocusableElement(this.panel, false);
            if (!newFocus) {
                newFocus = this.button;
            }
        }
        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            newFocus = nextFocusableElement(this.panel, false);
        }
        if (newFocus !== null) {
            event.stopPropagation();
            event.preventDefault();
            newFocus.focus();
        }
    }

    /**
     * Focus on the first focusable element of the subpanel.
     * @private
     */
    _focusPanelContent() {
        const pendingPromise = new Pending('core/dropdown/dialog:focuscontent');
        // Some Bootstrap events are triggered after the click event.
        // To prevent this from affecting the focus we wait a bit.
        setTimeout(() => {
            const firstFocusable = firstFocusableElement(this.panel);
            if (firstFocusable) {
                firstFocusable.focus();
            }
            pendingPromise.resolve();
        }, 100);
    }

    /**
     * Set the visibility of a subpanel.
     * @param {Boolean} visible true if the subpanel should be visible.
     */
    setVisible(visible) {
        if (visible === this.isVisible()) {
            return;
        }
        Dropdown.getOrCreateInstance(this.button).toggle();
    }

    /**
     * Get the visibility of a subpanel.
     * @returns {Boolean} true if the subpanel is visible.
     */
    isVisible() {
        return this.button.getAttribute('aria-expanded') === 'true';
    }

    /**
     * Set the content of the button.
     * @param {String} content
     */
    setButtonContent(content) {
        this.button.innerHTML = content;
    }

    /**
     * Set the disabled state of the button.
     * @param {Boolean} disabled
     */
    setButtonDisabled(disabled) {
        if (disabled) {
            this.button.setAttribute('disabled', 'disabled');
        } else {
            this.button.removeAttribute('disabled');
        }
    }

    /**
     * Return the main dropdown HTML element.
     * @returns {HTMLElement} The element.
     */
    getElement() {
        return this.element;
    }
}

/**
 * Get the dropdown dialog instance from a selector.
 * @param {string} selector The query selector to init.
 * @returns {DropdownDialog|null} The dropdown dialog instance if any.
 */
export const getDropdownDialog = (selector) => {
    const dropdownElement = document.querySelector(selector);
    if (!dropdownElement) {
        return null;
    }
    return new DropdownDialog(dropdownElement);
};

/**
 * Initialize module.
 *
 * @method
 * @param {string} selector The query selector to init.
 */
export const init = (selector) => {
    const dropdown = getDropdownDialog(selector);
    if (!dropdown) {
        throw new Error(`Dopdown dialog element not found: ${selector}`);
    }
    dropdown.init();
};
