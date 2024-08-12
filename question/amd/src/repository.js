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
 * A javascript module to handle core_question ajax actions.
 *
 * @module     core_question/repository
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * @param {integer} newContextId target bank context id
 * @param {integer} newCategoryId target question category id
 * @param {string} questionIds questionIds comma separated list of question ids to move.
 * @param {string} returnUrl optional url to add/update the filter param with the new category id
 * @return {*}
 */
export const moveQuestions = (
    newContextId,
    newCategoryId,
    questionIds,
    returnUrl = '',
) => Ajax.call([{
    methodname: 'core_question_move_questions',
    args: {
        newcontextid: newContextId,
        newcategoryid: newCategoryId,
        questionids: questionIds,
        returnurl: returnUrl,
    },
}])[0];
