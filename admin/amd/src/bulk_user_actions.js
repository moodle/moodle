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
 * Add bulk actions to the users list report
 *
 * @module     core_admin/bulk_user_actions
 * @copyright  2024 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as reportSelectors from 'core_reportbuilder/local/selectors';
import * as tableEvents from 'core_table/local/dynamic/events';
import * as FormChangeChecker from 'core_form/changechecker';
import * as CustomEvents from 'core/custom_interaction_events';
import jQuery from 'jquery';

const Selectors = {
    bulkActionsForm: 'form#user-bulk-action-form',
    userReportWrapper: '[data-region="report-user-list-wrapper"]',
    checkbox: 'input[type="checkbox"][data-togglegroup="report-select-all"][data-toggle="slave"]',
    masterCheckbox: 'input[type="checkbox"][data-togglegroup="report-select-all"][data-toggle="master"]',
    checkedRows: '[data-togglegroup="report-select-all"][data-toggle="slave"]:checked',
};

/**
 * Initialise module
 */
export const init = () => {

    const userBulkForm = document.querySelector(Selectors.bulkActionsForm);
    const userReport = userBulkForm?.closest(Selectors.userReportWrapper)?.querySelector(reportSelectors.regions.report);
    if (!userBulkForm || !userReport) {
        return;
    }
    const actionSelect = userBulkForm.querySelector('select');
    CustomEvents.define(actionSelect, [CustomEvents.events.accessibleChange]);

    jQuery(actionSelect).on(CustomEvents.events.accessibleChange, event => {
        if (event.target.value && `${event.target.value}` !== "0") {
            const e = new Event('submit', {cancelable: true});
            userBulkForm.dispatchEvent(e);
            if (!e.defaultPrevented) {
                FormChangeChecker.markFormSubmitted(userBulkForm);
                userBulkForm.submit();
            }
        }
    });

    // Every time the checkboxes in the report are changed, update the list of users in the form values
    // and enable/disable the action select.
    const updateUserIds = () => {
        const selectedUsers = [...userReport.querySelectorAll(Selectors.checkedRows)];
        const selectedUserIds = selectedUsers.map(check => parseInt(check.value));
        userBulkForm.querySelector('[name="userids"]').value = selectedUserIds.join(',');

        // Disable the action selector if nothing selected, and reset the current selection.
        actionSelect.disabled = selectedUsers.length === 0;
        if (actionSelect.disabled) {
            actionSelect.value = "0";
        }

        const selectedUsersNames = selectedUsers.map(check => document.querySelector(`label[for="${check.id}"]`).textContent);
        // Add the user ids and names to the form data attributes so they can be available from the
        // other JS modules that listen to the form submit event.
        userBulkForm.data = {userids: selectedUserIds, usernames: selectedUsersNames};
    };

    updateUserIds();

    document.addEventListener('change', event => {
        // When checkboxes are checked next to individual users or the master toggle (Select all/none).
        if ((event.target.matches(Selectors.checkbox) || event.target.matches(Selectors.masterCheckbox))
                && userReport.contains(event.target)) {
            updateUserIds();
        }
    });

    document.addEventListener(tableEvents.tableContentRefreshed, event => {
        // When the report contents is updated (i.e. page is changed, filters applied, etc).
        if (userReport.contains(event.target)) {
            updateUserIds();
        }
    });
};
