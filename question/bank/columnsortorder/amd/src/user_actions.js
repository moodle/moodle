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
 * Javascript for customising the user's view of the question bank
 *
 * @module     qbank_columnsortorder/user_actions
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as actions from 'qbank_columnsortorder/actions';
import * as repository from 'qbank_columnsortorder/repository';
import {get_string as getString} from 'core/str';
import ModalEvents from 'core/modal_events';
import ModalSaveCancel from 'core/modal_save_cancel';
import Notification from "core/notification";
import SortableList from 'core/sortable_list';
import Templates from "core/templates";


const SELECTORS = {
    uiRoot: '.questionbankwindow',
    moveAction: '.menu-action[data-action=move]',
    resizeAction: '.menu-action[data-action=resize]',
    resizeHandle: '.qbank_columnsortorder-action-handle.resize',
    handleContainer: '.handle-container',
    headerContainer: '.header-container',
    tableColumn: identifier => `td[data-columnid="${identifier.replace(/["\\]/g, '\\$&')}"]`,
};

/** To track mouse event on a table header */
let currentHeader;

/** Current mouse x postion, to track mouse event on a table header */
let currentX;

/** Minimum size for the column currently being resized. */
let currentMin;

/**
 * Flag to temporarily prevent move and resize handles from being shown or hidden.
 *
 * @type {boolean}
 */
let suspendShowHideHandles = false;

/**
 * Add handle containers for move and resize handles.
 *
 * @param {Element} uiRoot The root element of the quesiton bank UI.
 * @return {Promise} Resolved after the containers have been added to each column header.
 */
const addHandleContainers = uiRoot => {
    return new Promise((resolve) => {
        const headerContainers = uiRoot.querySelectorAll(SELECTORS.headerContainer);
        Templates.renderForPromise('qbank_columnsortorder/handle_container', {})
            .then(({html, js}) => {
                headerContainers.forEach(container => {
                    Templates.prependNodeContents(container, html, js);
                });
                resolve();
                return headerContainers;
            }).catch(Notification.exception);
    });
};

/**
 * Render move handles in each container.
 *
 * This takes a list of the move actions rendered in each column header, and creates a corresponding drag handle for each.
 *
 * @param {NodeList} moveActions Menu actions for moving columns.
 */
const setUpMoveHandles = moveActions => {
    moveActions.forEach(moveAction => {
        const header = moveAction.closest('th');
        header.classList.add('qbank-sortable-column');
        const handleContainer = header.querySelector(SELECTORS.handleContainer);
        const context = {
            action: "move",
            dragtype: "move",
            target: '',
            title: moveAction.title,
            pixicon: "i/dragdrop",
            pixcomponent: "core",
            popup: true
        };
        return Templates.renderForPromise('qbank_columnsortorder/action_handle', context)
            .then(({html, js}) => {
                Templates.prependNodeContents(handleContainer, html, js);
                return handleContainer;
            }).catch(Notification.exception);
    });
};

/**
 * Serialise the current column sizes.
 *
 * This finds the current width set in each column header's style property, and returns them encoded as a JSON string.
 *
 * @param {Element} uiRoot The root element of the quesiton bank UI.
 * @return {String} JSON array containing a list of objects with column and width properties.
 */
const serialiseColumnSizes = (uiRoot) => {
    const columnSizes = [];
    const tableHeaders = uiRoot.querySelectorAll('th');
    tableHeaders.forEach(header => {
        // Only get the width set via style attribute (set by move action).
        const width = parseInt(header.style.width);
        if (!width || isNaN(width)) {
            return;
        }
        columnSizes.push({
            column: header.dataset.columnid,
            width: width
        });
    });
    return JSON.stringify(columnSizes);
};

/**
 * Find the minimum width for a header, based on the width of its contents.
 *
 * This is to simulate `min-width: min-content;`, which doesn't work on Chrome because
 * min-width is ignored width `table-layout: fixed;`.
 *
 * @param {Element} header The table header
 * @return {Number} The minimum width in pixels
 */
const getMinWidth = (header) => {
    const contents = Array.from(header.querySelector('.header-text').children);
    const contentWidth = contents.reduce((width, contentElement) => width + contentElement.getBoundingClientRect().width, 0);
    return Math.ceil(contentWidth);
};

/**
 * Render resize handles in each container.
 *
 * This takes a list of the resize actions rendered in each column header, and creates a corresponding drag handle for each.
 * It also initialises the event handlers for the drag handles and resize modal.
 *
 * @param {Element} uiRoot Question bank UI root element.
 */
const setUpResizeHandles = (uiRoot) => {
    const resizeActions = uiRoot.querySelectorAll(SELECTORS.resizeAction);
    resizeActions.forEach(resizeAction => {
        const headerContainer = resizeAction.closest(SELECTORS.headerContainer);
        const header = resizeAction.closest(actions.SELECTORS.sortableColumn);
        const minWidth = getMinWidth(header);
        if (header.offsetWidth < minWidth) {
            header.style.width = minWidth + 'px';
        }
        const handleContainer = headerContainer.querySelector(SELECTORS.handleContainer);
        const context = {
            action: "resize",
            target: '',
            title: resizeAction.title,
            pixicon: 'i/twoway',
            pixcomponent: 'core',
            popup: true
        };
        return Templates.renderForPromise('qbank_columnsortorder/action_handle', context)
            .then(({html, js}) => {
                Templates.appendNodeContents(handleContainer, html, js);
                return handleContainer;
            }).catch(Notification.exception);
    });

    let moveTracker = false;
    let currentResizeHandle = null;
    // Start mouse event on headers.
    uiRoot.addEventListener('mousedown', e => {
        currentResizeHandle = e.target.closest(SELECTORS.resizeHandle);
        // Return if it is not ' resize' button.
        if (!currentResizeHandle) {
            return;
        }
        // Save current position.
        currentX = e.pageX;
        // Find the header.
        currentHeader = e.target.closest(actions.SELECTORS.sortableColumn);
        currentMin = getMinWidth(currentHeader);
        moveTracker = false;
        suspendShowHideHandles = true;
    });

    // Resize column as the mouse move.
    document.addEventListener('mousemove', e => {
        if (!currentHeader || !currentResizeHandle || currentX === 0) {
            return;
        }

        // Prevent text selection as the handle is dragged.
        document.getSelection().removeAllRanges();

        // Adjust the column width according the amount the handle was dragged.
        const offset = e.pageX - currentX;
        currentX = e.pageX;
        const newWidth = currentHeader.offsetWidth + offset;
        if (newWidth >= currentMin) {
            currentHeader.style.width = newWidth + 'px';
        }
        moveTracker = true;
    });

    // Set new size when mouse is up.
    document.addEventListener('mouseup', () => {
        if (!currentHeader || !currentResizeHandle || currentX === 0) {
            return;
        }
        if (moveTracker) {
            // If the mouse moved, we are changing the size by drag, so save the change.
            repository.setColumnSize(serialiseColumnSizes(uiRoot)).catch(Notification.exception);
        } else {
            // If the mouse didn't move, display a modal to change the size using a form.
            showResizeModal(currentHeader, uiRoot);
        }
        currentMin = null;
        currentHeader = null;
        currentResizeHandle = null;
        currentX = 0;
        moveTracker = false;
        suspendShowHideHandles = false;
    });
};

/**
 * Event handler for resize actions in each column header.
 *
 * This will listen for a click on any resize action, and activate the corresponding resize modal.
 *
 * @param {Element} uiRoot Question bank UI root element.
 */
const setUpResizeActions = uiRoot => {
    uiRoot.addEventListener('click', (e) => {
        const resizeAction = e.target.closest(SELECTORS.resizeAction);
        if (resizeAction) {
            e.preventDefault();
            const currentHeader = resizeAction.closest('th');
            showResizeModal(currentHeader, uiRoot);
        }
    });
};

/**
 * Show a modal containing a number input for changing a column width without click-and-drag.
 *
 * @param {Element} currentHeader The header element that is being resized.
 * @param {Element} uiRoot The question bank UI root element.
 * @returns {Promise<void>}
 */
const showResizeModal = async(currentHeader, uiRoot) => {
    const initialWidth = currentHeader.offsetWidth;
    const minWidth = getMinWidth(currentHeader);

    const modal = await ModalSaveCancel.create({
        title: getString('resizecolumn', 'qbank_columnsortorder', currentHeader.dataset.name),
        body: Templates.render('qbank_columnsortorder/resize_modal', {width: initialWidth, min: minWidth}),
        show: true,
    });
    const root = modal.getRoot();
    root.on(ModalEvents.cancel, () => {
        currentHeader.style.width = `${initialWidth}px`;
    });
    root.on(ModalEvents.save, () => {
        repository.setColumnSize(serialiseColumnSizes(uiRoot)).catch(Notification.exception);
    });

    const body = await modal.bodyPromise;
    const input = body.get(0).querySelector('input');

    input.addEventListener('change', e => {
        const valid = e.target.checkValidity();
        e.target.closest('.has-validation').classList.add('was-validated');
        if (valid) {
            const newWidth = e.target.value;
            currentHeader.style.width = `${newWidth}px`;
        }
    });
};

/**
 * Event handler for move actions in each column header.
 *
 * This will listen for a click on any move action, pass the click to the corresponding move handle, causing its modal to be shown.
 *
 * @param {Element} uiRoot Question bank UI root element.
 */
const setUpMoveActions = uiRoot => {
    uiRoot.addEventListener('click', e => {
        const moveAction = e.target.closest(SELECTORS.moveAction);
        if (moveAction) {
            e.preventDefault();
            const sortableColumn = moveAction.closest(actions.SELECTORS.sortableColumn);
            const moveHandle = sortableColumn.querySelector(actions.SELECTORS.moveHandler);
            moveHandle.click();
        }
    });
};

/**
 * Event handler for showing and hiding handles when the mouse is over a column header.
 *
 * Implementing this behaviour using the :hover CSS pseudoclass is not sufficient, as the mouse may move over the neighbouring
 * header while dragging, leading to some odd behaviour. This allows us to suspend the show/hide behaviour while a handle is being
 * dragged, and so keep the active handle visible until the drag is finished.
 *
 * @param {Element} uiRoot Question bank UI root element.
 */
const setupShowHideHandles = uiRoot => {
    let shownHeader = null;
    let tableHead = uiRoot.querySelector('thead');
    uiRoot.addEventListener('mouseover', e => {
        if (suspendShowHideHandles) {
            return;
        }
        const header = e.target.closest(actions.SELECTORS.sortableColumn);
        if (!header && !shownHeader) {
            return;
        }
        if (!header || header !== shownHeader) {
            tableHead.querySelector('.show-handles')?.classList.remove('show-handles');
            shownHeader = header;
            if (header) {
                header.classList.add('show-handles');
            }
        }
    });
};

/**
 * Event handler for sortable list DROP event.
 *
 * Find all table cells corresponding to the column of the dropped header, and move them to the new position.
 *
 * @param {Event} event
 */
const reorderColumns = event => {
    // Current header.
    const header = event.target;
    // Find the previous sibling of the header, which will be used when moving columns.
    const insertAfter = header.previousElementSibling;
    // Move columns.
    const uiRoot = document.querySelector(SELECTORS.uiRoot);
    const columns = uiRoot.querySelectorAll(SELECTORS.tableColumn(header.dataset.columnid));
    columns.forEach(column => {
        const row = column.parentElement;
        if (insertAfter) {
            // Find the column to insert after.
            const insertAfterColumn = row.querySelector(SELECTORS.tableColumn(insertAfter.dataset.columnid));
            // Insert the column.
            insertAfterColumn.after(column);
        } else {
            // Insert as the first child (first column in the table).
            row.insertBefore(column, row.firstChild);
        }
    });
};

/**
 * Initialize module
 *
 * Add containers for the drag handles to each column header, then render handles, enable show/hide behaviour, set up drag/drop
 * column sorting, then enable the move and resize modals to be triggered from menu actions.
 */
export const init = async() => {
    const uiRoot = document.getElementById('questionscontainer');
    await addHandleContainers(uiRoot);
    setUpMoveHandles(uiRoot.querySelectorAll(SELECTORS.moveAction));
    setUpResizeHandles(uiRoot);
    setupShowHideHandles(uiRoot);
    const sortableColumns = actions.setupSortableLists(uiRoot.querySelector(actions.SELECTORS.columnList));
    sortableColumns.on(SortableList.EVENTS.DROP, reorderColumns);
    sortableColumns.on(SortableList.EVENTS.DRAGSTART, () => {
        suspendShowHideHandles = true;
    });
    sortableColumns.on(SortableList.EVENTS.DRAGEND, () => {
        suspendShowHideHandles = false;
    });
    setUpMoveActions(uiRoot);
    setUpResizeActions(uiRoot);
    actions.setupActionButtons(uiRoot);
};
