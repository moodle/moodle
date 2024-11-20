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
 * Message users.
 *
 * @module     report_insights/message_users
 * @copyright  2019 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/log', 'core/modal_save_cancel', 'core/modal_events', 'core/templates',
    'core/notification', 'core/ajax'],
        function($, Str, Log, ModalSaveCancel, ModalEvents, Templates, Notification, Ajax) {

    var SELECTORS = {
        BULKACTIONSELECT: "#formactionid"
    };

    /**
     * Constructor.
     *
     * @param {String} rootNode
     * @param {String} actionName
     */
    var MessageUsers = function(rootNode, actionName) {
        this.actionName = actionName;
        this.attachEventListeners(rootNode);
    };

    /**
     * @var {String} actionName
     * @private
     */
    MessageUsers.prototype.actionName = null;

    /**
     * @var {Modal} modal
     * @private
     */
    MessageUsers.prototype.modal = null;

    /**
     * Attach the event listener to the send message bulk action.
     * @param {String} rootNode
     */
    MessageUsers.prototype.attachEventListeners = function(rootNode) {
        $(rootNode + ' button[data-bulk-sendmessage]').on('click', function(e) {
            e.preventDefault();
            var cTarget = $(e.currentTarget);

            // Using an associative array in case there is more than 1 prediction for the same user.
            var users = {};
            var predictionToUserMapping = cTarget.data('prediction-to-user-id');

            var checkedSelector = '.insights-list input[data-togglegroup^="insight-bulk-action"][data-toggle="slave"]:checked';
            $(checkedSelector).each(function(index, value) {
                var predictionId = $(value).closest('tr[data-prediction-id]').data('prediction-id');

                if (typeof predictionToUserMapping[predictionId] === 'undefined') {
                    Log.error('Unknown user for prediction ' + predictionId);
                    return;
                }

                var userId = predictionToUserMapping[predictionId];
                users[predictionId] = userId;

            });

            if (Object.keys(users).length === 0) {
                return this;
            }

            this.showSendMessage(users);

            return this;
        }.bind(this));
    };

    /**
     * Show the send message popup.
     *
     * @method showSendMessage
     * @private
     * @param {Object} users Prediction id to user id mapping.
     * @returns {Promise}
     */
    MessageUsers.prototype.showSendMessage = function(users) {

        var userIds = new Set(Object.values(users));

        if (userIds.length == 0) {
            // Nothing to do.
            return $.Deferred().resolve().promise();
        }
        var titlePromise = null;
        if (userIds.size == 1) {
            titlePromise = Str.get_string('sendbulkmessagesingle', 'core_message');
        } else {
            titlePromise = Str.get_string('sendbulkmessage', 'core_message', userIds.size);
        }

        // eslint-disable-next-line promise/catch-or-return
        ModalSaveCancel.create({
            body: Templates.render('core_user/send_bulk_message', {}),
            title: titlePromise,
            buttons: {
                save: titlePromise,
            },
            show: true,
        })
        .then(function(modal) {
            // Keep a reference to the modal.
            this.modal = modal;

            // We want to focus on the action select when the dialog is closed.
            this.modal.getRoot().on(ModalEvents.hidden, function() {
                $(SELECTORS.BULKACTIONSELECT).focus();
                this.modal.getRoot().remove();
            }.bind(this));

            this.modal.getRoot().on(ModalEvents.save, this.submitSendMessage.bind(this, users));

            return this.modal;
        }.bind(this));
    };

    /**
     * Send a message to these users.
     *
     * @method submitSendMessage
     * @private
     * @param {Object} users Prediction id to user id mapping.
     * @returns {Promise}
     */
    MessageUsers.prototype.submitSendMessage = function(users) {

        var messageText = this.modal.getRoot().find('form textarea').val();

        var messages = [];

        var userIds = new Set(Object.values(users));
        userIds.forEach(function(userId) {
            messages.push({touserid: userId, text: messageText});
        });

        var actionName = this.actionName;
        var message = null;
        return Ajax.call([{
            methodname: 'core_message_send_instant_messages',
            args: {messages: messages}
        }])[0].then(function(messageIds) {
            if (messageIds.length == 1) {
                return Str.get_string('sendbulkmessagesentsingle', 'core_message');
            } else {
                return Str.get_string('sendbulkmessagesent', 'core_message', messageIds.length);
            }
        }).then(function(msg) {

            // Save this for the following callback. Now that we got everything
            // done we can flag this action as executed.
            message = msg;

            return Ajax.call([{
                methodname: 'report_insights_action_executed',
                args: {
                    actionname: actionName,
                    predictionids: Object.keys(users)
                }
            }])[0];
        }).then(function() {
            Notification.addNotification({
                message: message,
                type: "success"
            });
            return true;
        }).catch(Notification.exception);
    };

    return /** @alias module:report_insights/message_users */ {
        // Public variables and functions.

        /**
         * @method init
         * @param {String} rootNode
         * @param {String} actionName
         * @returns {MessageUsers}
         */
        'init': function(rootNode, actionName) {
            return new MessageUsers(rootNode, actionName);
        }
    };
});
