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
 * Retrieves messages from the server.
 *
 * @module     core_message/message_repository
 * @class      message_repository
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    var CONVERSATION_TYPES = {
        PRIVATE: 1,
        PUBLIC: 2
    };

    /**
     * Retrieve a list of messages from the server.
     *
     * @param {object} args The request arguments:
     * @return {object} jQuery promise
     */
    var query = function(args) {
        // Normalise the arguments to use limit/offset rather than limitnum/limitfrom.
        if (typeof args.limit === 'undefined') {
            args.limit = 0;
        }

        if (typeof args.offset === 'undefined') {
            args.offset = 0;
        }

        if (typeof args.type === 'undefined') {
            args.type = null;
        }

        if (typeof args.favouritesonly === 'undefined') {
            args.favouritesonly = false;
        }

        args.limitfrom = args.offset;
        args.limitnum = args.limit;

        delete args.limit;
        delete args.offset;

        var request = {
            methodname: 'core_message_data_for_messagearea_conversations',
            args: args
        };

        var promise = Ajax.call([request])[0];

        promise.fail(Notification.exception);

        return promise;
    };

    /**
     * Count the number of unread conversations (one or more messages from a user)
     * for a given user.
     *
     * @param {object} args The request arguments:
     * @return {object} jQuery promise
     */
    var countUnreadConversations = function(args) {
        var request = {
            methodname: 'core_message_get_unread_conversations_count',
            args: args
        };

        var promise = Ajax.call([request])[0];

        promise.fail(Notification.exception);

        return promise;
    };

    /**
     * Mark all of unread messages for a user as read.
     *
     * @param {object} args The request arguments:
     * @return {object} jQuery promise
     */
    var markAllAsRead = function(args) {
        var request = {
            methodname: 'core_message_mark_all_messages_as_read',
            args: args
        };

        var promise = Ajax.call([request])[0];

        promise.fail(Notification.exception);

        return promise;
    };

    /**
     * Get contacts for given user.
     *
     * @param {int} userId The user id
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @return {object} jQuery promise
     */
    var getContacts = function(userId, limit, offset) {
        var args = {
            userid: userId
        };

        if (typeof limit !== 'undefined') {
            args.limitnum = limit;
        }

        if (typeof offset !== 'undefined') {
            args.limitfrom = offset;
        }

        var request = {
            methodname: 'core_message_get_user_contacts',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Request profile information as a user for a given user.
     *
     * @param {int} userId The requesting user
     * @param {int} profileUserId The id of the user who's profile is being requested
     * @return {object} jQuery promise
     */
    var getProfile = function(userId, profileUserId) {
        var request = {
            methodname: 'core_message_data_for_messagearea_get_profile',
            args: {
                currentuserid: userId,
                otheruserid: profileUserId
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Block a user.
     *
     * @param {int} userId The requesting user
     * @param {int} blockedUserId Id of user to block
     * @return {object} jQuery promise
     */
    var blockUser = function(userId, blockedUserId) {
        var requests = [
            {
                methodname: 'core_message_block_user',
                args: {
                    userid: userId,
                    blockeduserid: blockedUserId
                }
            },
            {
                methodname: 'core_message_get_member_info',
                args: {
                    referenceuserid: userId,
                    userids: [blockedUserId],
                    includecontactrequests: true,
                    includeprivacyinfo: true
                }
            }
        ];

        // Wrap both requests in a single promise so that we can catch an error
        // from either request.
        return $.when.apply(null, Ajax.call(requests)).then(function(reponse1, profiles) {
            // Only return the profile.
            return profiles.length ? profiles[0] : {};
        });
    };

    /**
     * Unblock a user.
     *
     * @param {int} userId The requesting user
     * @param {int} unblockedUserId Id of user to unblock
     * @return {object} jQuery promise
     */
    var unblockUser = function(userId, unblockedUserId) {
        var requests = [
            {
                methodname: 'core_message_unblock_user',
                args: {
                    userid: userId,
                    unblockeduserid: unblockedUserId
                }
            },
            {
                methodname: 'core_message_get_member_info',
                args: {
                    referenceuserid: userId,
                    userids: [unblockedUserId],
                    includecontactrequests: true,
                    includeprivacyinfo: true
                }
            }
        ];

        // Wrap both requests in a single promise so that we can catch an error
        // from either request.
        return $.when.apply(null, Ajax.call(requests)).then(function(reponse1, profiles) {
            // Only return the profile.
            return profiles.length ? profiles[0] : {};
        });
    };

    /**
     * Create a request to add a user as a contact.
     *
     * @param {int} userId The requesting user
     * @param {int[]} requestUserIds List of user ids to add
     * @return {object} jQuery promise
     */
    var createContactRequest = function(userId, requestUserIds) {
        var request = {
            methodname: 'core_message_create_contact_request',
            args: {
                userid: userId,
                requesteduserid: requestUserIds
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Remove a list of users as contacts.
     *
     * @param {int} userId The requesting user
     * @param {int[]} contactUserIds List of user ids to add
     * @return {object} jQuery promise
     */
    var deleteContacts = function(userId, contactUserIds) {
        var requests = [
            {
                methodname: 'core_message_delete_contacts',
                args: {
                    userid: userId,
                    userids: contactUserIds
                }
            },
            {
                methodname: 'core_message_get_member_info',
                args: {
                    referenceuserid: userId,
                    userids: contactUserIds,
                    includecontactrequests: true,
                    includeprivacyinfo: true
                }
            }
        ];

        return $.when.apply(null, Ajax.call(requests)).then(function(response1, profiles) {
            // Return all of the profiles as an array.
            return profiles;
        });
    };

    /**
     * Get messages between two users.
     *
     * @param {int} currentUserId The requesting user
     * @param {int} conversationId Other user in the conversation
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @param {bool} newestFirst Order results by newest first
     * @param {int} timeFrom Only return messages after this timestamp
     * @return {object} jQuery promise
     */
    var getMessages = function(currentUserId, conversationId, limit, offset, newestFirst, timeFrom) {
        var args = {
            currentuserid: currentUserId,
            convid: conversationId,
            newest: newestFirst ? true : false
        };

        if (typeof limit !== 'undefined') {
            args.limitnum = limit;
        }

        if (typeof offset !== 'undefined') {
            args.limitfrom = offset;
        }

        if (typeof timeFrom !== 'undefined') {
            args.timefrom = timeFrom;
        }

        var request = {
            methodname: 'core_message_get_conversation_messages',
            args: args
        };
        return Ajax.call([request])[0];
    };

    /**
     * Search for users.
     *
     * @param {int} userId The requesting user
     * @param {string} searchString Search string
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @return {object} jQuery promise
     */
    var searchUsers = function(userId, searchString, limit, offset) {
        var args = {
            userid: userId,
            search: searchString
        };

        if (typeof limit !== 'undefined') {
            args.limitnum = limit;
        }

        if (typeof offset !== 'undefined') {
            args.limitfrom = offset;
        }

        var request = {
            methodname: 'core_message_message_search_users',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Search for messages.
     *
     * @param {int} userId The requesting user
     * @param {string} searchString Search string
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @return {object} jQuery promise
     */
    var searchMessages = function(userId, searchString, limit, offset) {
        var args = {
            userid: userId,
            search: searchString
        };

        if (typeof limit !== 'undefined') {
            args.limitnum = limit;
        }

        if (typeof offset !== 'undefined') {
            args.limitfrom = offset;
        }

        var request = {
            methodname: 'core_message_data_for_messagearea_search_messages',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Send a list of messages to a user.
     *
     * @param {int} toUserId The recipient user id
     * @param {string[]} messages List of messages to send
     * @return {object} jQuery promise
     */
    var sendMessagesToUser = function(toUserId, messages) {
        var formattedMessages = messages.map(function(message) {
            return {
                touserid: toUserId,
                text: message
            };
        });
        var request = {
            methodname: 'core_message_send_instant_messages',
            args: {
                messages: formattedMessages
            }
        };

        return Ajax.call([request])[0]
            .then(function(results) {
                // Error handling for the weird way the old function works.
                var errors = results.reduce(function(carry, result) {
                    if (result.errormessage) {
                        carry.push(result.errormessage);
                    }

                    return carry;
                }, []);
                if (errors.length) {
                    throw new Error(errors.join("\n"));
                }

                return results;
            })
            .then(function(results) {
                // Format the results to match the other send message function.
                return results.map(function(result) {
                    return {
                        id: result.msgid,
                        text: result.text,
                        timecreated: result.timecreated,
                        useridfrom: result.useridfrom,
                        conversationid: result.conversationid
                    };
                });
            });
    };

    /**
     * Send a single message to a user.
     *
     * @param {int} toUserId The recipient user id
     * @param {string} text The message text
     * @return {object} jQuery promise
     */
    var sendMessageToUser = function(toUserId, text) {
        return sendMessagesToUser(toUserId, [text])
            .then(function(results) {
                return results[0];
            });
    };

    /**
     * Send messages to a conversation.
     *
     * @param {int} conversationId The conversation id
     * @param {string[]} messages List of messages to send
     * @return {object} jQuery promise
     */
    var sendMessagesToConversation = function(conversationId, messages) {
        var formattedMessages = messages.map(function(message) {
            return {
                text: message
            };
        });
        var request = {
            methodname: 'core_message_send_messages_to_conversation',
            args: {
                conversationid: conversationId,
                messages: formattedMessages
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Send a message to a conversation.
     *
     * @param {int} conversationId The conversation id
     * @param {string} text The message text
     * @return {object} jQuery promise
     */
    var sendMessageToConversation = function(conversationId, text) {
        return sendMessagesToConversation(conversationId, [text])
            .then(function(result) {
                return result[0];
            });
    };

    /**
     * Save message preferences.
     *
     * @param {int} userId The owner of the preferences
     * @param {object[]} preferences New preferences values
     * @return {object} jQuery promise
     */
    var savePreferences = function(userId, preferences) {
        var request = {
            methodname: 'core_user_update_user_preferences',
            args: {
                userid: userId,
                preferences: preferences
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get the user's preferences.
     *
     * @param {int} userId The target user
     * @return {object} jQuery promise
     */
    var getPreferences = function(userId) {
        var request = {
            methodname: 'core_user_get_user_preferences',
            args: {
                userid: userId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Delete a list of messages.
     *
     * @param {int} userId The user to delete messages for
     * @param {int[]} messageIds List of message ids to delete
     * @return {object} jQuery promise
     */
    var deleteMessages = function(userId, messageIds) {
        return $.when.apply(null, Ajax.call(messageIds.map(function(messageId) {
            return {
                methodname: 'core_message_delete_message',
                args: {
                    messageid: messageId,
                    userid: userId
                }
            };
        })));
    };

    /**
     * Delete a conversation between two users.
     *
     * @param {int} userId The user to delete messages for
     * @param {int} conversationId The id of the conversation
     * @return {object} jQuery promise
     */
    var deleteConversation = function(userId, conversationId) {
        var request = {
            methodname: 'core_message_delete_conversations_by_id',
            args: {
                userid: userId,
                conversationids: [conversationId]
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get the list of contact requests for a user.
     *
     * @param {int} userId The user id
     * @return {object} jQuery promise
     */
    var getContactRequests = function(userId) {
        var request = {
            methodname: 'core_message_get_contact_requests',
            args: {
                userid: userId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Accept a contact request.
     *
     * @param {int} sendingUserId The user that sent the request
     * @param {int} recipientUserId The user that received the request
     * @return {object} jQuery promise
     */
    var acceptContactRequest = function(sendingUserId, recipientUserId) {
        var requests = [
            {
                methodname: 'core_message_confirm_contact_request',
                args: {
                    userid: sendingUserId,
                    requesteduserid: recipientUserId
                }
            },
            {
                methodname: 'core_message_get_member_info',
                args: {
                    referenceuserid: recipientUserId,
                    userids: [sendingUserId],
                    includecontactrequests: true,
                    includeprivacyinfo: true
                }
            }
        ];

        // Wrap both requests in a single promise so that we can catch an error
        // from either request.
        return $.when.apply(null, Ajax.call(requests)).then(function(reponse1, profiles) {
            // Only return the profile.
            return profiles.length ? profiles[0] : {};
        });
    };

    /**
     * Decline a contact request.
     *
     * @param {int} sendingUserId The user that sent the request
     * @param {int} recipientUserId The user that received the request
     * @return {object} jQuery promise
     */
    var declineContactRequest = function(sendingUserId, recipientUserId) {
        var requests = [
            {
                methodname: 'core_message_decline_contact_request',
                args: {
                    userid: sendingUserId,
                    requesteduserid: recipientUserId
                }
            },
            {
                methodname: 'core_message_get_member_info',
                args: {
                    referenceuserid: recipientUserId,
                    userids: [sendingUserId],
                    includecontactrequests: true,
                    includeprivacyinfo: true
                }
            }
        ];

        // Wrap both requests in a single promise so that we can catch an error
        // from either request.
        return $.when.apply(null, Ajax.call(requests)).then(function(reponse1, profiles) {
            // Only return the profile.
            return profiles.length ? profiles[0] : {};
        });
    };

    /**
     * Get a conversation.
     *
     * @param {int} loggedInUserId The logged in user
     * @param {int} conversationId The conversation id
     * @param {bool} includeContactRequests Incldue contact requests between members
     * @param {bool} includePrivacyInfo Include privacy info for members
     * @param {int} memberLimit Limit for members
     * @param {int} memberOffset Offset for members
     * @param {int} messageLimit Limit for messages
     * @param {int} messageOffset Offset for messages
     * @param {bool} newestMessagesFirst Order the messages by newest first
     * @return {object} jQuery promise
     */
    var getConversation = function(
        loggedInUserId,
        conversationId,
        includeContactRequests,
        includePrivacyInfo,
        memberLimit,
        memberOffset,
        messageLimit,
        messageOffset,
        newestMessagesFirst
    ) {
        var args = {
            userid: loggedInUserId,
            conversationid: conversationId
        };

        if (typeof includeContactRequests != 'undefined' && includeContactRequests !== null) {
            args.includecontactrequests = includeContactRequests;
        }

        if (typeof includePrivacyInfo != 'undefined' && includePrivacyInfo !== null) {
            args.includeprivacyinfo = includePrivacyInfo;
        }

        if (typeof memberLimit != 'undefined' && memberLimit !== null) {
            args.memberlimit = memberLimit;
        }

        if (typeof memberOffset != 'undefined' && memberOffset !== null) {
            args.memberoffset = memberOffset;
        }

        if (typeof messageLimit != 'undefined' && messageLimit !== null) {
            args.messagelimit = messageLimit;
        }

        if (typeof messageOffset != 'undefined' && messageOffset !== null) {
            args.messageoffset = messageOffset;
        }

        if (typeof newestMessagesFirst != 'undefined' && newestMessagesFirst !== null) {
            args.newestmessagesfirst = newestMessagesFirst;
        }

        var request = {
            methodname: 'core_message_get_conversation',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get a conversation between users.
     *
     * @param {int} loggedInUserId The logged in user
     * @param {int} otherUserId The other user id
     * @param {bool} includeContactRequests Incldue contact requests between members
     * @param {bool} includePrivacyInfo Include privacy info for members
     * @param {int} memberLimit Limit for members
     * @param {int} memberOffset Offset for members
     * @param {int} messageLimit Limit for messages
     * @param {int} messageOffset Offset for messages
     * @param {bool} newestMessagesFirst Order the messages by newest first
     * @return {object} jQuery promise
     */
    var getConversationBetweenUsers = function(
        loggedInUserId,
        otherUserId,
        includeContactRequests,
        includePrivacyInfo,
        memberLimit,
        memberOffset,
        messageLimit,
        messageOffset,
        newestMessagesFirst
    ) {
        var args = {
            userid: loggedInUserId,
            otheruserid: otherUserId
        };

        if (typeof includeContactRequests != 'undefined' && includeContactRequests !== null) {
            args.includecontactrequests = includeContactRequests;
        }

        if (typeof includePrivacyInfo != 'undefined' && includePrivacyInfo !== null) {
            args.includeprivacyinfo = includePrivacyInfo;
        }

        if (typeof memberLimit != 'undefined' && memberLimit !== null) {
            args.memberlimit = memberLimit;
        }

        if (typeof memberOffset != 'undefined' && memberOffset !== null) {
            args.memberoffset = memberOffset;
        }

        if (typeof messageLimit != 'undefined' && messageLimit !== null) {
            args.messagelimit = messageLimit;
        }

        if (typeof messageOffset != 'undefined' && messageOffset !== null) {
            args.messageoffset = messageOffset;
        }

        if (typeof newestMessagesFirst != 'undefined' && newestMessagesFirst !== null) {
            args.newestmessagesfirst = newestMessagesFirst;
        }

        var request = {
            methodname: 'core_message_get_conversation_between_users',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get the conversations for a user.
     *
     * @param {int} userId The logged in user
     * @param {int|null} type The type of conversation to get
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @param {bool|null} favourites If favourites should be included or not
     * @return {object} jQuery promise
     */
    var getConversations = function(
        userId,
        type,
        limit,
        offset,
        favourites
    ) {
        var args = {
            userid: userId,
            type: type
        };

        if (typeof limit != 'undefined' && limit !== null) {
            args.limitnum = limit;
        }

        if (typeof offset != 'undefined' && offset !== null) {
            args.limitfrom = offset;
        }

        if (typeof favourites != 'undefined' && favourites !== null) {
            args.favourites = favourites;
        }

        var request = {
            methodname: 'core_message_get_conversations',
            args: args
        };

        return Ajax.call([request])[0]
            .then(function(result) {
                if (result.conversations.length) {
                    result.conversations = result.conversations.map(function(conversation) {
                        if (conversation.type == CONVERSATION_TYPES.PRIVATE) {
                            var otherUser = conversation.members.length ? conversation.members[0] : null;

                            if (otherUser) {
                                conversation.name = conversation.name ? conversation.name : otherUser.fullname;
                                conversation.imageurl = conversation.imageurl ? conversation.imageurl : otherUser.profileimageurl;
                            }
                        }

                        return conversation;
                    });
                }

                return result;
            });
    };

    /**
     * Get the conversations for a user.
     *
     * @param {int} conversationId The conversation id
     * @param {int} loggedInUserId The logged in user
     * @param {int} limit Limit for results
     * @param {int} offset Offset for results
     * @param {bool} includeContactRequests If contact requests should be included in result
     * @return {object} jQuery promise
     */
    var getConversationMembers = function(conversationId, loggedInUserId, limit, offset, includeContactRequests) {
        var args = {
            userid: loggedInUserId,
            conversationid: conversationId
        };

        if (typeof limit != 'undefined' && limit !== null) {
            args.limitnum = limit;
        }

        if (typeof offset != 'undefined' && offset !== null) {
            args.limitfrom = offset;
        }

        if (typeof includeContactRequests != 'undefined' && includeContactRequests !== null) {
            args.includecontactrequests = includeContactRequests;
        }

        var request = {
            methodname: 'core_message_get_conversation_members',
            args: args
        };

        return Ajax.call([request])[0];
    };

    /**
     * Set a list of conversations to set as favourites for the given user.
     *
     * @param {int} userId The user id
     * @param {array} conversationIds List of conversation ids to set as favourite
     * @return {object} jQuery promise
     */
    var setFavouriteConversations = function(userId, conversationIds) {

        var request = {
            methodname: 'core_message_set_favourite_conversations',
            args: {
                userid: userId,
                conversations: conversationIds
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Set a list of conversations to unset as favourites for the given user.
     *
     * @param {int} userId The user id
     * @param {array} conversationIds List of conversation ids to unset as favourite
     * @return {object} jQuery promise
     */
    var unsetFavouriteConversations = function(userId, conversationIds) {

        var request = {
            methodname: 'core_message_unset_favourite_conversations',
            args: {
                userid: userId,
                conversations: conversationIds
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get a list of user's member info.
     *
     * @param {int} referenceUserId The user id
     * @param {array} userIds List of user ids to get
     * @param {bool} includeContactRequests Include contact requests between users in response
     * @param {bool} includePrivacyInfo Include privacy info for reference user in response
     * @return {object} jQuery promise
     */
    var getMemberInfo = function(referenceUserId, userIds, includeContactRequests, includePrivacyInfo) {
        var args = {
            referenceuserid: referenceUserId,
            userids: userIds
        };

        if (typeof includeContactRequests != 'undefined') {
            args.includecontactrequests = includeContactRequests;
        }

        if (typeof includePrivacyInfo != 'undefined') {
            args.includeprivacyinfo = includePrivacyInfo;
        }

        var request = {
            methodname: 'core_message_get_member_info',
            args: args
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get a list of user's member info.
     *
     * @param {int} userId The user id to mark as read for
     * @param {int} conversationId The conversation to mark as read
     * @return {object} jQuery promise
     */
    var markAllConversationMessagesAsRead = function(userId, conversationId) {

        var request = {
            methodname: 'core_message_mark_all_conversation_messages_as_read',
            args: {
                userid: userId,
                conversationid: conversationId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get the user's message preferences.
     *
     * @param {int} userId The user id to load preferences for
     * @return {object} jQuery promise
     */
    var getUserMessagePreferences = function(userId) {
        var request = {
            methodname: 'core_message_get_user_message_preferences',
            args: {
                userid: userId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * The the count of the user's conversations grouped by type.
     *
     * @param {Number} userId The user's id.
     * @return {Object} jQuery promise.
     */
    var getTotalConversationCounts = function(userId) {
        var request = {
            methodname: 'core_message_get_conversation_counts',
            args: {
                userid: userId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * The the count of the user's unread conversations grouped by type.
     *
     * @param {Number} userId The user's id.
     * @return {Object} jQuery promise.
     */
    var getUnreadConversationCounts = function(userId) {
        var request = {
            methodname: 'core_message_get_unread_conversation_counts',
            args: {
                userid: userId
            }
        };
        return Ajax.call([request])[0];
    };

    /**
     * Get both the unread and total conversation counts in a single request.
     *
     * @param {Number} userId The user's id.
     * @return {Object} jQuery promise.
     */
    var getAllConversationCounts = function(userId) {
        var requests = [
            {
                methodname: 'core_message_get_conversation_counts',
                args: {
                    userid: userId
                }
            },
            {
                methodname: 'core_message_get_unread_conversation_counts',
                args: {
                    userid: userId
                }
            },
        ];
        return $.when.apply(null, Ajax.call(requests)).then(function(total, unread) {
            return {
                total: total,
                unread: unread
            };
        });
    };

    return {
        query: query,
        countUnreadConversations: countUnreadConversations,
        markAllAsRead: markAllAsRead,
        getContacts: getContacts,
        getProfile: getProfile,
        blockUser: blockUser,
        unblockUser: unblockUser,
        createContactRequest: createContactRequest,
        deleteContacts: deleteContacts,
        getMessages: getMessages,
        searchUsers: searchUsers,
        searchMessages: searchMessages,
        sendMessagesToUser: sendMessagesToUser,
        sendMessageToUser: sendMessageToUser,
        sendMessagesToConversation: sendMessagesToConversation,
        sendMessageToConversation: sendMessageToConversation,
        savePreferences: savePreferences,
        getPreferences: getPreferences,
        deleteMessages: deleteMessages,
        deleteConversation: deleteConversation,
        getContactRequests: getContactRequests,
        acceptContactRequest: acceptContactRequest,
        declineContactRequest: declineContactRequest,
        getConversation: getConversation,
        getConversationBetweenUsers: getConversationBetweenUsers,
        getConversations: getConversations,
        getConversationMembers: getConversationMembers,
        setFavouriteConversations: setFavouriteConversations,
        unsetFavouriteConversations: unsetFavouriteConversations,
        getMemberInfo: getMemberInfo,
        markAllConversationMessagesAsRead: markAllConversationMessagesAsRead,
        getUserMessagePreferences: getUserMessagePreferences,
        getTotalConversationCounts: getTotalConversationCounts,
        getUnreadConversationCounts: getUnreadConversationCounts,
        getAllConversationCounts: getAllConversationCounts
    };
});
