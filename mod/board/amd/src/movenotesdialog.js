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

import ModalCancel from 'core/modal_cancel';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import $ from "jquery";
import * as Str from 'core/str';

/**
 * Generate a list of destinations that the note can be moved to.
 *
 * @param {DomNode} moveNoteControl
 * @returns {DomNode} list of destinations
 */
const getDestinationsList = (moveNoteControl) => {
    // Fetch the editable columns.
    const editableColumns = document.querySelectorAll('.board_column[data-locked="false"]');
    const wrapper = document.createElement('div');

    // Get the data from the note being moved.
    const movingNote = moveNoteControl.closest('.board_note');
    const movingNoteId = movingNote.dataset.ident;
    const movingNoteColumn = movingNote.dataset.column;

    // Process each column.
    editableColumns.forEach(column => {
        const list = document.createElement('ul');
        list.classList.add('move-dialog-destinations', 'list-unstyled');
        const columnName = column.querySelector('.mod_board_column_name').innerText;
        Str.get_string('move_to_firstitemcolumn', 'mod_board', columnName).then((stringColumn) => {
            // Create the first item in the list, the column name.
            const li = document.createElement('li');
            li.classList.add('move-dialog-destination');
            const link = moveLink(column.dataset.ident, movingNoteId, 0, movingNoteColumn, 0, stringColumn);
            li.appendChild(link);
            list.appendChild(li);

            // Create the list of items in the column.
            const destinations = column.querySelectorAll('.move_note');
            let count = 0;
            destinations.forEach(destination => {
                count++;
                const li = document.createElement('li');
                li.classList.add('move-dialog-destination', 'ml-2');

                // Get the name of the post from the aria-label.
                const targetNote = $(destination.closest('.board_note'));
                let noteTitle = targetNote.find(".mod_board_note_heading").html();
                if (noteTitle === "") {
                    noteTitle = count;
                }
                const sortOrder = targetNote.data('sortorder');
                const noteId = targetNote.data('ident');
                if (noteId == movingNoteId) {
                    return;
                }
                Str.get_string('move_to_afterpost', 'mod_board', noteTitle).then((stringPost) => {
                    const link = moveLink(column.dataset.ident, movingNoteId, noteId, movingNoteColumn, sortOrder + 1, stringPost);
                    li.appendChild(link);
                    list.appendChild(li);
                    return '';
                }).catch(Notification.exception);
            });
            wrapper.appendChild(list);
            return '';
        }).catch(Notification.exception);
    });
    return wrapper;
};

/**
 * Create a moving link.
 * @param {int} columnId The column id
 * @param {int} movingNoteId The note id of the note being moved
 * @param {int} targetNoteId The note id of the note being moved to
 * @param {int} movingNoteColumn The column id of the note being moved
 * @param {int} sortOrder The new sort order
 * @param {string} linkString The link string
 * @returns {DomNode} link
 */
const moveLink = (columnId, movingNoteId, targetNoteId, movingNoteColumn, sortOrder, linkString) => {
    const link = document.createElement('a');
    link.setAttribute('href', '#');
    link.dataset.columnid = columnId;
    link.dataset.movingnoteid = movingNoteId;
    link.dataset.targetnoteid = targetNoteId;
    link.dataset.movingnotecolumn = movingNoteColumn;
    link.dataset.sortorder = sortOrder;
    link.innerText = linkString;
    return link;
};

/**
 * Display the Move Modal.
 *
 * @param {DomeNode} moveNoteControl The move note control
 */
const displayMoveModal = (moveNoteControl) => {
    const modalTitle = moveNoteControl.getAttribute('aria-label');
    ModalCancel.create({
        title: modalTitle,
        body: getDestinationsList(moveNoteControl)
    }).then((modal) => {

        // Handle hidden event.
        modal.getRoot().on(ModalEvents.hidden, () => {
            // Destroy when hidden.
            modal.destroy();
        });

        // Listen for the closemovedialog event triggerd by the links in the modal.
        document.addEventListener('closemovedialog', () => {
            modal.destroy();
        });

        modal.show();

        return modal;
    }).catch(Notification.exception);
};

/**
 * Initialise the move dialog.
 * @param {Int} ownerId
 * @param {Function} moveNote Function from Board.js
 */
const init = (ownerId, moveNote) => {
    const body = document.querySelector('body');
    const initialized = body.classList.contains('move-dialog-initialized');
    if (!initialized) {
        document.addEventListener('click', e => {
            const moveNoteControl = e.target.closest('.mod_board_note_controls .move_note');
            if (moveNoteControl) {
                displayMoveModal(moveNoteControl);
            }
            const moveDestination = e.target.closest('.move-dialog-destination a');
            if (moveDestination) {
                e.preventDefault();
                let payload = {
                    id: parseInt(moveDestination.dataset.movingnoteid),
                    columnid: parseInt(moveDestination.dataset.columnid),
                    ownerid: ownerId,
                    sortorder: parseInt(moveDestination.dataset.sortorder)
                };

                const movingNoteColumn = parseInt(moveDestination.dataset.movingnotecolumn);
                // The note needs to be added to the column it is being moved to. The sortorder will be updated
                // by the board JS.
                const movingNote = document.querySelector(
                    `.board_note[data-ident="${moveDestination.dataset.movingnoteid}"]`);
                const toColumn = document.querySelector(
                        `.board_column[data-ident="${moveDestination.dataset.columnid}"] .board_column_content`);

                if (moveDestination.dataset.sortorder == 0) {
                    toColumn.prepend(movingNote);
                } else {
                    const targetNote = document.querySelector(
                        `.board_note[data-ident="${moveDestination.dataset.targetnoteid}"]`);
                    if (targetNote) {
                        toColumn.insertBefore(movingNote, targetNote.nextSibling);
                    } else {
                        toColumn.appendChild(movingNote);
                    }
                }

                // Call the move note function from Board.js.
                moveNote(movingNoteColumn, payload, false);

                // Close the modal.
                document.dispatchEvent(new Event('closemovedialog'));
            }
        });
        document.addEventListener('keypress', e => {
            const moveNoteControl = e.target.closest('.mod_board_note_controls .move_note');
            if (moveNoteControl) {
                displayMoveModal(moveNoteControl);
            }
        });
        body.classList.add('move-dialog-initialized');
    }
};

export default {
    init: init
};
