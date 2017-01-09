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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/custom_interaction_events',
        'core/auto_rows', 'core_message/message_area_actions', 'core/modal_factory', 'core/modal_events',
        'core/str', 'core_message/message_area_events', 'core/backoff_timer'],
    function($, Ajax, Templates, Notification, CustomEvents, AutoRows, Actions, ModalFactory,
             ModalEvents, Str, Events, BackOffTimer) {

        /** @type {int} The message area default height. */
        var MESSAGES_AREA_DEFAULT_HEIGHT = 500;

        /** @type {int} The response default height. */
        var MESSAGES_RESPONSE_DEFAULT_HEIGHT = 50;

        /** @type {Object} The list of selectors for the message area. */
        var SELECTORS = {
            BLOCKTIME: "[data-region='blocktime']",
            CANCELDELETEMESSAGES: "[data-action='cancel-delete-messages']",
            CONTACT: "[data-region='contact']",
            CONVERSATIONS: "[data-region='contacts'][data-region-content='conversations']",
            DELETEALLMESSAGES: "[data-action='delete-all-messages']",
            DELETEMESSAGES: "[data-action='delete-messages']",
            LOADINGICON: '.loading-icon',
            MESSAGE: "[data-region='message']",
            MESSAGERESPONSE: "[data-region='response']",
            MESSAGES: "[data-region='messages']",
            MESSAGESAREA: "[data-region='messages-area']",
            MESSAGINGAREA: "[data-region='messaging-area']",
            SENDMESSAGE: "[data-action='send-message']",
            SENDMESSAGETEXT: "[data-region='send-message-txt']",
            SHOWCONTACTS: "[data-action='show-contacts']",
            STARTDELETEMESSAGES: "[data-action='start-delete-messages']",
        };

        /** @type {int} The number of milliseconds in a second. */
        var MILLISECONDSINSEC = 1000;

        /**
         * Messages class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Messages(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

        /** @type {Boolean} checks if we are sending a message */
        Messages.prototype._isSendingMessage = false;

        /** @type {Boolean} checks if we are currently loading messages */
        Messages.prototype._isLoadingMessages = false;

        /** @type {int} the number of messagess displayed */
        Messages.prototype._numMessagesDisplayed = 0;

        /** @type {array} the messages displayed or about to be displayed on the page */
        Messages.prototype._messageQueue = [];

        /** @type {int} the number of messages to retrieve */
        Messages.prototype._numMessagesToRetrieve = 20;

        /** @type {Modal} the confirmation modal */
        Messages.prototype._confirmationModal = null;

        /** @type {int} the timestamp for the most recent visible message */
        Messages.prototype._latestMessageTimestamp = 0;

        /** @type {BackOffTimer} the backoff timer */
        Messages.prototype._backoffTimer = null;

        /** @type {Messagearea} The messaging area object. */
        Messages.prototype.messageArea = null;

        /**
         * Initialise the event listeners.
         *
         * @private
         */
        Messages.prototype._init = function() {
            CustomEvents.define(this.messageArea.node, [
                CustomEvents.events.activate,
                CustomEvents.events.up,
                CustomEvents.events.down,
                CustomEvents.events.enter,
            ]);

            // We have a responsive media query based on height that reduces this size on screens shorter than 670.
            if ($(window).height() <= 670) {
                MESSAGES_AREA_DEFAULT_HEIGHT = 400;
            }

            AutoRows.init(this.messageArea.node);

            this.messageArea.onCustomEvent(Events.CONVERSATIONSELECTED, this._viewMessages.bind(this));
            this.messageArea.onCustomEvent(Events.SENDMESSAGE, this._viewMessages.bind(this));
            this.messageArea.onCustomEvent(Events.CHOOSEMESSAGESTODELETE, this._chooseMessagesToDelete.bind(this));
            this.messageArea.onCustomEvent(Events.CANCELDELETEMESSAGES, this._hideDeleteAction.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.SENDMESSAGE,
                this._sendMessage.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.STARTDELETEMESSAGES,
                this._startDeleting.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.DELETEMESSAGES,
                this._deleteMessages.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.DELETEALLMESSAGES,
                this._deleteAllMessages.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.CANCELDELETEMESSAGES,
                this._triggerCancelMessagesToDelete.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.MESSAGE,
                this._toggleMessage.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.SHOWCONTACTS,
                this._hideMessagingArea.bind(this));

            this.messageArea.onDelegateEvent(CustomEvents.events.up, SELECTORS.MESSAGE,
                this._selectPreviousMessage.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.down, SELECTORS.MESSAGE,
                this._selectNextMessage.bind(this));

            this.messageArea.onDelegateEvent('focus', SELECTORS.SENDMESSAGETEXT, this._setMessaging.bind(this));
            this.messageArea.onDelegateEvent('blur', SELECTORS.SENDMESSAGETEXT, this._clearMessaging.bind(this));

            this.messageArea.onDelegateEvent(CustomEvents.events.enter, SELECTORS.SENDMESSAGETEXT,
                this._sendMessageHandler.bind(this));

            $(document).on(AutoRows.events.ROW_CHANGE, this._adjustMessagesAreaHeight.bind(this));

            // Check if any messages have been displayed on page load.
            var messages = this.messageArea.find(SELECTORS.MESSAGES);
            if (messages.length) {
                this._addScrollEventListener(messages.find(SELECTORS.MESSAGE).length);
                this._latestMessageTimestamp = messages.find(SELECTORS.MESSAGE + ':last').data('timecreated');
            }

            // Create a timer to poll the server for new messages.
            this._backoffTimer = new BackOffTimer(this._loadNewMessages.bind(this),
                BackOffTimer.getIncrementalCallback(this.messageArea.pollmin * MILLISECONDSINSEC, MILLISECONDSINSEC,
                    this.messageArea.pollmax * MILLISECONDSINSEC, this.messageArea.polltimeout * MILLISECONDSINSEC));

            // Start the timer.
            this._backoffTimer.start();
        };

        /**
         * View the message panel.
         *
         * @param {Event} event
         * @param {int} userid
         * @return {Promise} The promise resolved when the messages have been loaded.
         * @private
         */
        Messages.prototype._viewMessages = function(event, userid) {
            // We are viewing another user, or re-loading the panel, so set number of messages displayed to 0.
            this._numMessagesDisplayed = 0;
            // Stop the existing timer so we can set up the new user's messages.
            this._backoffTimer.stop();
            // Reset the latest timestamp when we change the messages view.
            this._latestMessageTimestamp = 0;

            // Mark all the messages as read.
            var markMessagesAsRead = Ajax.call([{
                methodname: 'core_message_mark_all_messages_as_read',
                args: {
                    useridto: this.messageArea.getCurrentUserId(),
                    useridfrom: userid
                }
            }]);

            // Keep track of the number of messages received.
            var numberreceived = 0;
            // Show loading template.
            return Templates.render('core/loading', {}).then(function(html, js) {
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.MESSAGESAREA), html, js);
                return markMessagesAsRead[0];
            }.bind(this)).then(function() {
                var conversationnode = this.messageArea.find(SELECTORS.CONVERSATIONS + " " +
                    SELECTORS.CONTACT + "[data-userid='" + userid + "']");
                if (conversationnode.hasClass('unread')) {
                    // Remove the class.
                    conversationnode.removeClass('unread');
                    // Trigger an event letting the notification popover (and whoever else) know.
                    $(document).trigger('messagearea:conversationselected', userid);
                }
                return this._getMessages(userid);
            }.bind(this)).then(function(data) {
                numberreceived = data.messages.length;
                // We have the data - lets render the template with it.
                return Templates.render('core_message/message_area_messages_area', data);
            }).then(function(html, js) {
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.MESSAGESAREA), html, js);
                this._addScrollEventListener(numberreceived);
                // Restart the poll timer.
                this._backoffTimer.restart();
                this.messageArea.find(SELECTORS.SENDMESSAGETEXT).focus();
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Loads messages while scrolling.
         *
         * @return {Promise|boolean} The promise resolved when the messages have been loaded.
         * @private
         */
        Messages.prototype._loadMessages = function() {
            if (this._isLoadingMessages) {
                return false;
            }

            this._isLoadingMessages = true;

            // Keep track of the number of messages received.
            var numberreceived = 0;
            // Show loading template.
            return Templates.render('core/loading', {}).then(function(html, js) {
                Templates.prependNodeContents(this.messageArea.find(SELECTORS.MESSAGES),
                    "<div style='text-align:center'>" + html + "</div>", js);
                return this._getMessages(this._getUserId());
            }.bind(this)).then(function(data) {
                numberreceived = data.messages.length;
                // We have the data - lets render the template with it.
                return Templates.render('core_message/message_area_messages', data);
            }).then(function(html, js) {
                // Remove the loading icon.
                this.messageArea.find(SELECTORS.MESSAGES + " " +
                    SELECTORS.LOADINGICON).remove();
                // Check if we got something to do.
                if (numberreceived > 0) {
                    var newHtml = $('<div>' + html + '</div>');
                    if (this._hasMatchingBlockTime(this.messageArea.node, newHtml, true)) {
                        this.messageArea.node.find(SELECTORS.BLOCKTIME + ':first').remove();
                    }
                    // Get height before we add the messages.
                    var oldheight = this.messageArea.find(SELECTORS.MESSAGES)[0].scrollHeight;
                    // Show the new content.
                    Templates.prependNodeContents(this.messageArea.find(SELECTORS.MESSAGES), html, js);
                    // Get height after we add the messages.
                    var newheight = this.messageArea.find(SELECTORS.MESSAGES)[0].scrollHeight;
                    // Make sure scroll bar is at the location before we loaded more messages.
                    this.messageArea.find(SELECTORS.MESSAGES).scrollTop(newheight - oldheight);
                    // Increment the number of messages displayed.
                    this._numMessagesDisplayed += numberreceived;
                }
                // Mark that we are no longer busy loading data.
                this._isLoadingMessages = false;
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Loads and renders messages newer than the most recently seen messages.
         *
         * @return {Promise|boolean} The promise resolved when the messages have been loaded.
         * @private
         */
        Messages.prototype._loadNewMessages = function() {
            if (this._isLoadingMessages) {
                return false;
            }

            // If we have no user id yet then bail early.
            if (!this._getUserId()) {
                return false;
            }

            this._isLoadingMessages = true;

            // Only scroll the message window if the user hasn't scrolled up.
            var shouldScrollBottom = false;
            var messages = this.messageArea.find(SELECTORS.MESSAGES);
            if (messages.length !== 0) {
                var scrollTop = messages.scrollTop();
                var innerHeight = messages.innerHeight();
                var scrollHeight = messages[0].scrollHeight;

                if (scrollTop + innerHeight >= scrollHeight) {
                    shouldScrollBottom = true;
                }
            }

            // Keep track of the number of messages received.
            return this._getMessages(this._getUserId(), true).then(function(data) {
                return this._addMessagesToDom(data.messages, shouldScrollBottom);
            }.bind(this)).always(function() {
                // Mark that we are no longer busy loading data.
                this._isLoadingMessages = false;
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Handles returning a list of messages to display.
         *
         * @param {int} userid
         * @param {bool} fromTimestamp Load messages from the latest known timestamp
         * @return {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Messages.prototype._getMessages = function(userid, fromTimestamp) {
            var args = {
                currentuserid: this.messageArea.getCurrentUserId(),
                otheruserid: userid,
                limitfrom: this._numMessagesDisplayed,
                limitnum: this._numMessagesToRetrieve,
                newest: true
            };

            // If we're trying to load new messages since the message UI was
            // rendered. Used for ajax polling while user is on the message UI.
            if (fromTimestamp) {
                args.timefrom = this._latestMessageTimestamp;
                // Remove limit and offset. We want all new messages.
                args.limitfrom = 0;
                args.limitnum = 0;
            }

            // Call the web service to get our data.
            var promises = Ajax.call([{
                methodname: 'core_message_data_for_messagearea_messages',
                args: args,
            }]);

            // Do stuff when we get data back.
            return promises[0].then(function(data) {
                var messages = data.messages;

                // Did we get any new messages?
                if (messages && messages.length) {
                    var latestMessage = messages[messages.length - 1];

                    // Update our record of the latest known message for future requests.
                    if (latestMessage.timecreated > this._latestMessageTimestamp) {
                        // Next request should be for the second after the most recent message we've seen.
                        this._latestMessageTimestamp = latestMessage.timecreated + 1;
                    }
                }

                return data;
            }.bind(this)).fail(function(ex) {
                // Stop the timer if we received an error so that we don't keep spamming the server.
                this._backoffTimer.stop();
                Notification.exception(ex);
            }.bind(this));
        };

        /**
         * Handles sending a message.
         *
         * @return {Promise|boolean} The promise resolved once the message has been sent.
         * @private
         */
        Messages.prototype._sendMessage = function() {
            var element = this.messageArea.find(SELECTORS.SENDMESSAGETEXT);
            var text = element.val().trim();

            // Do not do anything if it is empty.
            if (text === '') {
                return false;
            }

            // If we are sending a message, don't do anything, be patient!
            if (this._isSendingMessage) {
                return false;
            }

            // Ok, mark that we are sending a message.
            this._isSendingMessage = true;

            // Call the web service to save our message.
            var promises = Ajax.call([{
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

            element.prop('disabled', true);

            // Update the DOM when we get some data back.
            return promises[0].then(function(response) {
                if (response.length < 0) {
                    // Even errors should return valid data.
                    throw new Error('Invalid response');
                }
                if (response[0].errormessage) {
                    throw new Error(response[0].errormessage);
                }
                // Fire an event to say the message was sent.
                this.messageArea.trigger(Events.MESSAGESENT, [this._getUserId(), text]);
                // Update the messaging area.
                return this._addLastMessageToDom();
            }.bind(this)).then(function() {
                // Ok, we are no longer sending a message.
                this._isSendingMessage = false;
            }.bind(this)).always(function() {
                element.prop('disabled', false);
                element.focus();
            }).fail(Notification.exception);
        };

        /**
         * Handles selecting messages to delete.
         *
         * @private
         */
        Messages.prototype._chooseMessagesToDelete = function() {
            this.messageArea.find(SELECTORS.MESSAGESAREA).addClass('editing');
            this.messageArea.find(SELECTORS.MESSAGE)
                .attr('role', 'checkbox')
                .attr('aria-checked', 'false');
        };

        /**
         * Handles deleting messages.
         *
         * @private
         */
        Messages.prototype._deleteMessages = function() {
            var userid = this.messageArea.getCurrentUserId();
            var checkboxes = this.messageArea.find(SELECTORS.MESSAGE + "[aria-checked='true']");
            var requests = [];
            var messagestoremove = [];

            // Go through all the checked checkboxes and prepare them for deletion.
            checkboxes.each(function(id, element) {
                var node = $(element);
                var messageid = node.data('messageid');
                var isread = node.data('messageread') ? 1 : 0;
                messagestoremove.push(node);
                requests.push({
                    methodname: 'core_message_delete_message',
                    args: {
                        messageid: messageid,
                        userid: userid,
                        read: isread
                    }
                });
            });

            if (requests.length > 0) {
                Ajax.call(requests)[requests.length - 1].then(function() {
                    // Store the last message on the page, and the last message being deleted.
                    var updatemessage = null;
                    var messages = this.messageArea.find(SELECTORS.MESSAGE);
                    var lastmessage = messages.last();
                    var lastremovedmessage = messagestoremove[messagestoremove.length - 1];
                    // Remove the messages from the DOM.
                    $.each(messagestoremove, function(key, message) {
                        // Remove the message.
                        message.remove();
                    });
                    // If the last message was deleted then we need to provide the new last message.
                    if (lastmessage.data('id') === lastremovedmessage.data('id')) {
                        updatemessage = this.messageArea.find(SELECTORS.MESSAGE).last();
                    }
                    // Now we have removed all the messages from the DOM lets remove any block times we may need to as well.
                    $.each(messagestoremove, function(key, message) {
                        // First - let's make sure there are no more messages in that time block.
                        var blocktime = message.data('blocktime');
                        if (this.messageArea.find(SELECTORS.MESSAGE +
                            "[data-blocktime='" + blocktime + "']").length === 0) {
                            this.messageArea.find(SELECTORS.BLOCKTIME +
                                "[data-blocktime='" + blocktime + "']").remove();
                        }
                    }.bind(this));

                    // If there are no messages at all, then remove conversation panel.
                    if (this.messageArea.find(SELECTORS.MESSAGE).length === 0) {
                        this.messageArea.find(SELECTORS.CONVERSATIONS + " " +
                            SELECTORS.CONTACT + "[data-userid='" + this._getUserId() + "']").remove();
                    }

                    // Trigger event letting other modules know messages were deleted.
                    this.messageArea.trigger(Events.MESSAGESDELETED, [this._getUserId(), updatemessage]);
                }.bind(this), Notification.exception);
            } else {
                // Trigger event letting other modules know messages were deleted.
                this.messageArea.trigger(Events.MESSAGESDELETED, this._getUserId());
            }

            // Hide the items responsible for deleting messages.
            this._hideDeleteAction();
        };

        /**
         * Handles adding a scrolling event listener.
         *
         * @param {int} numberreceived The number of messages received
         * @private
         */
        Messages.prototype._addScrollEventListener = function(numberreceived) {
            // Scroll to the bottom.
            this._scrollBottom();
            // Set the number of messages displayed.
            this._numMessagesDisplayed = numberreceived;
            // Now enable the ability to infinitely scroll through messages.
            CustomEvents.define(this.messageArea.find(SELECTORS.MESSAGES), [
                CustomEvents.events.scrollTop
            ]);
            // Assign the event for scrolling.
            this.messageArea.onCustomEvent(CustomEvents.events.scrollTop, this._loadMessages.bind(this));
        };

        /**
         * Handles deleting a conversation.
         *
         * @private
         */
        Messages.prototype._deleteAllMessages = function() {
            // Create the confirmation modal if we haven't already.
            if (!this._confirmationModal) {
                Str.get_strings([
                    {key: 'confirm'},
                    {key: 'deleteallconfirm', component: 'message'}
                ]).done(function(s) {
                    ModalFactory.create({
                        title: s[0],
                        type: ModalFactory.types.CONFIRM,
                        body: s[1]
                    }, this.messageArea.find(SELECTORS.DELETEALLMESSAGES))
                        .done(function(modal) {
                            this._confirmationModal = modal;

                            // Only delete the conversation if the user agreed in the confirmation modal.
                            modal.getRoot().on(ModalEvents.yes, function() {
                                var otherUserId = this._getUserId();
                                var request = {
                                    methodname: 'core_message_delete_conversation',
                                    args: {
                                        userid: this.messageArea.getCurrentUserId(),
                                        otheruserid: otherUserId
                                    }
                                };

                                // Delete the conversation.
                                Ajax.call([request])[0].then(function() {
                                    // Clear the message area.
                                    this.messageArea.find(SELECTORS.MESSAGESAREA).empty();
                                    // Let the app know a conversation was deleted.
                                    this.messageArea.trigger(Events.CONVERSATIONDELETED, otherUserId);
                                    this._hideDeleteAction();
                                }.bind(this), Notification.exception);
                            }.bind(this));

                            // Display the confirmation.
                            modal.show();
                        }.bind(this));
                }.bind(this));
            } else {
                // Otherwise just show the existing modal.
                this._confirmationModal.show();
            }
        };

        /**
         * Handles hiding the delete checkboxes and replacing the response area.
         *
         * @private
         */
        Messages.prototype._hideDeleteAction = function() {
            this.messageArea.find(SELECTORS.MESSAGE)
                .removeAttr('role')
                .removeAttr('aria-checked');
            this.messageArea.find(SELECTORS.MESSAGESAREA).removeClass('editing');
        };

        /**
         * Triggers the CANCELDELETEMESSAGES event.
         *
         * @private
         */
        Messages.prototype._triggerCancelMessagesToDelete = function() {
            // Trigger event letting other modules know message deletion was canceled.
            this.messageArea.trigger(Events.CANCELDELETEMESSAGES);
        };

        /**
         * Handles adding messages to the DOM.
         *
         * @param {array} messages An array of messages to be added to the DOM.
         * @param {boolean} shouldScrollBottom True will scroll to the bottom of the message window and show the new messages.
         * @return {Promise} The promise resolved when the messages have been added to the DOM.
         * @private
         */
        Messages.prototype._addMessagesToDom = function(messages, shouldScrollBottom) {
            var numberreceived = 0;
            var messagesArea = this.messageArea.find(SELECTORS.MESSAGES);
            messages = messages.filter(function(message) {
                var id = "" + message.id + message.isread;
                // If the message is already queued to be rendered, remove from the list of messages.
                if (this._messageQueue[id]) {
                    return false;
                }
                // Filter out any messages already rendered.
                var result = messagesArea.find(SELECTORS.MESSAGE + '[data-id="' + id + '"]');
                // Any message we are rendering should go in the messageQueue.
                if (!result.length) {
                    this._messageQueue[id] = true;
                }
                return !result.length;
            }.bind(this));
            numberreceived = messages.length;
            // We have the data - lets render the template with it.
            return Templates.render('core_message/message_area_messages', {messages: messages}).then(function(html, js) {
                // Check if we got something to do.
                if (numberreceived > 0) {
                    var newHtml = $('<div>' + html + '</div>');
                    if (this._hasMatchingBlockTime(this.messageArea.node, newHtml, false)) {
                        newHtml.find(SELECTORS.BLOCKTIME + ':first').remove();
                    }
                    // Show the new content.
                    Templates.appendNodeContents(this.messageArea.find(SELECTORS.MESSAGES), newHtml, js);
                    // Scroll the new message into view.
                    if (shouldScrollBottom) {
                        this._scrollBottom();
                    }
                    // Increment the number of messages displayed.
                    this._numMessagesDisplayed += numberreceived;
                    // Reset the poll timer because the user may be active.
                    this._backoffTimer.restart();
                }
            }.bind(this));
        };

        /**
         * Handles adding the last message to the DOM.
         *
         * @return {Promise} The promise resolved when the message has been added to the DOM.
         * @private
         */
        Messages.prototype._addLastMessageToDom = function() {
            // Call the web service to return how the message should look.
            var promises = Ajax.call([{
                methodname: 'core_message_data_for_messagearea_get_most_recent_message',
                args: {
                    currentuserid: this.messageArea.getCurrentUserId(),
                    otheruserid: this._getUserId()
                }
            }]);

            // Add the message.
            return promises[0].then(function(data) {
                return this._addMessagesToDom([data], true);
            }.bind(this)).always(function() {
                // Empty the response text area.text
                this.messageArea.find(SELECTORS.SENDMESSAGETEXT).val('').trigger('input');
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Returns the ID of the other user in the conversation.
         *
         * @return {int} The user id
         * @private
         */
        Messages.prototype._getUserId = function() {
            return this.messageArea.find(SELECTORS.MESSAGES).data('userid');
        };

        /**
         * Scrolls to the bottom of the messages.
         *
         * @private
         */
        Messages.prototype._scrollBottom = function() {
            // Scroll to the bottom.
            var messages = this.messageArea.find(SELECTORS.MESSAGES);
            if (messages.length !== 0) {
                messages.scrollTop(messages[0].scrollHeight);
            }
        };

        /**
         * Select the previous message in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Extra event data
         * @private
         */
        Messages.prototype._selectPreviousMessage = function(e, data) {
            var currentMessage = $(e.target).closest(SELECTORS.MESSAGE);

            do {
                currentMessage = currentMessage.prev();
            } while (currentMessage.length && !currentMessage.is(SELECTORS.MESSAGE));

            currentMessage.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Select the next message in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Extra event data
         * @private
         */
        Messages.prototype._selectNextMessage = function(e, data) {
            var currentMessage = $(e.target).closest(SELECTORS.MESSAGE);

            do {
                currentMessage = currentMessage.next();
            } while (currentMessage.length && !currentMessage.is(SELECTORS.MESSAGE));

            currentMessage.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Flag the response area as messaging.
         *
         * @param {event} e The jquery event
         * @private
         */
        Messages.prototype._setMessaging = function(e) {
            $(e.target).closest(SELECTORS.MESSAGERESPONSE).addClass('messaging');
        };

        /**
         * Clear the response area as messaging flag.
         *
         * @param {event} e The jquery event
         * @private
         */
        Messages.prototype._clearMessaging = function(e) {
            $(e.target).closest(SELECTORS.MESSAGERESPONSE).removeClass('messaging');
        };

        /**
         * Turn on delete message mode.
         *
         * @param {event} e The jquery event
         * @private
         */
        Messages.prototype._startDeleting = function(e) {
            var actions = new Actions(this.messageArea);
            actions.chooseMessagesToDelete();

            e.preventDefault();
        };

        /**
         * Check if the message area is in editing mode.
         *
         * @return {bool}
         * @private
         */
        Messages.prototype._isEditing = function() {
            return this.messageArea.find(SELECTORS.MESSAGESAREA).hasClass('editing');
        };

        /**
         * Check or uncheck the message if the message area is in editing mode.
         *
         * @param {event} e The jquery event
         * @private
         */
        Messages.prototype._toggleMessage = function(e) {
            if (!this._isEditing()) {
                return;
            }

            var message = $(e.target).closest(SELECTORS.MESSAGE);

            if (message.attr('aria-checked') === 'true') {
                message.attr('aria-checked', 'false');
            } else {
                message.attr('aria-checked', 'true');
            }
        };

        /**
         * Adjust the height of the messages area to match the changed height of
         * the response area.
         *
         * @private
         */
        Messages.prototype._adjustMessagesAreaHeight = function() {
            var messagesArea = this.messageArea.find(SELECTORS.MESSAGES);
            var messagesResponse = this.messageArea.find(SELECTORS.MESSAGERESPONSE);

            var currentMessageResponseHeight = messagesResponse.outerHeight();
            var diffResponseHeight = currentMessageResponseHeight - MESSAGES_RESPONSE_DEFAULT_HEIGHT;
            var newMessagesAreaHeight = MESSAGES_AREA_DEFAULT_HEIGHT - diffResponseHeight;

            messagesArea.outerHeight(newMessagesAreaHeight);
        };

        /**
         * Handle the event that triggers sending a message from the messages area.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         * @private
         */
        Messages.prototype._sendMessageHandler = function(e, data) {
            data.originalEvent.preventDefault();

            this._sendMessage();
        };

        /**
         * Hide the messaging area. This only applies on smaller screen resolutions.
         *
         * @private
         */
        Messages.prototype._hideMessagingArea = function() {
            this.messageArea.find(SELECTORS.MESSAGINGAREA)
                .removeClass('show-messages')
                .addClass('hide-messages');
        };

        /**
         * Checks if a day separator needs to be removed.
         *
         * Example - scrolling up and loading previous messages that belong to the
         * same day as the last message that was previously shown, meaning we can
         * remove the original separator.
         *
         * @param {jQuery} domHtml The HTML in the DOM.
         * @param {jQuery} newHtml The HTML to compare to the DOM
         * @param {boolean} loadingPreviousMessages Are we loading previous messages?
         * @return {boolean}
         * @private
         */
        Messages.prototype._hasMatchingBlockTime = function(domHtml, newHtml, loadingPreviousMessages) {
            var blockTime, blockTimePos, newBlockTime, newBlockTimePos;

            if (loadingPreviousMessages) {
                blockTimePos = ':first';
                newBlockTimePos = ':last';
            } else {
                blockTimePos = ':last';
                newBlockTimePos = ':first';
            }

            blockTime = domHtml.find(SELECTORS.BLOCKTIME + blockTimePos);
            newBlockTime = newHtml.find(SELECTORS.BLOCKTIME + newBlockTimePos);

            if (blockTime.length && newBlockTime.length) {
                return blockTime.data('blocktime') == newBlockTime.data('blocktime');
            }

            return false;
        };

        return Messages;
    }
);
