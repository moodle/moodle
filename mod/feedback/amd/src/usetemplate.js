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
 * Javascript module for using an existing template
 *
 * @module      mod_feedback/usetemplate
 * @copyright   2021 Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {getString} from 'core/str';

const selectors = {
    modaltrigger: '[data-action="usetemplate"]',
};

/**
 * Initialize module
 */
export const init = () => {
    const trigger = document.querySelector(selectors.modaltrigger);

    trigger.addEventListener('click', event => {
        event.preventDefault();

        const modalForm = new ModalForm({
            modalConfig: {
                title: getString('use_this_template', 'mod_feedback'),
            },
            formClass: 'mod_feedback\\form\\use_template_form',
            args: {
                id: trigger.getAttribute('data-dataid'),
                templateid: trigger.getAttribute('data-templateid')
            },
            saveButtonText: getString('save', 'core')
        });

        // Show a toast notification when the form is submitted.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
            if (event.detail.result) {
                window.location.assign(event.detail.url);
            } else {
                Notification.addNotification({
                    type: 'error',
                    message:  getString('saving_failed', 'mod_feedback')
                });
            }
        });

        modalForm.show();
    });
};
