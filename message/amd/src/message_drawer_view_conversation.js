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
 * Controls the conversation page in the message drawer.
 *
 * This function handles all of the user actions that the user can take
 * when interacting with the conversation page.
 *
 * It maintains a view state which is a data representation of the view
 * and only operates on that data.
 *
 * The view state is immutable and should never be modified directly. Instead
 * all changes to the view state should be done using the StateManager which
 * will generate a new version of the view state with the requested changes.
 *
 * After any changes to the view state the module will call the render function
 * to ask the renderer to update the UI.
 *
 * General rules for this module:
 * 1.) Never modify viewState directly. All changes should be via the StateManager.
 * 2.) Call render() with the new state when you want to update the UI
 * 3.) Never modify the UI directly in this module. This module is only concerned
 *     with the data in the view state.
 *
 * The general flow for a user interaction will be something like:
 * User interaction: User clicks "confirm block" button to block the other user
 *      1.) This module is hears the click
 *      2.) This module sends a request to the server to block the user
 *      3.) The server responds with the new user profile
 *      4.) This module generates a new state using the StateManager with the updated
 *          user profile.
 *      5.) This module asks the Patcher to generate a patch from the current state and
 *          the newly generated state. This patch tells the renderer what has changed
 *          between the states.
 *      6.) This module gives the Renderer the generated patch. The renderer updates
 *          the UI with changes according to the patch.
 *
 * @module     core_message/message_drawer_view_conversation
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/auto_rows',
    'core/backoff_timer',
    'core/custom_interaction_events',
    'core/notification',
    'core/pubsub',
    'core/str',
    'core_message/message_repository',
    'core_message/message_drawer_events',
    'core_message/message_drawer_view_conversation_constants',
    'core_message/message_drawer_view_conversation_patcher',
    'core_message/message_drawer_view_conversation_renderer',
    'core_message/message_drawer_view_conversation_state_manager',
    'core_message/message_drawer_router',
    'core_message/message_drawer_routes',
],
function(
    $,
    AutoRows,
    BackOffTimer,
    CustomEvents,
    Notification,
    PubSub,
    Str,
    Repository,
    MessageDrawerEvents,
    Constants,
    Patcher,
    Renderer,
    StateManager,
    MessageDrawerRouter,
    MessageDrawerRoutes
) {

    // Contains a cache of all view states that have been loaded so far
    // which saves us having to reload stuff with network requests when
    // switching between conversations.
    var stateCache = {};
    // The current data representation of the view.
    var viewState = null;
    var loadedAllMessages = false;
    var messagesOffset = 0;
    var newMessagesPollTimer = null;
    // If the UI is currently resetting.
    var isResetting = true;
    // If the UI is currently sending a message.
    var isSendingMessage = false;
    // This is the render function which will be generated when this module is
    // first called. See generateRenderFunction for details.
    var render = null;

    var NEWEST_FIRST = Constants.NEWEST_MESSAGES_FIRST;
    var LOAD_MESSAGE_LIMIT = Constants.LOAD_MESSAGE_LIMIT;
    var INITIAL_NEW_MESSAGE_POLL_TIMEOUT = Constants.INITIAL_NEW_MESSAGE_POLL_TIMEOUT;
    var SELECTORS = Constants.SELECTORS;
    var CONVERSATION_TYPES = Constants.CONVERSATION_TYPES;

    /**
     * Get the other user userid.
     *
     * @return {Number} Userid.
     */
    var getOtherUserId = function() {
        if (!viewState || viewState.type != CONVERSATION_TYPES.PRIVATE) {
            return null;
        }

        var loggedInUserId = viewState.loggedInUserId;
        var otherUserIds = Object.keys(viewState.members).filter(function(userId) {
            return loggedInUserId != userId;
        });

        return otherUserIds.length ? otherUserIds[0] : null;
    };

    /**
     * Search the cache to see if we've already loaded a private conversation
     * with the given user id.
     *
     * @param {Number} userId The id of the other user.
     * @return {Number|null} Conversation id.
     */
    var getCachedPrivateConversationIdFromUserId = function(userId) {
        return Object.keys(stateCache).reduce(function(carry, id) {
            if (!carry) {
                var state = stateCache[id].state;

                if (state.type == CONVERSATION_TYPES.PRIVATE) {
                    if (userId in state.members) {
                        // We've found a cached conversation for this user!
                        carry = state.id;
                    }
                }
            }

            return carry;
        }, null);
    };

    /**
     * Get profile info for logged in user.
     *
     * @param {Object} body Conversation body container element.
     * @return {Object}
     */
    var getLoggedInUserProfile = function(body) {
        return {
            id: parseInt(body.attr('data-user-id'), 10),
            fullname: null,
            profileimageurl: null,
            profileimageurlsmall: null,
            isonline:  null,
            showonlinestatus: null,
            isblocked: null,
            iscontact: null,
            isdeleted: null,
            canmessage:  null,
            requirescontact: null,
            contactrequests: []
        };
    };

    /**
     * Get the messages offset value to load more messages.
     *
     * @return {Number}
     */
    var getMessagesOffset = function() {
        return messagesOffset;
    };

    /**
     * Set the messages offset value for loading more messages.
     *
     * @param {Number} value The offset value
     */
    var setMessagesOffset = function(value) {
        messagesOffset = value;
        stateCache[viewState.id].messagesOffset = value;
    };

    /**
     * Check if all messages have been loaded.
     *
     * @return {Bool}
     */
    var hasLoadedAllMessages = function() {
        return loadedAllMessages;
    };

    /**
     * Set whether all messages have been loaded or not.
     *
     * @param {Bool} value If all messages have been loaded.
     */
    var setLoadedAllMessages = function(value) {
        loadedAllMessages = value;
        stateCache[viewState.id].loadedAllMessages = value;
    };

    /**
     * Get the messages container element.
     *
     * @param  {Object} body Conversation body container element.
     * @return {Object} The messages container element.
     */
    var getMessagesContainer = function(body) {
        return body.find(SELECTORS.MESSAGES_CONTAINER);
    };

    /**
     * Reformat the conversation for an event payload.
     *
     * @param  {Object} state The view state.
     * @return {Object} New formatted conversation.
     */
    var formatConversationForEvent = function(state) {
        return {
            id: state.id,
            name: state.name,
            subname: state.subname,
            imageUrl: state.imageUrl,
            isFavourite: state.isFavourite,
            type: state.type,
            totalMemberCount: state.totalMemberCount,
            loggedInUserId: state.loggedInUserId,
            messages: state.messages.map(function(message) {
                return $.extend({}, message);
            }),
            members: Object.keys(state.members).reduce(function(carry, id) {
                carry[id] = $.extend({}, state.members[id]);
                carry[id].contactrequests = state.members[id].contactrequests.map(function(request) {
                    return $.extend({}, request);
                });
                return carry;
            }, {})
        };
    };

    /**
     * Load up an empty private conversation between the logged in user and the
     * other user. Sets all of the conversation details based on the other user.
     *
     * A conversation isn't created until the user sends the first message.
     *
     * @param  {Object} loggedInUserProfile The logged in user profile.
     * @param  {Number} otherUserId The other user id.
     * @return {Object} Profile returned from repository.
     */
    var loadEmptyPrivateConversation = function(loggedInUserProfile, otherUserId) {
        var loggedInUserId = loggedInUserProfile.id;
        var newState = StateManager.setLoadingMembers(viewState, true);
        newState = StateManager.setLoadingMessages(newState, true);
        return render(newState)
            .then(function() {
                return Repository.getMemberInfo(loggedInUserId, [otherUserId], true, true);
            })
            .then(function(profiles) {
                if (profiles.length) {
                    return profiles[0];
                } else {
                    throw new Error('Unable to load other user profile');
                }
            })
            .then(function(profile) {
                var newState = StateManager.addMembers(viewState, [profile, loggedInUserProfile]);
                newState = StateManager.setLoadingMembers(newState, false);
                newState = StateManager.setLoadingMessages(newState, false);
                newState = StateManager.setName(newState, profile.fullname);
                newState = StateManager.setType(newState, 1);
                newState = StateManager.setImageUrl(newState, profile.profileimageurl);
                newState = StateManager.setTotalMemberCount(newState, 2);
                return render(newState)
                    .then(function() {
                        return profile;
                    });
            })
            .catch(function(error) {
                var newState = StateManager.setLoadingMembers(viewState, false);
                render(newState);
                Notification.exception(error);
            });
    };

    /**
     * Create a new state from a conversation object.
     *
     * @param {Object} conversation The conversation object.
     * @param {Number} loggedInUserId The logged in user id.
     * @return {Object} new state.
     */
    var updateStateFromConversation = function(conversation, loggedInUserId) {
        var otherUsers = conversation.members.filter(function(member) {
            return member.id != loggedInUserId;
        });
        var otherUser = otherUsers.length ? otherUsers[0] : null;
        var name = conversation.name;
        var imageUrl = conversation.imageurl;

        if (conversation.type == CONVERSATION_TYPES.PRIVATE) {
            name = name || otherUser ? otherUser.fullname : '';
            imageUrl = imageUrl || otherUser ? otherUser.profileimageurl : '';
        }

        var newState = StateManager.addMembers(viewState, conversation.members);
        newState = StateManager.setName(newState, name);
        newState = StateManager.setSubname(newState, conversation.subname);
        newState = StateManager.setType(newState, conversation.type);
        newState = StateManager.setImageUrl(newState, imageUrl);
        newState = StateManager.setTotalMemberCount(newState, conversation.membercount);
        newState = StateManager.setIsFavourite(newState, conversation.isfavourite);
        newState = StateManager.addMessages(newState, conversation.messages);
        return newState;
    };

    /**
     * Get the details for a conversation from the conversation id.
     *
     * @param  {Number} conversationId The conversation id.
     * @param  {Object} loggedInUserProfile The logged in user profile.
     * @param  {Number} messageLimit The number of messages to include.
     * @param  {Number} messageOffset The number of messages to skip.
     * @param  {Bool} newestFirst Order messages newest first.
     * @return {Object} Promise resolved when loaded.
     */
    var loadNewConversation = function(
        conversationId,
        loggedInUserProfile,
        messageLimit,
        messageOffset,
        newestFirst
    ) {
        var loggedInUserId = loggedInUserProfile.id;
        var newState = StateManager.setLoadingMembers(viewState, true);
        newState = StateManager.setLoadingMessages(newState, true);
        return render(newState)
            .then(function() {
                return Repository.getConversation(
                    loggedInUserId,
                    conversationId,
                    true,
                    true,
                    0,
                    0,
                    messageLimit + 1,
                    messageOffset,
                    newestFirst
                );
            })
            .then(function(conversation) {
                if (conversation.messages.length > messageLimit) {
                    conversation.messages = conversation.messages.slice(1);
                } else {
                    setLoadedAllMessages(true);
                }

                setMessagesOffset(messageOffset + messageLimit);

                return conversation;
            })
            .then(function(conversation) {
                var hasLoggedInUser = conversation.members.filter(function(member) {
                    return member.id == loggedInUserProfile.id;
                });

                if (hasLoggedInUser.length < 1) {
                    conversation.members = conversation.members.concat([loggedInUserProfile]);
                }

                var newState = updateStateFromConversation(conversation, loggedInUserProfile.id);
                newState = StateManager.setLoadingMembers(newState, false);
                newState = StateManager.setLoadingMessages(newState, false);
                return render(newState)
                    .then(function() {
                        return conversation;
                    });
            })
            .then(function() {
                return markConversationAsRead(conversationId);
            })
            .catch(function(error) {
                var newState = StateManager.setLoadingMembers(viewState, false);
                newState = StateManager.setLoadingMessages(newState, false);
                render(newState);
                Notification.exception(error);
            });
    };

    /**
     * Get the details for a conversation from and existing conversation object.
     *
     * @param  {Object} conversation The conversation object.
     * @param  {Object} loggedInUserProfile The logged in user profile.
     * @param  {Number} messageLimit The number of messages to include.
     * @param  {Bool} newestFirst Order messages newest first.
     * @return {Object} Promise resolved when loaded.
     */
    var loadExistingConversation = function(
        conversation,
        loggedInUserProfile,
        messageLimit,
        newestFirst
    ) {
        var hasLoggedInUser = conversation.members.filter(function(member) {
            return member.id == loggedInUserProfile.id;
        });

        if (hasLoggedInUser.length < 1) {
            conversation.members = conversation.members.concat([loggedInUserProfile]);
        }

        var newState = updateStateFromConversation(conversation, loggedInUserProfile.id);
        newState = StateManager.setLoadingMembers(newState, false);
        newState = StateManager.setLoadingMessages(newState, true);
        var messageCount = conversation.messages.length;
        return render(newState)
            .then(function() {
                if (messageCount < messageLimit) {
                    // We haven't got enough messages so let's load some more.
                    return loadMessages(conversation.id, messageLimit, messageCount, newestFirst, [])
                        .then(function(result) {
                            // Give the list of messages to the next handler.
                            return result.messages;
                        });
                } else {
                    // We've got enough messages. No need to load any more for now.
                    var newState = StateManager.setLoadingMessages(viewState, false);
                    return render(newState)
                        .then(function() {
                            // Give the list of messages to the next handler.
                            return conversation.messages;
                        });
                }
            })
            .then(function(messages) {
                // Update the offset to reflect the number of messages we've loaded.
                setMessagesOffset(messages.length);
                return messages;
            })
            .then(function() {
                return markConversationAsRead(conversation.id);
            })
            .catch(Notification.exception);
    };

    /**
     * Load messages for this conversation and pass them to the renderer.
     *
     * @param  {Number} conversationId Conversation id.
     * @param  {Number} limit Number of messages to load.
     * @param  {Number} offset Get messages from offset.
     * @param  {Bool} newestFirst Get newest messages first.
     * @param  {Array} ignoreList Ignore any messages with ids in this list.
     * @param  {Number|null} timeFrom Only get messages from this time onwards.
     * @return {Promise} renderer promise.
     */
    var loadMessages = function(conversationId, limit, offset, newestFirst, ignoreList, timeFrom) {
        return Repository.getMessages(
                viewState.loggedInUserId,
                conversationId,
                limit ? limit + 1 : limit,
                offset,
                newestFirst,
                timeFrom
            )
            .then(function(result) {
                if (result.messages.length && ignoreList.length) {
                    result.messages = result.messages.filter(function(message) {
                        // Skip any messages in our ignore list.
                        return ignoreList.indexOf(parseInt(message.id, 10)) < 0;
                    });
                }

                return result;
            })
            .then(function(result) {
                if (!limit) {
                    return result;
                } else if (result.messages.length > limit) {
                    // Ignore the last result which was just to test if there are more
                    // to load.
                    result.messages = result.messages.slice(0, -1);
                } else {
                    setLoadedAllMessages(true);
                }

                return result;
            })
            .then(function(result) {
                var membersToAdd = result.members.filter(function(member) {
                    return !(member.id in viewState.members);
                });
                var newState = StateManager.addMembers(viewState, membersToAdd);
                newState = StateManager.addMessages(newState, result.messages);
                newState = StateManager.setLoadingMessages(newState, false);
                return render(newState)
                    .then(function() {
                        return result;
                    });
            })
            .catch(function(error) {
                var newState = StateManager.setLoadingMessages(viewState, false);
                render(newState);
                // Re-throw the error for other error handlers.
                throw error;
            });
    };

    /**
     * Create a callback function for getting new messages for this conversation.
     *
     * @param  {Number} conversationId Conversation id.
     * @param  {Bool} newestFirst Show newest messages first
     * @return {Function} Callback function that returns a renderer promise.
     */
    var getLoadNewMessagesCallback = function(conversationId, newestFirst) {
        return function() {
            var messages = viewState.messages;
            var mostRecentMessage = messages.length ? messages[messages.length - 1] : null;

            if (mostRecentMessage && !isResetting && !isSendingMessage) {
                // There may be multiple messages with the same time created value since
                // the accuracy is only down to the second. The server will include these
                // messages in the result (since it does a >= comparison on time from) so
                // we need to filter them back out of the result so that we're left only
                // with the new messages.
                var ignoreMessageIds = [];
                for (var i = messages.length - 1; i >= 0; i--) {
                    var message = messages[i];
                    if (message.timeCreated === mostRecentMessage.timeCreated) {
                        ignoreMessageIds.push(message.id);
                    } else {
                        // Since the messages are ordered in ascending order of time created
                        // we can break as soon as we hit a message with a different time created
                        // because we know all other messages will have lower values.
                        break;
                    }
                }

                return loadMessages(
                        conversationId,
                        0,
                        0,
                        newestFirst,
                        ignoreMessageIds,
                        mostRecentMessage.timeCreated
                    )
                    .then(function(result) {
                        if (result.messages.length) {
                            // If we found some results then restart the polling timer
                            // because the other user might be sending messages.
                            newMessagesPollTimer.restart();
                            // We've also got a new last message so publish that for other
                            // components to update.
                            var conversation = formatConversationForEvent(viewState);
                            PubSub.publish(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, conversation);
                            return markConversationAsRead(conversationId);
                        } else {
                            return result;
                        }
                    });
            }

            return $.Deferred().resolve().promise();
        };
    };

    /**
     * Mark a conversation as read.
     *
     * @param  {Number} conversationId The conversation id.
     * @return {Promise} The renderer promise.
     */
    var markConversationAsRead = function(conversationId) {
        var loggedInUserId = viewState.loggedInUserId;

        return Repository.markAllConversationMessagesAsRead(loggedInUserId, conversationId)
            .then(function() {
                var newState = StateManager.markMessagesAsRead(viewState, viewState.messages);
                PubSub.publish(MessageDrawerEvents.CONVERSATION_READ, conversationId);
                return render(newState);
            });
    };

    /**
     * Tell the statemanager there is request to block a user and run the renderer
     * to show the block user dialogue.
     *
     * @param  {Number} userId User id.
     * @return {Promise} Renderer promise.
     */
    var requestBlockUser = function(userId) {
        return cancelRequest(userId).then(function() {
            var newState = StateManager.addPendingBlockUsersById(viewState, [userId]);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to block a user, update the statemanager and publish
     * a contact has been blocked.
     *
     * @param  {Number} userId User id of user to block.
     * @return {Promise} Renderer promise.
     */
    var blockUser = function(userId) {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.blockUser(viewState.loggedInUserId, userId);
            })
            .then(function(profile) {
                var newState = StateManager.addMembers(viewState, [profile]);
                newState = StateManager.removePendingBlockUsersById(newState, [userId]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                PubSub.publish(MessageDrawerEvents.CONTACT_BLOCKED, userId);
                return render(newState);
            });
    };

    /**
     * Tell the statemanager there is a request to unblock a user and run the renderer
     * to show the unblock user dialogue.
     *
     * @param  {Number} userId User id of user to unblock.
     * @return {Promise} Renderer promise.
     */
    var requestUnblockUser = function(userId) {
        return cancelRequest(userId).then(function() {
            var newState = StateManager.addPendingUnblockUsersById(viewState, [userId]);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to unblock a user, update the statemanager and publish
     * a contact has been unblocked.
     *
     * @param  {Number} userId User id of user to unblock.
     * @return {Promise} Renderer promise.
     */
    var unblockUser = function(userId) {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.unblockUser(viewState.loggedInUserId, userId);
            })
            .then(function(profile) {
                var newState = StateManager.addMembers(viewState, [profile]);
                newState = StateManager.removePendingUnblockUsersById(newState, [userId]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                PubSub.publish(MessageDrawerEvents.CONTACT_UNBLOCKED, userId);
                return render(newState);
            });
    };

    /**
     * Tell the statemanager there is a request to remove a user from the contact list
     * and run the renderer to show the remove user from contacts dialogue.
     *
     * @param  {Number} userId User id of user to remove from contacts.
     * @return {Promise} Renderer promise.
     */
    var requestRemoveContact = function(userId) {
        return cancelRequest(userId).then(function() {
            var newState = StateManager.addPendingRemoveContactsById(viewState, [userId]);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to remove a user from the contacts list. update the statemanager
     * and publish a contact has been removed.
     *
     * @param  {Number} userId User id of user to remove from contacts.
     * @return {Promise} Renderer promise.
     */
    var removeContact = function(userId) {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.deleteContacts(viewState.loggedInUserId, [userId]);
            })
            .then(function(profiles) {
                var newState = StateManager.addMembers(viewState, profiles);
                newState = StateManager.removePendingRemoveContactsById(newState, [userId]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                PubSub.publish(MessageDrawerEvents.CONTACT_REMOVED, userId);
                return render(newState);
            });
    };

    /**
     * Tell the statemanager there is a request to add a user to the contact list
     * and run the renderer to show the add user to contacts dialogue.
     *
     * @param  {Number} userId User id of user to add to contacts.
     * @return {Promise} Renderer promise.
     */
    var requestAddContact = function(userId) {
        return cancelRequest(userId).then(function() {
            var newState = StateManager.addPendingAddContactsById(viewState, [userId]);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to add a user to the contacts list. update the statemanager
     * and publish a contact has been added.
     *
     * @param  {Number} userId User id of user to add to contacts.
     * @return {Promise} Renderer promise.
     */
    var addContact = function(userId) {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.createContactRequest(viewState.loggedInUserId, userId);
            })
            .then(function(response) {
                if (!response.request) {
                    throw new Error(response.warnings[0].message);
                }

                return response.request;
            })
            .then(function(request) {
                var newState = StateManager.removePendingAddContactsById(viewState, [userId]);
                newState = StateManager.addContactRequests(newState, [request]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                return render(newState);
            });
    };

    /**
     * Set the current conversation as a favourite conversation.
     *
     * @return {Promise} Renderer promise.
     */
    var setFavourite = function() {
        var userId = viewState.loggedInUserId;
        var conversationId = viewState.id;

        return Repository.setFavouriteConversations(userId, [conversationId])
            .then(function() {
                var newState = StateManager.setIsFavourite(viewState, true);
                return render(newState);
            })
            .then(function() {
                return PubSub.publish(
                    MessageDrawerEvents.CONVERSATION_SET_FAVOURITE,
                    formatConversationForEvent(viewState)
                );
            });
    };

    /**
     * Unset the current conversation as a favourite conversation.
     *
     * @return {Promise} Renderer promise.
     */
    var unsetFavourite = function() {
        var userId = viewState.loggedInUserId;
        var conversationId = viewState.id;

        return Repository.unsetFavouriteConversations(userId, [conversationId])
            .then(function() {
                var newState = StateManager.setIsFavourite(viewState, false);
                return render(newState);
            })
            .then(function() {
                return PubSub.publish(
                    MessageDrawerEvents.CONVERSATION_UNSET_FAVOURITE,
                    formatConversationForEvent(viewState)
                );
            });
    };

    /**
     * Tell the statemanager there is a request to delete the selected messages
     * and run the renderer to show confirm delete messages dialogue.
     *
     * @param  {Number} userId User id.
     * @return {Promise} Renderer promise.
     */
    var requestDeleteSelectedMessages = function(userId) {
        var selectedMessageIds = viewState.selectedMessageIds;
        return cancelRequest(userId).then(function() {
            var newState = StateManager.addPendingDeleteMessagesById(viewState, selectedMessageIds);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to delete the messages pending deletion. Update the statemanager
     * and publish a message deletion event.
     *
     * @return {Promise} Renderer promise.
     */
    var deleteSelectedMessages = function() {
        var messageIds = viewState.pendingDeleteMessageIds;
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.deleteMessages(viewState.loggedInUserId, messageIds);
            })
            .then(function() {
                var newState = StateManager.removeMessagesById(viewState, messageIds);
                newState = StateManager.removePendingDeleteMessagesById(newState, messageIds);
                newState = StateManager.removeSelectedMessagesById(newState, messageIds);
                newState = StateManager.setLoadingConfirmAction(newState, false);

                var prevLastMessage = viewState.messages[viewState.messages.length - 1];
                var newLastMessage = newState.messages.length ? newState.messages[newState.messages.length - 1] : null;

                if (newLastMessage && newLastMessage.id != prevLastMessage.id) {
                    var conversation = formatConversationForEvent(newState);
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, conversation);
                } else if (!newState.messages.length) {
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_DELETED, newState.id);
                }

                return render(newState);
            });
    };

    /**
     * Tell the statemanager there is a request to delete a conversation
     * and run the renderer to show confirm delete conversation dialogue.
     *
     * @param  {Number} userId User id of other user.
     * @return {Promise} Renderer promise.
     */
    var requestDeleteConversation = function(userId) {
        return cancelRequest(userId).then(function() {
            var newState = StateManager.setPendingDeleteConversation(viewState, true);
            return render(newState);
        });
    };

    /**
     * Send the repository a request to delete a conversation. Update the statemanager
     * and publish a conversation deleted event.
     *
     * @return {Promise} Renderer promise.
     */
    var deleteConversation = function() {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.deleteConversation(viewState.loggedInUserId, viewState.id);
            })
            .then(function() {
                var newState = StateManager.removeMessages(viewState, viewState.messages);
                newState = StateManager.removeSelectedMessagesById(newState, viewState.selectedMessageIds);
                newState = StateManager.setPendingDeleteConversation(newState, false);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                PubSub.publish(MessageDrawerEvents.CONVERSATION_DELETED, newState.id);
                return render(newState);
            });
    };

    /**
     * Tell the statemanager to cancel all pending actions.
     *
     * @param  {Number} userId User id.
     * @return {Promise} Renderer promise.
     */
    var cancelRequest = function(userId) {
        var pendingDeleteMessageIds = viewState.pendingDeleteMessageIds;
        var newState = StateManager.removePendingAddContactsById(viewState, [userId]);
        newState = StateManager.removePendingRemoveContactsById(newState, [userId]);
        newState = StateManager.removePendingUnblockUsersById(newState, [userId]);
        newState = StateManager.removePendingBlockUsersById(newState, [userId]);
        newState = StateManager.removePendingDeleteMessagesById(newState, pendingDeleteMessageIds);
        newState = StateManager.setPendingDeleteConversation(newState, false);
        return render(newState);
    };

    /**
     * Accept the contact request from the given user.
     *
     * @param  {Number} userId User id of other user.
     * @return {Promise} Renderer promise.
     */
    var acceptContactRequest = function(userId) {
        // Search the list of the logged in user's contact requests to find the
        // one from this user.
        var loggedInUserId = viewState.loggedInUserId;
        var requests = viewState.members[userId].contactrequests.filter(function(request) {
            return request.requesteduserid == loggedInUserId;
        });
        var request = requests[0];
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.acceptContactRequest(userId, loggedInUserId);
            })
            .then(function(profile) {
                var newState = StateManager.removeContactRequests(viewState, [request]);
                newState = StateManager.addMembers(viewState, [profile]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                return render(newState);
            })
            .then(function() {
                PubSub.publish(MessageDrawerEvents.CONTACT_ADDED, viewState.members[userId]);
                PubSub.publish(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, request);
                return;
            });
    };

    /**
     * Decline the contact request from the given user.
     *
     * @param  {Number} userId User id of other user.
     * @return {Promise} Renderer promise.
     */
    var declineContactRequest = function(userId) {
        // Search the list of the logged in user's contact requests to find the
        // one from this user.
        var loggedInUserId = viewState.loggedInUserId;
        var requests = viewState.members[userId].contactrequests.filter(function(request) {
            return request.requesteduserid == loggedInUserId;
        });
        var request = requests[0];
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        return render(newState)
            .then(function() {
                return Repository.declineContactRequest(userId, loggedInUserId);
            })
            .then(function(profile) {
                var newState = StateManager.removeContactRequests(viewState, [request]);
                newState = StateManager.addMembers(viewState, [profile]);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                return render(newState);
            })
            .then(function() {
                PubSub.publish(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, request);
                return;
            });
    };

    /**
     * Send a message to the repository, update the statemanager publish a message send event
     * and call the renderer.
     *
     * @param  {Number} conversationId The conversation to send to.
     * @param  {String} text Text to send.
     * @return {Promise} Renderer promise.
     */
    var sendMessage = function(conversationId, text) {
        isSendingMessage = true;
        var newState = StateManager.setSendingMessage(viewState, true);
        var newConversationId = null;
        return render(newState)
            .then(function() {
                if (!conversationId && viewState.type == CONVERSATION_TYPES.PRIVATE) {
                    // If it's a new private conversation then we need to use the old
                    // web service function to create the conversation.
                    var otherUserId = getOtherUserId();
                    return Repository.sendMessageToUser(otherUserId, text)
                        .then(function(message) {
                            newConversationId = parseInt(message.conversationid, 10);
                            return message;
                        });
                } else {
                    return Repository.sendMessageToConversation(conversationId, text);
                }
            })
            .then(function(message) {
                var newState = StateManager.addMessages(viewState, [message]);
                newState = StateManager.setSendingMessage(newState, false);
                var conversation = formatConversationForEvent(newState);

                if (!newState.id) {
                    // If this message created the conversation then save the conversation
                    // id.
                    newState = StateManager.setId(newState, newConversationId);
                    conversation.id = newConversationId;
                    resetMessagePollTimer(newConversationId);
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_CREATED, conversation);
                }

                return render(newState)
                    .then(function() {
                        isSendingMessage = false;
                        PubSub.publish(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, conversation);
                        return;
                    });
            })
            .catch(function(error) {
                isSendingMessage = false;
                var newState = StateManager.setSendingMessage(viewState, false);
                render(newState);
                Notification.exception(error);
            });
    };

    /**
     * Toggle the selected messages update the statemanager and render the result.
     *
     * @param  {Number} messageId The id of the message to be toggled
     * @return {Promise} Renderer promise.
     */
    var toggleSelectMessage = function(messageId) {
        var newState = viewState;

        if (viewState.selectedMessageIds.indexOf(messageId) > -1) {
            newState = StateManager.removeSelectedMessagesById(viewState, [messageId]);
        } else {
            newState = StateManager.addSelectedMessagesById(viewState, [messageId]);
        }

        return render(newState);
    };

    /**
     * Cancel edit mode (selecting the messages).
     *
     * @return {Promise} Renderer promise.
     */
    var cancelEditMode = function() {
        return cancelRequest(getOtherUserId())
            .then(function() {
                var newState = StateManager.removeSelectedMessagesById(viewState, viewState.selectedMessageIds);
                return render(newState);
            });
    };

    /**
     * Create a function to render the Conversation.
     *
     * @param  {Object} header The conversation header container element.
     * @param  {Object} body The conversation body container element.
     * @param  {Object} footer The conversation footer container element.
     * @return {Promise} Renderer promise.
     */
    var generateRenderFunction = function(header, body, footer) {
        return function(newState) {
            var patch = Patcher.buildPatch(viewState, newState);
            // This is a great place to add in some console logging if you need
            // to debug something. You can log the current state, the next state,
            // and the generated patch and see exactly what will be updated.
            return Renderer.render(header, body, footer, patch)
                .then(function() {
                    viewState = newState;
                    if (newState.id) {
                        // Only cache created conversations.
                        stateCache[newState.id] = {
                            state: newState,
                            messagesOffset: getMessagesOffset(),
                            loadedAllMessages: hasLoadedAllMessages()
                        };
                    }
                    return;
                });
        };
    };

    /**
     * Create a confirm action function.
     *
     * @param {Function} actionCallback The callback function.
     * @return {Function} Confirm action handler.
     */
    var generateConfirmActionHandler = function(actionCallback) {
        return function(e, data) {
            if (!viewState.loadingConfirmAction) {
                actionCallback(getOtherUserId())
                    .catch(function(error) {
                        var newState = StateManager.setLoadingConfirmAction(viewState, false);
                        render(newState);
                        Notification.exception(error);
                    });
            }
            data.originalEvent.preventDefault();
        };
    };

    /**
     * Send message event handler.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleSendMessage = function(e, data) {
        var target = $(e.target);
        var footerContainer = target.closest(SELECTORS.FOOTER_CONTAINER);
        var textArea = footerContainer.find(SELECTORS.MESSAGE_TEXT_AREA);
        var text = textArea.val().trim();

        if (text !== '') {
            sendMessage(viewState.id, text);
        }

        data.originalEvent.preventDefault();
    };

    /**
     * Select message event handler.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleSelectMessage = function(e, data) {
        var selection = window.getSelection();
        var target = $(e.target);

        if (selection.toString() != '') {
            // Bail if we're selecting.
            return;
        }

        if (target.is('a')) {
            // Clicking on a link in the message so ignore it.
            return;
        }

        var element = target.closest(SELECTORS.MESSAGE);
        var messageId = parseInt(element.attr('data-message-id'), 10);

        toggleSelectMessage(messageId).catch(Notification.exception);

        data.originalEvent.preventDefault();
    };

    /**
     * Cancel edit mode event handler.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleCancelEditMode = function(e, data) {
        cancelEditMode().catch(Notification.exception);
        data.originalEvent.preventDefault();
    };

    /**
     * Show the view contact page.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleViewContact = function(e, data) {
        var otherUserId = getOtherUserId();
        var otherUser = viewState.members[otherUserId];
        MessageDrawerRouter.go(MessageDrawerRoutes.VIEW_CONTACT, otherUser);
        data.originalEvent.preventDefault();
    };

    /**
     * Set this conversation as a favourite.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleSetFavourite = function(e, data) {
        setFavourite().catch(Notification.exception);
        data.originalEvent.preventDefault();
    };

    /**
     * Unset this conversation as a favourite.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleUnsetFavourite = function(e, data) {
        unsetFavourite().catch(Notification.exception);
        data.originalEvent.preventDefault();
    };

    /**
     * Show the view contact page.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleViewGroupInfo = function(e, data) {
        MessageDrawerRouter.go(
            MessageDrawerRoutes.VIEW_GROUP_INFO,
            {
                id: viewState.id,
                name: viewState.name,
                subname: viewState.subname,
                imageUrl: viewState.imageUrl,
                totalMemberCount: viewState.totalMemberCount
            },
            viewState.loggedInUserId
        );
        data.originalEvent.preventDefault();
    };

    var headerActivateHandlers = [
        [SELECTORS.ACTION_REQUEST_BLOCK, generateConfirmActionHandler(requestBlockUser)],
        [SELECTORS.ACTION_REQUEST_UNBLOCK, generateConfirmActionHandler(requestUnblockUser)],
        [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
        [SELECTORS.ACTION_REQUEST_REMOVE_CONTACT, generateConfirmActionHandler(requestRemoveContact)],
        [SELECTORS.ACTION_REQUEST_DELETE_CONVERSATION, generateConfirmActionHandler(requestDeleteConversation)],
        [SELECTORS.ACTION_CANCEL_EDIT_MODE, handleCancelEditMode],
        [SELECTORS.ACTION_VIEW_CONTACT, handleViewContact],
        [SELECTORS.ACTION_VIEW_GROUP_INFO, handleViewGroupInfo],
        [SELECTORS.ACTION_CONFIRM_FAVOURITE, handleSetFavourite],
        [SELECTORS.ACTION_CONFIRM_UNFAVOURITE, handleUnsetFavourite],
    ];
    var bodyActivateHandlers = [
        [SELECTORS.ACTION_CANCEL_CONFIRM, generateConfirmActionHandler(cancelRequest)],
        [SELECTORS.ACTION_CONFIRM_BLOCK, generateConfirmActionHandler(blockUser)],
        [SELECTORS.ACTION_CONFIRM_UNBLOCK, generateConfirmActionHandler(unblockUser)],
        [SELECTORS.ACTION_CONFIRM_ADD_CONTACT, generateConfirmActionHandler(addContact)],
        [SELECTORS.ACTION_CONFIRM_REMOVE_CONTACT, generateConfirmActionHandler(removeContact)],
        [SELECTORS.ACTION_CONFIRM_DELETE_SELECTED_MESSAGES, generateConfirmActionHandler(deleteSelectedMessages)],
        [SELECTORS.ACTION_CONFIRM_DELETE_CONVERSATION, generateConfirmActionHandler(deleteConversation)],
        [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
        [SELECTORS.ACTION_ACCEPT_CONTACT_REQUEST, generateConfirmActionHandler(acceptContactRequest)],
        [SELECTORS.ACTION_DECLINE_CONTACT_REQUEST, generateConfirmActionHandler(declineContactRequest)],
        [SELECTORS.MESSAGE, handleSelectMessage]
    ];
    var footerActivateHandlers = [
        [SELECTORS.SEND_MESSAGE_BUTTON, handleSendMessage],
        [SELECTORS.ACTION_REQUEST_DELETE_SELECTED_MESSAGES, generateConfirmActionHandler(requestDeleteSelectedMessages)],
        [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
        [SELECTORS.ACTION_REQUEST_UNBLOCK, generateConfirmActionHandler(requestUnblockUser)],
    ];

    /**
     * Listen to, and handle events for conversations.
     *
     * @param {Object} header Conversation header container element.
     * @param {Object} body Conversation body container element.
     * @param {Object} footer Conversation footer container element.
     */
    var registerEventListeners = function(header, body, footer) {
        var isLoadingMoreMessages = false;
        var messagesContainer = getMessagesContainer(body);

        AutoRows.init(footer);

        CustomEvents.define(header, [
            CustomEvents.events.activate
        ]);
        CustomEvents.define(body, [
            CustomEvents.events.activate
        ]);
        CustomEvents.define(footer, [
            CustomEvents.events.activate,
            CustomEvents.events.enter
        ]);
        CustomEvents.define(messagesContainer, [
            CustomEvents.events.scrollTop,
            CustomEvents.events.scrollLock
        ]);

        messagesContainer.on(CustomEvents.events.scrollTop, function(e, data) {
            var hasMembers = Object.keys(viewState.members).length > 1;

            if (!isResetting && !isLoadingMoreMessages && !hasLoadedAllMessages() && hasMembers) {
                isLoadingMoreMessages = true;
                var newState = StateManager.setLoadingMessages(viewState, true);
                render(newState)
                    .then(function() {
                        return loadMessages(viewState.id, LOAD_MESSAGE_LIMIT, getMessagesOffset(), NEWEST_FIRST, []);
                    })
                    .then(function() {
                        isLoadingMoreMessages = false;
                        setMessagesOffset(getMessagesOffset() + LOAD_MESSAGE_LIMIT);
                        return;
                    })
                    .catch(function(error) {
                        isLoadingMoreMessages = false;
                        Notification.exception(error);
                    });
            }

            data.originalEvent.preventDefault();
        });

        headerActivateHandlers.forEach(function(handler) {
            var selector = handler[0];
            var handlerFunction = handler[1];
            header.on(CustomEvents.events.activate, selector, handlerFunction);
        });

        bodyActivateHandlers.forEach(function(handler) {
            var selector = handler[0];
            var handlerFunction = handler[1];
            body.on(CustomEvents.events.activate, selector, handlerFunction);
        });

        footerActivateHandlers.forEach(function(handler) {
            var selector = handler[0];
            var handlerFunction = handler[1];
            footer.on(CustomEvents.events.activate, selector, handlerFunction);
        });

        footer.on(CustomEvents.events.enter, SELECTORS.MESSAGE_TEXT_AREA, function(e, data) {
            var enterToSend = footer.attr('data-enter-to-send');
            if (enterToSend && enterToSend != 'false' && enterToSend != '0') {
                handleSendMessage(e, data);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.ROUTE_CHANGED, function(newRouteData) {
            if (newMessagesPollTimer) {
                if (newRouteData.route != MessageDrawerRoutes.VIEW_CONVERSATION) {
                    newMessagesPollTimer.stop();
                }
            }
        });
    };

    /**
     * Reset the timer that polls for new messages.
     *
     * @param  {Number} conversationId The conversation id
     */
    var resetMessagePollTimer = function(conversationId) {
        if (newMessagesPollTimer) {
            newMessagesPollTimer.stop();
        }

        newMessagesPollTimer = new BackOffTimer(
            getLoadNewMessagesCallback(conversationId, NEWEST_FIRST),
            function(time) {
                if (!time) {
                    return INITIAL_NEW_MESSAGE_POLL_TIMEOUT;
                }

                return time * 2;
            }
        );

        newMessagesPollTimer.start();
    };

    /**
     * Reset the state to the initial state and render the UI.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Number|null} conversationId The conversation id.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     * @return {Promise} Renderer promise.
     */
    var resetState = function(body, conversationId, loggedInUserProfile) {
        var loggedInUserId = loggedInUserProfile.id;
        var midnight = parseInt(body.attr('data-midnight'), 10);
        var initialState = StateManager.buildInitialState(midnight, loggedInUserId, conversationId);

        if (!viewState) {
            viewState = initialState;
        }

        if (newMessagesPollTimer) {
            newMessagesPollTimer.stop();
        }

        return render(initialState);
    };

    /**
     * Load a new empty private conversation between two users.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     * @param  {Int} otherUserId The other user's id.
     * @return {Promise} Renderer promise.
     */
    var resetNoConversation = function(body, loggedInUserProfile, otherUserId) {
        // Always reset the state back to the initial state so that the
        // state manager and patcher can work correctly.
        return resetState(body, null, loggedInUserProfile)
            .then(function() {
                return Repository.getConversationBetweenUsers(
                        loggedInUserProfile.id,
                        otherUserId,
                        true,
                        true,
                        0,
                        0,
                        LOAD_MESSAGE_LIMIT,
                        0,
                        NEWEST_FIRST
                    )
                    .then(function(conversation) {
                        // Looks like we have a conversation after all! Let's use that.
                        return resetByConversation(body, conversation, loggedInUserProfile);
                    })
                    .catch(function() {
                        // Can't find a conversation. Oh well. Just load up a blank one.
                        return loadEmptyPrivateConversation(loggedInUserProfile, otherUserId);
                    });
            });
    };

    /**
     * Load new messages into the conversation based on a time interval.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Number} conversationId The conversation id.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     * @return {Promise} Renderer promise.
     */
    var resetById = function(body, conversationId, loggedInUserProfile) {
        var cache = null;
        if (conversationId in stateCache) {
            cache = stateCache[conversationId];
        }

        // Always reset the state back to the initial state so that the
        // state manager and patcher can work correctly.
        return resetState(body, conversationId, loggedInUserProfile)
            .then(function() {
                if (cache) {
                    // We've seen this conversation before so there is no need to
                    // send any network requests.
                    var newState = cache.state;
                    // Reset some loading states just in case they were left weirdly.
                    newState = StateManager.setLoadingMessages(newState, false);
                    newState = StateManager.setLoadingMembers(newState, false);
                    setMessagesOffset(cache.messagesOffset);
                    setLoadedAllMessages(cache.loadedAllMessages);
                    return render(newState);
                } else {
                    return loadNewConversation(
                        conversationId,
                        loggedInUserProfile,
                        LOAD_MESSAGE_LIMIT,
                        0,
                        NEWEST_FIRST
                    );
                }
            })
            .then(function() {
                return resetMessagePollTimer(conversationId);
            });
    };

    /**
     * Load new messages into the conversation based on a time interval.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Object} conversation The conversation.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     * @return {Promise} Renderer promise.
     */
    var resetByConversation = function(body, conversation, loggedInUserProfile) {
        var cache = null;
        if (conversation.id in stateCache) {
            cache = stateCache[conversation.id];
        }

        // Always reset the state back to the initial state so that the
        // state manager and patcher can work correctly.
        return resetState(body, conversation.id, loggedInUserProfile)
            .then(function() {
                if (cache) {
                    // We've seen this conversation before so there is no need to
                    // send any network requests.
                    var newState = cache.state;
                    // Reset some loading states just in case they were left weirdly.
                    newState = StateManager.setLoadingMessages(newState, false);
                    newState = StateManager.setLoadingMembers(newState, false);
                    setMessagesOffset(cache.messagesOffset);
                    setLoadedAllMessages(cache.loadedAllMessages);
                    return render(newState);
                } else {
                    return loadExistingConversation(
                        conversation,
                        loggedInUserProfile,
                        LOAD_MESSAGE_LIMIT,
                        NEWEST_FIRST
                    );
                }
            })
            .then(function() {
                return resetMessagePollTimer(conversation.id);
            });
    };

    /**
     * Setup the conversation page. This is a rather complex function because there are a
     * few combinations of arguments that can be provided to this function to show the
     * conversation.
     *
     * There are:
     * 1.) A conversation object with no action or other user id (e.g. from the overview page)
     * 2.) A conversation id with no action or other user id (e.g. from the contacts page)
     * 3.) No conversation/id with an action and other other user id. (e.g. from contact page)
     *
     * @param {Object} header Conversation header container element.
     * @param {Object} body Conversation body container element.
     * @param {Object} footer Conversation footer container element.
     * @param {Object|Number|null} conversationOrId Conversation or id or null
     * @param {String} action An action to take on the conversation
     * @param {Number} otherUserId The other user id for a private conversation
     * @return {Object} jQuery promise
     */
    var show = function(header, body, footer, conversationOrId, action, otherUserId) {
        var conversation = null;
        var conversationId = null;

        // Check what we were given to identify the conversation.
        if (conversationOrId && conversationOrId !== null && typeof conversationOrId == 'object') {
            conversation = conversationOrId;
            conversationId = parseInt(conversation.id, 10);
        } else {
            conversation = null;
            conversationId = parseInt(conversationOrId, 10);
            conversationId = isNaN(conversationId) ? null : conversationId;
        }

        if (!conversationId && action && otherUserId) {
            // If we didn't get a conversation id got a user id then let's see if we've
            // previously loaded a private conversation with this user.
            conversationId = getCachedPrivateConversationIdFromUserId(otherUserId);
        }

        if (!body.attr('data-init')) {
            // Generate the render function to bind the header, body, and footer
            // elements to it so that we don't need to pass them around this module.
            render = generateRenderFunction(header, body, footer);
            registerEventListeners(header, body, footer);
            body.attr('data-init', true);
        }

        // This is a new conversation if:
        // 1. We don't already have a state
        // 2. The given conversation doesn't match the one currently loaded
        // 3. We have a view state without a conversation id and we weren't given one
        //    but we were given a different other user id. This happens when the user
        //    goes from viewing a user that they haven't yet initialised a conversation
        //    with to viewing a different user that they also haven't initialised a
        //    conversation with.
        var isNewConversation = !viewState || (viewState.id != conversationId) || (otherUserId && otherUserId != getOtherUserId());
        if (isNewConversation) {
            // Reset all of the states back to the beginning if we're loading a new
            // conversation.
            isResetting = true;
            var renderPromise = null;
            var loggedInUserProfile = getLoggedInUserProfile(body);
            if (conversation) {
                renderPromise = resetByConversation(body, conversation, loggedInUserProfile, otherUserId);
            } else if (conversationId) {
                renderPromise = resetById(body, conversationId, loggedInUserProfile, otherUserId);
            } else {
                renderPromise = resetNoConversation(body, loggedInUserProfile, otherUserId);
            }

            return renderPromise
                .then(function() {
                    isResetting = false;
                    // Focus the first element that can receieve it in the header.
                    header.find(Constants.SELECTORS.CAN_RECEIVE_FOCUS).first().focus();
                    return;
                })
                .catch(function(error) {
                    isResetting = false;
                    Notification.exception(error);
                });
        }

        // We're not loading a new conversation so we should reset the poll timer to try to load
        // new messages.
        resetMessagePollTimer(conversationId);

        if (viewState.type == CONVERSATION_TYPES.PRIVATE && action) {
            // There are special actions that the user can perform in a private (aka 1-to-1)
            // conversation.
            var currentOtherUserId = getOtherUserId();

            switch (action) {
                case 'block':
                    return requestBlockUser(currentOtherUserId);
                case 'unblock':
                    return requestUnblockUser(currentOtherUserId);
                case 'add-contact':
                    return requestAddContact(currentOtherUserId);
                case 'remove-contact':
                    return requestRemoveContact(currentOtherUserId);
            }
        }

        // Final fallback to return a promise if we didn't need to do anything.
        return $.Deferred().resolve().promise();
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @return {Object} jQuery promise
     */
    var description = function() {
        return Str.get_string('messagedrawerviewconversation', 'core_message', viewState.name);
    };

    return {
        show: show,
        description: description
    };
});
