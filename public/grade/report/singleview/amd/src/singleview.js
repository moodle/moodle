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
 * Allow navigation through table cells using Ctrl + arrow keys and handle override toggles.
 *
 * @module    gradereport_singleview/singleview
 * @copyright The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const selectors = {
    cell: 'td.cell, th.cell',
    col: 'td, th',
    keyboardHandled: 'table input, table select, table a',
    navigableCell: 'input:not([type="hidden"]):not([disabled]), select, a',
    override: 'input[name^=override_]',
    row: 'tr',
    // Dyanmic selectors.
    input: (interest) => `input[name$='${interest}'][data-uielement='text']`,
    select: (interest) => `select[name$='${interest}']`,
};

let initialized = false;

/**
 * Initializes the module, setting up event listeners for table cell navigation and override toggles.
 */
export function init() {
    if (initialized) {
        return;
    }
    initialized = true;

    // Add ctrl+arrow controls for navigation.
    // Use capturing phase to intercept events before they reach anchor elements.
    document.addEventListener('keydown', keydownHandler, true);

    // Handle override toggles.
    document.querySelectorAll(selectors.override).forEach(input => {
        input.addEventListener('change', () => {
            updateOverrideToggle(input);
        });
    });
}

/**
 * Handles control+arrow table navigation.
 *
 * @private
 * @param {KeyboardEvent} event The keydown event.
 */
function keydownHandler(event) {
    if (!event.ctrlKey) {
        return;
    }

    // Check if it's an arrow key.
    if (!['ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown'].includes(event.key)) {
        return;
    }

    const activeElement = document.activeElement;
    if (!activeElement.matches(selectors.keyboardHandled)) {
        return;
    }

    let next = null;
    switch (event.key) {
        case 'ArrowLeft':
            next = getPrevCell(activeElement.closest(selectors.col));
            break;
        case 'ArrowUp':
            next = getAboveCell(activeElement.closest(selectors.col));
            break;
        case 'ArrowRight':
            next = getNextCell(activeElement.closest(selectors.col));
            break;
        case 'ArrowDown':
            next = getBelowCell(activeElement.closest(selectors.col));
            break;
    }

    // Immediately prevent default behavior and stop propagation.
    event.preventDefault();
    event.stopImmediatePropagation();

    if (next) {
        next.querySelector(selectors.navigableCell)?.focus();
    }
}

/**
 * Handles changes to override toggles.
 *
 * @private
 * @param {HTMLInputElement} input The override toggle input element.
 */
function updateOverrideToggle(input) {
    const checked = input.checked;
    const [, itemid, userid] = input.getAttribute('name').split('_');
    const interest = `_${itemid}_${userid}`;

    // Handle text inputs.
    document.querySelectorAll(selectors.input(interest)).forEach(
        text => {
            text.disabled = !checked;
        }
    );

    // Handle select elements.
    document.querySelectorAll(selectors.select(interest)).forEach(
        select => {
            select.disabled = !checked;
        }
    );
}

/**
 * Helper function to get the next cell in the table.
 *
 * @private
 * @param {HTMLElement} cell The cell of the table.
 * @returns {HTMLElement|null} The next navigable cell or null if none found.
 */
function getNextCell(cell) {
    const checkElement = cell || document.activeElement;
    const next = checkElement.nextElementSibling?.matches(selectors.cell) ? checkElement.nextElementSibling : null;
    if (!next) {
        return null;
    }
    // Continue until we find a navigable cell.
    if (!next.querySelector(selectors.navigableCell)) {
        return getNextCell(next);
    }

    return next;
}

/**
 * Helper function to get the previous cell in the table.
 *
 * @private
 * @param {HTMLElement} cell The cell of the table.
 */
function getPrevCell(cell) {
    const checkElement = cell || document.activeElement;
    const prev = checkElement.previousElementSibling?.matches(selectors.cell) ? checkElement.previousElementSibling : null;
    if (!prev) {
        return null;
    }
    // Continue until we find a navigable cell.
    if (!prev.querySelector(selectors.navigableCell)) {
        return getPrevCell(prev);
    }

    return prev;
}

/**
 * Helper function to get the cell above the current cell in the table.
 *
 * @private
 * @param {HTMLElement} cell The current table cell element.
 * @returns {HTMLElement|null} The cell above or null if none found.
 */
function getAboveCell(cell) {
    const checkElement = cell || document.activeElement;
    const tr = checkElement.closest(selectors.row).previousElementSibling;
    const columnIndex = getColumnIndex(checkElement);
    if (!tr) {
        return null;
    }
    const next = tr.querySelectorAll(selectors.col)[columnIndex];
    // Continue until we find a navigable cell.
    if (!next?.querySelector(selectors.navigableCell)) {
        return getAboveCell(next);
    }

    return next;
}

/**
 * Helper function to get the cell below the current cell in the table.
 *
 * @private
 * @param {HTMLElement} cell The current table cell element.
 * @returns {HTMLElement|null} The cell below or null if none found.
 */
function getBelowCell(cell) {
    const checkElement = cell || document.activeElement;
    const tr = checkElement.closest('tr').nextElementSibling;
    const columnIndex = getColumnIndex(checkElement);
    if (!tr) {
        return null;
    }
    const next = tr.querySelectorAll('td, th')[columnIndex];
    // Continue until we find a navigable cell.
    if (!next?.querySelector(selectors.navigableCell)) {
        return getBelowCell(next);
    }

    return next;
}

/**
 * Helper function to get the column index of a cell.
 *
 * @param {HTMLElement} cell The cell of the table.
 * @returns {number} The index of the cell within its row.
 */
function getColumnIndex(cell) {
    const rowNode = cell.closest(selectors.row);
    if (!rowNode || !cell) {
        return -1;
    }
    const cells = Array.from(rowNode.querySelectorAll(selectors.col));

    return cells.indexOf(cell);
}
