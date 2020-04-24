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
 * @module     core_user/participants
 * @package    core_user
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DynamicTableSelectors from 'core_table/local/dynamic/selectors';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import CustomEvents from 'core/custom_interaction_events';
import {showAddNote, showSendMessage} from 'core_user/local/participants/bulkactions';

const Selectors = {
    bulkActionSelect: "#formactionid",
    bulkUserCheckBoxes: "input.usercheckbox",
    bulkUserSelectedCheckBoxes: "input.usercheckbox:checked",
    checkAllButton: "#checkall",
    stateHelpIcon: '[data-region="state-help-icon"]',
    tableForm: uniqueId => `form[data-table-unique-id="${uniqueId}"]`,
};

export const init = ({
    uniqueid,
    noteStateNames = {},
}) => {
    const root = document.querySelector(Selectors.tableForm(uniqueid));
    const getTableFromUniqueId = uniqueId => root.querySelector(DynamicTableSelectors.main.fromRegionId(uniqueId));

    /**
     * Private method.
     *
     * @method registerEventListeners
     * @private
     */
    const registerEventListeners = () => {
        root.querySelector(Selectors.bulkActionSelect).addEventListener(CustomEvents.events.accessibleChange, e => {
            const action = e.target.value;
            const tableRoot = getTableFromUniqueId(uniqueid);
            const checkboxes = tableRoot.querySelectorAll(Selectors.bulkUserSelectedCheckBoxes);

            if (action.indexOf('#') !== -1) {
                e.preventDefault();

                const ids = [];
                checkboxes.forEach(checkbox => {
                    ids.push(checkbox.getAttribute('name').replace('user', ''));
                });

                let bulkAction;
                if (action === '#messageselect') {
                    bulkAction = showSendMessage(ids);
                } else if (action === '#addgroupnote') {
                    bulkAction = showAddNote(
                        root.dataset.courseId,
                        ids,
                        noteStateNames,
                        root.querySelector(Selectors.stateHelpIcon)
                    );
                }

                if (bulkAction) {
                    bulkAction
                    .then(modal => {
                        modal.getRoot().on(ModalEvents.hidden, () => {
                            // Focus on the action select when the dialog is closed.
                            const bulkActionSelector = root.querySelector(Selectors.bulkActionSelect);
                            bulkActionSelector.focus();
                        });

                        return modal;
                    })
                    .catch(Notification.exception);
                }
            } else if (action !== '' && checkboxes.length) {
                e.target.form.submit();
            }

            resetBulkAction(e.target);
        });

        root.addEventListener('click', e => {
            const checkAllButton = e.target.closest(Selectors.checkAllButton);
            if (checkAllButton) {
                const showAllLink = checkAllButton.dataset.showalllink;
                if (showAllLink) {
                    window.location = showAllLink;
                }
            }
        });
    };

    const resetBulkAction = bulkActionSelect => {
        bulkActionSelect.value = '';
    };

    registerEventListeners();
};
