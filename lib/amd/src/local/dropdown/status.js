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
 * The status controls enable extra configurarions for the dropdown like:
 * - Sync the button text with the selected option.
 * - Update the status of the button when the selected option changes. This will
 *   trigger a "change" event when the status changes.
 *
 * @module      core/local/dropdown/status
 * @copyright   2023 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {DropdownDialog} from 'core/local/dropdown/dialog';

const Selectors = {
    checkedIcon: '[data-for="checkedIcon"]',
    option: '[role="option"]',
    optionItem: '[data-optionnumber]',
    optionIcon: '.option-icon',
    selectedOption: '[role="option"][aria-selected="true"]',
    uncheckedIcon: '[data-for="uncheckedIcon"]',
};

const Classes = {
    selected: 'selected',
    disabled: 'disabled',
    hidden: 'd-none',
};

/**
 * Dropdown dialog class.
 * @private
 */
export class DropdownStatus extends DropdownDialog {
    /**
     * Constructor.
     * @param {HTMLElement} element The element to initialize.
     */
    constructor(element) {
        super(element);
        this.buttonSync = element.dataset.buttonSync == 'true';
        this.updateStatus = element.dataset.updateStatus == 'true';
    }

    /**
     * Initialize the subpanel element.
     *
     * This method adds the event listeners to the subpanel and the position classes.
     * @private
     */
    init() {
        super.init();

        if (this.element.dataset.dropdownStatusInitialized) {
            return;
        }

        this.panel.addEventListener('click', this._contentClickHandler.bind(this));

        if (this.element.dataset.buttonSync == 'true') {
            this.setButtonSyncEnabled(true);
        }
        if (this.element.dataset.updateStatus == 'true') {
            this.setUpdateStatusEnabled(true);
        }

        this.element.dataset.dropdownStatusInitialized = true;
    }

    /**
     * Handle click events on the status content.
     * @param {Event} event The event.
     * @private
     */
    _contentClickHandler(event) {
        const option = event.target.closest(Selectors.option);
        if (!option) {
            return;
        }
        if (option.getAttribute('aria-disabled') === 'true') {
            return;
        }
        if (option.getAttribute('aria-selected') === 'true') {
            return;
        }
        if (this.isUpdateStatusEnabled()) {
            this.setSelectedValue(option.dataset.value);
        }
    }

    /**
     * Sets the selected value.
     * @param {string} value The value to set.
     */
    setSelectedValue(value) {
        const selected = this.panel.querySelector(Selectors.selectedOption);
        if (selected && selected.dataset.value === value) {
            return;
        }
        if (selected) {
            this._updateOptionChecked(selected, false);
        }
        const option = this.panel.querySelector(`${Selectors.option}[data-value="${value}"]`);
        if (option) {
            this._updateOptionChecked(option, true);
        }
        if (this.isButtonSyncEnabled()) {
            this.syncButtonText();
        }
        // Emit standard radio button event with the selected option.
        this.element.dispatchEvent(new Event('change'));
    }

    /**
     * Update the option checked content.
     * @private
     * @param {HTMLElement} option the option element to set
     * @param {Boolean} checked the new checked value
     */
    _updateOptionChecked(option, checked) {
        option.setAttribute('aria-selected', checked.toString());
        option.classList.toggle(Classes.selected, checked);
        option.classList.toggle(Classes.disabled, checked);

        const optionItem = option.closest(Selectors.optionItem);
        if (optionItem) {
            this._updateOptionItemChecked(optionItem, checked);
        }

        if (checked) {
            this.element.dataset.value = option.dataset.value;
        } else if (this.element.dataset.value === option.dataset.value) {
            delete this.element.dataset.value;
        }
    }

    /**
     * Update the option item checked content.
     * @private
     * @param {HTMLElement} optionItem
     * @param {Boolean} checked
     */
    _updateOptionItemChecked(optionItem, checked) {
        const selectedClasses = optionItem.dataset.selectedClasses ?? Classes.selected;
        for (const selectedClass of selectedClasses.split(' ')) {
            optionItem.classList.toggle(selectedClass, checked);
        }
        if (checked) {
            optionItem.dataset.selected = checked;
        } else {
            delete optionItem?.dataset.selected;
        }
        const checkedIcon = optionItem.querySelector(Selectors.checkedIcon);
        if (checkedIcon) {
            checkedIcon.classList.toggle(Classes.hidden, !checked);
        }
        const uncheckedIcon = optionItem.querySelector(Selectors.uncheckedIcon);
        if (uncheckedIcon) {
            uncheckedIcon.classList.toggle(Classes.hidden, checked);
        }
    }


    /**
     * Return the selected value.
     * @returns {string|null} The selected value.
     */
    getSelectedValue() {
        const selected = this.panel.querySelector(Selectors.selectedOption);
        return selected?.dataset.value ?? null;
    }

    /**
     * Set the button sync value.
     *
     * If the sync is enabled, the button text will show the selected option.
     *
     * @param {Boolean} value The value to set.
     */
    setButtonSyncEnabled(value) {
        if (value) {
            this.element.dataset.buttonSync = 'true';
        } else {
            delete this.element.dataset.buttonSync;
        }
        if (value) {
            this.syncButtonText();
        }
    }

    /**
     * Return if the button sync is enabled.
     * @returns {Boolean} The button sync value.
     */
    isButtonSyncEnabled() {
        return this.element.dataset.buttonSync == 'true';
    }

    /**
     * Sync the button text with the selected option.
     */
    syncButtonText() {
        const selected = this.panel.querySelector(Selectors.selectedOption);
        if (!selected) {
            return;
        }
        let newText = selected.textContent;
        const optionIcon = this._getOptionIcon(selected);
        if (optionIcon) {
            newText = optionIcon.innerHTML + newText;
        }
        this.button.innerHTML = newText;
    }

    /**
     * Set the update status value.
     *
     * @param {Boolean} value The value to set.
     */
    setUpdateStatusEnabled(value) {
        if (value) {
            this.element.dataset.updateStatus = 'true';
        } else {
            delete this.element.dataset.updateStatus;
        }
    }

    /**
     * Return if the update status is enabled.
     * @returns {Boolean} The update status value.
     */
    isUpdateStatusEnabled() {
        return this.element.dataset.updateStatus == 'true';
    }

    _getOptionIcon(option) {
        const optionItem = option.closest(Selectors.optionItem);
        if (!optionItem) {
            return null;
        }
        return optionItem.querySelector(Selectors.optionIcon);
    }

}

/**
 * Get the dropdown dialog instance form a selector.
 * @param {string} selector The query selector to init.
 * @returns {DropdownStatus|null} The dropdown dialog instance if any.
 */
export const getDropdownStatus = (selector) => {
    const dropdownElement = document.querySelector(selector);
    if (!dropdownElement) {
        return null;
    }
    return new DropdownStatus(dropdownElement);
};

/**
 * Initialize module.
 *
 * @method
 * @param {string} selector The query selector to init.
 */
export const init = (selector) => {
    const dropdown = getDropdownStatus(selector);
    if (!dropdown) {
        throw new Error(`Dopdown status element not found: ${selector}`);
    }
    dropdown.init();
};
