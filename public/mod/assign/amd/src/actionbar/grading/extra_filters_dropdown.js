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
    workflowFilterElement: 'select[name="workflowfilter"]',
    markerFilterElement: 'select[name="markingallocationfilter"]',
    suspendedParticipantsFilterCheckbox: 'input[type="checkbox"][name="suspendedparticipantsfilter"]',
    suspendedParticipantsFilterHidden: 'input[type="hidden"][name="suspendedparticipantsfilter"]'
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

    // Change event listener to the extra filters dropdown element.
    extraFiltersDropdown.getElement().addEventListener('change', e => {
        const suspendedParticipantsFilterCheckbox = e.target.closest(Selectors.suspendedParticipantsFilterCheckbox);
        // The target is the 'Suspended participants' filter checkbox.
        if (suspendedParticipantsFilterCheckbox) {
            // The 'Suspended participants' filter uses a hidden input and a checkbox. The hidden input is used to
            // submit '0' as a workaround when the checkbox is unchecked since unchecked checkboxes are not submitted
            // with the form. Therefore, we need to enable or disable the hidden input based on the checkbox state.
            const suspendedParticipantsFilterHidden = suspendedParticipantsFilterCheckbox.parentNode
                .querySelector(Selectors.suspendedParticipantsFilterHidden);
            suspendedParticipantsFilterHidden.disabled = suspendedParticipantsFilterCheckbox.checked;
        }
    });

    // Event listener triggered upon hiding of the dropdown.
    extraFiltersDropdown.getElement().addEventListener('hide.bs.dropdown', () => {
        // Restore the filters to their stored preference values once the dropdown is closed.
        restoreAppliedWorkflowFilter(extraFiltersDropdown);
        restoreAppliedMarkerFilter(extraFiltersDropdown);
        restoreAppliedSuspendedParticipantsFilter(extraFiltersDropdown);
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
 * Restores the currently applied marker filter to its stored preference value.
 *
 * @param {DropdownDialog} extraFiltersDropdown The dropdown dialog instance.
 */
const restoreAppliedMarkerFilter = async(extraFiltersDropdown) => {
    const markerFilterSelect = extraFiltersDropdown.getElement().querySelector(Selectors.markerFilterElement);
    if (markerFilterSelect) {
        const appliedMarkerFilter = await getUserPreference('assign_markerfilter');
        markerFilterSelect.value = appliedMarkerFilter;
    }
};

/**
 * Restores the currently suspended participants filter to its stored preference value.
 *
 * @param {DropdownDialog} extraFiltersDropdown The dropdown dialog instance.
 */
const restoreAppliedSuspendedParticipantsFilter = async(extraFiltersDropdown) => {
    const suspendedParticipantsFilterCheckbox = extraFiltersDropdown.getElement()
        .querySelector(Selectors.suspendedParticipantsFilterCheckbox);
    if (suspendedParticipantsFilterCheckbox) {
        const suspendedParticipantsFilterHidden = suspendedParticipantsFilterCheckbox.parentNode
            .querySelector(Selectors.suspendedParticipantsFilterHidden);
        const showOnlyActiveParticipants = await getUserPreference('grade_report_showonlyactiveenrol');
        suspendedParticipantsFilterCheckbox.checked = !showOnlyActiveParticipants;
        suspendedParticipantsFilterHidden.disabled = !showOnlyActiveParticipants;
    }
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
