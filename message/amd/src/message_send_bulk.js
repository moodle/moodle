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
 * Send bulk message to the given user ids.
 *
 * @module     core_message/message_send_bulk
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {get_string} from 'core/str';
import ModalFactory from 'core/modal_factory';
import Templates from 'core/templates';
import ModalEvents from 'core/modal_events';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Show the send message popup.
 *
 * @method showModal
 * @param {int[]} users
 * @param {Function} callback A callback to apply after the form is closed.
 * @returns {Promise}
 */
export const showModal = (users, callback = null) => {
    if (!users.length) {
        // Nothing to do.
        return Promise.resolve();
    }
    let titlePromise = null;
    if (users.length == 1) {
        titlePromise = get_string('sendbulkmessagesingle', 'core_message');
    } else {
        titlePromise = get_string('sendbulkmessage', 'core_message', users.length);
    }

    return ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        body: Templates.render('core_message/send_bulk_message', {}),
        title: titlePromise,
    })
    .then(function(modal) {
        modal.setSaveButtonText(titlePromise);

        // When the dialog is closed, perform the callback (if provided).
        modal.getRoot().on(ModalEvents.hidden, function() {
            if (callback) {
                callback();
            }
            modal.getRoot().remove();
        });

        modal.getRoot().on(ModalEvents.save, function() {
            let messageText = modal.getRoot().find('form textarea').val();
            sendMessage(messageText, users);
        });

        modal.show();

        return modal;
    });
};

/**
 * Send a message to these users.
 *
 * @method sendMessage
 * @param {String} messageText
 * @param {Number[]} users
 * @returns {Promise}
 */
export const sendMessage = (messageText, users) => {
    let messages = [];

    users.forEach(user => {
        messages.push({
            touserid: user,
            text: messageText
        });
    });

    return Ajax.call([{
        methodname: 'core_message_send_instant_messages',
        args: {messages: messages}
    }])[0]
    .then(function(messageIds) {
        if (messageIds.length == 1) {
            return get_string('sendbulkmessagesentsingle', 'core_message');
        } else {
            return get_string('sendbulkmessagesent', 'core_message', messageIds.length);
        }
    })
    .then(function(msg) {
        Notification.addNotification({
            message: msg,
            type: "success"
        });
        return true;
    })
    .catch(Notification.exception);
};
