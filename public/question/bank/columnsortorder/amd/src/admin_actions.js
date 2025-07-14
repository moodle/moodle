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
 * Javascript for handling actions on the admin page
 *
 * @module     qbank_columnsortorder/admin_actions
 * @copyright  2023 onwards Catalyst IT Europe Ltd
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as actions from 'qbank_columnsortorder/actions';
import * as repository from 'qbank_columnsortorder/repository';
import Notification from "core/notification";
import Pending from 'core/pending';

/**
 * Event handler to save the custom column widths when a field is edited.
 *
 * @param {Element} listRoot The root element of the list of columns.
 */
const setupSaveWidths = listRoot => {
    listRoot.addEventListener('change', async() => {
        const pendingPromise = new Pending('saveWidths');
        const columns = listRoot.querySelectorAll(actions.SELECTORS.sortableColumn);
        const widths = [];
        columns.forEach(column => {
            const widthInput = column.querySelector('.width-input');
            const valid = widthInput.checkValidity();
            widthInput.closest('.has-validation').classList.add('was-validated');
            if (!valid) {
                return;
            }
            widths.push({
                column: column.dataset.columnid,
                width: widthInput.value,
            });
        });
        await repository.setColumnSize(JSON.stringify(widths), true).catch(Notification.exception);
        pendingPromise.resolve();
    });
};

/**
 * Initialize module
 *
 * Set up event handlers for the action buttons, width fields and initialise column sorting.
 *
 * @param {String} id ID for the admin UI root element.
 */
export const init = id => {
    const uiRoot = document.getElementById(id);
    const listRoot = uiRoot.querySelector(actions.SELECTORS.columnList);
    actions.setupSortableLists(listRoot, true, true);
    actions.setupActionButtons(uiRoot, true);
    setupSaveWidths(listRoot);
};
