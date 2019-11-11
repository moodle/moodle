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
    'core/emoji/auto_complete',
    'core/emoji/picker'
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
    MessageDrawerRoutes,
    initialiseEmojiAutoComplete,
    initialiseEmojiPicker
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
    var isRendering = false;
    var renderBuffer = [];
    // If the UI is currently resetting.
    var isResetting = true;
    // If the UI is currently sending a message.
    var isSendingMessage = false;
    // A buffer of messages to send.
    var sendMessageBuffer = [];
    // These functions which will be generated when this module is
    // first called. See generateRenderFunction for details.
    var render = null;
    // The list of renderers that have been registered to render
    // this conversation. See generateRenderFunction for details.
    var renderers = [];

    var NEWEST_FIRST = Constants.NEWEST_MESSAGES_FIRST;
    var LOAD_MESSAGE_LIMIT = Constants.LOAD_MESSAGE_LIMIT;
    var MILLISECONDS_IN_SEC = Constants.MILLISECONDS_IN_SEC;
    var SELECTORS = Constants.SELECTORS;
    var CONVERSATION_TYPES = Constants.CONVERSATION_TYPES;

    /**
     * Get the other user userid.
     *
     * @return {Number} Userid.
     */
    var getOtherUserId = function() {
        if (!viewState || viewState.type == CONVERSATION_TYPES.PUBLIC) {
            return null;
        }

        var loggedInUserId = viewState.loggedInUserId;
        if (viewState.type == CONVERSATION_TYPES.SELF) {
            // It's a self-conversation, so the other user is the one logged in.
            return loggedInUserId;
        }

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

                if (state.type != CONVERSATION_TYPES.PUBLIC) {
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
            canmessage: null,
            canmessageevenifblocked: null,
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
            isMuted: state.isMuted,
            type: state.type,
            totalMemberCount: state.totalMemberCount,
            loggedInUserId: state.loggedInUserId,
            messages: state.messages.map(function(message) {
                return $.extend({}, message);
            }),
            members: Object.keys(state.members).map(function(id) {
                var formattedMember = $.extend({}, state.members[id]);
                formattedMember.contactrequests = state.members[id].contactrequests.map(function(request) {
                    return $.extend({}, request);
                });
                return formattedMember;
            })
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
        // If the other user id is the same as the logged in user then this is a self
        // conversation.
        var conversationType = loggedInUserId == otherUserId ? CONVERSATION_TYPES.SELF : CONVERSATION_TYPES.PRIVATE;
        var newState = StateManager.setLoadingMembers(viewState, true);
        newState = StateManager.setLoadingMessages(newState, true);
        render(newState);

        return Repository.getMemberInfo(loggedInUserId, [otherUserId], true, true)
            .then(function(profiles) {
                if (profiles.length) {
                    return profiles[0];
                } else {
                    throw new Error('Unable to load other user profile');
                }
            })
            .then(function(profile) {
                // If the conversation is a self conversation then the profile loaded is the
                // logged in user so only add that to the members array.
                var members = conversationType == CONVERSATION_TYPES.SELF ? [profile] : [profile, loggedInUserProfile];
                var newState = StateManager.addMembers(viewState, members);
                newState = StateManager.setLoadingMembers(newState, false);
                newState = StateManager.setLoadingMessages(newState, false);
                newState = StateManager.setName(newState, profile.fullname);
                newState = StateManager.setType(newState, conversationType);
                newState = StateManager.setImageUrl(newState, profile.profileimageurl);
                newState = StateManager.setTotalMemberCount(newState, members.length);
                render(newState);
                return profile;
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
        var otherUser = null;
        if (conversation.type == CONVERSATION_TYPES.PRIVATE) {
            // For private conversations, remove current logged in user from the members list to get the other user.
            var otherUsers = conversation.members.filter(function(member) {
                return member.id != loggedInUserId;
            });
            otherUser = otherUsers.length ? otherUsers[0] : null;
        } else if (conversation.type == CONVERSATION_TYPES.SELF) {
            // Self-conversations have only one member.
            otherUser = conversation.members[0];
        }

        var name = conversation.name;
        var imageUrl = conversation.imageurl;

        if (conversation.type != CONVERSATION_TYPES.PUBLIC) {
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
        newState = StateManager.setIsMuted(newState, conversation.ismuted);
        newState = StateManager.addMessages(newState, conversation.messages);
        newState = StateManager.setCanDeleteMessagesForAllUsers(newState, conversation.candeletemessagesforallusers);
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
        render(newState);

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
        )
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

        var messageCount = conversation.messages.length;
        var hasLoadedEnoughMessages = messageCount >= messageLimit;
        var newState = updateStateFromConversation(conversation, loggedInUserProfile.id);
        newState = StateManager.setLoadingMembers(newState, false);
        newState = StateManager.setLoadingMessages(newState, !hasLoadedEnoughMessages);
        var renderPromise = render(newState);

        return renderPromise.then(function() {
                if (!hasLoadedEnoughMessages) {
                    // We haven't got enough messages so let's load some more.
                    return loadMessages(conversation.id, messageLimit, messageCount, newestFirst, []);
                } else {
                    // We've got enough messages. No need to load any more for now.
                    return {messages: conversation.messages};
                }
            })
            .then(function() {
                var messages = viewState.messages;
                // Update the offset to reflect the number of messages we've loaded.
                setMessagesOffset(messages.length);
                markConversationAsRead(viewState.id);

                return messages;
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
            var lastTimeCreated = mostRecentMessage ? mostRecentMessage.timeCreated : null;

            if (lastTimeCreated && !isResetting && !isSendingMessage) {
                // There may be multiple messages with the same time created value since
                // the accuracy is only down to the second. The server will include these
                // messages in the result (since it does a >= comparison on time from) so
                // we need to filter them back out of the result so that we're left only
                // with the new messages.
                var ignoreMessageIds = [];
                for (var i = messages.length - 1; i >= 0; i--) {
                    var message = messages[i];
                    if (message.timeCreated === lastTimeCreated) {
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
                        lastTimeCreated
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
     * @param {Number} userId User id.
     */
    var requestBlockUser = function(userId) {
        cancelRequest(userId);
        var newState = StateManager.addPendingBlockUsersById(viewState, [userId]);
        render(newState);
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
        render(newState);

        return Repository.blockUser(viewState.loggedInUserId, userId)
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
     * @param {Number} userId User id of user to unblock.
     */
    var requestUnblockUser = function(userId) {
        cancelRequest(userId);
        var newState = StateManager.addPendingUnblockUsersById(viewState, [userId]);
        render(newState);
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
        render(newState);

        return Repository.unblockUser(viewState.loggedInUserId, userId)
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
     * @param {Number} userId User id of user to remove from contacts.
     */
    var requestRemoveContact = function(userId) {
        cancelRequest(userId);
        var newState = StateManager.addPendingRemoveContactsById(viewState, [userId]);
        render(newState);
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
        render(newState);

        return Repository.deleteContacts(viewState.loggedInUserId, [userId])
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
     * @param {Number} userId User id of user to add to contacts.
     */
    var requestAddContact = function(userId) {
        cancelRequest(userId);
        var newState = StateManager.addPendingAddContactsById(viewState, [userId]);
        render(newState);
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
        render(newState);

        return Repository.createContactRequest(viewState.loggedInUserId, userId)
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
     * Set the current conversation as a muted conversation.
     *
     * @return {Promise} Renderer promise.
     */
    var setMuted = function() {
        var userId = viewState.loggedInUserId;
        var conversationId = viewState.id;

        return Repository.setMutedConversations(userId, [conversationId])
            .then(function() {
                var newState = StateManager.setIsMuted(viewState, true);
                return render(newState);
            })
            .then(function() {
                return PubSub.publish(
                    MessageDrawerEvents.CONVERSATION_SET_MUTED,
                    formatConversationForEvent(viewState)
                );
            });
    };

    /**
     * Unset the current conversation as a muted conversation.
     *
     * @return {Promise} Renderer promise.
     */
    var unsetMuted = function() {
        var userId = viewState.loggedInUserId;
        var conversationId = viewState.id;

        return Repository.unsetMutedConversations(userId, [conversationId])
            .then(function() {
                var newState = StateManager.setIsMuted(viewState, false);
                return render(newState);
            })
            .then(function() {
                return PubSub.publish(
                    MessageDrawerEvents.CONVERSATION_UNSET_MUTED,
                    formatConversationForEvent(viewState)
                );
            });
    };

    /**
     * Tell the statemanager there is a request to delete the selected messages
     * and run the renderer to show confirm delete messages dialogue.
     *
     * @param {Number} userId User id.
     */
    var requestDeleteSelectedMessages = function(userId) {
        var selectedMessageIds = viewState.selectedMessageIds;
        cancelRequest(userId);
        var newState = StateManager.addPendingDeleteMessagesById(viewState, selectedMessageIds);
        render(newState);
    };

    /**
     * Send the repository a request to delete the messages pending deletion. Update the statemanager
     * and publish a message deletion event.
     *
     * @return {Promise} Renderer promise.
     */
    var deleteSelectedMessages = function() {
        var messageIds = viewState.pendingDeleteMessageIds;
        var sentMessages = viewState.messages.filter(function(message) {
            // If a message sendState is null then it means it was loaded from the server or if it's
            // set to sent then it means the user has successfully sent it in this page load.
            return messageIds.indexOf(message.id) >= 0 && (message.sendState == 'sent' || message.sendState === null);
        });
        var newState = StateManager.setLoadingConfirmAction(viewState, true);

        render(newState);

        var deleteMessagesPromise = $.Deferred().resolve().promise();

        if (sentMessages.length) {
            // We only need to send a request to the server if we're trying to delete messages that
            // have successfully been sent.
            var sentMessageIds = sentMessages.map(function(message) {
                return message.id;
            });
            if (newState.deleteMessagesForAllUsers) {
                deleteMessagesPromise = Repository.deleteMessagesForAllUsers(viewState.loggedInUserId, sentMessageIds);
            } else {
                deleteMessagesPromise = Repository.deleteMessages(viewState.loggedInUserId, sentMessageIds);
            }
        }

        return deleteMessagesPromise.then(function() {
                var newState = StateManager.removeMessagesById(viewState, messageIds);
                newState = StateManager.removePendingDeleteMessagesById(newState, messageIds);
                newState = StateManager.removeSelectedMessagesById(newState, messageIds);
                newState = StateManager.setLoadingConfirmAction(newState, false);
                newState = StateManager.setDeleteMessagesForAllUsers(newState, false);

                var prevLastMessage = viewState.messages[viewState.messages.length - 1];
                var newLastMessage = newState.messages.length ? newState.messages[newState.messages.length - 1] : null;

                if (newLastMessage && newLastMessage.id != prevLastMessage.id) {
                    var conversation = formatConversationForEvent(newState);
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, conversation);
                } else if (!newState.messages.length) {
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_DELETED, newState.id);
                }

                return render(newState);
            })
            .catch(Notification.exception);
    };

    /**
     * Tell the statemanager there is a request to delete a conversation
     * and run the renderer to show confirm delete conversation dialogue.
     *
     * @param {Number} userId User id of other user.
     */
    var requestDeleteConversation = function(userId) {
        cancelRequest(userId);
        var newState = StateManager.setPendingDeleteConversation(viewState, true);
        render(newState);
    };

    /**
     * Send the repository a request to delete a conversation. Update the statemanager
     * and publish a conversation deleted event.
     *
     * @return {Promise} Renderer promise.
     */
    var deleteConversation = function() {
        var newState = StateManager.setLoadingConfirmAction(viewState, true);
        render(newState);

        return Repository.deleteConversation(viewState.loggedInUserId, viewState.id)
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
     */
    var cancelRequest = function(userId) {
        var pendingDeleteMessageIds = viewState.pendingDeleteMessageIds;
        var newState = StateManager.removePendingAddContactsById(viewState, [userId]);
        newState = StateManager.removePendingRemoveContactsById(newState, [userId]);
        newState = StateManager.removePendingUnblockUsersById(newState, [userId]);
        newState = StateManager.removePendingBlockUsersById(newState, [userId]);
        newState = StateManager.removePendingDeleteMessagesById(newState, pendingDeleteMessageIds);
        newState = StateManager.setPendingDeleteConversation(newState, false);
        newState = StateManager.setDeleteMessagesForAllUsers(newState, false);
        render(newState);
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
        render(newState);

        return Repository.acceptContactRequest(userId, loggedInUserId)
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
        render(newState);

        return Repository.declineContactRequest(userId, loggedInUserId)
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
     * Send all of the messages in the buffer to the server to be created. Update the
     * UI with the newly created message information.
     *
     * This function will recursively call itself in order to make sure the buffer is
     * always being processed.
     */
    var processSendMessageBuffer = function() {
        if (isSendingMessage) {
            // We're already sending messages so nothing to do.
            return;
        }
        if (!sendMessageBuffer.length) {
            // No messages waiting to send. Nothing to do.
            return;
        }

        // Flag that we're processing the queue.
        isSendingMessage = true;
        // Grab all of the messages in the buffer.
        var messagesToSend = sendMessageBuffer.slice();
        // Empty the buffer since we're processing it.
        sendMessageBuffer = [];
        var conversationId = viewState.id;
        var newConversationId = null;
        var messagesText = messagesToSend.map(function(message) {
            return message.text;
        });
        var messageIds = messagesToSend.map(function(message) {
            return message.id;
        });
        var sendMessagePromise = null;
        var newCanDeleteMessagesForAllUsers = null;
        if (!conversationId && (viewState.type != CONVERSATION_TYPES.PUBLIC)) {
            // If it's a new private conversation then we need to use the old
            // web service function to create the conversation.
            var otherUserId = getOtherUserId();
            sendMessagePromise = Repository.sendMessagesToUser(otherUserId, messagesText)
                .then(function(messages) {
                    if (messages.length) {
                        newConversationId = parseInt(messages[0].conversationid, 10);
                        newCanDeleteMessagesForAllUsers = messages[0].candeletemessagesforallusers;
                    }
                    return messages;
                });
        } else {
            sendMessagePromise = Repository.sendMessagesToConversation(conversationId, messagesText);
        }

        sendMessagePromise
            .then(function(messages) {
                var newMessageIds = messages.map(function(message) {
                    return message.id;
                });
                var data = [];
                var selectedToRemove = [];
                var selectedToAdd = [];

                messagesToSend.forEach(function(oldMessage, index) {
                    var newMessage = messages[index];
                    // Update messages expects and array of arrays where the first value
                    // is the old message to update and the second value is the new values
                    // to set.
                    data.push([oldMessage, newMessage]);

                    if (viewState.selectedMessageIds.indexOf(oldMessage.id) >= 0) {
                        // If the message was added to the "selected messages" list while it was still
                        // being sent then we should update it's id in that list now to make sure future
                        // actions work.
                        selectedToRemove.push(oldMessage.id);
                        selectedToAdd.push(newMessage.id);
                    }
                });
                var newState = StateManager.updateMessages(viewState, data);
                newState = StateManager.setMessagesSendSuccessById(newState, newMessageIds);

                if (selectedToRemove.length) {
                    newState = StateManager.removeSelectedMessagesById(newState, selectedToRemove);
                }

                if (selectedToAdd.length) {
                    newState = StateManager.addSelectedMessagesById(newState, selectedToAdd);
                }

                var conversation = formatConversationForEvent(newState);

                if (!newState.id) {
                    // If this message created the conversation then save the conversation
                    // id.
                    newState = StateManager.setId(newState, newConversationId);
                    conversation.id = newConversationId;
                    resetMessagePollTimer(newConversationId);
                    PubSub.publish(MessageDrawerEvents.CONVERSATION_CREATED, conversation);
                    newState = StateManager.setCanDeleteMessagesForAllUsers(newState, newCanDeleteMessagesForAllUsers);
                }

                // Update the UI with the new message values from the server.
                render(newState);
                // Recurse just in case there has been more messages added to the buffer.
                isSendingMessage = false;
                processSendMessageBuffer();
                PubSub.publish(MessageDrawerEvents.CONVERSATION_NEW_LAST_MESSAGE, conversation);
                return;
            })
            .catch(function(e) {
                if (e.message) {
                    var errorMessage =  $.Deferred().resolve(e.message).promise();
                } else {
                    var errorMessage =  Str.get_string('unknownerror', 'core');
                }

                var handleFailedMessages = function(errorMessage) {
                    // We failed to create messages so remove the old messages from the pending queue
                    // and update the UI to indicate that the message failed.
                    var newState = StateManager.setMessagesSendFailById(viewState, messageIds, errorMessage);
                    render(newState);
                    isSendingMessage = false;
                    processSendMessageBuffer();
                };

                errorMessage.then(handleFailedMessages)
                    .catch(function(e) {
                        // Hrmm, we can't even load the error messages string! We'll have to
                        // hard code something in English here if we still haven't got a message
                        // to show.
                        var finalError = e.message || 'Something went wrong!';
                        handleFailedMessages(finalError);
                    });
            });
    };

    /**
     * Buffers messages to be sent to the server. We use a buffer here to allow the
     * user to freely input messages without blocking the interface for them.
     *
     * Instead we just queue all of their messages up and send them as fast as we can.
     *
     * @param {String} text Text to send.
     */
    var sendMessage = function(text) {
        var id = 'temp' + Date.now();
        var message = {
            id: id,
            useridfrom: viewState.loggedInUserId,
            text: text,
            timecreated: null
        };
        var newState = StateManager.addMessages(viewState, [message]);
        render(newState);
        sendMessageBuffer.push(message);
        processSendMessageBuffer();
    };

    /**
     * Retry sending a message that failed.
     *
     * @param {Object} message The message to send.
     */
    var retrySendMessage = function(message) {
        var newState = StateManager.setMessagesSendPendingById(viewState, [message.id]);
        render(newState);
        sendMessageBuffer.push(message);
        processSendMessageBuffer();
    };

    /**
     * Toggle the selected messages update the statemanager and render the result.
     *
     * @param  {Number} messageId The id of the message to be toggled
     */
    var toggleSelectMessage = function(messageId) {
        var newState = viewState;

        if (viewState.selectedMessageIds.indexOf(messageId) > -1) {
            newState = StateManager.removeSelectedMessagesById(viewState, [messageId]);
        } else {
            newState = StateManager.addSelectedMessagesById(viewState, [messageId]);
        }

        render(newState);
    };

    /**
     * Cancel edit mode (selecting the messages).
     *
     * @return {Promise} Renderer promise.
     */
    var cancelEditMode = function() {
        cancelRequest(getOtherUserId());
        var newState = StateManager.removeSelectedMessagesById(viewState, viewState.selectedMessageIds);
        render(newState);
    };

    /**
     * Process the patches in the render buffer one at a time in order until the
     * buffer is empty.
     *
     * @param {Object} header The conversation header container element.
     * @param {Object} body The conversation body container element.
     * @param {Object} footer The conversation footer container element.
     */
    var processRenderBuffer = function(header, body, footer) {
        if (isRendering) {
            return;
        }

        if (!renderBuffer.length) {
            return;
        }

        isRendering = true;
        var renderable = renderBuffer.shift();
        var renderPromises = renderers.map(function(renderFunc) {
            return renderFunc(renderable.patch);
        });

        $.when.apply(null, renderPromises)
            .then(function() {
                isRendering = false;
                renderable.deferred.resolve(true);
                // Keep processing the buffer until it's empty.
                processRenderBuffer(header, body, footer);
            })
            .catch(function(error) {
                isRendering = false;
                renderable.deferred.reject(error);
                Notification.exception(error);
            });
    };

    /**
     * Create a function to render the Conversation.
     *
     * @param  {Object} header The conversation header container element.
     * @param  {Object} body The conversation body container element.
     * @param  {Object} footer The conversation footer container element.
     * @param  {Bool} isNewConversation Has someone else already initialised a conversation?
     * @return {Promise} Renderer promise.
     */
    var generateRenderFunction = function(header, body, footer, isNewConversation) {
        var rendererFunc = function(patch) {
            return Renderer.render(header, body, footer, patch);
        };

        if (!isNewConversation) {
            // Looks like someone got here before us! We'd better update our
            // UI to make sure it matches.
            var initialState = StateManager.buildInitialState(viewState.midnight, viewState.loggedInUserId, viewState.id);
            var syncPatch = Patcher.buildPatch(initialState, viewState);
            rendererFunc(syncPatch);
        }

        renderers.push(rendererFunc);

        return function(newState) {
            var patch = Patcher.buildPatch(viewState, newState);
            var deferred = $.Deferred();

            // Check if the patch has any data. Ignore empty patches.
            if (Object.keys(patch).length) {
                // Add the patch to the render buffer which gets processed in order.
                renderBuffer.push({
                    patch: patch,
                    deferred: deferred
                });
            } else {
                deferred.resolve(true);
            }
            // This is a great place to add in some console logging if you need
            // to debug something. You can log the current state, the next state,
            // and the generated patch and see exactly what will be updated.

            // Optimistically update the state. We're going to assume that the rendering
            // will always succeed. The rendering is asynchronous (annoyingly) so it's buffered
            // but it'll reach eventual consistency with the current state.
            viewState = newState;
            if (newState.id) {
                // Only cache created conversations.
                stateCache[newState.id] = {
                    state: newState,
                    messagesOffset: getMessagesOffset(),
                    loadedAllMessages: hasLoadedAllMessages()
                };
            }

            // Start processing the buffer.
            processRenderBuffer(header, body, footer);

            return deferred.promise();
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
                actionCallback(getOtherUserId());
                var newState = StateManager.setLoadingConfirmAction(viewState, false);
                render(newState);
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
            sendMessage(text);
            textArea.val('');
            textArea.focus();
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
        var messageId = element.attr('data-message-id');

        toggleSelectMessage(messageId);

        data.originalEvent.preventDefault();
    };

    /**
     * Handle retry sending of message.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleRetrySendMessage = function(e, data) {
        var target = $(e.target);
        var element = target.closest(SELECTORS.MESSAGE);
        var messageId = element.attr('data-message-id');
        var messages = viewState.messages.filter(function(message) {
            return message.id == messageId;
        });
        var message = messages.length ? messages[0] : null;

        if (message) {
            retrySendMessage(message);
        }

        data.originalEvent.preventDefault();
        data.originalEvent.stopPropagation();
        e.stopPropagation();
    };

    /**
     * Cancel edit mode event handler.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleCancelEditMode = function(e, data) {
        cancelEditMode();
        data.originalEvent.preventDefault();
    };

    /**
     * Show the view contact page.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @return {Function} View contact handler.
     */
    var generateHandleViewContact = function(namespace) {
        return function(e, data) {
            var otherUserId = getOtherUserId();
            var otherUser = viewState.members[otherUserId];
            MessageDrawerRouter.go(namespace, MessageDrawerRoutes.VIEW_CONTACT, otherUser);
            data.originalEvent.preventDefault();
        };
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
     * Show the view group info page.
     * Set this conversation as muted.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleSetMuted = function(e, data) {
        setMuted().catch(Notification.exception);
        data.originalEvent.preventDefault();
    };

    /**
     * Unset this conversation as muted.
     *
     * @param {Object} e Element this event handler is called on.
     * @param {Object} data Data for this event.
     */
    var handleUnsetMuted = function(e, data) {
        unsetMuted().catch(Notification.exception);
        data.originalEvent.preventDefault();
    };

    /**
     * Handle clicking on the checkbox that toggles deleting messages for
     * all users.
     *
     * @param {Object} e Element this event handler is called on.
     */
    var handleDeleteMessagesForAllUsersToggle = function(e) {
        var newValue = $(e.target).prop('checked');
        var newState = StateManager.setDeleteMessagesForAllUsers(viewState, newValue);
        render(newState);
    };

    /**
     * Show the view contact page.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @return {Function} View group info handler.
     */
    var generateHandleViewGroupInfo = function(namespace) {
        return function(e, data) {
            MessageDrawerRouter.go(
                namespace,
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
    };

    /**
     * Handle clicking on the emoji toggle button.
     *
     * @param {Object} e The event
     * @param {Object} data The custom interaction event data
     */
    var handleToggleEmojiPicker = function(e, data) {
        var newState = StateManager.setShowEmojiPicker(viewState, !viewState.showEmojiPicker);
        render(newState);
        data.originalEvent.preventDefault();
    };

    /**
     * Handle clicking outside the emoji picker to close it.
     *
     * @param {Object} e The event
     */
    var handleCloseEmojiPicker = function(e) {
        var target = $(e.target);

        if (
            viewState.showEmojiPicker &&
            !target.closest(SELECTORS.EMOJI_PICKER_CONTAINER).length &&
            !target.closest(SELECTORS.TOGGLE_EMOJI_PICKER_BUTTON).length
        ) {
            var newState = StateManager.setShowEmojiPicker(viewState, false);
            render(newState);
        }
    };

    /**
     * Listen to, and handle events for conversations.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} header Conversation header container element.
     * @param {Object} body Conversation body container element.
     * @param {Object} footer Conversation footer container element.
     */
    var registerEventListeners = function(namespace, header, body, footer) {
        var isLoadingMoreMessages = false;
        var messagesContainer = getMessagesContainer(body);
        var emojiPickerElement = footer.find(SELECTORS.EMOJI_PICKER);
        var emojiAutoCompleteContainer = footer.find(SELECTORS.EMOJI_AUTO_COMPLETE_CONTAINER);
        var messageTextArea = footer.find(SELECTORS.MESSAGE_TEXT_AREA);
        var headerActivateHandlers = [
            [SELECTORS.ACTION_REQUEST_BLOCK, generateConfirmActionHandler(requestBlockUser)],
            [SELECTORS.ACTION_REQUEST_UNBLOCK, generateConfirmActionHandler(requestUnblockUser)],
            [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
            [SELECTORS.ACTION_REQUEST_REMOVE_CONTACT, generateConfirmActionHandler(requestRemoveContact)],
            [SELECTORS.ACTION_REQUEST_DELETE_CONVERSATION, generateConfirmActionHandler(requestDeleteConversation)],
            [SELECTORS.ACTION_CANCEL_EDIT_MODE, handleCancelEditMode],
            [SELECTORS.ACTION_VIEW_CONTACT, generateHandleViewContact(namespace)],
            [SELECTORS.ACTION_VIEW_GROUP_INFO, generateHandleViewGroupInfo(namespace)],
            [SELECTORS.ACTION_CONFIRM_FAVOURITE, handleSetFavourite],
            [SELECTORS.ACTION_CONFIRM_MUTE, handleSetMuted],
            [SELECTORS.ACTION_CONFIRM_UNFAVOURITE, handleUnsetFavourite],
            [SELECTORS.ACTION_CONFIRM_UNMUTE, handleUnsetMuted]
        ];
        var bodyActivateHandlers = [
            [SELECTORS.ACTION_CANCEL_CONFIRM, generateConfirmActionHandler(cancelRequest)],
            [SELECTORS.ACTION_CONFIRM_BLOCK, generateConfirmActionHandler(blockUser)],
            [SELECTORS.ACTION_CONFIRM_UNBLOCK, generateConfirmActionHandler(unblockUser)],
            [SELECTORS.ACTION_CONFIRM_ADD_CONTACT, generateConfirmActionHandler(addContact)],
            [SELECTORS.ACTION_CONFIRM_REMOVE_CONTACT, generateConfirmActionHandler(removeContact)],
            [SELECTORS.ACTION_CONFIRM_DELETE_SELECTED_MESSAGES, generateConfirmActionHandler(deleteSelectedMessages)],
            [SELECTORS.ACTION_CONFIRM_DELETE_CONVERSATION, generateConfirmActionHandler(deleteConversation)],
            [SELECTORS.ACTION_OKAY_CONFIRM, generateConfirmActionHandler(cancelRequest)],
            [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
            [SELECTORS.ACTION_ACCEPT_CONTACT_REQUEST, generateConfirmActionHandler(acceptContactRequest)],
            [SELECTORS.ACTION_DECLINE_CONTACT_REQUEST, generateConfirmActionHandler(declineContactRequest)],
            [SELECTORS.MESSAGE, handleSelectMessage],
            [SELECTORS.DELETE_MESSAGES_FOR_ALL_USERS_TOGGLE, handleDeleteMessagesForAllUsersToggle],
            [SELECTORS.RETRY_SEND, handleRetrySendMessage]
        ];
        var footerActivateHandlers = [
            [SELECTORS.SEND_MESSAGE_BUTTON, handleSendMessage],
            [SELECTORS.TOGGLE_EMOJI_PICKER_BUTTON, handleToggleEmojiPicker],
            [SELECTORS.ACTION_REQUEST_DELETE_SELECTED_MESSAGES, generateConfirmActionHandler(requestDeleteSelectedMessages)],
            [SELECTORS.ACTION_REQUEST_ADD_CONTACT, generateConfirmActionHandler(requestAddContact)],
            [SELECTORS.ACTION_REQUEST_UNBLOCK, generateConfirmActionHandler(requestUnblockUser)],
        ];

        AutoRows.init(footer);

        if (emojiAutoCompleteContainer.length) {
            initialiseEmojiAutoComplete(
                emojiAutoCompleteContainer[0],
                messageTextArea[0],
                function(hasSuggestions) {
                    var newState = StateManager.setShowEmojiAutoComplete(viewState, hasSuggestions);
                    render(newState);
                },
                function(emoji) {
                    var newState = StateManager.setShowEmojiAutoComplete(viewState, false);
                    render(newState);

                    messageTextArea.focus();
                    var cursorPos = messageTextArea.prop('selectionStart');
                    var currentText = messageTextArea.val();
                    var textBefore = currentText.substring(0, cursorPos).replace(/\S*$/, '');
                    var textAfter = currentText.substring(cursorPos).replace(/^\S*/, '');

                    messageTextArea.val(textBefore + emoji + textAfter);
                    // Set the cursor position to after the inserted emoji.
                    messageTextArea.prop('selectionStart', textBefore.length + emoji.length);
                    messageTextArea.prop('selectionEnd', textBefore.length + emoji.length);
                }
            );
        }

        if (emojiPickerElement.length) {
            initialiseEmojiPicker(emojiPickerElement[0], function(emoji) {
                var newState = StateManager.setShowEmojiPicker(viewState, !viewState.showEmojiPicker);
                render(newState);

                messageTextArea.focus();
                var cursorPos = messageTextArea.prop('selectionStart');
                var currentText = messageTextArea.val();
                var textBefore = currentText.substring(0, cursorPos);
                var textAfter = currentText.substring(cursorPos, currentText.length);

                messageTextArea.val(textBefore + emoji + textAfter);
                // Set the cursor position to after the inserted emoji.
                messageTextArea.prop('selectionStart', cursorPos + emoji.length);
                messageTextArea.prop('selectionEnd', cursorPos + emoji.length);
            });
        }

        CustomEvents.define(header, [
            CustomEvents.events.activate
        ]);
        CustomEvents.define(body, [
            CustomEvents.events.activate
        ]);
        CustomEvents.define(footer, [
            CustomEvents.events.activate,
            CustomEvents.events.enter,
            CustomEvents.events.escape
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
                render(newState);

                loadMessages(viewState.id, LOAD_MESSAGE_LIMIT, getMessagesOffset(), NEWEST_FIRST, [])
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

        footer.on(CustomEvents.events.escape, SELECTORS.EMOJI_PICKER_CONTAINER, handleToggleEmojiPicker);
        $(document.body).on('click', handleCloseEmojiPicker);

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
            BackOffTimer.getIncrementalCallback(
                viewState.messagePollMin * MILLISECONDS_IN_SEC,
                MILLISECONDS_IN_SEC,
                viewState.messagePollMax * MILLISECONDS_IN_SEC,
                viewState.messagePollAfterMax * MILLISECONDS_IN_SEC
            )
        );

        newMessagesPollTimer.start();
    };

    /**
     * Reset the state to the initial state and render the UI.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Number|null} conversationId The conversation id.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     */
    var resetState = function(body, conversationId, loggedInUserProfile) {
        // Reset all of the states back to the beginning if we're loading a new
        // conversation.
        loadedAllMessages = false;
        messagesOffset = 0;
        newMessagesPollTimer = null;
        isRendering = false;
        renderBuffer = [];
        isResetting = true;
        isSendingMessage = false;
        sendMessageBuffer = [];

        var loggedInUserId = loggedInUserProfile.id;
        var midnight = parseInt(body.attr('data-midnight'), 10);
        var messagePollMin = parseInt(body.attr('data-message-poll-min'), 10);
        var messagePollMax = parseInt(body.attr('data-message-poll-max'), 10);
        var messagePollAfterMax = parseInt(body.attr('data-message-poll-after-max'), 10);
        var initialState = StateManager.buildInitialState(
            midnight,
            loggedInUserId,
            conversationId,
            messagePollMin,
            messagePollMax,
            messagePollAfterMax
        );

        if (!viewState) {
            viewState = initialState;
        }

        if (newMessagesPollTimer) {
            newMessagesPollTimer.stop();
        }

        render(initialState);
    };

    /**
     * Load a new empty private conversation between two users or self-conversation.
     *
     * @param  {Object} body Conversation body container element.
     * @param  {Object} loggedInUserProfile The logged in user's profile.
     * @param  {Int} otherUserId The other user's id.
     * @return {Promise} Renderer promise.
     */
    var resetNoConversation = function(body, loggedInUserProfile, otherUserId) {
        // Always reset the state back to the initial state so that the
        // state manager and patcher can work correctly.
        resetState(body, null, loggedInUserProfile);

        var resetNoConversationPromise = null;

        if (loggedInUserProfile.id != otherUserId) {
            // Private conversation between two different users.
            resetNoConversationPromise = Repository.getConversationBetweenUsers(
                loggedInUserProfile.id,
                otherUserId,
                true,
                true,
                0,
                0,
                LOAD_MESSAGE_LIMIT,
                0,
                NEWEST_FIRST
            );
        } else {
            // Self conversation.
            resetNoConversationPromise = Repository.getSelfConversation(
                loggedInUserProfile.id,
                LOAD_MESSAGE_LIMIT,
                0,
                NEWEST_FIRST
            );
        }

        return resetNoConversationPromise.then(function(conversation) {
                // Looks like we have a conversation after all! Let's use that.
                return resetByConversation(body, conversation, loggedInUserProfile);
            })
            .catch(function() {
                // Can't find a conversation. Oh well. Just load up a blank one.
                return loadEmptyPrivateConversation(loggedInUserProfile, otherUserId);
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
        resetState(body, conversationId, loggedInUserProfile);

        var promise = $.Deferred().resolve({}).promise();
        if (cache) {
            // We've seen this conversation before so there is no need to
            // send any network requests.
            var newState = cache.state;
            // Reset some loading states just in case they were left weirdly.
            newState = StateManager.setLoadingMessages(newState, false);
            newState = StateManager.setLoadingMembers(newState, false);
            setMessagesOffset(cache.messagesOffset);
            setLoadedAllMessages(cache.loadedAllMessages);
            render(newState);
        } else {
            promise = loadNewConversation(
                conversationId,
                loggedInUserProfile,
                LOAD_MESSAGE_LIMIT,
                0,
                NEWEST_FIRST
            );
        }

        return promise.then(function() {
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
        resetState(body, conversation.id, loggedInUserProfile);

        var promise = $.Deferred().resolve({}).promise();
        if (cache) {
            // We've seen this conversation before so there is no need to
            // send any network requests.
            var newState = cache.state;
            // Reset some loading states just in case they were left weirdly.
            newState = StateManager.setLoadingMessages(newState, false);
            newState = StateManager.setLoadingMembers(newState, false);
            setMessagesOffset(cache.messagesOffset);
            setLoadedAllMessages(cache.loadedAllMessages);
            render(newState);
        } else {
            promise = loadExistingConversation(
                conversation,
                loggedInUserProfile,
                LOAD_MESSAGE_LIMIT,
                NEWEST_FIRST
            );
        }

        return promise.then(function() {
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
     * @param {string} namespace The route namespace.
     * @param {Object} header Conversation header container element.
     * @param {Object} body Conversation body container element.
     * @param {Object} footer Conversation footer container element.
     * @param {Object|Number|null} conversationOrId Conversation or id or null
     * @param {String} action An action to take on the conversation
     * @param {Number} otherUserId The other user id for a private conversation
     * @return {Object} jQuery promise
     */
    var show = function(namespace, header, body, footer, conversationOrId, action, otherUserId) {
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

        // This is a new conversation if:
        // 1. We don't already have a state
        // 2. The given conversation doesn't match the one currently loaded
        // 3. We have a view state without a conversation id and we weren't given one
        //    but we were given a different other user id. This happens when the user
        //    goes from viewing a user that they haven't yet initialised a conversation
        //    with to viewing a different user that they also haven't initialised a
        //    conversation with.
        var isNewConversation = !viewState || (viewState.id != conversationId) || (otherUserId && otherUserId != getOtherUserId());

        if (!body.attr('data-init')) {
            // Generate the render function to bind the header, body, and footer
            // elements to it so that we don't need to pass them around this module.
            render = generateRenderFunction(header, body, footer, isNewConversation);
            registerEventListeners(namespace, header, body, footer);
            body.attr('data-init', true);
        }

        if (isNewConversation) {
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
