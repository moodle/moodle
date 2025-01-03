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
 * Module handling the form submission of the statistics tables of local_ai_manager.
 *
 * @module     local_ai_manager/rights_config_table
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import {getString} from 'core/str';

let table = null;

export const selectors = {
    CHECKBOX: 'input[data-userid]',
    SELECTALLCHECKBOX: '#rights-table-selectall_checkbox',
    SELECTIONINFO: '#rights-table-selection_info',
    USERIDS_INPUT_FIELD: '#rights-table-userids'
};

/**
 * Initialize the bulk handling on the statistics table.
 * @param {string} id the id of the table to operate on
 */
export const init = (id) => {
    const pendingPromise = new Pending('local_ai_manager/rights_config_table');
    table = document.getElementById(id);
    table.querySelectorAll(selectors.CHECKBOX).forEach(checkbox => {
        checkbox.addEventListener('change', event => {
            updateSelectAllCheckboxState();
            updateUserIds(event.target);
            updateSelectionCountInfo();
        });
    });
    table.querySelector(selectors.SELECTALLCHECKBOX).addEventListener('change', event => {
        updateSelection(event);
        // If we set the status of the checkboxes via JS there is no change event on the checkboxes,
        // so we need to manually trigger the update of the user ids.
        updateUserIds();
    });
    updateSelectionCountInfo();
    // In case the browser remembered the state after site reload, we need to set the initial state of user ids dependent on the
    // boxes' current state.
    updateUserIds();
    pendingPromise.resolve();
};

/**
 * Update the user ids input field for form submission.
 */
const updateUserIds = () => {
    const userIdsInputField = document.querySelector(selectors.USERIDS_INPUT_FIELD);
    const userIds = [];
    document.querySelectorAll(selectors.CHECKBOX).forEach(checkbox => {
        if (checkbox.checked) {
            userIds.push(checkbox.dataset.userid);
        }
    });
    userIdsInputField.value = userIds.join(';');
};

/**
 * Updates the checked states of the user checkboxes according to the change of the "select/deselect all" checkbox.
 *
 * @param {object} changedEvent the changed event of the "select/deselect all" checkbox
 */
const updateSelection = (changedEvent) => {
    const allBoxes = table.querySelectorAll(selectors.CHECKBOX);
    if (allBoxes.length === 0) {
        return;
    }
    if (changedEvent.target.checked) {
        allBoxes.forEach((box) => {
            if (!box.checked) {
                box.checked = true;
            }
        });
    } else {
        allBoxes.forEach((box) => {
            box.checked = false;
        });
    }
    updateSelectionCountInfo();
};

/**
 * Updates the "select/deselect all" checkbox according to the state of the other checkboxes.
 */
const updateSelectAllCheckboxState = () => {
    const selectAllCheckbox = table.querySelector(selectors.SELECTALLCHECKBOX);
    selectAllCheckbox.checked = !!areAllBoxesChecked();
};

/**
 * Helper function to determine if all user checkboxes are checked or not.
 *
 * @returns {bool} true if all boxes are checked, false otherwise
 */
const areAllBoxesChecked = () => {
    const allBoxes = table.querySelectorAll(selectors.CHECKBOX);
    return Array.from(allBoxes).reduce((a, b) => a && b.checked, true);
};

/**
 * Returns the amount of currently checked checkboxes.
 *
 * @returns {number} the count of currently checked checkboxes
 */
const checkedCheckboxesCount = () => {
    const allBoxes = table.querySelectorAll(selectors.CHECKBOX);
    const checkedBoxes = Array.from(allBoxes).filter(checkbox => checkbox.checked);
    return checkedBoxes.length;
};

/**
 * Updates the selection count info text box.
 */
const updateSelectionCountInfo = async() => {
    const selectionCountInfoTarget = table.querySelector(selectors.SELECTIONINFO);
    const infoText = await getString('selecteduserscount', 'local_ai_manager', checkedCheckboxesCount());
    selectionCountInfoTarget.innerHTML = infoText;
};
