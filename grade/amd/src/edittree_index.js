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
 * Enhance the gradebook tree setup with various facilities.
 *
 * @module     core_grades/edittree_index
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import storage from 'core/localstorage';
import {addIconToContainer} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';

const SELECTORS = {
    CATEGORY_TOGGLE: '.toggle-category',
    GRADEBOOK_SETUP_TABLE: '.setup-grades',
    WEIGHT_OVERRIDE_CHECKBOX: '.weightoverride',
    BULK_MOVE_SELECT: '#menumoveafter',
    BULK_MOVE_INPUT: '#bulkmoveinput',
    GRADEBOOK_SETUP_WRAPPER: '.gradetree-wrapper',
    GRADEBOOK_SETUP_BOX: '.gradetreebox'
};

/**
 * Register related event listeners.
 *
 * @method registerListenerEvents
 * @param {int} courseId The ID of course.
 * @param {int} userId The ID of the current logged user.
 */
const registerListenerEvents = (courseId, userId) => {

    document.addEventListener('change', e => {
        // Toggle the availability of the weight input field based on the changed state (checked/unchecked) of the
        // related checkbox element.
        if (e.target.matches(SELECTORS.WEIGHT_OVERRIDE_CHECKBOX)) {
            toggleWeightInput(e.target);
        }
        // Submit the bulk move form when the selected option in the bulk move select element has been changed.
        if (e.target.matches(SELECTORS.BULK_MOVE_SELECT)) {
            submitBulkMoveForm(e.target);
        }
    });

    const gradebookSetup = document.querySelector(SELECTORS.GRADEBOOK_SETUP_TABLE);
    gradebookSetup.addEventListener('click', e => {
        const toggle = e.target.closest(SELECTORS.CATEGORY_TOGGLE);
        // Collapse or expand the grade category when the visibility toggle button is activated.
        if (toggle) {
            toggleCategory(toggle, courseId, userId, true);
        }
    });
};

/**
 * Toggle the weight input field based on its checkbox.
 *
 * @method toggleWeightInput
 * @param {object} weightOverrideCheckbox The weight override checkbox element.
 */
const toggleWeightInput = (weightOverrideCheckbox) => {
    const row = weightOverrideCheckbox.closest('tr');
    const itemId = row.dataset.itemid;
    const weightOverrideInput = row.querySelector(`input[name="weight_${itemId}"]`);
    weightOverrideInput.disabled = !weightOverrideCheckbox.checked;
};

/**
 * Submit the bulk move form.
 *
 * @method toggleWeightInput
 * @param {object} bulkMoveSelect The bulk move select element.
 */
const submitBulkMoveForm = (bulkMoveSelect) => {
    const form = bulkMoveSelect.closest('form');
    const bulkMoveInput = form.querySelector(SELECTORS.BULK_MOVE_INPUT);
    bulkMoveInput.value = 1;
    form.submit();
};

/**
 * Method that collapses all relevant grade categories based on the locally stored state of collapsed grade categories
 * for a given user.
 *
 * @method collapseGradeCategories
 * @param {int} courseId The ID of course.
 * @param {int} userId The ID of the current logged user.
 */
const collapseGradeCategories = (courseId, userId) => {
    const gradebookSetup = document.querySelector(SELECTORS.GRADEBOOK_SETUP_TABLE);
    const storedCollapsedCategories = storage.get(`core_grade_collapsedgradecategories_${courseId}_${userId}`);

    if (storedCollapsedCategories) {
        // Fetch all grade categories that are locally stored as collapsed and re-apply the collapse action.
        const collapsedCategories = JSON.parse(storedCollapsedCategories);

        collapsedCategories.forEach((category) => {
            const categoryToggleElement =
                gradebookSetup.querySelector(`${SELECTORS.CATEGORY_TOGGLE}[data-category="${category}"`);
            if (categoryToggleElement) {
                toggleCategory(categoryToggleElement, courseId, userId, false);
            }
        });
    }
};

/**
 * Method that updates the locally stored state of collapsed grade categories based on a performed toggle action on a
 * given grade category.
 *
 * @method updateCollapsedCategoriesStoredState
 * @param {string} category The category to be added or removed from the collapsed grade categories local storage.
 * @param {int} courseId The ID of course.
 * @param {int} userId The ID of the current logged user.
 * @param {boolean} isCollapsing Whether the category is being collapsed or not.
 */
const updateCollapsedCategoriesStoredState = (category, courseId, userId, isCollapsing) => {
    const currentStoredCollapsedCategories = storage.get(`core_grade_collapsedgradecategories_${courseId}_${userId}`);
    let collapsedCategories = currentStoredCollapsedCategories ?
        JSON.parse(currentStoredCollapsedCategories) : [];

    if (isCollapsing) {
        collapsedCategories.push(category);
    } else {
        collapsedCategories = collapsedCategories.filter(cat => cat !== category);
    }
    storage.set(`core_grade_collapsedgradecategories_${courseId}_${userId}`, JSON.stringify(collapsedCategories));
};

/**
 * Method that handles the grade category toggle action.
 *
 * @method toggleCategory
 * @param {object} toggleElement The category toggle node that was clicked.
 * @param {int} courseId The ID of course.
 * @param {int} userId The ID of the current logged user.
 * @param {boolean} storeCollapsedState Whether to store (local storage) the state of collapsed grade categories.
 */
const toggleCategory = (toggleElement, courseId, userId, storeCollapsedState) => {
    const target = toggleElement.dataset.target;
    const category = toggleElement.dataset.category;
    // Whether the toggle action is collapsing the category or not.
    const isCollapsing = toggleElement.getAttribute('aria-expanded') === "true";
    const gradebookSetup = toggleElement.closest(SELECTORS.GRADEBOOK_SETUP_TABLE);
    // Find all targeted 'children' rows of the toggled category.
    const targetRows = gradebookSetup.querySelectorAll(target);
    // Find the maximum grade cell in the grade category that is being collapsed/expanded.
    const toggleElementRow = toggleElement.closest('tr');
    const maxGradeCell = toggleElementRow.querySelector('.column-range');

    if (isCollapsing) {
        toggleElement.setAttribute('aria-expanded', 'false');
        // Update the 'data-target' of the toggle category node to make sure that when we perform another toggle action
        // to expand this category we only target rows which have been hidden by this category toggle action.
        toggleElement.dataset.target = `[data-hidden-by='${category}']`;
        if (maxGradeCell) {
            const relatedCategoryAggregationRow = gradebookSetup.querySelector(`[data-aggregationforcategory='${category}']`);
            maxGradeCell.innerHTML = relatedCategoryAggregationRow.querySelector('.column-range').innerHTML;
        }
    } else {
        toggleElement.setAttribute('aria-expanded', 'true');
        // Update the 'data-target' of the toggle category node to make sure that when we perform another toggle action
        // to collapse this category we only target rows which are children of this category and are not currently hidden.
        toggleElement.dataset.target = `.${category}[data-hidden='false']`;
        if (maxGradeCell) {
            maxGradeCell.innerHTML = '';
        }
    }
    // If explicitly instructed, update accordingly the locally stored state of collapsed categories based on the
    // toggle action performed on the given grade category.
    if (storeCollapsedState) {
        updateCollapsedCategoriesStoredState(category, courseId, userId, isCollapsing);
    }

    // Loop through all targeted child row elements and update the required data attributes to either hide or show
    // them depending on the toggle action (collapsing or expanding).
    targetRows.forEach((row) => {
        if (isCollapsing) {
            row.dataset.hidden = 'true';
            row.dataset.hiddenBy = category;
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
    const gradebookSetup = toggleElement.closest(SELECTORS.GRADEBOOK_SETUP_TABLE);
    // Get the row element which contains the category toggle node.
    const rowElement = toggleElement.closest('tr');

    // Loop through the class list of the toggle category row element.
    // The list contains classes which identify all parent categories of the toggled category.
    rowElement.classList.forEach((className) => {
        // Find the toggle node of the 'parent' category that is identified by the given class name.
        const parentCategoryToggleElement = gradebookSetup.querySelector(`[data-target=".${className}[data-hidden='false']"`);
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
 * Initialize module.
 *
 * @method init
 * @param {int} courseId The ID of course.
 * @param {int} userId The ID of the current logged user.
 */
export const init = (courseId, userId) => {
    const pendingPromise = new Pending();
    const gradebookSetupBox = document.querySelector(SELECTORS.GRADEBOOK_SETUP_BOX);
    // Display a loader while the relevant grade categories are being re-collapsed on page load (based on the locally
    // stored state for the given user).
    addIconToContainer(gradebookSetupBox).then((loader) => {
        setTimeout(() => {
            collapseGradeCategories(courseId, userId);
            // Once the grade categories have been re-collapsed, remove the loader and display the Gradebook setup content.
            loader.remove();
            document.querySelector(SELECTORS.GRADEBOOK_SETUP_WRAPPER).classList.remove('d-none');
            pendingPromise.resolve();
        }, 150);
        return;
    }).fail(Notification.exception);

    registerListenerEvents(courseId, userId);
};
