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
 * This module handles the message area of the messaging area.
 *
 * @module     core_message/message_area_messages
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'],
    function($, ajax, templates, notification) {

        /**
         * Messages class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Messages(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

        /** @type {Messagearea} The messaging area object. */
        Messages.prototype.messageArea = null;

        /**
         * Initialise the event listeners.
         *
         * @private
         */
        Messages.prototype._init = function() {
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONVERSATIONDELETED, this._handleConversationDeleted.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONVERSATIONSELECTED, this._loadMessages.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.SENDMESSAGE, this._loadMessages.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CHOOSEMESSAGESTODELETE, this._chooseMessagesToDelete.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.SENDMESSAGE, this._sendMessage.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.DELETEMESSAGES, this._deleteMessages.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.CANCELDELETEMESSAGES,
                this._cancelMessagesToDelete.bind(this));
        };

        /**
         * Loads messages for a specific user.
         *
         * @param {Event} event
         * @param {int} userid
         * @returns {Promise} The promise resolved when the messages have been loaded.
         * @private
         */
        Messages.prototype._loadMessages = function(event, userid) {
            // Show loading template.
            templates.render('core/loading', {}).done(function(html, js) {
                templates.replaceNodeContents(this.messageArea.SELECTORS.MESSAGESAREA, html, js);
            }.bind(this));

            // Call the web service to get our data.
            var promises = ajax.call([{
                methodname: 'core_message_data_for_messagearea_messages',
                args: {
                    currentuserid: this.messageArea.getCurrentUserId(),
                    otheruserid: userid
                }
            }]);

            // Do stuff when we get data back.
            return promises[0].then(function(data) {
                // We have the data - lets re-render the template with it.
                return templates.render('core_message/message_area_messages', data);
            }).then(function(html, js) {
                templates.replaceNodeContents(this.messageArea.SELECTORS.MESSAGESAREA, html, js);
            }.bind(this)).fail(notification.exception);
        };

        /**
         * Handles sending a message.
         *
         * @returns {Promise} The promise resolved once the message has been sent.
         * @private
         */
        Messages.prototype._sendMessage = function() {
            var text = this.messageArea.find(this.messageArea.SELECTORS.SENDMESSAGETEXT).val();
            // Call the web service to save our message.
            var promises = ajax.call([{
                methodname: 'core_message_send_instant_messages',
                args: {
                    messages: [
                        {
                            touserid: this._getUserId(),
                            text: text
                        }
                    ]
                }
            }]);

            // Update the DOM when we get some data back.
            return promises[0].then(function() {
                // Fire an event to say the message was sent.
                this.messageArea.trigger(this.messageArea.EVENTS.MESSAGESENT, [this._getUserId(), text]);
                // Update the messaging area.
                this._addMessageToDom();
            }.bind(this)).fail(notification.exception);
        };

        /**
         * Handles selecting messages to delete.
         *
         * @returns {Promise} The promise resolved when the messages to delete have been selected.
         * @private
         */
        Messages.prototype._chooseMessagesToDelete = function() {
            // Show the checkboxes.
            this.messageArea.find(this.messageArea.SELECTORS.DELETEMESSAGECHECKBOX).show();
            // Display the confirmation message.
            var responseSelector = this.messageArea.SELECTORS.MESSAGESAREA + " " +
                this.messageArea.SELECTORS.MESSAGERESPONSE;
            return templates.render('core_message/message_area_delete_confirmation', {}).then(function(html, js) {
                templates.replaceNodeContents(responseSelector, html, js);
            });
        };

        /**
         * Handles deleting messages.
         *
         * @private
         */
        Messages.prototype._deleteMessages = function() {
            var userid = this.messageArea.getCurrentUserId();
            var checkboxes = this.messageArea.find(this.messageArea.SELECTORS.DELETEMESSAGECHECKBOX + " input:checked");
            var requests = [];
            var messagestoremove = [];

            // Go through all the checked checkboxes and prepare them for deletion.
            checkboxes.each(function(id, element) {
                var node = $(element);
                var messageid = node.data('messageid');
                var isread = node.data('messageread') ? 1 : 0;
                var message = this.messageArea.find(this.messageArea.SELECTORS.MESSAGE +
                    "[data-id='" + messageid + '' + isread + "']");
                messagestoremove.push(message);
                requests.push({
                    methodname: 'core_message_delete_message',
                    args: {
                        messageid: messageid,
                        userid: userid,
                        read: isread
                    }
                });
            }.bind(this));

            if (requests.length > 0) {
                ajax.call(requests)[requests.length - 1].then(function() {
                    // Remove the messages from the DOM.
                    $.each(messagestoremove, function(key, message) {
                        // Remove the message.
                        message.remove();
                    });
                    // Now we have removed all the messages from the DOM lets remove any block times we may need to as well.
                    $.each(messagestoremove, function(key, message) {
                        // First - let's make sure there are no more messages in that time block.
                        var blocktime = message.data('blocktime');
                        if (this.messageArea.find(this.messageArea.SELECTORS.MESSAGE +
                            "[data-blocktime='" + blocktime + "']").length === 0) {
                            this.messageArea.find(this.messageArea.SELECTORS.BLOCKTIME +
                                "[data-blocktime='" + blocktime + "']").remove();
                        }
                    }.bind(this));

                    // Trigger event letting other modules know messages were deleted.
                    this.messageArea.trigger(this.messageArea.EVENTS.MESSAGESDELETED, this._getUserId());
                }.bind(this), notification.exception);
            } else {
                // Trigger event letting other modules know messages were deleted.
                this.messageArea.trigger(this.messageArea.EVENTS.MESSAGESDELETED, this._getUserId());
            }

            // Hide the items responsible for deleting messages.
            this._hideDeleteAction();


        };

        /**
         * Returns the ID of the other user in the conversation.
         *
         * @params {Event} event
         * @params {int} The user id
         * @private
         */
        Messages.prototype._handleConversationDeleted = function(event, userid) {
            if (userid == this._getUserId()) {
                // Clear the current panel.
                this.messageArea.find(this.messageArea.SELECTORS.MESSAGESAREA).empty();
            }
        };

        /**
         * Handles hiding the delete checkboxes and replacing the response area.
         *
         * @return {Promise} JQuery promise object resolved when the template has been rendered.
         * @private
         */
        Messages.prototype._hideDeleteAction = function() {
            // Uncheck all checkboxes.
            this.messageArea.find(this.messageArea.SELECTORS.DELETEMESSAGECHECKBOX + " input:checked").removeAttr('checked');
            // Hide the checkboxes.
            this.messageArea.find(this.messageArea.SELECTORS.DELETEMESSAGECHECKBOX).hide();
            // Remove the confirmation message.
            var responseSelector = this.messageArea.SELECTORS.MESSAGESAREA + " " + this.messageArea.SELECTORS.MESSAGERESPONSE;
            this.messageArea.find(responseSelector).empty();
            // Only show a response text area if we are viewing the logged in user's messages.
            if (this.messageArea.getLoggedInUserId() == this.messageArea.getCurrentUserId()) {
                return templates.render('core_message/message_area_response', {}).then(function(html, js) {
                    templates.replaceNodeContents(responseSelector, html, js);
                });
            }
        };

        /**
         * Handles canceling deleting messages.
         *
         * @private
         */
        Messages.prototype._cancelMessagesToDelete = function() {
            // Hide the items responsible for deleting messages.
            this._hideDeleteAction();
            // Trigger event letting other modules know message deletion was canceled.
            this.messageArea.trigger(this.messageArea.EVENTS.CANCELDELETEMESSAGES);
        };

        /**
         * Handles adding messages to the DOM.
         *
         * @returns {Promise} The promise resolved when the message has been added to the DOM.
         * @private
         */
        Messages.prototype._addMessageToDom = function() {
            // Call the web service to return how the message should look.
            var promises = ajax.call([{
                methodname: 'core_message_data_for_messagearea_get_most_recent_message',
                args: {
                    currentuserid: this.messageArea.getCurrentUserId(),
                    otheruserid: this._getUserId()
                }
            }]);

            // Add the message.
            return promises[0].then(function(data) {
                return templates.render('core_message/message_area_message', data);
            }).then(function(html, js) {
                templates.appendNodeContents(this.messageArea.SELECTORS.MESSAGES, html, js);
                // Empty the response text area.
                this.messageArea.find(this.messageArea.SELECTORS.SENDMESSAGETEXT).val('');
            }.bind(this)).fail(notification.exception);
        };

        /**
         * Returns the ID of the other user in the conversation.
         *
         * @returns {int} The user id
         * @private
         */
        Messages.prototype._getUserId = function() {
            return this.messageArea.find(this.messageArea.SELECTORS.MESSAGES).data('userid');
        };

        return Messages;
    }
);