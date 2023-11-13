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
 * Field controller for choicedropdown field.
 *
 * @module core_form/choicedropdown
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getDropdownStatus} from 'core/local/dropdown/status';
import {markFormAsDirty} from 'core_form/changechecker';

const Classes = {
    notClickable: 'not-clickable',
    hidden: 'd-none',
};

/**
 * Internal form element class.
 *
 * @private
 * @class     FieldController
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class FieldController {
    /**
     * Class constructor.
     *
     * @param {String} elementId Form element id
     */
    constructor(elementId) {
        this.elementId = elementId;
        this.mainSelect = document.getElementById(this.elementId);
        this.dropdown = getDropdownStatus(`[data-form-controls="${this.elementId}"]`);
        this.dropdown.getElement().classList.remove(Classes.hidden);
    }

    /**
     * Add form element event listener.
     */
    addEventListeners() {
        this.dropdown.getElement().addEventListener(
            'change',
            this.updateSelect.bind(this)
        );
        // Click on a dropdown link can trigger a wrong dirty form reload warning.
        this.dropdown.getElement().addEventListener(
            'click',
            (event) => event.preventDefault()
        );
        this.mainSelect.addEventListener(
            'change',
            this.updateDropdown.bind(this)
        );
        // Enabling or disabling the select does not trigger any JS event.
        const observerCallback = (mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type !== 'attributes' || mutation.attributeName !== 'disabled') {
                    return;
                }
                this.updateDropdown();
            });
        };
        new MutationObserver(observerCallback).observe(
            this.mainSelect,
            {attributeFilter: ['disabled']}
        );
    }

    /**
     * Check if the field is disabled.
     * @returns {Boolean}
     */
    isDisabled() {
        return this.mainSelect?.hasAttribute('disabled');
    }

    /**
     * Update selected option preview in form.
     */
    async updateDropdown() {
        this.dropdown.setButtonDisabled(this.isDisabled());
        if (this.dropdown.getSelectedValue() == this.mainSelect.value) {
            return;
        }
        this.dropdown.setSelectedValue(this.mainSelect.value);
    }

    /**
     * Update selected option preview in form.
     */
    async updateSelect() {
        if (this.dropdown.getSelectedValue() == this.mainSelect.value) {
            return;
        }
        this.mainSelect.value = this.dropdown.getSelectedValue();
        markFormAsDirty(this.mainSelect.closest('form'));
        // Change the select element via JS does not trigger the standard change event.
        this.mainSelect.dispatchEvent(new Event('change'));
    }

    /**
     * Disable the choice dialog and convert it into a regular select field.
     */
    disableInteractiveDialog() {
        this.mainSelect?.classList.remove(Classes.hidden);
        const dropdownElement = this.dropdown.getElement();
        dropdownElement.classList.add(Classes.hidden);
    }

    /**
     * Check if the field has a force dialog attribute.
    //  *
     * The force dialog is a setting to force the javascript control even in
     * behat test.
     *
     * @returns {Boolean} if the dialog modal should be forced or not
     */
    hasForceDialog() {
        return !!this.mainSelect?.dataset.forceDialog;
    }
}

/**
 * Initialises a choice dialog field.
 *
 * @method init
 * @param {String} elementId Form element id
 * @listens event:uploadStarted
 * @listens event:uploadCompleted
 */
export const init = (elementId) => {
    const field = new FieldController(elementId);
    // This field is just a select wrapper. To optimize tests, we don't want to keep behat
    // waiting for extra loadings in this case. The set field steps are about testing other
    // stuff, not to test fancy javascript form fields. However, we keep the possibility of
    // testing the javascript part using behat when necessary.
    if (document.body.classList.contains('behat-site') && !field.hasForceDialog()) {
        field.disableInteractiveDialog();
        return;
    }
    field.addEventListeners();
};
