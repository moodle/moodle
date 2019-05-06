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
 * This module updates the UI for the conversation page in the message
 * drawer.
 *
 * The module will take a patch from the message_drawer_view_conversation_patcher
 * module and update the UI to reflect the changes.
 *
 * This is the only module that ever modifies the UI of the conversation page.
 *
 * @module     core_message/message_drawer_view_conversation_renderer
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/notification',
    'core/str',
    'core/templates',
    'core/user_date',
    'core_message/message_drawer_view_conversation_constants'
],
function(
    $,
    Notification,
    Str,
    Templates,
    UserDate,
    Constants
) {
    var SELECTORS = Constants.SELECTORS;
    var TEMPLATES = Constants.TEMPLATES;
    var CONVERSATION_TYPES = Constants.CONVERSATION_TYPES;

    /**
     * Get the messages container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var getMessagesContainer = function(body) {
        return body.find(SELECTORS.CONTENT_MESSAGES_CONTAINER);
    };

    /**
     * Show the messages container element.
     *
     * @param  {Object} body Conversation body container element.
     */
    var showMessagesContainer = function(body) {
        getMessagesContainer(body).removeClass('hidden');
    };

    /**
     * Hide the messages container element.
     *
     * @param  {Object} body Conversation body container element.
     */
    var hideMessagesContainer = function(body) {
        getMessagesContainer(body).addClass('hidden');
    };

    /**
     * Get the self-conversation message container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var getSelfConversationMessageContainer = function(body) {
        return body.find(SELECTORS.SELF_CONVERSATION_MESSAGE_CONTAINER);
    };

    /**
     * Hide the self-conversation message container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var hideSelfConversationMessageContainer = function(body) {
        return getSelfConversationMessageContainer(body).addClass('hidden');
    };

    /**
     * Get the contact request sent container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var getContactRequestSentContainer = function(body) {
        return body.find(SELECTORS.CONTACT_REQUEST_SENT_MESSAGE_CONTAINER);
    };

    /**
     * Hide the contact request sent container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var hideContactRequestSentContainer = function(body) {
        return getContactRequestSentContainer(body).addClass('hidden');
    };

    /**
     * Get the footer container element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer container element.
     */
    var getFooterContentContainer = function(footer) {
        return footer.find(SELECTORS.CONTENT_MESSAGES_FOOTER_CONTAINER);
    };

    /**
     * Show the footer container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterContent = function(footer) {
        getFooterContentContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterContent = function(footer) {
        getFooterContentContainer(footer).addClass('hidden');
    };

    /**
     * Get the footer edit mode container element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer container element.
     */
    var getFooterEditModeContainer = function(footer) {
        return footer.find(SELECTORS.CONTENT_MESSAGES_FOOTER_EDIT_MODE_CONTAINER);
    };

    /**
     * Show the footer edit mode container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterEditMode = function(footer) {
        getFooterEditModeContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer edit mode container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterEditMode = function(footer) {
        getFooterEditModeContainer(footer).addClass('hidden');
    };

    /**
     * Get the footer placeholder.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer placeholder container element.
     */
    var getFooterPlaceholderContainer = function(footer) {
        return footer.find(SELECTORS.PLACEHOLDER_CONTAINER);
    };

    /**
     * Show the footer placeholder
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterPlaceholder = function(footer) {
        getFooterPlaceholderContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer placeholder
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterPlaceholder = function(footer) {
        getFooterPlaceholderContainer(footer).addClass('hidden');
    };

    /**
     * Get the footer Require add as contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer Require add as contact container element.
     */
    var getFooterRequireContactContainer = function(footer) {
        return footer.find(SELECTORS.CONTENT_MESSAGES_FOOTER_REQUIRE_CONTACT_CONTAINER);
    };

    /**
     * Show the footer add as contact dialogue.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterRequireContact = function(footer) {
        getFooterRequireContactContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer add as contact dialogue.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterRequireContact = function(footer) {
        getFooterRequireContactContainer(footer).addClass('hidden');
    };

    /**
     * Get the footer Required to unblock contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer Required to unblock contact container element.
     */
    var getFooterRequireUnblockContainer = function(footer) {
        return footer.find(SELECTORS.CONTENT_MESSAGES_FOOTER_REQUIRE_UNBLOCK_CONTAINER);
    };

    /**
     * Show the footer Required to unblock contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterRequireUnblock = function(footer) {
        getFooterRequireUnblockContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer Required to unblock contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterRequireUnblock = function(footer) {
        getFooterRequireUnblockContainer(footer).addClass('hidden');
    };

    /**
     * Get the footer Unable to message contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer Unable to message contact container element.
     */
    var getFooterUnableToMessageContainer = function(footer) {
        return footer.find(SELECTORS.CONTENT_MESSAGES_FOOTER_UNABLE_TO_MESSAGE_CONTAINER);
    };

    /**
     * Show the footer Unable to message contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var showFooterUnableToMessage = function(footer) {
        getFooterUnableToMessageContainer(footer).removeClass('hidden');
    };

    /**
     * Hide the footer Unable to message contact container element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideFooterUnableToMessage = function(footer) {
        getFooterUnableToMessageContainer(footer).addClass('hidden');
    };

    /**
     * Hide all header elements.
     *
     * @param  {Object} header Conversation header container element.
     */
    var hideAllHeaderElements = function(header) {
        hideHeaderContent(header);
        hideHeaderEditMode(header);
        hideHeaderPlaceholder(header);
    };

    /**
     * Hide all footer dialogues and messages.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hideAllFooterElements = function(footer) {
        hideFooterContent(footer);
        hideFooterEditMode(footer);
        hideFooterPlaceholder(footer);
        hideFooterRequireContact(footer);
        hideFooterRequireUnblock(footer);
        hideFooterUnableToMessage(footer);
    };

    /**
     * Get the content placeholder container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The body placeholder container element.
     */
    var getContentPlaceholderContainer = function(body) {
        return body.find(SELECTORS.CONTENT_PLACEHOLDER_CONTAINER);
    };

    /**
     * Show the content placeholder.
     *
     * @param  {Object} body Conversation body container element.
     */
    var showContentPlaceholder = function(body) {
        getContentPlaceholderContainer(body).removeClass('hidden');
    };

    /**
     * Hide the content placeholder.
     *
     * @param  {Object} body Conversation body container element.
     */
    var hideContentPlaceholder = function(body) {
        getContentPlaceholderContainer(body).addClass('hidden');
    };

    /**
     * Get the header content container element.
     *
     * @param  {Object} header Conversation header container element.
     * @return {Object} The header content container element.
     */
    var getHeaderContent = function(header) {
        return header.find(SELECTORS.HEADER);
    };

    /**
     * Show the header content.
     *
     * @param  {Object} header Conversation header container element.
     */
    var showHeaderContent = function(header) {
        getHeaderContent(header).removeClass('hidden');
    };

    /**
     * Hide the header content.
     *
     * @param  {Object} header Conversation header container element.
     */
    var hideHeaderContent = function(header) {
        getHeaderContent(header).addClass('hidden');
    };

    /**
     * Get the header edit mode container element.
     *
     * @param  {Object} header Conversation header container element.
     * @return {Object} The header content container element.
     */
    var getHeaderEditMode = function(header) {
        return header.find(SELECTORS.HEADER_EDIT_MODE);
    };

    /**
     * Show the header edit mode container.
     *
     * @param  {Object} header Conversation header container element.
     */
    var showHeaderEditMode = function(header) {
        getHeaderEditMode(header).removeClass('hidden');
    };

    /**
     * Hide the header edit mode container.
     *
     * @param  {Object} header Conversation header container element.
     */
    var hideHeaderEditMode = function(header) {
        getHeaderEditMode(header).addClass('hidden');
    };

    /**
     * Get the header placeholder container element.
     *
     * @param  {Object} header Conversation header container element.
     * @return {Object} The header placeholder container element.
     */
    var getHeaderPlaceholderContainer = function(header) {
        return header.find(SELECTORS.HEADER_PLACEHOLDER_CONTAINER);
    };

    /**
     * Show the header placeholder.
     *
     * @param  {Object} header Conversation header container element.
     */
    var showHeaderPlaceholder = function(header) {
        getHeaderPlaceholderContainer(header).removeClass('hidden');
    };

    /**
     * Hide the header placeholder.
     *
     * @param  {Object} header Conversation header container element.
     */
    var hideHeaderPlaceholder = function(header) {
        getHeaderPlaceholderContainer(header).addClass('hidden');
    };

    /**
     * Get the text input area element.
     *
     * @param  {Object} footer Conversation footer container element.
     * @return {Object} The footer placeholder container element.
     */
    var getMessageTextArea = function(footer) {
        return footer.find(SELECTORS.MESSAGE_TEXT_AREA);
    };

    /**
     * Get a message element.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Number} messageId the Message id.
     * @return {Object} A message element from the conversation.
     */
    var getMessageElement = function(body, messageId) {
        var messagesContainer = getMessagesContainer(body);
        return messagesContainer.find('[data-message-id="' + messageId + '"]');
    };

    /**
     * Get the day container element. The day container element holds a list of messages for that day.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Number} dayTimeCreated Midnight timestamp for the day.
     * @return {Object} jQuery object
     */
    var getDayElement = function(body, dayTimeCreated) {
        var messagesContainer = getMessagesContainer(body);
        return messagesContainer.find('[data-day-id="' + dayTimeCreated + '"]');
    };

    /**
     * Get the more messages loading icon container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The more messages loading container element.
     */
    var getMoreMessagesLoadingIconContainer = function(body) {
        return body.find(SELECTORS.MORE_MESSAGES_LOADING_ICON_CONTAINER);
    };

    /**
     * Show the more messages loading icon.
     *
     * @param  {Object} body Conversation body container element.
     */
    var showMoreMessagesLoadingIcon = function(body) {
        getMoreMessagesLoadingIconContainer(body).removeClass('hidden');
    };

    /**
     * Hide the more messages loading icon.
     *
     * @param  {Object} body Conversation body container element.
     */
    var hideMoreMessagesLoadingIcon = function(body) {
        getMoreMessagesLoadingIconContainer(body).addClass('hidden');
    };

    /**
     * Disable the message controls for sending a message.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var disableSendMessage = function(footer) {
        footer.find(SELECTORS.SEND_MESSAGE_BUTTON).prop('disabled', true);
        getMessageTextArea(footer).prop('disabled', true);
    };

    /**
     * Enable the message controls for sending a message.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var enableSendMessage = function(footer) {
        footer.find(SELECTORS.SEND_MESSAGE_BUTTON).prop('disabled', false);
        getMessageTextArea(footer).prop('disabled', false);
    };

    /**
     * Show the sending message loading icon and disable sending more.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var startSendMessageLoading = function(footer) {
        disableSendMessage(footer);
        footer.find(SELECTORS.SEND_MESSAGE_ICON_CONTAINER).addClass('hidden');
        footer.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the sending message loading icon and allow sending new messages.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var stopSendMessageLoading = function(footer) {
        enableSendMessage(footer);
        footer.find(SELECTORS.SEND_MESSAGE_ICON_CONTAINER).removeClass('hidden');
        footer.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Clear out message text input and focus the input element.
     *
     * @param  {Object} footer Conversation footer container element.
     */
    var hasSentMessage = function(footer) {
        var textArea = getMessageTextArea(footer);
        textArea.val('');
        textArea.focus();
    };

    /**
     * Get the confirm dialogue container element.
     *
     * @param  {Object} root The container element to search.
     * @return {Object} The confirm dialogue container element.
     */
    var getConfirmDialogueContainer = function(root) {
        return root.find(SELECTORS.CONFIRM_DIALOGUE_CONTAINER);
    };

    /**
     * Show the confirm dialogue container element.
     *
     * @param  {Object} root The container element containing a dialogue.
     */
    var showConfirmDialogueContainer = function(root) {
        var container = getConfirmDialogueContainer(root);
        var siblings = container.siblings(':not(.hidden)');
        siblings.attr('aria-hidden', true);
        siblings.attr('tabindex', -1);
        siblings.attr('data-confirm-dialogue-hidden', true);

        container.removeClass('hidden');
    };

    /**
     * Hide the confirm dialogue container element.
     *
     * @param  {Object} root The container element containing a dialogue.
     */
    var hideConfirmDialogueContainer = function(root) {
        var container = getConfirmDialogueContainer(root);
        var siblings = container.siblings('[data-confirm-dialogue-hidden="true"]');
        siblings.removeAttr('aria-hidden');
        siblings.removeAttr('tabindex');
        siblings.removeAttr('data-confirm-dialogue-hidden');

        container.addClass('hidden');
    };

    /**
     * Set the number of selected messages.
     *
     * @param {Object} header The header container element.
     * @param {Number} value The new number to display.
     */
    var setMessagesSelectedCount = function(header, value) {
        getHeaderEditMode(header).find(SELECTORS.MESSAGES_SELECTED_COUNT).text(value);
    };

    /**
     * Format message for the mustache template, transform camelCase properties to lowercase properties.
     *
     * @param  {Array} messages Array of message objects.
     * @param  {Object} datesCache Cache timestamps and their formatted date string.
     * @return {Array} Messages formated for mustache template.
     */
    var formatMessagesForTemplate = function(messages, datesCache) {
        return messages.map(function(message) {
            return {
                id: message.id,
                isread: message.isRead,
                fromloggedinuser: message.fromLoggedInUser,
                userfrom: message.userFrom,
                text: message.text,
                formattedtime: datesCache[message.timeCreated]
            };
        });
    };

    /**
     * Create rendering promises for each day containing messages.
     *
     * @param  {Object} header The header container element.
     * @param  {Object} body The body container element.
     * @param  {Object} footer The footer container element.
     * @param  {Array} days Array of days containing messages.
     * @param  {Object} datesCache Cache timestamps and their formatted date string.
     * @return {Promise} Days rendering promises.
     */
    var renderAddDays = function(header, body, footer, days, datesCache) {
        var messagesContainer = getMessagesContainer(body);
        var daysRenderPromises = days.map(function(data) {
            return Templates.render(TEMPLATES.DAY, {
                timestamp: data.value.timestamp,
                messages: formatMessagesForTemplate(data.value.messages, datesCache)
            });
        });

        return $.when.apply($, daysRenderPromises).then(function() {
            // Wait until all of the rendering is done for each of the days
            // to ensure they are added to the page in the correct order.
            days.forEach(function(data, index) {
                daysRenderPromises[index]
                    .then(function(html) {
                        if (data.before) {
                            var element = getDayElement(body, data.before.timestamp);
                            return $(html).insertBefore(element);
                        } else {
                            return messagesContainer.append(html);
                        }
                    })
                    .catch(function() {
                        // Fail silently.
                    });
            });

            return;
        });
    };

    /**
     * Add (more) messages to day containers.
     *
     * @param  {Object} header The header container element.
     * @param  {Object} body The body container element.
     * @param  {Object} footer The footer container element.
     * @param  {Array} messages List of messages.
     * @param  {Object} datesCache Cache timestamps and their formatted date string.
     * @return {Promise} Messages rendering promises.
     */
    var renderAddMessages = function(header, body, footer, messages, datesCache) {
        var messagesData = messages.map(function(data) {
            return data.value;
        });
        var formattedMessages = formatMessagesForTemplate(messagesData, datesCache);

        return Templates.render(TEMPLATES.MESSAGES, {messages: formattedMessages})
            .then(function(html) {
                var messageList = $(html);
                messages.forEach(function(data) {
                    var messageHtml = messageList.find('[data-message-id="' + data.value.id + '"]');
                    if (data.before) {
                        var element = getMessageElement(body, data.before.id);
                        return messageHtml.insertBefore(element);
                    } else {
                        var dayContainer = getDayElement(body, data.day.timestamp);
                        var dayMessagesContainer = dayContainer.find(SELECTORS.DAY_MESSAGES_CONTAINER);
                        return dayMessagesContainer.append(messageHtml);
                    }
                });

                return;
            });
    };

    /**
     * Remove days from conversation.
     *
     * @param  {Object} body The body container element.
     * @param  {Array} days Array of days to be removed.
     */
    var renderRemoveDays = function(body, days) {
        days.forEach(function(data) {
            getDayElement(body, data.timestamp).remove();
        });
    };

    /**
     * Remove messages from conversation.
     *
     * @param  {Object} body The body container element.
     * @param  {Array} messages Array of messages to be removed.
     */
    var renderRemoveMessages = function(body, messages) {
        messages.forEach(function(data) {
            getMessageElement(body, data.id).remove();
        });
    };

    /**
     * Render the full conversation base on input from the statemanager.
     *
     * This will pre-load all of the formatted timestamps for each message that
     * needs to render to reduce the number of networks requests.
     *
     * @param  {Object} header The header container element.
     * @param  {Object} body The body container element.
     * @param  {Object} footer The footer container element.
     * @param  {Object} data The conversation diff.
     * @return {Object} jQuery promise.
     */
    var renderConversation = function(header, body, footer, data) {
        var renderingPromises = [];
        var hasAddDays = data.days.add.length > 0;
        var hasAddMessages = data.messages.add.length > 0;
        var timestampsToFormat = [];
        var datesCachePromise = $.Deferred().resolve({}).promise();

        if (hasAddDays) {
            // Search for all of the timeCreated values in all of the messages in all of
            // the days that we need to render.
            timestampsToFormat = timestampsToFormat.concat(data.days.add.reduce(function(carry, day) {
                return carry.concat(day.value.messages.map(function(message) {
                    return message.timeCreated;
                }));
            }, []));
        }

        if (hasAddMessages) {
            // Search for all of the timeCreated values in all of the messages that we
            // need to render.
            timestampsToFormat = timestampsToFormat.concat(data.messages.add.map(function(message) {
                return message.value.timeCreated;
            }));
        }

        if (timestampsToFormat.length) {
            // If we have timestamps then pre-load the formatted version of each of them
            // in a single request to the server. This saves the templates doing multiple
            // individual requests.
            datesCachePromise = Str.get_string('strftimetime24', 'core_langconfig')
                .then(function(format) {
                    var requests = timestampsToFormat.map(function(timestamp) {
                        return {
                            timestamp: timestamp,
                            format: format
                        };
                    });

                    return UserDate.get(requests);
                })
                .then(function(formattedTimes) {
                    return timestampsToFormat.reduce(function(carry, timestamp, index) {
                        carry[timestamp] = formattedTimes[index];
                        return carry;
                    }, {});
                });
        }

        if (hasAddDays) {
            renderingPromises.push(datesCachePromise.then(function(datesCache) {
                return renderAddDays(header, body, footer, data.days.add, datesCache);
            }));
        }

        if (hasAddMessages) {
            renderingPromises.push(datesCachePromise.then(function(datesCache) {
                return renderAddMessages(header, body, footer, data.messages.add, datesCache);
            }));
        }

        if (data.days.remove.length > 0) {
            renderRemoveDays(body, data.days.remove);
        }

        if (data.messages.remove.length > 0) {
            renderRemoveMessages(body, data.messages.remove);
        }

        return $.when.apply($, renderingPromises);
    };

    /**
     * Render the conversation header.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} data Data for header.
     * @return {Object} jQuery promise
     */
    var renderHeader = function(header, body, footer, data) {
        var headerContainer = getHeaderContent(header);
        var template = TEMPLATES.HEADER_PUBLIC;
        data.context.showrouteback = (header.attr('data-from-panel') === "false");
        if (data.type == CONVERSATION_TYPES.PRIVATE) {
            template = data.showControls ? TEMPLATES.HEADER_PRIVATE : TEMPLATES.HEADER_PRIVATE_NO_CONTROLS;
        } else if (data.type == CONVERSATION_TYPES.SELF) {
            template = TEMPLATES.HEADER_SELF;
        }

        return Templates.render(template, data.context)
            .then(function(html, js) {
                Templates.replaceNodeContents(headerContainer, html, js);
                return;
            });
    };

    /**
     * Render the conversation footer.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} data Data for footer.
     * @return {Object} jQuery promise.
     */
    var renderFooter = function(header, body, footer, data) {
        hideAllFooterElements(footer);

        switch (data.type) {
            case 'placeholder':
                return showFooterPlaceholder(footer);
            case 'add-contact':
                return Str.get_strings([
                        {
                            key: 'requirecontacttomessage',
                            component: 'core_message',
                            param: data.user.fullname
                        },
                        {
                            key: 'isnotinyourcontacts',
                            component: 'core_message',
                            param: data.user.fullname
                        }
                    ])
                    .then(function(strings) {
                        var title = strings[1];
                        var text = strings[0];
                        var footerContainer = getFooterRequireContactContainer(footer);
                        footerContainer.find(SELECTORS.TITLE).text(title);
                        footerContainer.find(SELECTORS.TEXT).text(text);
                        showFooterRequireContact(footer);
                        return strings;
                    });
            case 'edit-mode':
                return showFooterEditMode(footer);
            case 'content':
                return showFooterContent(footer);
            case 'unblock':
                return showFooterRequireUnblock(footer);
            case 'unable-to-message':
                return showFooterUnableToMessage(footer);
        }

        return true;
    };

    /**
     * Scroll to a message in the conversation.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Number} messageId Message id.
     */
    var renderScrollToMessage = function(header, body, footer, messageId) {
        var messagesContainer = getMessagesContainer(body);
        var messageElement = getMessageElement(body, messageId);
        var position = messageElement.position();
        // Scroll the message container down to the top of the message element.
        if (position) {
            var scrollTop = messagesContainer.scrollTop() + position.top;
            messagesContainer.scrollTop(scrollTop);
        }
    };

    /**
     * Hide or show the conversation header.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isLoadingMembers Members loading.
     */
    var renderLoadingMembers = function(header, body, footer, isLoadingMembers) {
        if (isLoadingMembers) {
            hideHeaderContent(header);
            showHeaderPlaceholder(header);
        } else {
            showHeaderContent(header);
            hideHeaderPlaceholder(header);
        }
    };

    /**
     * Hide or show loading conversation messages.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isLoadingFirstMessages Messages loading.
     */
    var renderLoadingFirstMessages = function(header, body, footer, isLoadingFirstMessages) {
        if (isLoadingFirstMessages) {
            hideMessagesContainer(body);
            showContentPlaceholder(body);
        } else {
            showMessagesContainer(body);
            hideContentPlaceholder(body);
        }
    };

    /**
     * Hide or show loading more messages.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isLoading Messages loading.
     */
    var renderLoadingMessages = function(header, body, footer, isLoading) {
        if (isLoading) {
            showMoreMessagesLoadingIcon(body);
        } else {
            hideMoreMessagesLoadingIcon(body);
        }
    };

    /**
     * Activate or deactivate send message controls.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isSending Message sending.
     */
    var renderSendingMessage = function(header, body, footer, isSending) {
        if (isSending) {
            startSendMessageLoading(footer);
        } else {
            stopSendMessageLoading(footer);
            hasSentMessage(footer);
        }
    };

    /**
     * Show a confirmation dialogue
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {String} buttonSelectors Selectors for the buttons to show.
     * @param {String} bodyText Text to show in dialogue.
     * @param {String} headerText Text to show in dialogue header.
     * @param {Bool} canCancel Can this dialogue be cancelled.
     * @param {Bool} skipHeader Skip blanking out the header
     */
    var showConfirmDialogue = function(
        header,
        body,
        footer,
        buttonSelectors,
        bodyText,
        headerText,
        canCancel,
        skipHeader
    ) {
        var dialogue = getConfirmDialogueContainer(body);
        var buttons = buttonSelectors.map(function(selector) {
            return dialogue.find(selector);
        });
        var cancelButton = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_CANCEL_BUTTON);
        var text = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_TEXT);
        var dialogueHeader = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_HEADER);

        dialogue.find('button').addClass('hidden');

        if (canCancel) {
            cancelButton.removeClass('hidden');
        } else {
            cancelButton.addClass('hidden');
        }

        if (headerText) {
            dialogueHeader.removeClass('hidden');
            dialogueHeader.text(headerText);
        } else {
            dialogueHeader.addClass('hidden');
            dialogueHeader.text('');
        }

        buttons.forEach(function(button) {
            button.removeClass('hidden');
        });
        text.text(bodyText);
        showConfirmDialogueContainer(footer);
        showConfirmDialogueContainer(body);

        if (!skipHeader) {
            showConfirmDialogueContainer(header);
        }

        dialogue.find(SELECTORS.CAN_RECEIVE_FOCUS).filter(':visible').first().focus();
    };

    /**
     * Hide the dialogue
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @return {Bool} always true.
     */
    var hideConfirmDialogue = function(header, body, footer) {
        var dialogue = getConfirmDialogueContainer(body);
        var cancelButton = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_CANCEL_BUTTON);
        var text = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_TEXT);
        var dialogueHeader = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_HEADER);

        hideCheckDeleteDialogue(body);
        hideConfirmDialogueContainer(body);
        hideConfirmDialogueContainer(footer);
        hideConfirmDialogueContainer(header);
        dialogue.find('button').addClass('hidden');
        cancelButton.removeClass('hidden');
        text.text('');
        dialogueHeader.addClass('hidden');
        dialogueHeader.text('');

        header.find(SELECTORS.CAN_RECEIVE_FOCUS).first().focus();
        return true;
    };

    /**
     * Render the confirm block user dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} user User to block.
     * @return {Object} jQuery promise
     */
    var renderConfirmBlockUser = function(header, body, footer, user) {
        if (user) {
            return Str.get_string('blockuserconfirm', 'core_message', user.fullname)
                .then(function(string) {
                    return showConfirmDialogue(header, body, footer, [SELECTORS.ACTION_CONFIRM_BLOCK], string, '', true, false);
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the confirm unblock user dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} user User to unblock.
     * @return {Object} jQuery promise
     */
    var renderConfirmUnblockUser = function(header, body, footer, user) {
        if (user) {
            return Str.get_string('unblockuserconfirm', 'core_message', user.fullname)
                .then(function(string) {
                    return showConfirmDialogue(header, body, footer, [SELECTORS.ACTION_CONFIRM_UNBLOCK], string, '', true, false);
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the add user as contact dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} user User to add as contact.
     * @return {Object} jQuery promise
     */
    var renderConfirmAddContact = function(header, body, footer, user) {
        if (user) {
            return Str.get_string('addcontactconfirm', 'core_message', user.fullname)
                .then(function(string) {
                    return showConfirmDialogue(
                        header,
                        body,
                        footer,
                        [SELECTORS.ACTION_CONFIRM_ADD_CONTACT],
                        string,
                        '',
                        true,
                        false
                    );
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the remove user from contacts dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} user User to remove from contacts.
     * @return {Object} jQuery promise
     */
    var renderConfirmRemoveContact = function(header, body, footer, user) {
        if (user) {
            return Str.get_string('removecontactconfirm', 'core_message', user.fullname)
                .then(function(string) {
                    return showConfirmDialogue(
                        header,
                        body,
                        footer,
                        [SELECTORS.ACTION_CONFIRM_REMOVE_CONTACT],
                        string,
                        '',
                        true,
                        false
                    );
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the delete selected messages dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} data If the dialogue should show and checkbox shows to delete message for all users.
     * @return {Object} jQuery promise
     */
    var renderConfirmDeleteSelectedMessages = function(header, body, footer, data) {
        var showmessage = null;
        if (data.type == CONVERSATION_TYPES.SELF) {
            // Message displayed to self-conversations is slighly different.
            showmessage = 'deleteselectedmessagesconfirmselfconversation';
        } else {
            // This other message should be displayed.
            if (data.canDeleteMessagesForAllUsers) {
                showCheckDeleteDialogue(body);
                showmessage = 'deleteforeveryoneselectedmessagesconfirm';
            } else {
                showmessage = 'deleteselectedmessagesconfirm';
            }
        }

        if (data.show) {
            return Str.get_string(showmessage, 'core_message')
                .then(function(string) {
                    return showConfirmDialogue(
                        header,
                        body,
                        footer,
                        [SELECTORS.ACTION_CONFIRM_DELETE_SELECTED_MESSAGES],
                        string,
                        '',
                        true,
                        false
                    );
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the confirm delete conversation dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {int|Null} type The conversation type to be removed.
     * @return {Object} jQuery promise
     */
    var renderConfirmDeleteConversation = function(header, body, footer, type) {
        var showmessage = null;
        if (type == CONVERSATION_TYPES.SELF) {
            // Message displayed to self-conversations is slighly different.
            showmessage = 'deleteallselfconfirm';
        } else if (type) {
            // This other message should be displayed.
            showmessage = 'deleteallconfirm';
        }

        if (showmessage) {
            return Str.get_string(showmessage, 'core_message')
                .then(function(string) {
                    return showConfirmDialogue(
                        header,
                        body,
                        footer,
                        [SELECTORS.ACTION_CONFIRM_DELETE_CONVERSATION],
                        string,
                        '',
                        true,
                        false
                    );
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Render the confirm delete conversation dialogue.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} user The other user object.
     * @return {Object} jQuery promise
     */
    var renderConfirmContactRequest = function(header, body, footer, user) {
        if (user) {
            return Str.get_string('userwouldliketocontactyou', 'core_message', user.fullname)
                .then(function(string) {
                    var buttonSelectors = [
                        SELECTORS.ACTION_ACCEPT_CONTACT_REQUEST,
                        SELECTORS.ACTION_DECLINE_CONTACT_REQUEST
                    ];
                    return showConfirmDialogue(header, body, footer, buttonSelectors, string, '', false, true);
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Show the checkbox to allow delete message for all.
     *
     * @param {Object} body The body container element.
     */
    var showCheckDeleteDialogue = function(body) {
        var dialogue = getConfirmDialogueContainer(body);
        var checkboxRegion = dialogue.find(SELECTORS.DELETE_MESSAGES_FOR_ALL_USERS_TOGGLE_CONTAINER);
        checkboxRegion.removeClass('hidden');
    };

    /**
     * Hide the checkbox to allow delete message for all.
     *
     * @param {Object} body The body container element.
     */
    var hideCheckDeleteDialogue = function(body) {
        var dialogue = getConfirmDialogueContainer(body);
        var checkboxRegion = dialogue.find(SELECTORS.DELETE_MESSAGES_FOR_ALL_USERS_TOGGLE_CONTAINER);
        var checkbox = dialogue.find(SELECTORS.DELETE_MESSAGES_FOR_ALL_USERS_TOGGLE);
        checkbox.prop('checked', false);
        checkboxRegion.addClass('hidden');
    };

    /**
     * Show or hide the block / unblock option in the header dropdown menu.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isBlocked is user blocked.
     */
    var renderIsBlocked = function(header, body, footer, isBlocked) {
        if (isBlocked) {
            header.find(SELECTORS.ACTION_REQUEST_BLOCK).addClass('hidden');
            header.find(SELECTORS.ACTION_REQUEST_UNBLOCK).removeClass('hidden');
        } else {
            header.find(SELECTORS.ACTION_REQUEST_BLOCK).removeClass('hidden');
            header.find(SELECTORS.ACTION_REQUEST_UNBLOCK).addClass('hidden');
        }
    };

    /**
     * Show or hide the favourite / unfavourite option in the header dropdown menu
     * and the favourite star in the header title.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isFavourite is this conversation a favourite.
     */
    var renderIsFavourite = function(header, body, footer, state) {
        var favouriteIcon = header.find(SELECTORS.FAVOURITE_ICON_CONTAINER);
        var addFavourite = header.find(SELECTORS.ACTION_CONFIRM_FAVOURITE);
        var removeFavourite = header.find(SELECTORS.ACTION_CONFIRM_UNFAVOURITE);

        switch (state) {
            case 'hide':
                favouriteIcon.addClass('hidden');
                addFavourite.addClass('hidden');
                removeFavourite.addClass('hidden');
                break;
            case 'show-add':
                favouriteIcon.addClass('hidden');
                addFavourite.removeClass('hidden');
                removeFavourite.addClass('hidden');
                break;
            case 'show-remove':
                favouriteIcon.removeClass('hidden');
                addFavourite.addClass('hidden');
                removeFavourite.removeClass('hidden');
                break;
        }
    };

    /**
     * Show or hide the mute / unmute option in the header dropdown menu
     * and the muted icon in the header title.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {string} state The state of the conversation as defined by the patcher.
     */
    var renderIsMuted = function(header, body, footer, state) {
        var muteIcon = header.find(SELECTORS.MUTED_ICON_CONTAINER);
        var setMuted = header.find(SELECTORS.ACTION_CONFIRM_MUTE);
        var unsetMuted = header.find(SELECTORS.ACTION_CONFIRM_UNMUTE);

        switch (state) {
            case 'hide':
                muteIcon.addClass('hidden');
                setMuted.addClass('hidden');
                unsetMuted.addClass('hidden');
                break;
            case 'show-mute':
                muteIcon.addClass('hidden');
                setMuted.removeClass('hidden');
                unsetMuted.addClass('hidden');
                break;
            case 'show-unmute':
                muteIcon.removeClass('hidden');
                setMuted.addClass('hidden');
                unsetMuted.removeClass('hidden');
                break;
        }
    };

    /**
     * Show or hide the add / remove user as contact option in the header dropdown menu.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} state the contact state.
     */
    var renderIsContact = function(header, body, footer, state) {
        var addContact = header.find(SELECTORS.ACTION_REQUEST_ADD_CONTACT);
        var removeContact = header.find(SELECTORS.ACTION_REQUEST_REMOVE_CONTACT);

        switch (state) {
            case 'pending-contact':
                addContact.addClass('hidden');
                removeContact.addClass('hidden');
                break;
            case 'contact':
                addContact.addClass('hidden');
                removeContact.removeClass('hidden');
                break;
            case 'non-contact':
                addContact.removeClass('hidden');
                removeContact.addClass('hidden');
                break;
        }
    };

    /**
     * Show or hide confirm action from confirm dialogue is loading.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} isLoading confirm action is loading.
     */
    var renderLoadingConfirmAction = function(header, body, footer, isLoading) {
        var dialogue = getConfirmDialogueContainer(body);
        var buttons = dialogue.find('button');
        var buttonText = dialogue.find(SELECTORS.CONFIRM_DIALOGUE_BUTTON_TEXT);
        var loadingIcon = dialogue.find(SELECTORS.LOADING_ICON_CONTAINER);

        if (isLoading) {
            buttons.prop('disabled', true);
            buttonText.addClass('hidden');
            loadingIcon.removeClass('hidden');
        } else {
            buttons.prop('disabled', false);
            buttonText.removeClass('hidden');
            loadingIcon.addClass('hidden');
        }
    };

    /**
     * Show or hide the header and footer content for edit mode.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Bool} inEditMode In edit mode or not.
     */
    var renderInEditMode = function(header, body, footer, inEditMode) {
        var messages = null;

        if (inEditMode) {
            messages = body.find(SELECTORS.MESSAGE_NOT_SELECTED);
            messages.find(SELECTORS.MESSAGE_NOT_SELECTED_ICON).removeClass('hidden');
            hideHeaderContent(header);
            showHeaderEditMode(header);
        } else {
            messages = getMessagesContainer(body);
            messages.find(SELECTORS.MESSAGE_NOT_SELECTED_ICON).addClass('hidden');
            messages.find(SELECTORS.MESSAGE_SELECTED_ICON).addClass('hidden');
            showHeaderContent(header);
            hideHeaderEditMode(header);
        }
    };

    /**
     * Select or unselect messages.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} data The messages to select or unselect.
     */
    var renderSelectedMessages = function(header, body, footer, data) {
        var hasSelectedMessages = data.count > 0;

        if (data.add.length) {
            data.add.forEach(function(messageId) {
                var message = getMessageElement(body, messageId);
                message.find(SELECTORS.MESSAGE_NOT_SELECTED_ICON).addClass('hidden');
                message.find(SELECTORS.MESSAGE_SELECTED_ICON).removeClass('hidden');
                message.attr('aria-checked', true);
            });
        }

        if (data.remove.length) {
            data.remove.forEach(function(messageId) {
                var message = getMessageElement(body, messageId);

                if (hasSelectedMessages) {
                    message.find(SELECTORS.MESSAGE_NOT_SELECTED_ICON).removeClass('hidden');
                }

                message.find(SELECTORS.MESSAGE_SELECTED_ICON).addClass('hidden');
                message.attr('aria-checked', false);
            });
        }

        setMessagesSelectedCount(header, data.count);
    };

    /**
     * Show or hide the require add contact panel.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} data Whether the user has to be added a a contact.
     * @return {Object} jQuery promise
     */
    var renderRequireAddContact = function(header, body, footer, data) {
        if (data.show && !data.hasMessages) {
            return Str.get_strings([
                    {
                        key: 'requirecontacttomessage',
                        component: 'core_message',
                        param: data.user.fullname
                    },
                    {
                        key: 'isnotinyourcontacts',
                        component: 'core_message',
                        param: data.user.fullname
                    }
                ])
                .then(function(strings) {
                    var title = strings[1];
                    var text = strings[0];
                    return showConfirmDialogue(
                        header,
                        body,
                        footer,
                        [SELECTORS.ACTION_REQUEST_ADD_CONTACT],
                        text,
                        title,
                        false,
                        true
                    );
                });
        } else {
            return hideConfirmDialogue(header, body, footer);
        }
    };

    /**
     * Show or hide the self-conversation message.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} displayMessage should the message be displayed?.
     * @return {Object|true} jQuery promise
     */
    var renderSelfConversationMessage = function(header, body, footer, displayMessage) {
        var container = getSelfConversationMessageContainer(body);
        if (displayMessage) {
            container.removeClass('hidden');
        } else {
            container.addClass('hidden');
        }
        return true;
    };

    /**
     * Show or hide the require add contact panel.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @param {Object} userFullName Full name of the other user.
     * @return {Object|true} jQuery promise
     */
    var renderContactRequestSent = function(header, body, footer, userFullName) {
        var container = getContactRequestSentContainer(body);
        if (userFullName) {
            return Str.get_string('yourcontactrequestpending', 'core_message', userFullName)
                .then(function(string) {
                    container.find(SELECTORS.TEXT).text(string);
                    container.removeClass('hidden');
                    return string;
                });
        } else {
            container.addClass('hidden');
            return true;
        }
    };

    /**
     * Reset the UI to the initial state.
     *
     * @param {Object} header The header container element.
     * @param {Object} body The body container element.
     * @param {Object} footer The footer container element.
     * @return {Bool}
     */
    var renderReset = function(header, body, footer) {
        hideConfirmDialogue(header, body, footer);
        hideContactRequestSentContainer(body);
        hideSelfConversationMessageContainer(body);
        hideAllHeaderElements(header);
        showHeaderPlaceholder(header);
        hideAllFooterElements(footer);
        showFooterPlaceholder(footer);
        return true;
    };

    var render = function(header, body, footer, patch) {
        var configs = [
            {
                // Resetting the UI needs to come first, if it's required.
                reset: renderReset
            },
            {
                // Any async rendering (stuff that requires templates, strings etc) should
                // go in here.
                conversation: renderConversation,
                header: renderHeader,
                footer: renderFooter,
                confirmBlockUser: renderConfirmBlockUser,
                confirmUnblockUser: renderConfirmUnblockUser,
                confirmAddContact: renderConfirmAddContact,
                confirmRemoveContact: renderConfirmRemoveContact,
                confirmDeleteSelectedMessages: renderConfirmDeleteSelectedMessages,
                confirmDeleteConversation: renderConfirmDeleteConversation,
                confirmContactRequest: renderConfirmContactRequest,
                requireAddContact: renderRequireAddContact,
                selfConversationMessage: renderSelfConversationMessage,
                contactRequestSent: renderContactRequestSent
            },
            {
                loadingMembers: renderLoadingMembers,
                loadingFirstMessages: renderLoadingFirstMessages,
                loadingMessages: renderLoadingMessages,
                sendingMessage: renderSendingMessage,
                isBlocked: renderIsBlocked,
                isContact: renderIsContact,
                isFavourite: renderIsFavourite,
                isMuted: renderIsMuted,
                loadingConfirmAction: renderLoadingConfirmAction,
                inEditMode: renderInEditMode
            },
            {
                // Scrolling should be last to make sure everything
                // on the page is visible.
                scrollToMessage: renderScrollToMessage,
                selectedMessages: renderSelectedMessages
            }
        ];
        // Helper function to process each of the configs above.
        var processConfig = function(config) {
            var results = [];

            for (var key in patch) {
                if (config.hasOwnProperty(key)) {
                    var renderFunc = config[key];
                    var patchValue = patch[key];
                    results.push(renderFunc(header, body, footer, patchValue));
                }
            }

            return results;
        };

        // The first config is special because it resets the UI.
        var renderingPromises = processConfig(configs[0]);
        // The second config is special because it contains async rendering.
        renderingPromises = renderingPromises.concat(processConfig(configs[1]));

        // Wait for the async rendering to complete before processing the
        // rest of the configs, in order.
        return $.when.apply($, renderingPromises)
            .then(function() {
                for (var i = 2; i < configs.length; i++) {
                    processConfig(configs[i]);
                }

                return;
            })
            .catch(Notification.exception);
    };

    return {
        render: render,
    };
});
