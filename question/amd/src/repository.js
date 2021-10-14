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
 * A javascript module to handle question ajax actions.
 *
 * @module     core_question/repository
 * @copyright  2017 Simey Lameze <lameze@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function($, Ajax) {

    /**
     * Submit the form data for the question tags form.
     *
     * @method submitTagCreateUpdateForm
     * @param  {number} questionId
     * @param  {number} contextId
     * @param {string} formdata The URL encoded values from the form
     * @returns {promise}
     */
    var submitTagCreateUpdateForm = function(questionId, contextId, formdata) {
        var request = {
            methodname: 'core_question_submit_tags_form',
            args: {
                questionid: questionId,
                contextid: contextId,
                formdata: formdata
            }
        };

        return Ajax.call([request])[0];
    };

    return {
        submitTagCreateUpdateForm: submitTagCreateUpdateForm
    };
});
