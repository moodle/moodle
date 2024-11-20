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
 * @module     core_group/index
 * @copyright  2022 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import GroupPicker from "./grouppicker";

const groupPicker = new GroupPicker();

/**
 * Initialise page.
 */
export const init = () => {
    // Init event listeners.
    groupPicker.getDomElement().addEventListener("change", updateBulkActionButtons);

    // Call initially to set initial button state.
    updateBulkActionButtons();
};

/**
 * Updates the bulk action buttons depending on specific conditions.
 */
export const updateBulkActionButtons = () => {
    const groupsSelected = groupPicker.getSelectedValues();
    const aGroupIsSelected = groupsSelected.length !== 0;

    // Collate the conditions where each button is enabled/disabled.
    const bulkActionsEnabledStatuses = {
        'enablemessaging': aGroupIsSelected,
        'disablemessaging': aGroupIsSelected
    };

    // Update the status of each button.
    Object.entries(bulkActionsEnabledStatuses).map(([buttonId, enabled]) => setElementEnabled(buttonId, enabled));
};

/**
 * Adds or removes the given element's disabled attribute.
 * @param {string} domElementId ID of the dom element (without the #)
 * @param {bool} enabled If false, the disable attribute is applied, else it is removed.
 */
export const setElementEnabled = (domElementId, enabled) => {
    const element = document.getElementById(domElementId);

    if (!element) {
        // If there is no element, we do nothing.
        // The element could be purposefully hidden or removed.
        return;
    }

    if (!enabled) {
        element.setAttribute('disabled', 'disabled');
    } else {
        element.removeAttribute('disabled');
    }
};
