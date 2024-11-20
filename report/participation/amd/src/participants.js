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
 * Some UI stuff for participants page.
 * This is also used by the report/participants/index.php because it has the same functionality.
 *
 * @module     report_participation/participants
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import jQuery from 'jquery';
import CustomEvents from 'core/custom_interaction_events';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import {showSendMessage} from 'core_user/local/participants/bulkactions';

const Selectors = {
    bulkActionSelect: "#formactionid",
    bulkUserSelectedCheckBoxes: "input[data-togglegroup^='participants-table'][data-toggle='slave']:checked",
    participantsForm: '#participantsform',
};

export const init = () => {
    const root = document.querySelector(Selectors.participantsForm);

    /**
     * Private method.
     *
     * @method registerEventListeners
     * @private
     */
    const registerEventListeners = () => {
        CustomEvents.define(Selectors.bulkActionSelect, [CustomEvents.events.accessibleChange]);
        jQuery(Selectors.bulkActionSelect).on(CustomEvents.events.accessibleChange, e => {
            const action = e.target.value;
            const checkboxes = root.querySelectorAll(Selectors.bulkUserSelectedCheckBoxes);

            if (action.indexOf('#') !== -1) {
                e.preventDefault();

                const ids = [];
                checkboxes.forEach(checkbox => {
                    ids.push(checkbox.getAttribute('name').replace('user', ''));
                });

                if (action === '#messageselect') {
                    showSendMessage(ids)
                    .then(modal => {
                        modal.getRoot().on(ModalEvents.hidden, () => {
                            // Focus on the action select when the dialog is closed.
                            const bulkActionSelector = root.querySelector(Selectors.bulkActionSelect);
                            resetBulkAction(bulkActionSelector);
                            bulkActionSelector.focus();
                        });

                        return modal;
                    })
                    .catch(Notification.exception);
                }
            } else if (action !== '' && checkboxes.length) {
                e.target.form().submit();
            }

            resetBulkAction(e.target);
        });
    };

    const resetBulkAction = bulkActionSelect => {
        bulkActionSelect.value = '';
    };

    registerEventListeners();
};
