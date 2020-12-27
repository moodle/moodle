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
 * JavaScript for the random_question_form_preview of the
 * add_random_form class.
 *
 * @module    mod_quiz/random_question_form_preview
 * @package   mod_quiz
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'core/ajax',
        'core/str',
        'core/notification',
        'core/templates',
        'core/paged_content_factory'
    ],
    function(
        $,
        Ajax,
        Str,
        Notification,
        Templates,
        PagedContentFactory
    ) {

    var ITEMS_PER_PAGE = 5;
    var TEMPLATE_NAME = 'mod_quiz/random_question_form_preview_question_list';
    var SELECTORS = {
        LOADING_ICON_CONTAINER: '[data-region="overlay-icon-container"]',
        QUESTION_COUNT_CONTAINER: '[data-region="question-count-container"]',
        QUESTION_LIST_CONTAINER: '[data-region="question-list-container"]'
    };

    /**
     * Show the loading spinner over the preview section.
     *
     * @param  {jquery} root The root element.
     */
    var showLoadingIcon = function(root) {
        root.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the loading spinner.
     *
     * @param  {jquery} root The root element.
     */
    var hideLoadingIcon = function(root) {
        root.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Render the section of text to show the question count.
     *
     * @param  {jquery} root The root element.
     * @param  {int} questionCount The number of questions.
     */
    var renderQuestionCount = function(root, questionCount) {
        Str.get_string('questionsmatchingfilter', 'mod_quiz', questionCount)
            .then(function(string) {
                root.find(SELECTORS.QUESTION_COUNT_CONTAINER).html(string);
                return;
            })
            .fail(Notification.exception);
    };

    /**
     * Send a request to the server for more questions.
     *
     * @param  {int} categoryId A question category id.
     * @param  {bool} includeSubcategories If the results should include subcategory questions
     * @param  {int[]} tagIds The list of tag ids that each question must have.
     * @param  {int} contextId The context where the questions will be added.
     * @param  {int} limit How many questions to retrieve.
     * @param  {int} offset How many questions to skip from the start of the result set.
     * @return {promise} Resolved when the preview section has rendered.
     */
    var requestQuestions = function(
        categoryId,
        includeSubcategories,
        tagIds,
        contextId,
        limit,
        offset
    ) {
        var request = {
            methodname: 'core_question_get_random_question_summaries',
            args: {
                categoryid: categoryId,
                includesubcategories: includeSubcategories,
                tagids: tagIds,
                contextid: contextId,
                limit: limit,
                offset: offset
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Build a paged content widget for questions with the given criteria. The
     * criteria is used to fetch more questions from the server as the user
     * requests new pages.
     *
     * @param  {int} categoryId A question category id.
     * @param  {bool} includeSubcategories If the results should include subcategory questions
     * @param  {int[]} tagIds The list of tag ids that each question must have.
     * @param  {int} contextId The context where the questions will be added.
     * @param  {int} totalQuestionCount How many questions match the criteria above.
     * @param  {object[]} firstPageQuestions List of questions for the first page.
     * @return {promise} A promise resolved with the HTML and JS for the paged content.
     */
    var renderQuestionsAsPagedContent = function(
        categoryId,
        includeSubcategories,
        tagIds,
        contextId,
        totalQuestionCount,
        firstPageQuestions
    ) {
        // Provide a callback, renderQuestionsPages,
        // to control how the questions on each page are rendered.
        return PagedContentFactory.createFromAjax(
            totalQuestionCount,
            ITEMS_PER_PAGE,
            // Callback function to render the requested pages.
            function(pagesData) {
                return pagesData.map(function(pageData) {
                    var limit = pageData.limit;
                    var offset = pageData.offset;

                    if (offset == 0) {
                        // The first page is being requested and we've already got
                        // that data so we can just render it immediately.
                        return Templates.render(TEMPLATE_NAME, {questions: firstPageQuestions});
                    } else {
                        // Otherwise we need to ask the server for the data.
                        return requestQuestions(
                            categoryId,
                            includeSubcategories,
                            tagIds,
                            contextId,
                            limit,
                            offset
                        )
                        .then(function(response) {
                            var questions = response.questions;
                            return Templates.render(TEMPLATE_NAME, {questions: questions});
                        })
                        .fail(Notification.exception);
                    }
                });
            }
        );
    };

    /**
     * Re-render the preview section based on the provided filter criteria.
     *
     * @param  {jquery} root The root element.
     * @param  {int} categoryId A question category id.
     * @param  {bool} includeSubcategories If the results should include subcategory questions
     * @param  {int[]} tagIds The list of tag ids that each question must have.
     * @param  {int} contextId The context where the questions will be added.
     * @return {promise} Resolved when the preview section has rendered.
     */
    var reload = function(root, categoryId, includeSubcategories, tagIds, contextId) {
        // Show the loading spinner to tell the user that something is happening.
        showLoadingIcon(root);
        // Load the first set of questions.
        return requestQuestions(categoryId, includeSubcategories, tagIds, contextId, ITEMS_PER_PAGE, 0)
            .then(function(response) {
                var totalCount = response.totalcount;
                // Show the help message for the user to indicate how many questions
                // match their filter criteria.
                renderQuestionCount(root, totalCount);
                return response;
            })
            .then(function(response) {
                var totalQuestionCount = response.totalcount;
                var questions = response.questions;

                if (questions.length) {
                    // We received some questions so render them as paged content
                    // with a paging bar.
                    return renderQuestionsAsPagedContent(
                        categoryId,
                        includeSubcategories,
                        tagIds,
                        contextId,
                        totalQuestionCount,
                        questions
                    );
                } else {
                    // If we didn't receive any questions then we can return empty
                    // HTML and JS to clear the preview section.
                    return $.Deferred().resolve('', '');
                }
            })
            .then(function(html, js) {
                // Show the user the question set.
                var container = root.find(SELECTORS.QUESTION_LIST_CONTAINER);
                Templates.replaceNodeContents(container, html, js);
                return;
            })
            .always(function() {
                hideLoadingIcon(root);
            })
            .fail(Notification.exception);
    };

    return {
        reload: reload,
        showLoadingIcon: showLoadingIcon,
        hideLoadingIcon: hideLoadingIcon
    };
});
