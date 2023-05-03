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

const SELECTORS = {
    GRADEPARENT: '.gradeparent',
    STUDENTHEADER: '#studentheader',
    TABLEHEADER: 'th.header',
    BEHAT: 'body.behat-site',
    AVERAGEROW: 'tr.lastrow',
    TABLEHEADING: 'tr.heading',
    GRADERDROPDOWN: 'tr th.category .dropdown-menu',
};

/**
 * Initialize module
 */
export const init = () => {
    const grader = document.querySelector(SELECTORS.GRADEPARENT);
    const tableHeaders = grader.querySelectorAll(SELECTORS.TABLEHEADER);

    let i = 0;
    tableHeaders.forEach((tableHeader) => {
        if (tableHeader.colSpan <= 1) {
            tableHeader.style.zIndex = tableHeaders.length - i;
        }
        i++;
    });

    const categoryDropdowns = grader.querySelectorAll(SELECTORS.GRADERDROPDOWN);
    categoryDropdowns.forEach(dropdown => {
        // Ensure we take all the displayed users + any & all categories and add a bit extra for safe measure.
        dropdown.style.zIndex = (tableHeaders.length + categoryDropdowns.length) + 1;
    });

    const tableHeader = grader.querySelector(SELECTORS.TABLEHEADING);
    tableHeader.style.zIndex = tableHeaders.length + 1;

    if (!document.querySelector(SELECTORS.BEHAT)) {
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
