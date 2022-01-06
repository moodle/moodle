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
 * Initialise the add random question modal on the quiz page.
 *
 * @module    mod_quiz/add_random_question
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'mod_quiz/add_question_modal_launcher',
        'mod_quiz/modal_add_random_question'
    ],
    function(
        AddQuestionModalLauncher,
        ModalAddRandomQuestion
    ) {

    return {
        /**
         * Create the add random question modal.
         *
         * @param  {int} contextId Current context id.
         * @param  {string} category Category id and category context id comma separated.
         * @param  {string} returnUrl URL to return to after form submission.
         * @param  {int} cmid Current course module id.
         */
        init: function(contextId, category, returnUrl, cmid) {
            AddQuestionModalLauncher.init(
                ModalAddRandomQuestion.TYPE,
                '.menu [data-action="addarandomquestion"]',
                contextId,
                // Additional values that should be set before the modal is shown.
                function(triggerElement, modal) {
                    modal.setCategory(category);
                    modal.setReturnUrl(returnUrl);
                    modal.setCMID(cmid);
                }
            );
        }
    };
});
