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
 * Expand the collapse section element.
 *
 * @module      core_admin/expand_hash
 * @copyright   Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       4.5
 */

const SELECTORS = {
    COLLAPSE_ELEMENTS: '[data-toggle="collapse"]',
    FOCUSTHENEXPAND_ELEMENTS: '.focus-expand',
};

/**
 * Initializes the focus and expand functionality.
 */
export const init = () => {
    // Select all collapsible elements only.
    const focusexpand = document.querySelector(SELECTORS.FOCUSTHENEXPAND_ELEMENTS);

    // Add click event listener to the anchor element
    focusexpand?.addEventListener('click', () => {
        expandSection(`${focusexpand.getAttribute('href')}`);
    });
};

/**
 * Expands a section based on the provided URL hash.
 *
 * This function takes a hash string, finds the corresponding element in the DOM,
 * and expands it if it is currently collapsed. It updates the necessary ARIA
 * attributes and classes to reflect the expanded state.
 *
 * @param {string} hash - The hash (e.g., '#elementId') of the element to expand.
 */
export const expandSection = (hash) => {
    const container = document.querySelector(hash);
    const targetContainer = container?.querySelector(SELECTORS.COLLAPSE_ELEMENTS);

    if (targetContainer?.getAttribute('aria-expanded') === 'false') {
        const collapseId = targetContainer.getAttribute('aria-controls');
        const collapseContainer = document.getElementById(collapseId);

        // Show the content.
        collapseContainer.classList.remove('collapse');
        collapseContainer.classList.add('show');

        // Update aria-expanded attribute to reflect the new state.
        targetContainer.setAttribute('aria-expanded', 'true');
        targetContainer.classList.remove('collapsed');

        // Get collapse expand menu element.
        const collapseElement = document.querySelector('.collapseexpand.collapsemenu');
        // Ensure it gets noticed to make it work.
        collapseElement.setAttribute('aria-expanded', 'true');
        collapseElement.classList.remove('collapsed');
    }
};
