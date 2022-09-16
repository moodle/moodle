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

import {end, arrowLeft, arrowRight, arrowUp, arrowDown, home, enter, space} from 'core/key_codes';

/**
 * ARIA helpers related to the combobox role.
 *
 * @module     core/local/aria/aria-combobox.
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Our entry point into adding accessibility handling for comboboxes.
 *
 * @param {Element} comboInput The combobox area to add aria handling to.
 */
export const comboBox = (comboInput) => {
    registerEventListeners(comboInput);
};

/**
 * Event management of the provided combobox.
 *
 * @param {Element} comboInput The combobox area to add aria handling to.
 */
const registerEventListeners = (comboInput) => {
    document.addEventListener('keydown', (e) => {
        if (e.target === comboInput) {
            let next = null;
            const comboResultArea = document.querySelector('[data-region="search-result-items-container"]');
            const resultRows = Array.from(comboResultArea.querySelectorAll('[role="row"]'));
            const resultCells = Array.from(comboResultArea.querySelectorAll('[role="gridcell"]'));
            const activeResultRow = comboResultArea.querySelector('.active[role="row"]');
            const activeResultCell = comboResultArea.querySelector('.focused-cell[role="gridcell"]');
            switch (e.keyCode) {
                case arrowUp: {
                    // TODO: Handle the wrapping.
                    if (activeResultRow === null) {
                        next = setFirstActiveRow(next, resultRows, comboInput, resultRows.length - 1);
                    } else {
                        for (let i = 0; i < resultRows.length; i++) {
                            if (resultRows[i].id === activeResultRow.id) {
                                next = resultRows[i - 1];
                                break;
                            }
                        }
                    }
                    break;
                }
                case arrowDown: {
                    if (activeResultRow === null) {
                        next = setFirstActiveRow(next, resultRows, comboInput, 0);
                    } else {
                        for (let i = 0; i < resultRows.length - 1; i++) {
                            if (resultRows[i].id === activeResultRow.id) {
                                next = resultRows[i + 1];
                                break;
                            }
                        }
                    }
                    break;
                }
                case home: {
                    next = resultRows[0];
                    break;
                }
                case end: {
                    next = resultRows[resultRows.length - 1];
                    break;
                }
                case enter || space: {
                    // Redirect the user to the appropriate link.
                    // TODO: Space does not work, special handler on the cell itself?
                    window.location = activeResultCell.href;
                    break;
                }
                case arrowLeft: {
                    if (activeResultRow === null) {
                        next = setFirstActiveRow(next, resultRows, comboInput, 0);
                    } else {
                        for (let i = 0; i < resultCells.length; i++) {
                            if (resultCells[i].id === activeResultCell.id) {
                                if (resultCells[i - 1] === undefined) {
                                    resultCells[i].classList.remove('focused-cell');
                                    resultCells[resultCells.length - 1].classList.add('focused-cell');
                                    break;
                                } else {
                                    resultCells[i].classList.remove('focused-cell');
                                    resultCells[i - 1].classList.add('focused-cell');
                                    break;
                                }
                            }
                        }
                    }
                    break;
                }
                case arrowRight: {
                    if (activeResultRow === null) {
                        next = setFirstActiveRow(next, resultRows, comboInput, 0);
                    } else {
                        for (let i = 0; i < resultCells.length - 1; i++) {
                            if (resultCells[i].id === activeResultCell.id) {
                                resultCells[i].classList.remove('focused-cell');
                                resultCells[i + 1].classList.add('focused-cell');
                                break;
                            }
                            if (resultCells[i + 2] === undefined) {
                                resultCells[i + 1].classList.remove('focused-cell');
                                resultCells[0].classList.add('focused-cell');
                                break;
                            }
                        }
                    }
                    break;
                }
                default: {
                    window.console.log('nothing to see here!');
                    break;
                }
            }
            // Variable next is set if we do want to act on the keypress.
            nextHandler(next, e, activeResultRow, comboInput);
        }
    });
};

/**
 * With search, we can't automatically set aria elements in the results field, so we do it here.
 *
 * @param {Element} next
 * @param {Array} resultRows
 * @param {Element} comboInput
 * @param {Number} val
 * @returns {Element}
 */
const setFirstActiveRow = (next, resultRows, comboInput, val) => {
    // Set first option as active.
    next = resultRows[val];
    next.setAttribute('aria-selected', 'true');
    next.classList.add('active');
    comboInput.setAttribute('aria-activedescendant', next.id);
    next.querySelector('.result-cell').classList.add('focused-cell');
    return next;
};

/**
 * Given we have a value to next set active, handle some of the basic handling.
 *
 * @param {Element} next
 * @param {Event} e
 * @param {Element} activeResultRow
 * @param {Element} comboInput
 */
const nextHandler = (next, e, activeResultRow, comboInput) => {
    if (next) {
        e.preventDefault();
        if (activeResultRow !== null) {
            activeResultRow.classList.remove('active');
            activeResultRow.querySelector('.result-cell').classList.remove('focused-cell');
        }
        next.classList.add('active');
        // Find whatever the first result cell is to add the class.
        next.querySelector('.result-cell').classList.add('focused-cell');
        comboInput.setAttribute('aria-activedescendant', next.id);
    }
};
