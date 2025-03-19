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
 * Modal manager for the override delete modal.
 *
 * @module     mod_assign/override_modal_manager
 * @copyright  2025 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import OverrideDeleteModal from 'mod_assign/override_delete_modal';

const SELECTORS = {
    DELETE_BUTTONS: '.delete-override',
    PARENT_CONTAINER: '#assignoverrides',
    USER_GROUP_NAME: '.usergroupname',
};

/**
 * Initialise the modal manager.
 *
 * @param {string} mode The override mode.
 * @param {boolean} showRecalculationCheckBox Whether to show the recalculation checkbox.
 */
export const init = (mode, showRecalculationCheckBox) => {
    document.querySelector(SELECTORS.PARENT_CONTAINER).addEventListener('click', async(event) => {
        const button = event.target.closest(SELECTORS.DELETE_BUTTONS);

        if (!button) {
            return;
        }

        event.preventDefault();

        // Get the name of the user or group from the first column of the row.
        const name = event.target.closest('tr').querySelector(SELECTORS.USER_GROUP_NAME).innerText;

        // Get the confirm message for the modal.
        const confirmMessage = await getConfirmMessage(mode, name);

        // Create and show the modal.
        const modal = await OverrideDeleteModal.create({
            templateContext: {
                confirmmessage: confirmMessage,
                showpenaltyrecalculation: showRecalculationCheckBox,
            },
        });
        modal.setOverrideId(button.getAttribute('data-overrideid'));
        modal.setSessionKey(button.getAttribute('data-sesskey'));
        modal.show();
    });
};

/**
 * Get the confirm message for the modal.
 *
 * @param {string} mode The override mode.
 * @param {boolean} name The name of the user or group.
 * @returns {Promise<string>} The confirm message.
 */
const getConfirmMessage = async(mode, name) => {
    switch (mode) {
        case "group":
            return await getString('overridedeletegroupsure', 'assign', name);
        case "user":
            return await getString('overridedeleteusersure', 'assign', name);
        default:
            return "";
    }
};
