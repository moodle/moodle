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
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import MoodleConfig from 'core/config';
import {addIconToContainer} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {get_string as getString} from 'core/str';
import {render as renderTemplate} from 'core/templates';
import {replaceNode} from 'core/templates';

/**
 * @type {Object} selectors used in this code.
 */
const SELECTORS = {
    'addGradeItemButton': '#mod_quiz-add_grade_item',
    'editingPageContents': '#edit_grading_page-contents',
    'gradeItemList': 'table#mod_quiz-grade-item-list',
    'gradeItemSelect': 'select[data-slot-id]',
    'gradeItemSelectId': (id) => 'select#grade-item-choice-' + id,
    'updateGradeItemLink': (id) => 'tr[data-quiz-grade-item-id="' + id + '"] .quickeditlink',
    'inplaceEditable': 'span.inplaceeditable',
    'inplaceEditableOn': 'span.inplaceeditable.inplaceeditingon',
    'slotList': 'table#mod_quiz-slot-list',
};

/**
 * Call the Ajax service to create a quiz grade item.
 *
 * @param {Number} quizId id of the quiz to update.
 * @param {String} name name of the grade item to create.
 * @returns {Promise<Object>} a promise that resolves to the template context required to re-render the page.
 */
const createGradeItem = (
    quizId,
    name
) => callServiceAndReturnRenderingData({
    methodname: 'mod_quiz_create_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{name: name}],
    }
});

/**
 * Call the Ajax service to update a quiz grade item.
 *
 * @param {Number} quizId id of the quiz to update.
 * @param {Number} gradeItemId id of the grade item to update.
 * @param {String} newName the new name to set.
 * @return {Promise} Promise that resolves to the context required to re-render the page.
 */
const updateGradeItem = (
    quizId,
    gradeItemId,
    newName
) => callServiceAndReturnRenderingData({
    methodname: 'mod_quiz_update_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{id: gradeItemId, name: newName}],
    }
});

/**
 * Call the Ajax service to delete a quiz grade item.
 *
 * @param {Number} quizId id of the quiz to update.
 * @param {Number} gradeItemId id of the grade item to delete.
 * @return {Promise} Promise that resolves to the context required to re-render the page.
 */
const deleteGradeItem = (
    quizId,
    gradeItemId
) => callServiceAndReturnRenderingData({
    methodname: 'mod_quiz_delete_grade_items',
    args: {
        quizid: quizId,
        quizgradeitems: [{id: gradeItemId}],
    }
});

/**
 * Call the Ajax service to update the quiz grade item used by a slot.
 *
 * @param {Number} quizId id of the quiz to update.
 * @param {Number} slotId id of the slot to update.
 * @param {Number|null} gradeItemId new grade item ot set, or null to un-set.
 * @return {Promise} Promise that resolves to the context required to re-render the page.
 */
const updateSlotGradeItem = (
    quizId,
    slotId,
    gradeItemId
) => callServiceAndReturnRenderingData({
    methodname: 'mod_quiz_update_slots',
    args: {
        quizid: quizId,
        slots: [{id: slotId, quizgradeitemid: gradeItemId}],
    }
});

/**
 * Make a web service call, and also call mod_quiz_get_edit_grading_page_data to get the date to re-render the page.
 *
 * @param {Object} methodCall a web service call to pass to fetchMany. Must include methodCall.args.quizid.
 * @returns {Promise<Object>} a promise that resolves to the template context required to re-render the page.
 */
const callServiceAndReturnRenderingData = (methodCall) => {
    return Promise.all(fetchMany([
        methodCall,
        {
            methodname: 'mod_quiz_get_edit_grading_page_data',
            args: {
                quizid: methodCall.args.quizid,
            }
        },
    ]))
    .then(results => JSON.parse(results[1]));
};

/**
 * Handle click events on the delete icon.
 *
 * @param {Event} e click event.
 */
const handleGradeItemDelete = (e) => {
    e.preventDefault();
    const pending = new Pending('delete-quiz-grade-item');

    const tableCell = e.target.closest('td');
    addIconToContainer(tableCell, pending);

    const tableRow = tableCell.closest('tr');
    const quizId = tableRow.closest('table').dataset.quizId;
    const gradeItemId = tableRow.dataset.quizGradeItemId;

    let nextItemToFocus;
    if (tableRow.nextElementSibling) {
        nextItemToFocus = SELECTORS.updateGradeItemLink(tableRow.nextElementSibling.dataset.quizGradeItemId);
    } else {
        nextItemToFocus = SELECTORS.addGradeItemButton;
    }

    deleteGradeItem(quizId, gradeItemId)
        .then(reRenderPage)
        .then(() => {
            pending.resolve();
            document.querySelector(nextItemToFocus).focus();
        })
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
    const editableSpan = e.target.closest(SELECTORS.inplaceEditable);

    document.querySelectorAll(SELECTORS.inplaceEditableOn).forEach(stopEditingGadeItem);

    editableSpan.dataset.oldContent = editableSpan.innerHTML;
    getString('edittitleinstructions')
        .then((instructions) => {
            const uniqueId = 'gi-edit-input-' + editableSpan.closest('tr').dataset.quizGradeItemId;
            editableSpan.innerHTML = '<span class="editinstructions">' + instructions + '</span>' +
                    '<label class="sr-only" for="' + uniqueId + '">' + editableSpan.dataset.editLabel + '</label>' +
                    '<input type="text" id="' + uniqueId + '" value="' + editableSpan.dataset.rawName +
                            '" class="ignoredirty form-control w-100">';

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

    const editableSpan = e.target.closest(SELECTORS.inplaceEditableOn);

    // Check this click is on a relevant element.
    if (!editableSpan || !editableSpan.closest(SELECTORS.gradeItemList)) {
        return;
    }

    e.preventDefault();
    const pending = new Pending('edit-quiz-grade-item-save');

    const newName = editableSpan.querySelector('input').value;
    const tableCell = e.target.closest('th');
    addIconToContainer(tableCell);

    const tableRow = tableCell.closest('tr');
    const quizId = tableRow.closest('table').dataset.quizId;
    const gradeItemId = tableRow.dataset.quizGradeItemId;

    updateGradeItem(quizId, gradeItemId, newName)
        .then(reRenderPage)
        .then(() => {
            pending.resolve();
            document.querySelector(SELECTORS.updateGradeItemLink(gradeItemId)).focus({'focusVisible': true});
        })
        .catch(Notification.exception);
};

/**
 * Replace the contents of the page with the page re-rendered from the provided data, once that promise resolves.
 *
 * @param {Object} editGradingPageData the template context data required to re-render the page.
 * @returns {Promise<void>} a promise that will resolve when the page is updated.
 */
const reRenderPage = (editGradingPageData) => {
    return renderTemplate('mod_quiz/edit_grading_page', editGradingPageData)
        .then((html, js) => replaceNode(document.querySelector(SELECTORS.editingPageContents), html, js || ''));
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

    const editableSpan = e.target.closest(SELECTORS.inplaceEditableOn);

    // Check this click is on a relevant element.
    if (!editableSpan || !editableSpan.closest(SELECTORS.gradeItemList)) {
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

    const editableSpan = e.target.closest(SELECTORS.inplaceEditableOn);

    // Check this click is on a relevant element.
    if (!editableSpan || !editableSpan.closest(SELECTORS.gradeItemList)) {
        return;
    }

    e.preventDefault();
    stopEditingGadeItem(editableSpan);
};

/**
 * Handle when the selected grade item for a slot is changed.
 *
 * @param {Event} e event.
 */
const handleSlotGradeItemChanged = (e) => {
    const select = e.target.closest(SELECTORS.gradeItemSelect);

    // Check this click is on a relevant element.
    if (!select || !select.closest(SELECTORS.slotList)) {
        return;
    }

    e.preventDefault();
    const pending = new Pending('edit-slot-grade-item-updated');

    const slotId = select.dataset.slotId;
    const newGradeItemId = select.value ? select.value : null;
    const tableCell = e.target.closest('td');
    addIconToContainer(tableCell, pending);

    const quizId = tableCell.closest('table').dataset.quizId;

    updateSlotGradeItem(quizId, slotId, newGradeItemId)
        .then(reRenderPage)
        .then(() => {
            pending.resolve();
            document.querySelector(SELECTORS.gradeItemSelectId(slotId)).focus();
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

    // Check this click is on a relevant element.
    if (!link || !link.closest(SELECTORS.gradeItemList)) {
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
    // Check the click is on the element of interest.
    if (!e.target.closest(SELECTORS.addGradeItemButton)) {
        return;
    }

    e.preventDefault();
    const pending = new Pending('create-quiz-grade-item');
    addIconToContainer(e.target.parentNode, pending);

    const quizId = e.target.dataset.quizId;

    getString('gradeitemdefaultname', 'quiz')
        .then((name) => createGradeItem(quizId, name))
        .then(reRenderPage)
        .then(() => {
            pending.resolve();
            document.querySelector(SELECTORS.addGradeItemButton).focus();
        })
        .catch(Notification.exception);
};

/**
 * Replace the container with a new version.
 */
const registerEventListeners = () => {
    document.body.addEventListener('click', handleGradeItemClick);
    document.body.addEventListener('keydown', handleGradeItemKeyDown);
    document.body.addEventListener('keyup', handleGradeItemKeyUp);
    document.body.addEventListener('focusout', handleGradeItemFocusOut);

    document.body.addEventListener('click', handleAddGradeItemClick);

    document.body.addEventListener('change', handleSlotGradeItemChanged);
};

/**
 * Entry point.
 */
export const init = () => {
    registerEventListeners();
};
