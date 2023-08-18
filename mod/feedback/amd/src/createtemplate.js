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
 * Javascript module for saving a new template.
 *
 * @module      mod_feedback/createtemplate
 * @copyright   2021 Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';

const selectors = {
    modaltrigger: '[data-action="createtemplate"]',
};

/**
 * Initialize module
 */
export const init = () => {
    const trigger = document.querySelector(selectors.modaltrigger);

    trigger.addEventListener('click', event => {
        event.preventDefault();
        const ele = event.currentTarget;

        const modalForm = new ModalForm({
            modalConfig: {
                title: getString('save_as_new_template', 'mod_feedback'),
            },
            formClass: 'mod_feedback\\form\\create_template_form',
            args: {
                id: ele.dataset.dataid
            },
            saveButtonText: getString('save', 'core')
        });

        // Show a toast notification when the form is submitted.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
            if (event.detail.result) {
                getString('template_saved', 'feedback').then(addToast).catch();
            } else {
                getString('saving_failed', 'feedback').then(string => {
                    return Notification.addNotification({
                        type: 'error',
                        message: string
                    });
                }).catch();
            }
        });

        modalForm.show();
    });
};
