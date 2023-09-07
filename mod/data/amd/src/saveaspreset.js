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
 * Javascript module for saving a database as a preset.
 *
 * @module      mod_data/saveaspreset
 * @copyright   2021 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {getString} from 'core/str';

const selectors = {
    saveAsPresetButton: '[data-action="saveaspreset"]',
};

/**
 * Initialize module.
 */
export const init = () => {

    document.addEventListener('click', (event) => {
        const saveAsPresetButton = event.target.closest(selectors.saveAsPresetButton);

        if (!saveAsPresetButton) {
            return;
        }

        event.preventDefault();
        const modalForm = new ModalForm({
            modalConfig: {
                title: getString('savedataaspreset', 'mod_data'),
            },
            formClass: 'mod_data\\form\\save_as_preset',
            args: {d: saveAsPresetButton.dataset.dataid},
            saveButtonText: getString('save'),
            returnFocus: saveAsPresetButton,
        });

        // Show a toast notification when the form is submitted.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
            if (event.detail.result) {
                window.location.reload();
            } else {
                Notification.addNotification({
                    type: 'error',
                    message:  event.detail.errors.join('<br>')
                });
            }
        });

        modalForm.show();
    });
};
