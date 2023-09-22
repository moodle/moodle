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
 * Common javascript for handling actions on the admin page and the user's view of the question bank.
 *
 * @module     qbank_columnsortorder/actions
 * @copyright  2023 onwards Catalyst IT Europe Ltd
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import SortableList from 'core/sortable_list';
import $ from 'jquery';
import * as repository from 'qbank_columnsortorder/repository';
import Notification from "core/notification";
import RefreshUi from 'core_question/refresh_ui';

export const SELECTORS = {
    columnList: '.qbank-column-list',
    sortableColumn: '.qbank-sortable-column',
    removeLink: '[data-action=remove]',
    moveHandler: '[data-drag-type=move]',
    addColumn: '.addcolumn',
    addLink: '[data-action=add]',
    actionLink: '.action-link',
};

/**
 * Sets up sortable list in the column sort order page.
 *
 * @param {Element} listRoot Element containing the sortable list.
 * @param {Boolean} vertical Is the list in vertical orientation, rather than horizonal?
 * @param {Boolean} global Should changes be saved to global config, rather than user preferences?
 * @return {jQuery} sortable column elements, for attaching additional event listeners.
 */
export const setupSortableLists = (listRoot, vertical = false, global = false) => {
    const sortableList = new SortableList(listRoot, {
        moveHandlerSelector: SELECTORS.moveHandler,
        isHorizontal: !vertical,
    });
    sortableList.getElementName = element => Promise.resolve(element.data('name'));

    const sortableColumns = $(SELECTORS.sortableColumn);

    sortableColumns.on(SortableList.EVENTS.DROP, () => {
        repository.setColumnbankOrder(getColumnOrder(listRoot), global).catch(Notification.exception);
        listRoot.querySelectorAll(SELECTORS.sortableColumn).forEach(item => item.classList.remove('active'));
    });

    sortableColumns.on(SortableList.EVENTS.DRAGSTART, (event) => {
        event.currentTarget.classList.add('active');
    });

    return sortableColumns;
};

/**
 * Set up event handlers for action buttons.
 *
 * For each action, call the web service to update the appropriate setting or user preference, then call the fragment to
 * refresh the view.
 *
 * @param {Element} uiRoot The root of the question bank UI.
 * @param {Boolean} global Should changes be saved to global config, rather than user preferences?
 */
export const setupActionButtons = (uiRoot, global = false) => {
    uiRoot.addEventListener('click', async(e) => {
        const actionLink = e.target.closest(SELECTORS.actionLink);
        if (!actionLink) {
            return;
        }
        try {
            e.preventDefault();
            const action = actionLink.dataset.action;
            if (action === 'add' || action === 'remove') {
                const hiddenColumns = [];
                const addColumnList = document.querySelector(SELECTORS.addColumn);
                if (addColumnList) {
                    addColumnList.querySelectorAll(SELECTORS.addLink).forEach(item => {
                        if (action === 'add' && item === actionLink) {
                            return;
                        }
                        hiddenColumns.push(item.dataset.column);
                    });
                }
                if (action === 'remove') {
                    hiddenColumns.push(actionLink.dataset.column);
                }
                await repository.setHiddenColumns(hiddenColumns, global);
            } else if (action === 'reset') {
                await repository.resetColumns(global);
            }
            const actionUrl = new URL(actionLink.href);
            const returnUrl = new URL(actionUrl.searchParams.get('returnurl').replaceAll('&amp;', '&'));
            await RefreshUi.refresh(uiRoot, returnUrl);
        } catch (ex) {
            await Notification.exception(ex);
        }
    });
};

/**
 * Gets the newly reordered columns to display in the question bank view.
 * @param {Element} listRoot
 * @returns {Array}
 */
export const getColumnOrder = listRoot => {
    const columns = Array.from(listRoot.querySelectorAll('[data-columnid]'))
        .map(column => column.dataset.columnid);

    return columns.filter((value, index) => columns.indexOf(value) === index);
};
