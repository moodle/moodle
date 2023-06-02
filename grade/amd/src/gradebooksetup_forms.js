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
import {get_string as getString} from 'core/str';
import Notification from 'core/notification';

/**
 * Initialize module
 */
export const init = () => {
    // Sometimes the trigger does not exist, so lets conditionally add it.
    document.addEventListener('click', event => {
        if (event.target.closest('[data-trigger="add-item-form"]')) {
            event.preventDefault();
            const trigger = event.target.closest('[data-trigger="add-item-form"]');
            // If we are adding or editing a grade item change the Modal header.
            const title = trigger.getAttribute('data-itemid') === '-1' ?
                getString('newitem', 'core_grades') : getString('itemsedit', 'core_grades');
            const modalForm = new ModalForm({
                modalConfig: {
                    title: title,
                },
                formClass: 'core_grades\\form\\add_item',
                args: {
                    itemid: trigger.getAttribute('data-itemid'),
                    courseid: trigger.getAttribute('data-courseid'),
                    gpr_plugin: trigger.getAttribute('data-gprplugin'),
                },
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
                        message:  getString('saving_failed', 'core_grades')
                    });
                }
            });

            modalForm.show();
        }
    });
};
