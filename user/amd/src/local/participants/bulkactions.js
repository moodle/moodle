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
 * Bulk actions for lists of participants.
 *
 * @module     core_user/local/participants/bulkactions
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from 'core_user/repository';
import * as Str from 'core/str';
import ModalEvents from 'core/modal_events';
import ModalFactory from 'core/modal_factory';
import Notification from 'core/notification';
import Templates from 'core/templates';
import {add as notifyUser} from 'core/toast';

/**
 * Show the add note popup
 *
 * @param {Number} courseid
 * @param {Number[]} users
 * @param {String[]} noteStateNames
 * @param {HTMLElement} stateHelpIcon
 * @return {Promise}
 */
export const showAddNote = (courseid, users, noteStateNames, stateHelpIcon) => {
    if (!users.length) {
        // No users were selected.
        return Promise.resolve();
    }

    const states = [];
    for (let key in noteStateNames) {
        switch (key) {
            case 'draft':
                states.push({value: 'personal', label: noteStateNames[key]});
                break;
            case 'public':
                states.push({value: 'course', label: noteStateNames[key], selected: 1});
                break;
            case 'site':
                states.push({value: key, label: noteStateNames[key]});
                break;
        }
    }

    const context = {
        stateNames: states,
        stateHelpIcon: stateHelpIcon.innerHTML,
    };

    let titlePromise = null;
    if (users.length === 1) {
        titlePromise = Str.get_string('addbulknotesingle', 'core_notes');
    } else {
        titlePromise = Str.get_string('addbulknote', 'core_notes', users.length);
    }

    return ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        body: Templates.render('core_user/add_bulk_note', context),
        title: titlePromise,
        buttons: {
            save: titlePromise,
        },
        removeOnClose: true,
    })
    .then(modal => {
        modal.getRoot().on(ModalEvents.save, () => submitAddNote(courseid, users, modal));

        modal.show();

        return modal;
    });
};

/**
 * Add a note to this list of users.
 *
 * @param {Number} courseid
 * @param {Number[]} users
 * @param {Modal} modal
 * @return {Promise}
 */
const submitAddNote = (courseid, users, modal) => {
    const text = modal.getRoot().find('form textarea').val();
    const publishstate = modal.getRoot().find('form select').val();

    const notes = users.map(userid => {
        return {
            userid,
            text,
            courseid,
            publishstate,
        };
    });

    return Repository.createNotesForUsers(notes)
    .then(noteIds => {
        if (noteIds.length === 1) {
            return Str.get_string('addbulknotedonesingle', 'core_notes');
        } else {
            return Str.get_string('addbulknotedone', 'core_notes', noteIds.length);
        }
    })
    .then(msg => notifyUser(msg))
    .catch(Notification.exception);
};

/**
 * Show the send message popup.
 *
 * @param {Number[]} users
 * @return {Promise}
 */
export const showSendMessage = users => {
    if (!users.length) {
        // Nothing to do.
        return Promise.resolve();
    }

    let titlePromise;
    if (users.length === 1) {
        titlePromise = Str.get_string('sendbulkmessagesingle', 'core_message');
    } else {
        titlePromise = Str.get_string('sendbulkmessage', 'core_message', users.length);
    }

    return ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        body: Templates.render('core_user/send_bulk_message', {}),
        title: titlePromise,
        buttons: {
            save: titlePromise,
        },
        removeOnClose: true,
    })
    .then(modal => {
        modal.getRoot().on(ModalEvents.save, (e) => {
            const text = modal.getRoot().find('form textarea').val();
            if (text.trim() === '') {
                modal.getRoot().find('[data-role="messagetextrequired"]').removeAttr('hidden');
                e.preventDefault();
                return;
            }

            submitSendMessage(modal, users, text);
        });

        modal.show();

        return modal;
    });
};

/**
 * Send a message to these users.
 *
 * @param {Modal} modal
 * @param {Number[]} users
 * @param {String} text
 * @return {Promise}
 */
const submitSendMessage = (modal, users, text) => {
    const messages = users.map(touserid => {
        return {
            touserid,
            text,
        };
    });

    return Repository.sendMessagesToUsers(messages)
    .then(messageIds => {
        if (messageIds.length == 1) {
            return Str.get_string('sendbulkmessagesentsingle', 'core_message');
        } else {
            return Str.get_string('sendbulkmessagesent', 'core_message', messageIds.length);
        }
    })
    .then(msg => notifyUser(msg))
    .catch(Notification.exception);
};
