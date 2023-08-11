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
import MoodleConfig from 'core/config';
import {addIconToContainerRemoveOnCompletion} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {get_string as getString} from 'core/str';

/**
 * Call the Ajax service to create a quiz grade item.
 *
 * @param {Number} quizId
 * @param {String} name
 * @return {Promise}
 */
const createGradeItem = (quizId, name) => fetchMany([{
    methodname: 'mod_quiz_create_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{name: name}],
    }
}])[0];

/**
 * Call the Ajax service to update a quiz grade item.
 *
 * @param {Number} quizId
 * @param {Number} gradeItemId
 * @param {String} newName
 * @return {Promise}
 */
const updateGradeItem = (quizId, gradeItemId, newName) => fetchMany([{
    methodname: 'mod_quiz_update_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{id: gradeItemId, name: newName}],
    }
}])[0];

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
        .then(() => window.location.reload())
        .catch(Notification.exception);
};

/**
 *
 * @param {HTMLElement} editableSpan the editable to turn off.
 */
const stopEditingGadeItem = (editableSpan) => {
    editableSpan.innerHTML = editableSpan.dataset.oldContent;
    delete editableSpan.dataset.oldContent;

    editableSpan.classList.remove('inplaceeditingon');
    editableSpan.querySelector('[data-action-edit]').focus();
};

/**
 * Handle click events on the start rename icon.
 *
 * @param {Event} e click event.
 */
const handleGradeItemEditStart = (e) => {
    e.preventDefault();
    const pending = new Pending('edit-quiz-grade-item-start');
    const editableSpan = e.target.closest('span.inplaceeditable');

    document.querySelectorAll('span.inplaceeditable.inplaceeditingon').forEach(stopEditingGadeItem);

    editableSpan.dataset.oldContent = editableSpan.innerHTML;
    getString('edittitleinstructions')
        .then((instructions) => {
            const uniqueId = 'gi-edit-input-' + editableSpan.closest('tr').dataset.quizGradeItemId;
            editableSpan.innerHTML = '<span class="editinstructions">' + instructions + '</span>' +
                    '<label class="sr-only" for="' + uniqueId + '">' + editableSpan.dataset.editLabel + '</label>' +
                    '<input type="text" id="' + uniqueId + '" value="' + editableSpan.dataset.rawName +
                            '" class="ignoredirty form-control">';

            const inputElement = editableSpan.querySelector('input');
            inputElement.focus();
            inputElement.select();
            editableSpan.classList.add('inplaceeditingon');
            pending.resolve();
            return null;
        })
        .catch(Notification.exception);
};

/**
 * Handle key down in the editable.
 *
 * @param {Event} e key event.
 */
const handleGradeItemKeyDown = (e) => {
    if (e.keyCode !== 13) {
        return;
    }

    const editableSpan = e.target.closest('span.inplaceeditable.inplaceeditingon');
    if (!editableSpan) {
        return;
    }

    e.preventDefault();
    const pending = new Pending('edit-quiz-grade-item-save');

    const newName = editableSpan.querySelector('input').value;
    const tableCell = e.target.closest('th');
    addIconToContainerRemoveOnCompletion(tableCell, pending);

    const tableRow = tableCell.closest('tr');
    const quizId = tableRow.closest('table').dataset.quizId;
    const gradeItemId = tableRow.dataset.quizGradeItemId;

    updateGradeItem(quizId, gradeItemId, newName)
        .then(() => pending.resolve())
        .then(() => window.location.reload())
        .catch(Notification.exception);
};

/**
 * Handle key up in the editable.
 *
 * @param {Event} e key event.
 */
const handleGradeItemKeyUp = (e) => {
    if (e.keyCode !== 27) {
        return;
    }

    const editableSpan = e.target.closest('span.inplaceeditable.inplaceeditingon');
    if (!editableSpan) {
        return;
    }

    e.preventDefault();
    stopEditingGadeItem(editableSpan);
};

/**
 * Handle focus out of the editable.
 *
 * @param {Event} e event.
 */
const handleGradeItemFocusOut = (e) => {
    if (MoodleConfig.behatsiterunning) {
        // Behat triggers focusout too often so ignore.
        return;
    }

    const editableSpan = e.target.closest('span.inplaceeditable.inplaceeditingon');
    if (!editableSpan) {
        return;
    }

    e.preventDefault();
    stopEditingGadeItem(editableSpan);
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

    if (link.dataset.actionEdit) {
        handleGradeItemEditStart(e);
    }
};

/**
 * Handle clicks on the 'Add grade item' table.
 *
 * @param {Event} e click event.
 */
const handleAddGradeItemClick = (e) => {
    e.preventDefault();
    const pending = new Pending('create-quiz-grade-item');
    addIconToContainerRemoveOnCompletion(e.target.parentNode, pending);

    const quizId = e.target.dataset.quizId;

    getString('gradeitemdefaultname', 'quiz')
        .then((name) => createGradeItem(quizId, name))
        .then(() => pending.resolve())
        .then(() => window.location.reload())
        .catch(Notification.exception);
};

/**
 * Replace the container with a new version.
 */
const registerEventListeners = () => {
    const gradeItemTable = document.getElementById('mod_quiz-grade-item-list');
    if (gradeItemTable) {
        gradeItemTable.addEventListener('click', handleGradeItemClick);
        gradeItemTable.addEventListener('keydown', handleGradeItemKeyDown);
        gradeItemTable.addEventListener('keyup', handleGradeItemKeyUp);
        gradeItemTable.addEventListener('focusout', handleGradeItemFocusOut);
    }

    document.getElementById('mod_quiz-add_grade_item').addEventListener('click', handleAddGradeItemClick);
};

/**
 * Entry point.
 */
export const init = () => {
    registerEventListeners();
};
