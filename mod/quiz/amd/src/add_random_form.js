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
 * JavaScript for the add_random_form class.
 *
 * @module    mod_quiz/add_random_form
 * @package   mod_quiz
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'mod_quiz/random_question_form_preview'
    ],
    function(
        $,
        RandomQuestionFormPreview
    ) {

    // Wait 2 seconds before reloading the question set just in case
    // the user is still changing the criteria.
    var RELOAD_DELAY = 2000;
    var SELECTORS = {
        PREVIEW_CONTAINER: '[data-region="random-question-preview-container"]',
        CATEGORY_FORM_ELEMENT: '[name="category"]',
        SUBCATEGORY_FORM_ELEMENT: '[name="includesubcategories"]',
        TAG_IDS_FORM_ELEMENT: '[name="fromtags[]"]'
    };

    /**
     * Get the selected category value from the form.
     *
     * @param {jquery} form The form element.
     * @return {string} The category value.
     */
    var getCategorySelectValue = function(form) {
        return form.find(SELECTORS.CATEGORY_FORM_ELEMENT).val();
    };

    /**
     * Get the category id from the form.
     *
     * @param {jquery} form The form element.
     * @return {string} The category id.
     */
    var getCategoryId = function(form) {
        // The value string is the category id and category context id joined
        // by a comma.
        var valueString = getCategorySelectValue(form);
        // Split the two ids.
        var values = valueString.split(',');
        // Return just the category id.
        return values[0];
    };

    /**
     * Check if a top level category is selected in the form.
     *
     * @param {jquery} form The form element.
     * @param {string[]} topCategories List of top category values (matching the select box values)
     * @return {bool}
     */
    var isTopLevelCategorySelected = function(form, topCategories) {
        var selectedValue = getCategorySelectValue(form);
        return (topCategories.indexOf(selectedValue) > -1);
    };

    /**
     * Check if the form indicates we should include include subcategories in
     * the filter.
     *
     * @param {jquery} form The form element.
     * @param {string[]} topCategories List of top category values (matching the select box values)
     * @return {bool}
     */
    var shouldIncludeSubcategories = function(form, topCategories) {
        if (isTopLevelCategorySelected(form, topCategories)) {
            return true;
        } else {
            return form.find(SELECTORS.SUBCATEGORY_FORM_ELEMENT).is(':checked');
        }
    };

    /**
     * Get the tag ids for the selected tags in the form.
     *
     * @param {jquery} form The form element.
     * @return {string[]} The tag ids.
     */
    var getTagIds = function(form) {
        var values = form.find(SELECTORS.TAG_IDS_FORM_ELEMENT).val();
        return values.map(function(value) {
            // The tag element value is the tag id and tag name joined
            // by a comma. So we need to split them to get the tag id.
            var parts = value.split(',');
            return parts[0];
        });
    };

    /**
     * Reload the preview section with a new set of filters.
     *
     * @param {jquery} form The form element.
     * @param {int} contextId The current context id.
     * @param {string[]} topCategories List of top category values (matching the select box values)
     */
    var reloadQuestionPreview = function(form, contextId, topCategories) {
        var previewContainer = form.find(SELECTORS.PREVIEW_CONTAINER);
        RandomQuestionFormPreview.reload(
            previewContainer,
            getCategoryId(form),
            shouldIncludeSubcategories(form, topCategories),
            getTagIds(form),
            contextId
        );
    };

    /**
     * Is this an element we're interested in listening to changes on.
     *
     * @param {jquery} element The element to check.
     * @return {bool}
     */
    var isInterestingElement = function(element) {
        if (element.closest(SELECTORS.CATEGORY_FORM_ELEMENT).length > 0) {
            return true;
        }

        if (element.closest(SELECTORS.SUBCATEGORY_FORM_ELEMENT).length > 0) {
            return true;
        }

        if (element.closest(SELECTORS.TAG_IDS_FORM_ELEMENT).length > 0) {
            return true;
        }

        return false;
    };

    /**
     * Listen for changes to any of the interesting elements and reload the form
     * preview with the new filter values if they are changed.
     *
     * The reload is delayed for a small amount of time (see RELOAD_DELAY) in case
     * the user is actively editing the form. This allows us to avoid having to
     * send multiple requests to the server on each change.
     *
     * Instead we can just send a single request when the user appears to have
     * finished editing the form.
     *
     * @param {jquery} form The form element.
     * @param {int} contextId The current context id.
     * @param {string[]} topCategories List of top category values (matching the select box values)
     */
    var addEventListeners = function(form, contextId, topCategories) {
        var reloadTimerId = null;

        form.on('change', function(e) {
            // Only reload the preview when elements that will change the result
            // are modified.
            if (!isInterestingElement($(e.target))) {
                return;
            }

            // Show the loading icon to let the user know that the preview
            // will be updated after their actions.
            RandomQuestionFormPreview.showLoadingIcon(form);

            if (reloadTimerId) {
                // Reset the timer each time the form is modified.
                clearTimeout(reloadTimerId);
            }

            // Don't immediately reload the question preview section just
            // in case the user is still modifying the form. We don't want to
            // spam reload requests.
            reloadTimerId = setTimeout(function() {
                reloadQuestionPreview(form, contextId, topCategories);
            }, RELOAD_DELAY);
        });
    };

    /**
     * Trigger the first load of the preview section and then listen for modifications
     * to the form to reload the preview with new filter values.
     *
     * @param {jquery} formId The form element id.
     * @param {int} contextId The current context id.
     * @param {string[]} topCategories List of top category values (matching the select box values)
     * @param {bool} isTagsEnabled Whether tags feature is enabled or not.
     */
    var init = function(formId, contextId, topCategories, isTagsEnabled) {
         if (isTagsEnabled == true) {
             var form = $('#' + formId);
             reloadQuestionPreview(form, contextId, topCategories, isTagsEnabled);
             addEventListeners(form, contextId, topCategories, isTagsEnabled);
         }
    };

    return {
        init: init
    };
});
