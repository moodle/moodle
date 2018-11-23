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
        var newState = $.extend({}, state);
        newState.messages = state.messages.map(function(message) {
            return $.extend({}, message);
        });
        newState.members = Object.keys(state.members).reduce(function(carry, id) {
            carry[id] = $.extend({}, state.members[id]);
            carry[id].contactrequests = state.members[id].contactrequests.map(function(request) {
                return $.extend({}, request);
            });
            return carry;
        }, {});
        return newState;
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
                id: parseInt(message.id, 10),
                isRead: message.isread,
                fromLoggedInUser: fromLoggedInUser,
                userFrom: members[message.useridfrom],
                text: message.text,
                timeCreated: parseInt(message.timecreated, 10)
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
                profileimageurl: member.profileimageurl,
                profileimageurlsmall: member.profileimageurlsmall,
                isonline:  member.isonline,
                showonlinestatus: member.showonlinestatus,
                isblocked: member.isblocked,
                iscontact: member.iscontact,
                isdeleted: member.isdeleted,
                canmessage:  member.canmessage,
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
     * @return {Object} Initial state.
     */
    var buildInitialState = function(midnight, loggedInUserId, id) {
        return {
            midnight: midnight,
            loggedInUserId: loggedInUserId,
            id: id,
            name: null,
            subname: null,
            type: null,
            totalMemberCount: null,
            imageUrl: null,
            isFavourite: null,
            members: {},
            messages: [],
            hasTriedToLoadMessages: false,
            loadingMessages: true,
            sendingMessage: false,
            loadingMembers: true,
            loadingConfirmAction: false,
            pendingBlockUserIds: [],
            pendingUnblockUserIds: [],
            pendingRemoveContactIds: [],
            pendingAddContactIds: [],
            pendingDeleteMessageIds: [],
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
        var allMessages = state.messages.concat(formattedMessages);
        // Sort the messages. Oldest to newest.
        allMessages.sort(function(a, b) {
            if (a.timeCreated < b.timeCreated) {
                return -1;
            } else if (a.timeCreated > b.timeCreated) {
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
            return !index || message.id !== sortedMessages[index - 1].id;
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
            return message.id;
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
     * @param  {Array} messagesIds Message ids to remove from state.
     * @return {Object} state New state with removed messages.
     */
    var removeMessagesById = function(state, messagesIds) {
        var newState = cloneState(state);
        newState.messages = newState.messages.filter(function(message) {
            return messagesIds.indexOf(message.id) < 0;
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
     * Set the state sending message attribute.
     *
     * @param  {Object} state Current state.
     * @param  {Bool} value New sending message value.
     * @return {Object} New state with sending message attribute.
     */
    var setSendingMessage = function(state, value) {
        var newState = cloneState(state);
        newState.sendingMessage = value;
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

    return {
        buildInitialState: buildInitialState,
        addMessages: addMessages,
        removeMessages: removeMessages,
        removeMessagesById: removeMessagesById,
        addMembers: addMembers,
        removeMembers: removeMembers,
        setLoadingMessages: setLoadingMessages,
        setSendingMessage: setSendingMessage,
        setLoadingMembers: setLoadingMembers,
        setId: setId,
        setName: setName,
        setSubname: setSubname,
        setType: setType,
        setIsFavourite: setIsFavourite,
        setTotalMemberCount: setTotalMemberCount,
        setImageUrl: setImageUrl,
        setLoadingConfirmAction: setLoadingConfirmAction,
        setPendingDeleteConversation: setPendingDeleteConversation,
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
