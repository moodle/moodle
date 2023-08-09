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
 * JavaScript for managing multiple grade items for a quiz.
 *
 * @module     mod_quiz/edit_multiple_grades
 * @copyright  2023 THe Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import {addIconToContainerRemoveOnCompletion} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';

/**
 * Call the Ajax service to delete a quiz grade item.
 *
 * @param {Number} quizId
 * @param {Number} gradeItemId
 * @return {Promise}
 */
const deleteGradeItem = (quizId, gradeItemId) => fetchMany([{
    methodname: 'mod_quiz_delete_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{id: gradeItemId}],
    }
}])[0];

/**
 * Handle click events on the delete icon.
 *
 * @param {Event} e click event.
 */
const handleGradeItemDelete = (e) => {
    e.preventDefault();
    const pending = new Pending('delete-quiz-grade-item');

    const tableCell = e.target.closest('td');
    addIconToContainerRemoveOnCompletion(tableCell, pending);

    const tableRow = tableCell.closest('tr');
    const quizId = tableRow.closest('table').dataset.quizId;
    const gradeItemId = tableRow.dataset.quizGradeItemId;

    deleteGradeItem(quizId, gradeItemId)
        .then(() => pending.resolve())
        .then(() => {
            window.location.reload();
        })
        .catch(Notification.exception);
};

/**
 * Handle clicks in the table the shows the grade items.
 *
 * @param {Event} e click event.
 */
const handleGradeItemClick = (e) => {
    const link = e.target.closest('a');

    if (!link) {
        return;
    }

    if (link.dataset.actionDelete) {
        handleGradeItemDelete(e);
    }
};

/**
 * Replace the container with a new version.
 */
const registerEventListeners = () => {
    const gradeItemTable = document.getElementById('mod_quiz-grade-item-list');
    if (!gradeItemTable) {
        return;
    }

    gradeItemTable.addEventListener('click', handleGradeItemClick);
};

/**
 * Entry point.
 */
export const init = () => {
    registerEventListeners();
};
