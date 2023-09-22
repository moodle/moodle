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
 * This module provides functionality for managing weight calculations and adjustments for grade items.
 *
 * @module     core_grades/edittree_weight
 * @copyright  2023 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {prefetchStrings} from 'core/prefetch';

/**
 * Selectors.
 *
 * @type {Object}
 */
const selectors = {
    weightOverrideCheckbox: 'input[type="checkbox"][name^="weightoverride_"]',
    weightOverrideInput: 'input[type="text"][name^="weight_"]',
    aggregationForCategory: category => `[data-aggregationforcategory='${category}']`,
    childrenByCategory: category => `tr[data-parent-category="${category}"]`,
    categoryByIdentifier: identifier => `tr.category[data-category="${identifier}"]`,
};

/**
 * An object representing grading-related constants.
 * The same as what's defined in lib/grade/constants.php.
 *
 * @type {Object}
 * @property {Object} aggregation Aggregation settings.
 * @property {number} aggregation.sum Aggregation method: sum.
 * @property {Object} type Grade type settings.
 * @property {number} type.none Grade type: none.
 * @property {number} type.value Grade type: value.
 * @property {number} type.scale Grade type: scale.
 */
const grade = {
    aggregation: {
        sum: 13,
    },
};

/**
 * The character used as the decimal separator for number formatting.
 *
 * @type {string}
 */
let decimalSeparator;

/**
 * This setting indicates if we should use algorithm prior to MDL-49257 fix for calculating extra credit weights.
 * Even though the old algorithm has bugs in it, we need to preserve existing grades.
 *
 * @type {boolean}
 */
let oldExtraCreditCalculation;

/**
 * Recalculates the natural weights for grade items within a given category.
 *
 * @param {HTMLElement} categoryElement The DOM element representing the category.
 */
// Suppress 'complexity' linting rule to keep this function as close to grade_category::auto_update_weights.
// eslint-disable-next-line complexity
const recalculateNaturalWeights = (categoryElement) => {
    const childElements = document.querySelectorAll(selectors.childrenByCategory(categoryElement.dataset.category));

    // Calculate the sum of the grademax's of all the items within this category.
    let totalGradeMax = 0;

    // Out of 100, how much weight has been manually overridden by a user?
    let totalOverriddenWeight = 0;
    let totalOverriddenGradeMax = 0;

    // Has every assessment in this category been overridden?
    let automaticGradeItemsPresent = false;
    // Does the grade item require normalising?
    let requiresNormalising = false;

    // Is there an error in the weight calculations?
    let erroneous = false;

    // This array keeps track of the id and weight of every grade item that has been overridden.
    const overrideArray = {};

    for (const childElement of childElements) {
        const weightInput = childElement.querySelector(selectors.weightOverrideInput);
        const weightCheckbox = childElement.querySelector(selectors.weightOverrideCheckbox);

        // There are cases where a grade item should be excluded from calculations:
        // - If the item's grade type is 'text' or 'none'.
        // - If the grade item is an outcome item and the settings are set to not aggregate outcome items.
        // - If the item's grade type is 'scale' and the settings are set to ignore scales in aggregations.
        // All these cases are already taken care of in the backend, and no 'weight' input element is rendered on the page
        // if a grade item should not have a weight.
        if (!weightInput) {
            continue;
        }

        const itemWeight = parseWeight(weightInput.value);
        const itemAggregationCoefficient = parseInt(childElement.dataset.aggregationcoef);
        const itemGradeMax = parseFloat(childElement.dataset.grademax);

        // Record the ID and the weight for this grade item.
        overrideArray[childElement.dataset.itemid] = {
            extraCredit: itemAggregationCoefficient,
            weight: itemWeight,
            weightOverride: weightCheckbox.checked,
        };
        // If this item has had its weight overridden then set the flag to true, but
        // only if all previous items were also overridden. Note that extra credit items
        // are counted as overridden grade items.
        if (!weightCheckbox.checked && itemAggregationCoefficient === 0) {
            automaticGradeItemsPresent = true;
        }

        if (itemAggregationCoefficient > 0) {
            // An extra credit grade item doesn't contribute to totalOverriddenGradeMax.
            continue;
        } else if (weightCheckbox.checked && itemWeight <= 0) {
            // An overridden item that defines a weight of 0 does not contribute to totalOverriddenGradeMax.
            continue;
        }

        totalGradeMax += itemGradeMax;
        if (weightCheckbox.checked) {
            totalOverriddenWeight += itemWeight;
            totalOverriddenGradeMax += itemGradeMax;
        }
    }

    // Initialise this variable (used to keep track of the weight override total).
    let normaliseTotal = 0;
    // Keep a record of how much the override total is to see if it is above 100. If it is then we need to set the
    // other weights to zero and normalise the others.
    let overriddenTotal = 0;
    // Total up all the weights.
    for (const gradeItemDetail of Object.values(overrideArray)) {
        // Exclude grade items with extra credit or negative weights (which will be set to zero later).
        if (!gradeItemDetail.extraCredit && gradeItemDetail.weight > 0) {
            normaliseTotal += gradeItemDetail.weight;
        }
        // The overridden total includes items that are marked as overridden, not extra credit, and have a positive weight.
        if (gradeItemDetail.weightOverride && !gradeItemDetail.extraCredit && gradeItemDetail.weight > 0) {
            // Add overridden weights up to see if they are greater than 1.
            overriddenTotal += gradeItemDetail.weight;
        }
    }
    if (overriddenTotal > 100) {
        // Make sure that this category of weights gets normalised.
        requiresNormalising = true;
        // The normalised weights are only the overridden weights, so we just use the total of those.
        normaliseTotal = overriddenTotal;
    }

    const totalNonOverriddenGradeMax = totalGradeMax - totalOverriddenGradeMax;

    for (const childElement of childElements) {
        const weightInput = childElement.querySelector(selectors.weightOverrideInput);
        const weightCheckbox = childElement.querySelector(selectors.weightOverrideCheckbox);
        const itemAggregationCoefficient = parseInt(childElement.dataset.aggregationcoef);
        const itemGradeMax = parseFloat(childElement.dataset.grademax);

        if (!weightInput) {
            continue;
        } else if (!oldExtraCreditCalculation && itemAggregationCoefficient > 0 && weightCheckbox.checked) {
            // For an item with extra credit ignore other weights and overrides but do not change anything at all
            // if its weight was already overridden.
            continue;
        }

        // Remove any error messages and classes.
        weightInput.classList.remove('is-invalid');
        const errorArea = weightInput.closest('td').querySelector('.invalid-feedback');
        errorArea.textContent = '';

        if (!oldExtraCreditCalculation && itemAggregationCoefficient > 0 && !weightCheckbox.checked) {
            // For an item with extra credit ignore other weights and overrides.
            weightInput.value = totalGradeMax ? formatFloat(itemGradeMax * 100 / totalGradeMax) : 0;
        } else if (!weightCheckbox.checked) {
            // Calculations with a grade maximum of zero will cause problems. Just set the weight to zero.
            if (totalOverriddenWeight >= 100 || totalNonOverriddenGradeMax === 0 || itemGradeMax === 0) {
                // There is no more weight to distribute.
                weightInput.value = formatFloat(0);
            } else {
                // Calculate this item's weight as a percentage of the non-overridden total grade maxes
                // then convert it to a proportion of the available non-overridden weight.
                weightInput.value = formatFloat((itemGradeMax / totalNonOverriddenGradeMax) * (100 - totalOverriddenWeight));
            }
        } else if ((!automaticGradeItemsPresent && normaliseTotal !== 100) || requiresNormalising ||
                overrideArray[childElement.dataset.itemid].weight < 0) {
            if (overrideArray[childElement.dataset.itemid].weight < 0) {
                weightInput.value = formatFloat(0);
            }

            // Zero is a special case. If the total is zero then we need to set the weight of the parent category to zero.
            if (normaliseTotal !== 0) {
                erroneous = true;
                const error = normaliseTotal > 100 ? 'erroroverweight' : 'errorunderweight';
                // eslint-disable-next-line promise/always-return,promise/catch-or-return
                getString(error, 'core_grades').then((errorString) => {
                    errorArea.textContent = errorString;
                });
                weightInput.classList.add('is-invalid');
            }
        }
    }

    if (!erroneous) {
        const categoryGradeMax = parseFloat(categoryElement.dataset.grademax);
        if (categoryGradeMax !== totalGradeMax) {
            // The category grade max is not the same as the total grade max, so we need to update the category grade max.
            categoryElement.dataset.grademax = totalGradeMax;
            const relatedCategoryAggregationRow = document.querySelector(
                selectors.aggregationForCategory(categoryElement.dataset.category)
            );
            relatedCategoryAggregationRow.querySelector('.column-range').innerHTML = formatFloat(totalGradeMax, 2, 2);

            const parentCategory = document.querySelector(selectors.categoryByIdentifier(categoryElement.dataset.parentCategory));
            if (parentCategory && (parseInt(parentCategory.dataset.aggregation) === grade.aggregation.sum)) {
                recalculateNaturalWeights(parentCategory);
            }
        }
    }
};

/**
 * Formats a floating-point number as a string with the specified number of decimal places.
 * Unnecessary trailing zeros are removed up to the specified minimum number of decimal places.
 *
 * @param {number} number The float value to be formatted.
 * @param {number} [decimalPoints=3] The number of decimal places to use.
 * @param {number} [minDecimals=1] The minimum number of decimal places to use.
 * @returns {string} The formatted weight value with the specified decimal places.
 */
const formatFloat = (number, decimalPoints = 3, minDecimals = 1) => {
    return number.toFixed(decimalPoints)
        .replace(new RegExp(`0{0,${decimalPoints - minDecimals}}$`), '')
        .replace('.', decimalSeparator);
};

/**
 * Parses a weight string and returns a normalized float value.
 *
 * @param {string} weightString The weight as a string, possibly with localized formatting.
 * @returns {number} The parsed weight as a float. If parsing fails, returns 0.
 */
const parseWeight = (weightString) => {
    const normalizedWeightString = weightString.replace(decimalSeparator, '.');
    return isNaN(Number(normalizedWeightString)) ? 0 : parseFloat(normalizedWeightString || 0);
};

/**
 * Initializes the weight management module with optional configuration.
 *
 * @param {string} decSep The character used as the decimal separator for number formatting.
 * @param {boolean} oldCalculation A flag indicating whether to use the old (pre MDL-49257) extra credit calculation.
 */
export const init = (decSep, oldCalculation) => {
    decimalSeparator = decSep;
    oldExtraCreditCalculation = oldCalculation;
    prefetchStrings('core_grades', ['erroroverweight', 'errorunderweight']);

    document.addEventListener('change', e => {
        // Update the weights of all grade items in the category when the weight of any grade item in the category is changed.
        if (e.target.matches(selectors.weightOverrideInput) || e.target.matches(selectors.weightOverrideCheckbox)) {
            // The following is named gradeItemRow, but it may also be a row that's representing a grade category.
            // It's ok because it serves as the categories associated grade item in our calculations.
            const gradeItemRow = e.target.closest('tr');
            const categoryElement = document.querySelector(selectors.categoryByIdentifier(gradeItemRow.dataset.parentCategory));

            // This is only required if we are using natural weights.
            if (parseInt(categoryElement.dataset.aggregation) === grade.aggregation.sum) {
                const weightElement = gradeItemRow.querySelector(selectors.weightOverrideInput);
                weightElement.value = formatFloat(Math.max(0, parseWeight(weightElement.value)));
                recalculateNaturalWeights(categoryElement);
            }
        }
    });

    document.addEventListener('submit', e => {
        // If the form is being submitted, then we need to ensure that the weight input fields are all set to
        // a valid value.
        if (e.target.matches('#gradetreeform')) {
            const firstInvalidWeightInput = e.target.querySelector('input.is-invalid');
            if (firstInvalidWeightInput) {
                const firstFocusableInvalidWeightInput = e.target.querySelector('input.is-invalid:enabled');
                if (firstFocusableInvalidWeightInput) {
                    firstFocusableInvalidWeightInput.focus();
                } else {
                    firstInvalidWeightInput.scrollIntoView({block: 'center'});
                }
                e.preventDefault();
            }
        }
    });
};
