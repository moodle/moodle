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
 * Javascript module for fixing the position of sticky headers with multiple colspans
 *
 * @module      gradereport_grader/stickycolspan
 * @copyright   2022 Bas Brands <bas@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

const SELECTORS = {
    GRADEPARENT: '.gradeparent',
    STUDENTHEADER: '#studentheader',
    TABLEHEADER: 'th.header',
    BEHAT: 'body.behat-site',
    USERDROPDOWN: '.userrow th .dropdown',
    AVERAGEROWHEADER: '.lastrow th',
};

/**
 * Initialize module
 */
export const init = () => {
    // The sticky positioning attributed to the user column cells affects the stacking context and makes the dropdowns
    // within these cells to be cut off. To solve this problem, whenever one of these action menus (dropdowns) is opened
    // we need to manually bump up the z-index value of the parent container element and revert once closed.
    $(SELECTORS.USERDROPDOWN).on('show.bs.dropdown hide.bs.dropdown', (e) => {
        // The closest heading element has sticky positioning which affects the stacking context in this case.
        e.target.closest(SELECTORS.TABLEHEADER).classList.toggle('actions-menu-active');
    });
    // Register an observer that will bump up the z-index value of the average overall row when it's pinned to prevent
    // the row being cut-off by the user column cells or other components within the report table that have higher
    // z-index values.
    const observer = new IntersectionObserver(
        ([e]) => e.target.closest('tr').classList.toggle('pinned', e.intersectionRatio < 1),
        {threshold: [1]}
    );
    observer.observe(document.querySelector(SELECTORS.AVERAGEROWHEADER));

    if (!document.querySelector(SELECTORS.BEHAT)) {
        const grader = document.querySelector(SELECTORS.GRADEPARENT);
        const tableHeaders = grader.querySelectorAll(SELECTORS.TABLEHEADER);
        const studentHeader = grader.querySelector(SELECTORS.STUDENTHEADER);
        const leftOffset = getComputedStyle(studentHeader).getPropertyValue('left');
        const rightOffset = getComputedStyle(studentHeader).getPropertyValue('right');

        tableHeaders.forEach((tableHeader) => {
            if (tableHeader.colSpan > 1) {
                const addOffset = (tableHeader.offsetWidth - studentHeader.offsetWidth);
                if (window.right_to_left()) {
                    tableHeader.style.right = 'calc(' + rightOffset + ' - ' + addOffset + 'px )';
                } else {
                    tableHeader.style.left = 'calc(' + leftOffset + ' - ' + addOffset + 'px )';
                }
            }
        });
    }
};
