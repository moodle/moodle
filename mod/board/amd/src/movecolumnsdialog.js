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
import * as Str from 'core/str';

/**
 * Generate a list of destinations that the note can be moved to.
 *
 * @param {DomNode} moveColumnControl
 * @returns {DomNode} list of destinations
 */
const getDestinationsList = (moveColumnControl) => {
    // Fetch the editable columns.
    const columns = document.querySelectorAll('.mod_board .board_column');
    const currentColumn = moveColumnControl.closest('.board_column');
    const columnsArray = Array.from(columns);
    let sortorder = columnsArray.indexOf(currentColumn);

    const wrapper = document.createElement('div');
    const list = document.createElement('ul');
    list.classList.add('move-column-dialog-destinations', 'list-unstyled');

    Str.get_string('move_column_to_firstplace', 'mod_board').then((moveToFirstString) => {
        if (sortorder !== 0) {
            const li = document.createElement('li');
            const link = moveLink(currentColumn.dataset.ident, 0, moveToFirstString);
            li.appendChild(link);
            list.appendChild(li);
        }
        columns.forEach(column => {
            const sortOrder = columnsArray.indexOf(column);
            if (currentColumn == column) {
                return;
            }
            const columnName = column.querySelector('.mod_board_column_name').innerText;
            Str.get_string('move_column_to_aftercolumn', 'mod_board', columnName).then((moveToColumnString) => {
                const li = document.createElement('li');
                const link = moveLink(currentColumn.dataset.ident, sortOrder, moveToColumnString);
                li.appendChild(link);
                list.appendChild(li);
                return '';
            }).catch(Notification.exception);
        });
        wrapper.appendChild(list);
        return '';
    }).catch(Notification.exception);
    wrapper.appendChild(list);
    return wrapper;
};

/**
 * Create a moving link.
 * @param {int} movingColumnId The column id of the column being moved
 * @param {int} sortOrder The new sort order
 * @param {string} linkString The link string
 * @returns {DomNode} link
 */
const moveLink = (movingColumnId, sortOrder, linkString) => {
    const link = document.createElement('a');
    link.setAttribute('href', '#');
    link.dataset.movingcolumnid = movingColumnId;
    link.dataset.sortorder = sortOrder;
    link.innerText = linkString;
    return link;
};

/**
 * Display the Move Modal.
 *
 * @param {DomeNode} moveColumnControl The move note control
 */
const displayMoveModal = (moveColumnControl) => {
    const modalTitle = moveColumnControl.getAttribute('aria-label');
    ModalCancel.create({
        title: modalTitle,
        body: getDestinationsList(moveColumnControl)
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
 *
 * @param {Function} moveColumn Function from Board.js
 */
const init = (moveColumn) => {
    const body = document.querySelector('body');
    const initialized = body.classList.contains('move-column-dialog-initialized');
    if (!initialized) {
        document.addEventListener('click', e => {
            const moveColumnControl = e.target.closest('.mod_column_move');
            if (moveColumnControl) {
                displayMoveModal(moveColumnControl);
            }
            const moveDestination = e.target.closest('.move-column-dialog-destinations a');
            if (moveDestination) {
                e.preventDefault();
                let payload = {
                    id: parseInt(moveDestination.dataset.movingcolumnid),
                    sortorder: parseInt(moveDestination.dataset.sortorder)
                };

                // Trigger the moveColumn function from Board.js.
                moveColumn(payload);

                // Close the modal.
                document.dispatchEvent(new Event('closemovedialog'));
            }
        });
        document.addEventListener('keypress', e => {
            const moveColumnControl = e.target.closest('.mod_column_move');
            if (moveColumnControl) {
                displayMoveModal(moveColumnControl);
            }
        });
        body.classList.add('move-column-dialog-initialized');
    }
};

export default {
    init: init
};
