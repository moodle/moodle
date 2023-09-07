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
 * Javascript module for contacting the site DPO
 *
 * @module      tool_dataprivacy/contactdpo
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';

const SELECTORS = {
    CONTACT_DPO: '[data-action="contactdpo"]',
};

/**
 * Initialize module
 */
export const init = () => {
    const triggerElement = document.querySelector(SELECTORS.CONTACT_DPO);

    triggerElement.addEventListener('click', event => {
        event.preventDefault();

        const modalForm = new ModalForm({
            modalConfig: {
                title: getString('contactdataprotectionofficer', 'tool_dataprivacy'),
            },
            formClass: 'tool_dataprivacy\\form\\contactdpo',
            saveButtonText: getString('send', 'tool_dataprivacy'),
            returnFocus: triggerElement,
        });

        // Show a toast notification when the form is submitted.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
            if (event.detail.result) {
                getString('requestsubmitted', 'tool_dataprivacy').then(addToast).catch();
            } else {
                const warningMessages = event.detail.warnings.map(warning => warning.message);
                Notification.addNotification({
                    type: 'error',
                    message: warningMessages.join('<br>')
                });
            }
        });

        modalForm.show();
    });
};
