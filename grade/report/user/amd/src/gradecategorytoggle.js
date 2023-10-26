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
 * Javascript module for toggling the visibility of the grade categories in the user report.
 *
 * @module    gradereport_user/gradecategorytoggle
 * @copyright 2022 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    CATEGORY_TOGGLE: '.toggle-category',
    USER_REPORT_TABLE: '.user-grade'
};

/**
 * Register related event listeners.
 *
 * @method registerListenerEvents
 * @param {string} userReportId The ID of the user report container element.
 */
const registerListenerEvents = (userReportId) => {
    const reportContainer = document.querySelector('#' + userReportId);
    const userReport = reportContainer.querySelector(SELECTORS.USER_REPORT_TABLE);

    userReport.addEventListener('click', e => {
        const toggle = e.target.closest(SELECTORS.CATEGORY_TOGGLE);

        if (toggle) {
            toggleCategory(toggle);
        }
    });
};

/**
 * Method that handles the category toggle action.
 *
 * @method toggleCategory
 * @param {object} toggleElement The category toggle node that was clicked.
 */
const toggleCategory = (toggleElement) => {
    const target = toggleElement.dataset.target;
    const categoryId = toggleElement.dataset.categoryid;
    // Whether the toggle action is collapsing the category or not.
    const isCollapsing = toggleElement.getAttribute('aria-expanded') === "true";
    const userReport = toggleElement.closest(SELECTORS.USER_REPORT_TABLE);

    // Find all targeted 'children' rows of the toggled category.
    const targetRows = userReport.querySelectorAll(target);

    if (isCollapsing) {
        toggleElement.setAttribute('aria-expanded', 'false');
        // Update the 'data-target' of the toggle category node to make sure that when we perform another toggle action
        // to expand this category we only target rows which have been hidden by this category toggle action.
        toggleElement.dataset.target = `[data-hidden-by='${categoryId}']`;
    } else {
        toggleElement.setAttribute('aria-expanded', 'true');
        // Update the 'data-target' of the toggle category node to make sure that when we perform another toggle action
        // to collapse this category we only target rows which are children of this category and are not currently hidden.
        toggleElement.dataset.target = `.cat_${categoryId}[data-hidden='false']`;
    }

    // Loop through all targeted children row elements and update the required data attributes to either hide or show
    // them depending on the toggle action (collapsing or expanding).
    targetRows.forEach((row) => {
        if (isCollapsing) {
            row.dataset.hidden = 'true';
            row.dataset.hiddenBy = categoryId;
        } else {
            row.dataset.hidden = 'false';
            row.dataset.hiddenBy = '';
        }
    });

    // Since the user report is presented in an HTML table, rowspans are used under each category to create a visual
    // hierarchy between categories and grading items. When expanding or collapsing a category we need to also update
    // (subtract or add) the rowspan values associated to each parent category row to preserve the correct visual
    // hierarchy in the table.
    updateParentCategoryRowspans(toggleElement, targetRows.length);
};

/**
 * Method that updates the rowspan value of all 'parent' category rows of a given category node.
 *
 * @method updateParentCategoryRowspans
 * @param {object} toggleElement The category toggle node that was clicked.
 * @param {int} num The number we want to add or subtract from the rowspan value of the 'parent' category row elements.
 */
const updateParentCategoryRowspans = (toggleElement, num) => {
    const userReport = toggleElement.closest(SELECTORS.USER_REPORT_TABLE);
    // Get the row element which contains the category toggle node.
    const rowElement = toggleElement.closest('tr');

    // Loop through the class list of the toggle category row element.
    // The list contains classes which identify all parent categories of the toggled category.
    rowElement.classList.forEach((className) => {
        // Find the toggle node of the 'parent' category that is identified by the given class name.
        const parentCategoryToggleElement = userReport.querySelector(`[data-target=".${className}[data-hidden='false']"`);
        if (parentCategoryToggleElement) {
            // Get the row element which contains the parent category toggle node.
            const categoryRowElement = parentCategoryToggleElement.closest('tr');
            // Find the rowspan element associated to this parent category.
            const categoryRowSpanElement = categoryRowElement.nextElementSibling.querySelector('[rowspan]');

            // Depending on whether the toggle action has expanded or collapsed the category, either add or
            // subtract from the 'parent' category rowspan.
            if (toggleElement.getAttribute('aria-expanded') === "true") {
                categoryRowSpanElement.rowSpan = categoryRowSpanElement.rowSpan + num;
            } else { // The category has been collapsed.
                categoryRowSpanElement.rowSpan = categoryRowSpanElement.rowSpan - num;
            }
        }
    });
};

/**
 * Init method.
 *
 * @param {string} userReportId The ID of the user report container element.
 */
export const init = (userReportId) => {
    registerListenerEvents(userReportId);
};
