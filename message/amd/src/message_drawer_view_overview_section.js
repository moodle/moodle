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
 * Controls a section of the overview page in the message drawer.
 *
 * @module     core_message/message_drawer_view_overview_section
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/notification',
    'core/pubsub',
    'core/str',
    'core/templates',
    'core/user_date',
    'core_message/message_repository',
    'core_message/message_drawer_events',
    'core_message/message_drawer_router',
    'core_message/message_drawer_routes',
    'core_message/message_drawer_lazy_load_list',
    'core_message/message_drawer_view_conversation_constants'
],
function(
    $,
    CustomEvents,
    Notification,
    PubSub,
    Str,
    Templates,
    UserDate,
    MessageRepository,
    MessageDrawerEvents,
    MessageDrawerRouter,
    MessageDrawerRoutes,
    LazyLoadList,
    MessageDrawerViewConversationContants
) {

    var SELECTORS = {
        TOGGLE: '[data-region="toggle"]',
        CONVERSATION: '[data-conversation-id]',
        BLOCKED_ICON_CONTAINER: '[data-region="contact-icon-blocked"]',
        LAST_MESSAGE: '[data-region="last-message"]',
        LAST_MESSAGE_DATE: '[data-region="last-message-date"]',
        MUTED_ICON_CONTAINER: '[data-region="muted-icon-container"]',
        UNREAD_COUNT: '[data-region="unread-count"]',
        SECTION_TOTAL_COUNT: '[data-region="section-total-count"]',
        SECTION_TOTAL_COUNT_CONTAINER: '[data-region="section-total-count-container"]',
        SECTION_UNREAD_COUNT: '[data-region="section-unread-count"]',
        PLACEHOLDER_CONTAINER: '[data-region="placeholder-container"]'
    };

    var TEMPLATES = {
        CONVERSATIONS_LIST: 'core_message/message_drawer_conversations_list',
        CONVERSATIONS_LIST_ITEMS_PLACEHOLDER: 'core_message/message_drawer_conversations_list_items_placeholder'
    };

    var LOAD_LIMIT = 50;
    var loadedConversationsById = {};
    var loadedTotalCounts = false;
    var loadedUnreadCounts = false;

    /**
     * Get the section visibility status.
     *
     * @param  {Object} root The section container element.
     * @return {Bool} Is section visible.
     */
    var isVisible = function(root) {
        return LazyLoadList.getRoot(root).hasClass('show');
    };

    /**
     * Set this section as expanded.
     *
     * @param  {Object} root The section container element.
     */
    var setExpanded = function(root) {
        root.addClass('expanded');
    };

    /**
     * Set this section as collapsed.
     *
     * @param  {Object} root The section container element.
     */
    var setCollapsed = function(root) {
        root.removeClass('expanded');
    };

    /**
     * Render the total count value and show it for the user. Also update the placeholder
     * HTML for better visuals.
     *
     * @param {Object} root The section container element.
     * @param {Number} count The total count
     */
    var renderTotalCount = function(root, count) {
        var container = root.find(SELECTORS.SECTION_TOTAL_COUNT_CONTAINER);
        var countElement = container.find(SELECTORS.SECTION_TOTAL_COUNT);
        countElement.text(count);
        container.removeClass('hidden');
        Str.get_string('totalconversations', 'core_message', count).done(function(string) {
            container.attr('aria-label', string);
        });

        var numPlaceholders = count > 20 ? 20 : count;
        // Array of "true" up to the number of placeholders we want.
        var placeholders = Array.apply(null, Array(numPlaceholders)).map(function() {
            return true;
        });

        // Replace the current placeholder (loading spinner) with some nicer placeholders that
        // better represent the content.
        Templates.render(TEMPLATES.CONVERSATIONS_LIST_ITEMS_PLACEHOLDER, {placeholders: placeholders})
            .then(function(html) {
                var placeholderContainer = root.find(SELECTORS.PLACEHOLDER_CONTAINER);
                placeholderContainer.html(html);
                return;
            })
            .catch(function() {
                // Silently ignore. Doesn't matter if we can't render the placeholders.
            });
    };

    /**
     * Render the unread count value and show it for the user if it's higher than zero.
     *
     * @param {Object} root The section container element.
     * @param {Number} count The unread count
     */
    var renderUnreadCount = function(root, count) {
        var countElement = root.find(SELECTORS.SECTION_UNREAD_COUNT);
        countElement.text(count);

        Str.get_string('unreadconversations', 'core_message', count).done(function(string) {
            countElement.attr('aria-label', string);
        });

        if (count > 0) {
            countElement.removeClass('hidden');
        }
    };

    /**
     * Create a formatted conversation object from the the one we get from events. The new object
     * will be in a format that matches what we receive from the server.
     *
     * @param {Object} conversation
     * @return {Object} formatted conversation.
     */
    var formatConversationFromEvent = function(conversation) {
        // Recursively lowercase all of the keys for an object.
        var recursivelyLowercaseKeys = function(object) {
            return Object.keys(object).reduce(function(carry, key) {
                if ($.isArray(object[key])) {
                    carry[key.toLowerCase()] = object[key].map(recursivelyLowercaseKeys);
                } else {
                    carry[key.toLowerCase()] = object[key];
                }

                return carry;
            }, {});
        };

        // Recursively lowercase all of the keys for the conversation.
        var formatted = recursivelyLowercaseKeys(conversation);

        // Make sure all messages have the useridfrom property set.
        formatted.messages = formatted.messages.map(function(message) {
            message.useridfrom = message.userfrom.id;
            return message;
        });

        return formatted;
    };

    /**
     * Render the messages in the overview page.
     *
     * @param {Object} contentContainer Conversations content container.
     * @param {Array} conversations List of conversations to render.
     * @param {Number} userId Logged in user id.
     * @return {Object} jQuery promise.
     */
    var render = function(conversations, userId) {
        var formattedConversations = conversations.map(function(conversation) {

            var lastMessage = conversation.messages.length ? conversation.messages[conversation.messages.length - 1] : null;

            var formattedConversation = {
                id: conversation.id,
                imageurl: conversation.imageurl,
                name: conversation.name,
                subname: conversation.subname,
                unreadcount: conversation.unreadcount,
                ismuted: conversation.ismuted,
                lastmessagedate: lastMessage ? lastMessage.timecreated : null,
                sentfromcurrentuser: lastMessage ? lastMessage.useridfrom == userId : null,
                lastmessage: lastMessage ? $(lastMessage.text).text() || lastMessage.text : null
            };

            var otherUser = null;
            if (conversation.type == MessageDrawerViewConversationContants.CONVERSATION_TYPES.SELF) {
                // Self-conversations have only one member.
                otherUser = conversation.members[0];
            } else if (conversation.type == MessageDrawerViewConversationContants.CONVERSATION_TYPES.PRIVATE) {
                // For private conversations, remove the current userId from the members to get the other user.
                otherUser = conversation.members.reduce(function(carry, member) {
                    if (!carry && member.id != userId) {
                        carry = member;
                    }
                    return carry;
                }, null);
            }

            if (otherUser !== null) {
                formattedConversation.userid = otherUser.id;
                formattedConversation.showonlinestatus = otherUser.showonlinestatus;
                formattedConversation.isonline = otherUser.isonline;
                formattedConversation.isblocked = otherUser.isblocked;
            }

            if (conversation.type == MessageDrawerViewConversationContants.CONVERSATION_TYPES.PUBLIC) {
                formattedConversation.lastsendername = conversation.members.reduce(function(carry, member) {
                    if (!carry && lastMessage && member.id == lastMessage.useridfrom) {
                        carry = member.fullname;
                    }
                    return carry;
                }, null);
            }

            return formattedConversation;
        });

        formattedConversations.forEach(function(conversation) {
            if (new Date().toDateString() == new Date(conversation.lastmessagedate * 1000).toDateString()) {
                conversation.istoday = true;
            }
        });

        return Templates.render(TEMPLATES.CONVERSATIONS_LIST, {conversations: formattedConversations});
    };

    /**
     * Build the callback to load conversations.
     *
     * @param  {Array|null} types The conversation types for this section.
     * @param  {bool} includeFavourites Include/exclude favourites.
     * @param  {Number} offset Result offset
     * @return {Function}
     */
    var getLoadCallback = function(types, includeFavourites, offset) {
        // Note: This function is a bit messy because we've added the concept of loading
        // multiple conversations types (e.g. private + self) at once but haven't properly
        // updated the web service to accept an array of types. Instead we've added a new
        // parameter for the self type which means we can only ever load self + other type.
        // This should be improved to make it more extensible in the future. Adding new params
        // for each type isn't very scalable.
        var type = null;
        // Include self conversations in the results by default.
        var includeSelfConversations = true;
        if (types && types.length) {
            // Just get the conversation types that aren't "self" for now.
            var nonSelfConversationTypes = types.filter(function(candidate) {
                return candidate != MessageDrawerViewConversationContants.CONVERSATION_TYPES.SELF;
            });
            // If we're specifically asking for a list of types that doesn't include the self
            // conversations then we don't need to include them.
            includeSelfConversations = types.length != nonSelfConversationTypes.length;
            // As mentioned above the webservice is currently limited to loading one type at a
            // time (plus self conversations) so let's hope we never change this.
            type = nonSelfConversationTypes[0];
        }

        return function(root, userId) {
            return MessageRepository.getConversations(
                    userId,
                    type,
                    LOAD_LIMIT + 1,
                    offset,
                    includeFavourites,
                    includeSelfConversations
                )
                .then(function(response) {
                    var conversations = response.conversations;

                    if (conversations.length > LOAD_LIMIT) {
                        conversations = conversations.slice(0, -1);
                    } else {
                        LazyLoadList.setLoadedAll(root, true);
                    }

                    offset = offset + LOAD_LIMIT;

                    conversations.forEach(function(conversation) {
                        loadedConversationsById[conversation.id] = conversation;
                    });

                    return conversations;
                })
                .catch(Notification.exception);
        };
    };

    /**
     * Get the total count container element.
     *
     * @param  {Object} root Overview messages container element.
     * @return {Object} Total count container element.
     */
    var getTotalConversationCountElement = function(root) {
        return root.find(SELECTORS.SECTION_TOTAL_COUNT);
    };

    /**
     * Get the unread conversations count container element.
     *
     * @param  {Object} root Overview messages container element.
     * @return {Object} Unread conversations count container element.
     */
    var getTotalUnreadConversationCountElement = function(root) {
        return root.find(SELECTORS.SECTION_UNREAD_COUNT);
    };

    /**
     * Increment the total conversations count.
     *
     * @param  {Object} root Overview messages container element.
     */
    var incrementTotalConversationCount = function(root) {
        if (loadedTotalCounts) {
            var element = getTotalConversationCountElement(root);
            var count = parseInt(element.text());
            count = count + 1;
            element.text(count);
        }
    };

    /**
     * Decrement the total conversations count.
     *
     * @param  {Object} root Overview messages container element.
     */
    var decrementTotalConversationCount = function(root) {
        if (loadedTotalCounts) {
            var element = getTotalConversationCountElement(root);
            var count = parseInt(element.text());
            count = count - 1;
            element.text(count);
        }
    };

    /**
     * Decrement the total unread conversations count.
     *
     * @param  {Object} root Overview messages container element.
     */
    var decrementTotalUnreadConversationCount = function(root) {
        if (loadedUnreadCounts) {
            var element = getTotalUnreadConversationCountElement(root);
            var count = parseInt(element.text());
            count = count - 1;
            element.text(count);

            if (count < 1) {
                element.addClass('hidden');
            }
        }
    };

    /**
     * Get a contact / conversation element.
     *
     * @param  {Object} root Overview messages container element.
     * @param  {Number} conversationId The conversation id.
     * @return {Object} Conversation element.
     */
    var getConversationElement = function(root, conversationId) {
        return root.find('[data-conversation-id="' + conversationId + '"]');
    };

    /**
     * Get a contact / conversation element from a user id.
     *
     * @param  {Object} root Overview messages container element.
     * @param  {Number} userId The user id.
     * @return {Object} Conversation element.
     */
    var getConversationElementFromUserId = function(root, userId) {
        return root.find('[data-user-id="' + userId + '"]');
    };

    /**
     * Show the conversation is muted icon.
     *
     * @param  {Object} conversationElement The conversation element.
     */
    var muteConversation = function(conversationElement) {
        conversationElement.find(SELECTORS.MUTED_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the conversation is muted icon.
     *
     * @param  {Object} conversationElement The conversation element.
     */
    var unmuteConversation = function(conversationElement) {
        conversationElement.find(SELECTORS.MUTED_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Show the contact is blocked icon.
     *
     * @param  {Object} conversationElement The conversation element.
     */
    var blockContact = function(conversationElement) {
        conversationElement.find(SELECTORS.BLOCKED_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the contact is blocked icon.
     *
     * @param  {Object} conversationElement The conversation element.
     */
    var unblockContact = function(conversationElement) {
        conversationElement.find(SELECTORS.BLOCKED_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Create an render new conversation element in the list of conversations.
     *
     * @param  {Object} root Overview messages container element.
     * @param  {Object} conversation The conversation.
     * @param  {Number} userId The logged in user id.
     * @return {Object} jQuery promise
     */
    var createNewConversationFromEvent = function(root, conversation, userId) {
        var existingConversations = root.find(SELECTORS.CONVERSATION);

        if (!existingConversations.length) {
            // If we didn't have any conversations then we need to show
            // the content of the list and hide the empty message.
            var listRoot = LazyLoadList.getRoot(root);
            LazyLoadList.showContent(listRoot);
            LazyLoadList.hideEmptyMessage(listRoot);
        }

        // Cache the conversation.
        loadedConversationsById[conversation.id] = conversation;

        return render([conversation], userId)
            .then(function(html) {
                var contentContainer = LazyLoadList.getContentContainer(root);
                return contentContainer.prepend(html);
            })
            .then(function() {
                return incrementTotalConversationCount(root);
            })
            .catch(Notification.exception);
    };

    /**
     * Delete a conversation from the list of conversations.
     *
     * @param  {Object} root Overview messages container element.
     * @param  {Object} conversationElement The conversation element.
     */
    var deleteConversation = function(root, conversationElement) {
        conversationElement.remove();
        decrementTotalConversationCount(root);

        var conversations = root.find(SELECTORS.CONVERSATION);
        if (!conversations.length) {
            // If we don't have any conversations then we need to hide
            // the content of the list and show the empty message.
            var listRoot = LazyLoadList.getRoot(root);
            LazyLoadList.hideContent(listRoot);
            LazyLoadList.showEmptyMessage(listRoot);
        }
    };

    /**
     * Mark a conversation as read.
     *
     * @param  {Object} root Overview messages container element.
     * @param  {Object} conversationElement The conversation element.
     */
    var markConversationAsRead = function(root, conversationElement) {
        var unreadCount = conversationElement.find(SELECTORS.UNREAD_COUNT);
        unreadCount.text('0');
        unreadCount.addClass('hidden');
        decrementTotalUnreadConversationCount(root);
    };

    /**
     * Listen to, and handle events in this section.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} root The section container element.
     * @param {Function} loadCallback The callback to load items.
     * @param {Array|null} types The conversation types for this section
     * @param {bool} includeFavourites If this section includes favourites
     * @param {String} fromPanel Routing argument to send if the section is loaded in message index left panel.
     */
    var registerEventListeners = function(namespace, root, loadCallback, types, includeFavourites, fromPanel) {
        var listRoot = LazyLoadList.getRoot(root);
        var conversationBelongsToThisSection = function(conversation) {
            // Make sure the type is an int so that the index of check matches correctly.
            var conversationType = parseInt(conversation.type, 10);
            if (
                // If the conversation type isn't one this section cares about then we can ignore it.
                (types && types.indexOf(conversationType) < 0) ||
                // If this is the favourites section and the conversation isn't a favourite then ignore it.
                (includeFavourites && !conversation.isFavourite) ||
                // If this section doesn't include favourites and the conversation is a favourite then ignore it.
                (!includeFavourites && conversation.isFavourite)
            ) {
                return false;
            }

            return true;
        };

        // Set the minimum height of the section to the height of the toggle. This
        // smooths out the collapse animation.
        var toggle = root.find(SELECTORS.TOGGLE);
        root.css('min-height', toggle.outerHeight());

        root.on('show.bs.collapse', function() {
            setExpanded(root);
            LazyLoadList.show(listRoot, loadCallback, function(contentContainer, conversations, userId) {
                return render(conversations, userId)
                    .then(function(html) {
                        contentContainer.append(html);
                        return html;
                    })
                    .catch(Notification.exception);
            });
        });

        root.on('hidden.bs.collapse', function() {
            setCollapsed(root);
        });

        PubSub.subscribe(MessageDrawerEvents.CONTACT_BLOCKED, function(userId) {
            var conversationElement = getConversationElementFromUserId(root, userId);
            if (conversationElement.length) {
                blockContact(conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONTACT_UNBLOCKED, function(userId) {
            var conversationElement = getConversationElementFromUserId(root, userId);

            if (conversationElement.length) {
                unblockContact(conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_SET_MUTED, function(conversation) {
            var conversationId = conversation.id;
            var conversationElement = getConversationElement(root, conversationId);
            if (conversationElement.length) {
                muteConversation(conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_UNSET_MUTED, function(conversation) {
            var conversationId = conversation.id;
            var conversationElement = getConversationElement(root, conversationId);
            if (conversationElement.length) {
                unmuteConversation(conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, function(conversation) {
            if (!conversationBelongsToThisSection(conversation)) {
                return;
            }

            var loggedInUserId = conversation.loggedInUserId;
            var conversationId = conversation.id;
            var element = getConversationElement(root, conversationId);
            conversation = formatConversationFromEvent(conversation);
            if (element.length) {
                var contentContainer = LazyLoadList.getContentContainer(root);
                render([conversation], loggedInUserId)
                    .then(function(html) {
                            contentContainer.prepend(html);
                            element.remove();
                            return html;
                        })
                    .catch(Notification.exception);
            } else {
                createNewConversationFromEvent(root, conversation, loggedInUserId);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_DELETED, function(conversationId) {
            var conversationElement = getConversationElement(root, conversationId);
            delete loadedConversationsById[conversationId];
            if (conversationElement.length) {
                deleteConversation(root, conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_READ, function(conversationId) {
            var conversationElement = getConversationElement(root, conversationId);
            if (conversationElement.length) {
                markConversationAsRead(root, conversationElement);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_SET_FAVOURITE, function(conversation) {
            var conversationElement = null;
            if (conversationBelongsToThisSection(conversation)) {
                conversationElement = getConversationElement(root, conversation.id);
                if (!conversationElement.length) {
                    createNewConversationFromEvent(
                        root,
                        formatConversationFromEvent(conversation),
                        conversation.loggedInUserId
                    );
                }
            } else {
                conversationElement = getConversationElement(root, conversation.id);
                if (conversationElement.length) {
                    deleteConversation(root, conversationElement);
                }
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_UNSET_FAVOURITE, function(conversation) {
            var conversationElement = null;
            if (conversationBelongsToThisSection(conversation)) {
                conversationElement = getConversationElement(root, conversation.id);
                if (!conversationElement.length) {
                    createNewConversationFromEvent(
                        root,
                        formatConversationFromEvent(conversation),
                        conversation.loggedInUserId
                    );
                }
            } else {
                conversationElement = getConversationElement(root, conversation.id);
                if (conversationElement.length) {
                    deleteConversation(root, conversationElement);
                }
            }
        });

        CustomEvents.define(root, [CustomEvents.events.activate]);
        root.on(CustomEvents.events.activate, SELECTORS.CONVERSATION, function(e, data) {
            var conversationElement = $(e.target).closest(SELECTORS.CONVERSATION);
            var conversationId = conversationElement.attr('data-conversation-id');
            var conversation = loadedConversationsById[conversationId];
            MessageDrawerRouter.go(namespace, MessageDrawerRoutes.VIEW_CONVERSATION, conversation, fromPanel);

            data.originalEvent.preventDefault();
        });
    };

    /**
     * Setup the section.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} header The header container element.
     * @param {Object} body The section container element.
     * @param {Object} footer The footer container element.
     * @param {Array} types The conversation types that show in this section
     * @param {bool} includeFavourites If this section includes favourites
     * @param {Object} totalCountPromise Resolves wth the total conversations count
     * @param {Object} unreadCountPromise Resolves wth the unread conversations count
     * @param {bool} fromPanel shown in message app panel.
     */
    var show = function(namespace, header, body, footer, types, includeFavourites, totalCountPromise, unreadCountPromise,
        fromPanel) {
        var root = $(body);

        if (!root.attr('data-init')) {
            var loadCallback = getLoadCallback(types, includeFavourites, 0);
            registerEventListeners(namespace, root, loadCallback, types, includeFavourites, fromPanel);

            if (isVisible(root)) {
                setExpanded(root);
                var listRoot = LazyLoadList.getRoot(root);
                LazyLoadList.show(listRoot, loadCallback, function(contentContainer, conversations, userId) {
                    return render(conversations, userId)
                        .then(function(html) {
                            contentContainer.append(html);
                            return html;
                        })
                        .catch(Notification.exception);
                });
            }

            // This is given to us by the calling code because the total counts for all sections
            // are loaded in a single ajax request rather than one request per section.
            totalCountPromise.then(function(count) {
                renderTotalCount(root, count);
                loadedTotalCounts = true;
                return;
            })
            .catch(function() {
                // Silently ignore if we can't updated the counts. No need to bother the user.
            });

            // This is given to us by the calling code because the unread counts for all sections
            // are loaded in a single ajax request rather than one request per section.
            unreadCountPromise.then(function(count) {
                renderUnreadCount(root, count);
                loadedUnreadCounts = true;
                return;
            })
            .catch(function() {
                // Silently ignore if we can't updated the counts. No need to bother the user.
            });

            root.attr('data-init', true);
        }
    };

    return {
        show: show,
        isVisible: isVisible
    };
});
