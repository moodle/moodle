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
 * Prints the add item gradebook form
 *
 * @module core_grades
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

import ModalForm from 'core_form/modalform';
import {getString} from 'core/str';
import Notification from 'core/notification';
import * as FormChangeChecker from 'core_form/changechecker';

const Selectors = {
    advancedFormLink: 'a.showadvancedform'
};

/**
 * Initialize module
 */
export const init = () => {
    // Sometimes the trigger does not exist, so lets conditionally add it.
    document.addEventListener('click', event => {
        const args = {};

        let formClass = null;
        let title = null;
        let trigger = null;
        if (event.target.closest('[data-trigger="add-item-form"]')) {
            event.preventDefault();
            trigger = event.target.closest('[data-trigger="add-item-form"]');
            formClass = 'core_grades\\form\\add_item';
            title = trigger.getAttribute('data-itemid') === '-1' ?
                getString('newitem', 'core_grades') : getString('itemsedit', 'core_grades');
            args.itemid = trigger.getAttribute('data-itemid');
        } else if (event.target.closest('[data-trigger="add-category-form"]')) {
            event.preventDefault();
            trigger = event.target.closest('[data-trigger="add-category-form"]');
            formClass = 'core_grades\\form\\add_category';
            title = trigger.getAttribute('data-category') === '-1' ?
                getString('newcategory', 'core_grades') : getString('categoryedit', 'core_grades');
            args.category = trigger.getAttribute('data-category');
        } else if (event.target.closest('[data-trigger="add-outcome-form"]')) {
            event.preventDefault();
            trigger = event.target.closest('[data-trigger="add-outcome-form"]');
            formClass = 'core_grades\\form\\add_outcome';
            title = trigger.getAttribute('data-itemid') === '-1' ?
                getString('newoutcomeitem', 'core_grades') : getString('outcomeitemsedit', 'core_grades');
            args.itemid = trigger.getAttribute('data-itemid');
        }

        if (trigger) {
            args.courseid = trigger.getAttribute('data-courseid');
            args.gpr_plugin = trigger.getAttribute('data-gprplugin');

            const modalForm = new ModalForm({
                modalConfig: {
                    title: title,
                },
                formClass: formClass,
                args: args,
                saveButtonText: getString('save', 'core'),
                returnFocus: trigger,
            });

            // Show a toast notification when the form is submitted.
            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
                if (event.detail.result) {
                    window.location.assign(event.detail.url);
                } else {
                    Notification.addNotification({
                        type: 'error',
                        message: getString('saving_failed', 'core_grades')
                    });
                }
            });

            modalForm.show();
        }

        const showAdvancedForm = event.target.closest(Selectors.advancedFormLink);
        if (showAdvancedForm) { // Navigate to the advanced form page and cary over any entered data.
            event.preventDefault();
            const form = event.target.closest('form');
            form.action = showAdvancedForm.href;
            // Disable the form change checker as we are going to carry over the data to the advanced form.
            FormChangeChecker.disableAllChecks();
            form.submit();
        }
    });
};
