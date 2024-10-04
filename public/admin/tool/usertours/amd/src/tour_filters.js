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
 * User tours filters.
 *
 * @module      tool_usertours/tour_filters
 * @copyright   2025 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
const ANY_VALUE = "__ANYVALUE__";

export const init = () => {
    // Initialize the category filter
    initConfigurationCategoryFilter();
};

/**
 * Initialize the category filter for the configuration page.
 */
const initConfigurationCategoryFilter = () => {
    const categorySelect = document.querySelector("[name='filter_category[]']");
    const excludeSelect = document.querySelector("[name='filter_exclude_category[]']");
    const excludeCategoriesContainer = document.getElementById('fitem_id_filter_exclude_category');

    if (categorySelect && excludeSelect) {
        // Add event listeners to update the exclude categories when the include categories change.
        categorySelect.addEventListener("change", () => {
            updateExcludeCategories(categorySelect, excludeSelect, excludeCategoriesContainer);
        });

        // Initialize the exclude categories based on the selected include categories.
        updateExcludeCategories(categorySelect, excludeSelect, excludeCategoriesContainer);
    }
};

/**
 * Adjust the height of a select element based on the number of options.
 *
 * @param {HTMLSelectElement} select
 */
const adjustHeight = (select) => {
    select.size = Math.min(select.options.length || 1, 10);
};

/**
 * Update the exclude categories based on the selected include categories.
 *
 * @param {HTMLSelectElement} categorySelect
 * @param {HTMLSelectElement} excludeSelect
 * @param {HTMLElement} excludeCategoriesContainer
 */
const updateExcludeCategories = (categorySelect, excludeSelect, excludeCategoriesContainer) => {
    // Get the selected categories and update the 'Any' option.
    const selectedCategories = new Set(Array.from(categorySelect.selectedOptions).map(option => option.value));

    // Get the selected exclude categories and create a map of options.
    const excludeSelected = new Set(Array.from(excludeSelect.selectedOptions).map(option => option.value));
    const excludeOptions = new Map();

    // Flag to check if 'Any' value is selected.
    const anySelected = selectedCategories.has(ANY_VALUE);
    Array.from(categorySelect.options).forEach(option => {
        const isNotAny = option.value !== ANY_VALUE;

        // If 'Any' is selected, include all options in excludeOptions.
        if (anySelected && isNotAny) {
            excludeOptions.set(option.value, option.text);
        } else if (isNotAny) {
            // Otherwise, check if the option is a child of any selected category.
            for (const selected of selectedCategories) {
                const selectedOption = categorySelect.querySelector(`option[value="${selected}"]`);
                if (option.text.startsWith(`${selectedOption.text} / `)) {
                    excludeOptions.set(option.value, option.text);
                    break;
                }
            }
        }
    });
    if (excludeOptions.size) {
        // Update the exclude categories select element.
        excludeSelect.innerHTML = '';
        Array.from(excludeOptions)
            .sort(([, a], [, b]) => a.localeCompare(b))
            .forEach(([key, value]) => {
                const option = document.createElement("option");
                option.value = key;
                option.text = value;
                if (excludeSelected.has(key)) {
                    option.selected = true;
                }
                excludeSelect.appendChild(option);
            });

        // Adjust the height of the select elements.
        adjustHeight(excludeSelect);
        // Show the exclude categories container if it was hidden.
        if (excludeCategoriesContainer.classList.contains('d-none')) {
            excludeCategoriesContainer.classList.remove('d-none');
        }
    } else {
        // Hide the exclude categories container when no child categories exist.
        if (!excludeCategoriesContainer.classList.contains('d-none')) {
            excludeCategoriesContainer.classList.add('d-none');
        }
        // Clear selections to prevent submitting excluded categories when container is hidden.
        excludeSelect.innerHTML = '';
    }

};
