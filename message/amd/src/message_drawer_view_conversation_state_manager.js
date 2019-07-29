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
 * This module operates on the view states from the message_drawer_view_conversation module.
 * It exposes functions that can be used to generate new version of the state.
 *
 * Important notes for this module:
 * 1.) The existing state is always immutable. It should never be modified.
 * 2.) All functions that operate on the state should always clone the state and
 *     modify the cloned state before returning it.
 *
 * It's important that the states remain immutable because they are diff'd in
 * the message_drawer_view_conversation_patcher module in order to work out what
 * has changed.
 *
 * @module     core_message/message_drawer_view_conversation_state_manager
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /**
     * Clone a state, a state is a collection of information about the variables required to build
     * the conversation user interface.
     *
     * @param  {Object} state State to clone
     * @return {Object} newstate A copy of the state to clone.
     */
    var cloneState = function(state) {
        // Do a deep extend to make sure we recursively copy objects and
        // arrays so that the new state doesn't contain any references to
        // the old state, e.g. adding a value to an array in the new state
        // shouldn't also add it to the old state.
        return $.extend(true, {}, state);
    };

    /**
     * Format messages to be used in a state.
     *
     * @param  {Array} messages The messages to format.
     * @param  {Number} loggedInUserId The logged in user id.
     * @param  {Array} members The converstation members.
     * @return {Array} Formatted messages.
     */
    var formatMessages = function(messages, loggedInUserId, members) {
        return messages.map(function(message) {
            var fromLoggedInUser = message.useridfrom == loggedInUserId;
            return {
                // Stringify the id.
                id: "" + message.id,
                fromLoggedInUser: fromLoggedInUser,
                userFrom: members[message.useridfrom],
                text: message.text,
                timeCreated: message.timecreated ? parseInt(message.timecreated, 10) : null
            };
        });
    };

    /**
     * Format members to be used in a state.
     *
     * @param  {Array} members The messages to format.
     * @return {Array} Formatted members.
     */
    var formatMembers = function(members) {
        return members.map(function(member) {
            return {
                id: member.id,
                fullname: member.fullname,
                profileurl: member.profileurl,
                profileimageurl: member.profileimageurl,
                profileimageurlsmall: member.profileimageurlsmall,
                isonline:  member.isonline,
                showonlinestatus: member.showonlinestatus,
                isblocked: member.isblocked,
                iscontact: member.iscontact,
                isdeleted: member.isdeleted,
                canmessage: member.canmessage,
                canmessageevenifblocked: member.canmessageevenifblocked,
                requirescontact: member.requirescontact,
                contactrequests: member.contactrequests || []
            };
        });
    };

    /**
     * Create an initial (blank) state.
     *
     * @param  {Number} midnight Midnight time.
     * @param  {Number} loggedInUserId The logged in user id.
     * @param  {Number} id The conversation id.
     * @param  {Number} messagePollMin The message poll start timeout in seconds.
     * @param  {Number} messagePollMax The message poll max timeout limit in seconds.
     * @param  {Number} messagePollAfterMax The message poll frequency in seconds to reset to after max limit is reached.
     * @return {Object} Initial state.
     */
    var buildInitialState = function(
        midnight,
        loggedInUserId,
        id,
        messagePollMin,
        messagePollMax,
        messagePollAfterMax
    ) {
        return {
            midnight: midnight,
            loggedInUserId: loggedInUserId,
            id: id,
            messagePollMin: messagePollMin,
            messagePollMax: messagePollMax,
            messagePollAfterMax: messagePollAfterMax,
            name: null,
            subname: null,
            type: null,
            totalMemberCount: null,
            imageUrl: null,
            isFavourite: null,
            isMuted: null,
            canDeleteMessagesForAllUsers: false,
            deleteMessagesForAllUsers: false,
            members: {},
            messages: [],
            hasTriedToLoadMessages: false,
            loadingMessages: true,
            loadingMembers: true,
            loadingConfirmAction: false,
            pendingBlockUserIds: [],
            pendingUnblockUserIds: [],
            pendingRemoveContactIds: [],
            pendingAddContactIds: [],
            pendingDeleteMessageIds: [],
            pendingSendMessageIds: [],
            pendingDeleteConversation: false,
            selectedMessageIds: []
        };
    };

    /**
     * Add messages to a state and sort them by timecreated.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messages Messages to add to state.
     * @return {Object} state New state with added messages.
     */
    var addMessages = function(state, messages) {
        var newState = cloneState(state);
        var formattedMessages = formatMessages(messages, state.loggedInUserId, state.members);
        formattedMessages = formattedMessages.map(function(message) {
            message.sendState = null;
            message.timeAdded = Date.now();
            message.errorMessage = null;
            return message;
        });
        var allMessages = state.messages.concat(formattedMessages);
        // Sort the messages. Oldest to newest.
        allMessages.sort(function(a, b) {
            if (a.timeCreated === null && b.timeCreated === null) {
                if (a.timeAdded < b.timeAdded) {
                    return -1;
                } else if (a.timeAdded > b.timeAdded) {
                    return 1;
                }
            }

            if (a.timeCreated === null && b.timeCreated !== null) {
                // A comes after b.
                return 1;
            } else if (a.timeCreated !== null && b.timeCreated === null) {
                // A comes before b.
                return -1;
            } else if (a.timeCreated < b.timeCreated) {
                // A comes before b.
                return -1;
            } else if (a.timeCreated > b.timeCreated) {
                // A comes after b.
                return 1;
            } else if (a.id < b.id) {
                return -1;
            } else if (a.id > b.id) {
                return 1;
            } else {
                return 0;
            }
        });

        // Filter out any duplicate messages.
        newState.messages = allMessages.filter(function(message, index, sortedMessages) {
            return !index || message.id != sortedMessages[index - 1].id;
        });

        return newState;
    };

    /**
     * Update existing messages.
     *
     * @param  {Object} state Current state.
     * @param  {Array} data 2D array of old and new messages
     * @return {Object} state.
     */
    var updateMessages = function(state, data) {
        var newState = cloneState(state);
        var updatesById = data.reduce(function(carry, messageData) {
            var oldMessage = messageData[0];
            var newMessage = messageData[1];
            var formattedMessages = formatMessages([newMessage], state.loggedInUserId, state.members);
            var formattedMessage = formattedMessages[0];

            carry[oldMessage.id] = formattedMessage;
            return carry;
        }, {});

        newState.messages = newState.messages.map(function(message) {
            if (message.id in updatesById) {
                return $.extend(message, updatesById[message.id]);
            } else {
                return message;
            }
        });

        return newState;
    };

    /**
     * Remove messages from state.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messages Messages to remove from state.
     * @return {Object} state New state with removed messages.
     */
    var removeMessages = function(state, messages) {
        var newState = cloneState(state);
        var removeMessageIds = messages.map(function(message) {
            return "" + message.id;
        });
        newState.messages = newState.messages.filter(function(message) {
            return removeMessageIds.indexOf(message.id) < 0;
        });

        return newState;
    };

    /**
     * Remove messages from state by message id.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Message ids to remove from state.
     * @return {Object} state New state with removed messages.
     */
    var removeMessagesById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.messages = newState.messages.filter(function(message) {
            return messageIds.indexOf(message.id) < 0;
        });

        return newState;
    };

    /**
     * Add conversation member to state.
     *
     * @param  {Object} state Current state.
     * @param  {Array} members Conversation members to be added to state.
     * @return {Object} New state with added members.
     */
    var addMembers = function(state, members) {
        var newState = cloneState(state);
        var formattedMembers = formatMembers(members);
        formattedMembers.forEach(function(member) {
            newState.members[member.id] = member;
        });
        return newState;
    };

    /**
     * Remove members from state.
     *
     * @param  {Object} state Current state.
     * @param  {Array} members Members to be removed from state.
     * @return {Object} New state with removed members.
     */
    var removeMembers = function(state, members) {
        var newState = cloneState(state);
        members.forEach(function(member) {
            delete newState.members[member.id];
        });
        return newState;
    };

    /**
     * Set the state loading messages attribute.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value New loading messages value.
     * @return {Object} New state with loading messages attribute.
     */
    var setLoadingMessages = function(state, value) {
        var newState = cloneState(state);
        newState.loadingMessages = value;
        if (state.loadingMessages && !value) {
            // If we're going from loading to not loading then
            // it means we've tried to load.
            newState.hasTriedToLoadMessages = true;
        }
        return newState;
    };

    /**
     * Set the state loading members attribute.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value New loading members value.
     * @return {Object} New state with loading members attribute.
     */
    var setLoadingMembers = function(state, value) {
        var newState = cloneState(state);
        newState.loadingMembers = value;
        return newState;
    };

    /**
     * Set the conversation id.
     *
     * @param  {Object} state Current state.
     * @param  {String} value The ID.
     * @return {Object} New state.
     */
    var setId = function(state, value) {
        var newState = cloneState(state);
        newState.id = value;
        return newState;
    };

    /**
     * Set the state name attribute.
     *
     * @param  {Object} state Current state.
     * @param  {String} value New name value.
     * @return {Object} New state with name attribute.
     */
    var setName = function(state, value) {
        var newState = cloneState(state);
        newState.name = value;
        return newState;
    };

    /**
     * Set the state subname attribute.
     *
     * @param  {Object} state Current state.
     * @param  {String} value New subname value.
     * @return {Object} New state.
     */
    var setSubname = function(state, value) {
        var newState = cloneState(state);
        newState.subname = value;
        return newState;
    };

    /**
     * Set the conversation type.
     *
     * @param  {Object} state Current state.
     * @param  {Int} type Conversation type.
     * @return {Object} New state.
     */
    var setType = function(state, type) {
        var newState = cloneState(state);
        newState.type = type;
        return newState;
    };

    /**
     * Set whether the conversation is a favourite conversation.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} isFavourite If it's a favourite.
     * @return {Object} New state.
     */
    var setIsFavourite = function(state, isFavourite) {
        var newState = cloneState(state);
        newState.isFavourite = isFavourite;
        return newState;
    };

    /**
     * Set whether the conversation is a muted conversation.
     *
     * @param  {Object} state Current state.
     * @param  {bool} isMuted If it's muted.
     * @return {Object} New state.
     */
    var setIsMuted = function(state, isMuted) {
        var newState = cloneState(state);
        newState.isMuted = isMuted;
        return newState;
    };

    /**
     * Set the total member count.
     *
     * @param  {Object} state Current state.
     * @param  {String} count The count.
     * @return {Object} New state.
     */
    var setTotalMemberCount = function(state, count) {
        var newState = cloneState(state);
        newState.totalMemberCount = count;
        return newState;
    };

    /**
     * Set the conversation image url.
     *
     * @param  {Object} state Current state.
     * @param  {String} url The url to the image.
     * @return {Object} New state.
     */
    var setImageUrl = function(state, url) {
        var newState = cloneState(state);
        newState.imageUrl = url;
        return newState;
    };

    /**
     * Set the state loading confirm action attribute.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value New loading confirm action value.
     * @return {Object} New state with loading confirm action attribute.
     */
    var setLoadingConfirmAction = function(state, value) {
        var newState = cloneState(state);
        newState.loadingConfirmAction = value;
        return newState;
    };

    /**
     * Set the state pending delete conversation attribute.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value New pending delete conversation value.
     * @return {Object} New state with pending delete conversation attribute.
     */
    var setPendingDeleteConversation = function(state, value) {
        var newState = cloneState(state);
        newState.pendingDeleteConversation = value;
        return newState;
    };

    /**
     * Set the state of message to pending.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages to delete.
     * @return {Object} New state with array of pending delete message ids.
     */
    var setMessagesSendPendingById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.messages.forEach(function(message) {
            if (messageIds.indexOf(message.id) >= 0) {
                message.sendState = 'pending';
                message.errorMessage = null;
            }
        });
        return newState;
    };

    /**
     * Set the state of message to sent.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages to delete.
     * @return {Object} New state with array of pending delete message ids.
     */
    var setMessagesSendSuccessById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.messages.forEach(function(message) {
            if (messageIds.indexOf(message.id) >= 0) {
                message.sendState = 'sent';
                message.errorMessage = null;
            }
        });
        return newState;
    };

    /**
     * Set the state of messages to error.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages to delete.
     * @return {Object} New state with array of pending delete message ids.
     */
    var setMessagesSendFailById = function(state, messageIds, errorMessage) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.messages.forEach(function(message) {
            if (messageIds.indexOf(message.id) >= 0) {
                message.sendState = 'error';
                message.errorMessage = errorMessage;
            }
        });
        return newState;
    };

    /**
     * Set the state pending block userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to block.
     * @return {Object} New state with array of pending block userids.
     */
    var addPendingBlockUsersById = function(state, userIds) {
        var newState = cloneState(state);
        userIds.forEach(function(id) {
            newState.pendingBlockUserIds.push(id);
        });
        return newState;
    };

    /**
     * Set the state pending remove userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to remove.
     * @return {Object} New state with array of pending remove userids.
     */
    var addPendingRemoveContactsById = function(state, userIds) {
        var newState = cloneState(state);
        userIds.forEach(function(id) {
            newState.pendingRemoveContactIds.push(id);
        });
        return newState;
    };

    /**
     * Set the state pending unblock userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to unblock.
     * @return {Object} New state with array of pending unblock userids.
     */
    var addPendingUnblockUsersById = function(state, userIds) {
        var newState = cloneState(state);
        userIds.forEach(function(id) {
            newState.pendingUnblockUserIds.push(id);
        });
        return newState;
    };

    /**
     * Set the state pending add users to contacts userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to add users to contacts.
     * @return {Object} New state with array of pending add users to contacts userids.
     */
    var addPendingAddContactsById = function(state, userIds) {
        var newState = cloneState(state);
        userIds.forEach(function(id) {
            newState.pendingAddContactIds.push(id);
        });
        return newState;
    };

    /**
     * Set the state pending delete messages.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages to delete.
     * @return {Object} New state with array of pending delete message ids.
     */
    var addPendingDeleteMessagesById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds.forEach(function(id) {
            newState.pendingDeleteMessageIds.push(id);
        });
        return newState;
    };

    /**
     * Update the state pending block userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to remove from the list of user ids to block.
     * @return {Object} New state with array of pending block userids.
     */
    var removePendingBlockUsersById = function(state, userIds) {
        var newState = cloneState(state);
        newState.pendingBlockUserIds = newState.pendingBlockUserIds.filter(function(id) {
            return userIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Update the state pending remove userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to remove from the list of user ids to remove.
     * @return {Object} New state with array of pending remove userids.
     */
    var removePendingRemoveContactsById = function(state, userIds) {
        var newState = cloneState(state);
        newState.pendingRemoveContactIds = newState.pendingRemoveContactIds.filter(function(id) {
            return userIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Update the state pending unblock userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to remove from the list of user ids to unblock.
     * @return {Object} New state with array of pending unblock userids.
     */
    var removePendingUnblockUsersById = function(state, userIds) {
        var newState = cloneState(state);
        newState.pendingUnblockUserIds = newState.pendingUnblockUserIds.filter(function(id) {
            return userIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Update the state pending add to contacts userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} userIds User ids to remove from the list of user ids to add to contacts.
     * @return {Object} New state with array of pending add to contacts userids.
     */
    var removePendingAddContactsById = function(state, userIds) {
        var newState = cloneState(state);
        newState.pendingAddContactIds = newState.pendingAddContactIds.filter(function(id) {
            return userIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Update the state pending delete messages userids.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Message ids to remove from the list of messages to delete.
     * @return {Object} New state with array of messages to delete.
     */
    var removePendingDeleteMessagesById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.pendingDeleteMessageIds = newState.pendingDeleteMessageIds.filter(function(id) {
            return messageIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Add messages to state selected messages.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages that are selected.
     * @return {Object} New state with array of not blocked members.
     */
    var addSelectedMessagesById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.selectedMessageIds = newState.selectedMessageIds.concat(messageIds);
        return newState;
    };

    /**
     * Remove messages from the state selected messages.
     *
     * @param  {Object} state Current state.
     * @param  {Array} messageIds Messages to remove from selected messages.
     * @return {Object} New state with array of selected messages.
     */
    var removeSelectedMessagesById = function(state, messageIds) {
        var newState = cloneState(state);
        messageIds = messageIds.map(function(id) {
            return "" + id;
        });
        newState.selectedMessageIds = newState.selectedMessageIds.filter(function(id) {
            return messageIds.indexOf(id) < 0;
        });
        return newState;
    };

    /**
     * Mark messages as read.
     *
     * @param  {Object} state Current state.
     * @param  {Array} readMessages Messages that are read.
     * @return {Object} New state with array of messages that have the isread attribute set.
     */
    var markMessagesAsRead = function(state, readMessages) {
        var newState = cloneState(state);
        var readMessageIds = readMessages.map(function(message) {
            return message.id;
        });
        newState.messages = newState.messages.map(function(message) {
            if (readMessageIds.indexOf(message.id) >= 0) {
                message.isRead = true;
            }

            return message;
        });
        return newState;
    };

    /**
     * Add a contact request to each of the members that the request is for.
     *
     * @param  {Object} state Current state.
     * @param  {Array} requests The contact requests
     * @return {Object} New state
     */
    var addContactRequests = function(state, requests) {
        var newState = cloneState(state);

        requests.forEach(function(request) {
            var fromUserId = request.userid;
            var toUserId = request.requesteduserid;
            newState.members[fromUserId].contactrequests.push(request);
            newState.members[toUserId].contactrequests.push(request);
        });

        return newState;
    };

    /**
     * Remove a contact request from the members of that request.
     *
     * @param  {Object} state Current state.
     * @param  {Array} requests The contact requests
     * @return {Object} New state
     */
    var removeContactRequests = function(state, requests) {
        var newState = cloneState(state);
        requests.forEach(function(request) {
            var fromUserId = request.userid;
            var toUserId = request.requesteduserid;

            newState.members[fromUserId].contactrequests = newState.members[fromUserId].contactrequests.filter(function(existing) {
                return existing.userid != fromUserId;
            });
            newState.members[toUserId].contactrequests = newState.members[toUserId].contactrequests.filter(function(existing) {
                return existing.requesteduserid != toUserId;
            });
        });

        return newState;
    };

    /**
     * Set wheter the message of the conversation can delete for all users.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value If it can delete for all users.
     * @return {Object} New state.
     */
    var setCanDeleteMessagesForAllUsers = function(state, value) {
        var newState = cloneState(state);
        newState.canDeleteMessagesForAllUsers = value;
        return newState;
    };

    /**
     * Set wheter the messages of the conversation delete for all users.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value Delete messages for all users.
     * @return {Object} New state.
     */
    var setDeleteMessagesForAllUsers = function(state, value) {
        var newState = cloneState(state);
        newState.deleteMessagesForAllUsers = value;
        return newState;
    };

    return {
        buildInitialState: buildInitialState,
        addMessages: addMessages,
        updateMessages: updateMessages,
        removeMessages: removeMessages,
        removeMessagesById: removeMessagesById,
        addMembers: addMembers,
        removeMembers: removeMembers,
        setLoadingMessages: setLoadingMessages,
        setLoadingMembers: setLoadingMembers,
        setId: setId,
        setName: setName,
        setSubname: setSubname,
        setType: setType,
        setIsFavourite: setIsFavourite,
        setIsMuted: setIsMuted,
        setCanDeleteMessagesForAllUsers: setCanDeleteMessagesForAllUsers,
        setDeleteMessagesForAllUsers: setDeleteMessagesForAllUsers,
        setTotalMemberCount: setTotalMemberCount,
        setImageUrl: setImageUrl,
        setLoadingConfirmAction: setLoadingConfirmAction,
        setPendingDeleteConversation: setPendingDeleteConversation,
        setMessagesSendPendingById: setMessagesSendPendingById,
        setMessagesSendSuccessById: setMessagesSendSuccessById,
        setMessagesSendFailById: setMessagesSendFailById,
        addPendingBlockUsersById: addPendingBlockUsersById,
        addPendingRemoveContactsById: addPendingRemoveContactsById,
        addPendingUnblockUsersById: addPendingUnblockUsersById,
        addPendingAddContactsById: addPendingAddContactsById,
        addPendingDeleteMessagesById: addPendingDeleteMessagesById,
        removePendingBlockUsersById: removePendingBlockUsersById,
        removePendingRemoveContactsById: removePendingRemoveContactsById,
        removePendingUnblockUsersById: removePendingUnblockUsersById,
        removePendingAddContactsById: removePendingAddContactsById,
        removePendingDeleteMessagesById: removePendingDeleteMessagesById,
        addSelectedMessagesById: addSelectedMessagesById,
        removeSelectedMessagesById: removeSelectedMessagesById,
        markMessagesAsRead: markMessagesAsRead,
        addContactRequests: addContactRequests,
        removeContactRequests: removeContactRequests
    };
});
