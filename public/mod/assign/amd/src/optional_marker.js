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
 * Shared optional marker helpers.
 *
 * @module     mod_assign/optional_marker
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'core/str',
    'core/modal_events',
    'core/modal_save_cancel',
], function(
    str,
    ModalEvents,
    SaveCancelModal
) {

    var SELECTORS = {
        MARKER_ENABLED_CHECKBOX: 'input[type="checkbox"][name*="allocatedmarkerenabled"]',
        GRADING_PANEL_GRADE_FIELD: 'form.gradeform [id="id_grade"]',
        QUICK_GRADE_FIELD: '.quickgrade[id^="quickgrade_"]',
    };

    return {
        /**
         * Save initial marker enabled states.
         */
        saveEnabledState: function() {
            var checkboxes = document.querySelectorAll(SELECTORS.MARKER_ENABLED_CHECKBOX);
            Array.from(checkboxes).forEach(function(checkbox) {
                checkbox.dataset.initialChecked = checkbox.checked ? '1' : '0';
            });
        },

        /**
         * Check whether the marker checkbox has an associated grade value in its context.
         *
         * @param {HTMLElement} checkbox
         * @return {Boolean}
         */
        hasGrade: function(checkbox) {
            if (!checkbox) {
                return false;
            }

            var gradeValue = null;
            var gradeInput = null;
            if (checkbox.name.includes('quickgrade')) {
                var gradeColumn = checkbox.closest('tr')?.querySelector('td.grade');
                gradeInput = checkbox.closest('tr')?.querySelector(SELECTORS.QUICK_GRADE_FIELD);
                if (!gradeInput && gradeColumn) {
                    gradeValue = gradeColumn.firstElementChild?.firstElementChild?.textContent.trim();
                }
            } else {
                gradeInput = document.querySelector(SELECTORS.GRADING_PANEL_GRADE_FIELD);
            }

            if (!gradeInput && !gradeValue) {
                return false;
            }

            if (!gradeValue) {
                gradeValue = String(gradeInput.value).trim();
            }
            return gradeValue !== '' && gradeValue !== '-1' && gradeValue !== '-';
        },

        /**
         * Check whether enabling a marker should show the confirmation.
         *
         * @param {HTMLElement} checkbox
         * @return {Boolean}
         */
        shouldConfirmEnable: function(checkbox) {
            if (!checkbox || !checkbox.checked) {
                return false;
            }
            return this.hasGrade(checkbox);
        },

        /**
         * Check if submit time confirmation is required.
         *
         * @return {Boolean}
         */
        shouldConfirmSubmit: function() {
            var checkboxes = document.querySelectorAll(SELECTORS.MARKER_ENABLED_CHECKBOX);
            return Array.from(checkboxes).some(function(checkbox) {
                if (!checkbox.checked || checkbox.dataset.initialChecked === '1') {
                    return false;
                }
                return this.hasGrade(checkbox);
            }.bind(this));
        },

        /**
         * Show the confirmation modal for enabling an optional marker after grading.
         *
         * @param {Boolean} onSubmit
         * @return {Promise}
         */
        showConfirm: async function(onSubmit) {
            return SaveCancelModal.create({
                title: str.get_string('confirm', 'moodle'),
                body: str.get_string('enableoptionalmarkeraftergraded', 'mod_assign'),
                buttons: {
                    save: onSubmit ? str.get_string('saveandcontinue', 'mod_assign') : str.get_string('enable', 'moodle'),
                },
                show: true,
                removeOnClose: true,
            }).then(function(modal) {
                return new Promise(function(resolve) {
                    var confirmed = false;
                    modal.getRoot().on(ModalEvents.save, function() {
                        confirmed = true;
                    });
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        resolve(confirmed);
                    });
                });
            });
        },
    };
});
