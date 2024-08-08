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

import {getDropdownDialog} from 'core/local/dropdown/dialog';
import {getUserPreference} from 'core_user/repository';
import $ from 'jquery';

/**
 * Module for the extra filters dropdown on the submissions page.
 *
 * @module     mod_assign/actionbar/grading/extra_filters_dropdown
 * @copyright  2024 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @constant {Object} The object containing the relevant selectors. */
const Selectors = {
    extraFiltersDropdown: '.dropdown.extrafilters',
    extraFiltersClose: 'a[data-action="close"]',
    workflowFilterElement: 'select[name="workflowfilter"]'
};

/**
 * Register event listeners for the extra filters dropdown.
 *
 * @param {DropdownDialog} extraFiltersDropdown The dropdown dialog instance.
 */
const registerEventListeners = (extraFiltersDropdown) => {
    // Click event listener to the extra filters dropdown element.
    extraFiltersDropdown.getElement().addEventListener('click', e => {
        // The target is the 'Close' button.
        if (e.target.closest(Selectors.extraFiltersClose)) {
            e.preventDefault();
            extraFiltersDropdown.setVisible(false);
        }
    });
    // Event listener triggered upon hiding of the dropdown.
    $(extraFiltersDropdown.getElement()).on('hide.bs.dropdown', () => {
        // Restore the filters to their stored preference values once the dropdown is closed.
        restoreAppliedWorkflowFilter(extraFiltersDropdown);
    });
};

/**
 * Restores the currently applied workflow filter to its stored preference value.
 *
 * @param {DropdownDialog} extraFiltersDropdown The dropdown dialog instance.
 */
const restoreAppliedWorkflowFilter = async(extraFiltersDropdown) => {
    const appliedWorkflowFilter = await getUserPreference('assign_workflowfilter');
    const workflowFilterSelect = extraFiltersDropdown.getElement().querySelector(Selectors.workflowFilterElement);
    workflowFilterSelect.value = appliedWorkflowFilter;
};

/**
 * Initialize module.
 */
export const init = () => {
    const extraFiltersDropdown = getDropdownDialog(Selectors.extraFiltersDropdown);
    if (extraFiltersDropdown) {
        registerEventListeners(extraFiltersDropdown);
    }
};
