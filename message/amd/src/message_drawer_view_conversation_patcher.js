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
 * This module will take 2 view states from the message_drawer_view_conversation
 * module and generate a patch that can be given to the
 * message_drawer_view_conversation_renderer module to update the UI.
 *
 * This module should never modify either state. It's purely a read only
 * module.
 *
 * @module     core_message/message_drawer_view_conversation_patcher
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/user_date',
    'core_message/message_drawer_view_conversation_constants'
],
function(
    $,
    UserDate,
    Constants
) {
    /**
     * Sort messages by day.
     *
     * @param  {Array} messages The list of messages to sort.
     * @param  {Number} midnight User's midnight timestamp.
     * @return {Array} messages sorted by day.
     */
    var sortMessagesByDay = function(messages, midnight) {
        var messagesByDay = messages.reduce(function(carry, message) {
            var dayTimestamp = UserDate.getUserMidnightForTimestamp(message.timeCreated, midnight);

            if (carry.hasOwnProperty(dayTimestamp)) {
                carry[dayTimestamp].push(message);
            } else {
                carry[dayTimestamp] = [message];
            }

            return carry;
        }, {});

        return Object.keys(messagesByDay).map(function(dayTimestamp) {
            return {
                timestamp: dayTimestamp,
                messages: messagesByDay[dayTimestamp]
            };
        });
    };

    /**
     * Diff 2 arrays using a match function
     *
     * @param  {Array} a The first array.
     * @param  {Array} b The second array.
     * @param  {Function} matchFunction Function used for matching array items.
     * @return {Object} Object containing array items missing from a, array items missing from b
     * and matches
     */
    var diffArrays = function(a, b, matchFunction) {
        // Make copy of it.
        b = b.slice();
        var missingFromA = [];
        var missingFromB = [];
        var matches = [];

        a.forEach(function(current) {
            var found = false;
            var index = 0;

            for (; index < b.length; index++) {
                var next = b[index];

                if (matchFunction(current, next)) {
                    found = true;
                    matches.push({
                        a: current,
                        b: next
                    });
                    break;
                }
            }

            if (found) {
                // This day has been processed so removed it from the list.
                b.splice(index, 1);
            } else {
                // If we couldn't find it in the next messages then it means
                // it needs to be added.
                missingFromB.push(current);
            }
        });

        missingFromA = b;

        return {
            missingFromA: missingFromA,
            missingFromB: missingFromB,
            matches: matches
        };
    };

    /**
     * Find an element in a array based on a matching function.
     *
     * @param  {array} array Array to search.
     * @param  {Function} breakFunction Function to run on array item.
     * @return {*} The array item.
     */
    var findPositionInArray = function(array, breakFunction) {
        var before = null;

        for (var i = 0; i < array.length; i++) {
            var candidate = array[i];

            if (breakFunction(candidate)) {
                return candidate;
            }
        }

        return before;
    };

    /**
     * Check if 2 arrays are equal.
     *
     * @param  {Array} a The first array.
     * @param  {Array} b The second array.
     * @return {Boolean} Are arrays equal.
     */
    var isArrayEqual = function(a, b) {
        a.sort();
        b.sort();
        var aLength = a.length;
        var bLength = b.length;

        if (aLength < 1 && bLength < 1) {
            return true;
        }

        if (aLength != bLength) {
            return false;
        }

        return a.every(function(item, index) {
            return item == b[index];
        });
    };

    /**
     * Build a patch based on days.
     *
     * @param  {Object} current Current list current items.
     * @param  {Object} daysDiff Difference between current and new.
     * @return {Object} Patch with elements to add and remove.
     */
    var buildDaysPatch = function(current, daysDiff) {
        return {
            remove: daysDiff.missingFromB,
            add: daysDiff.missingFromA.map(function(day) {
                // Any days left over in the "next" list weren't in the "current" list
                // so they will need to be added.
                var before = findPositionInArray(current, function(candidate) {
                    return day.timestamp < candidate.timestamp;
                });

                return {
                    before: before,
                    value: day
                };
            })
        };
    };

    /**
     * Build the messages patch for each day.
     *
     * @param {Array} matchingDays Array of old and new messages sorted by day.
     * @return {Object} patch.
     */
    var buildMessagesPatch = function(matchingDays) {
        var remove = [];
        var add = [];

        matchingDays.forEach(function(days) {
            var dayCurrent = days.a;
            var dayNext = days.b;
            var messagesDiff = diffArrays(dayCurrent.messages, dayNext.messages, function(messageCurrent, messageNext) {
                return messageCurrent.id == messageNext.id;
            });

            remove = remove.concat(messagesDiff.missingFromB);

            messagesDiff.missingFromA.forEach(function(message) {
                var before = findPositionInArray(dayCurrent.messages, function(candidate) {
                    if (message.timeCreated == candidate.timeCreated) {
                        return message.id < candidate.id;
                    } else {
                        return message.timeCreated < candidate.timeCreated;
                    }
                });

                add.push({
                    before: before,
                    value: message,
                    day: dayCurrent
                });
            });
        });

        return {
            add: add,
            remove: remove
        };
    };

    /**
     * Build a patch for this conversation.
     *
     * @param  {Object} state, The current state of this conversation.
     * @param  {Object} newState, The new state of this conversation.
     * @return {Object} Patch with days and messsages for each day.
     */
    var buildConversationPatch = function(state, newState) {
        var oldMessageIds = state.messages.map(function(message) {
            return message.id;
        });
        var newMessageIds = newState.messages.map(function(message) {
            return message.id;
        });

        if (!isArrayEqual(oldMessageIds, newMessageIds)) {
            var current = sortMessagesByDay(state.messages, state.midnight);
            var next = sortMessagesByDay(newState.messages, newState.midnight);
            var daysDiff = diffArrays(current, next, function(dayCurrent, dayNext) {
                return dayCurrent.timestamp == dayNext.timestamp;
            });

            return {
                days: buildDaysPatch(current, daysDiff),
                messages: buildMessagesPatch(daysDiff.matches)
            };
        } else {
            return null;
        }
    };

    /**
     * Build a patch for the header of this conversation. Check if this conversation
     * is a group conversation.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} patch
     */
    var buildHeaderPatchTypePrivate = function(state, newState) {
        var requireAddContact = buildRequireAddContact(state, newState);
        var confirmContactRequest = buildConfirmContactRequest(state, newState);
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);
        var requiresAddContact = requireAddContact && requireAddContact.show && !requireAddContact.hasMessages;
        var requiredAddContact = requireAddContact && !requireAddContact.show;
        // Render the header once we've got a user.
        var shouldRenderHeader = !oldOtherUser && newOtherUser;
        // We should also re-render the header if the other user requires
        // being added as a contact or if they did but no longer do.
        shouldRenderHeader = shouldRenderHeader || requiresAddContact || requiredAddContact;
        // Finally, we should re-render if the other user has sent this user
        // a contact request that is waiting for approval or if it's been approved/declined.
        shouldRenderHeader = shouldRenderHeader || confirmContactRequest !== null;

        if (shouldRenderHeader) {
            return {
                type: Constants.CONVERSATION_TYPES.PRIVATE,
                // We can show controls if the other user doesn't require add contact
                // and we aren't waiting for this user to respond to a contact request.
                showControls: !requiresAddContact && !confirmContactRequest,
                context: {
                    id: newState.id,
                    name: newState.name,
                    subname: newState.subname,
                    totalmembercount: newState.totalMemberCount,
                    imageurl: newState.imageUrl,
                    isfavourite: newState.isFavourite,
                    // Don't show favouriting if we don't have a conversation.
                    showfavourite: newState.id !== null,
                    userid: newOtherUser.id,
                    showonlinestatus: newOtherUser.showonlinestatus,
                    isonline: newOtherUser.isonline,
                    isblocked: newOtherUser.isblocked,
                    iscontact: newOtherUser.iscontact
                }
            };
        }

        return null;
    };


    /**
     * Build a patch for the header of this conversation. Check if this conversation
     * is a group conversation.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} patch
     */
    var buildHeaderPatchTypePublic = function(state, newState) {
        var oldMemberCount = state.totalMemberCount;
        var newMemberCount = newState.totalMemberCount;

        if (oldMemberCount != newMemberCount) {
            return {
                type: Constants.CONVERSATION_TYPES.PUBLIC,
                showControls: true,
                context: {
                    id: newState.id,
                    name: newState.name,
                    subname: newState.subname,
                    totalmembercount: newState.totalMemberCount,
                    imageurl: newState.imageUrl,
                    isfavourite: newState.isFavourite,
                    // Don't show favouriting if we don't have a conversation.
                    showfavourite: newState.id !== null
                }
            };
        } else {
            return null;
        }
    };

    /**
     * Find the newest or oldest message.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Number} Oldest or newest message id.
     */
    var buildScrollToMessagePatch = function(state, newState) {
        var oldMessages = state.messages;
        var newMessages = newState.messages;

        if (newMessages.length < 1) {
            return null;
        }

        if (oldMessages.length < 1) {
            return newMessages[newMessages.length - 1].id;
        }

        var previousNewest = oldMessages[state.messages.length - 1];
        var currentNewest = newMessages[newMessages.length - 1];
        var previousOldest = oldMessages[0];
        var currentOldest = newMessages[0];

        if (previousNewest.id != currentNewest.id) {
            return currentNewest.id;
        } else if (previousOldest.id != currentOldest.id) {
            return previousOldest.id;
        }

        return null;
    };

    /**
     * Check if members should be loaded.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildLoadingMembersPatch = function(state, newState) {
        if (!state.loadingMembers && newState.loadingMembers) {
            return true;
        } else if (state.loadingMembers && !newState.loadingMembers) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Check if the messages are being loaded for the first time.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildLoadingFirstMessages = function(state, newState) {
        if (state.hasTriedToLoadMessages === newState.hasTriedToLoadMessages) {
            return null;
        } else if (!newState.hasTriedToLoadMessages && newState.loadingMessages) {
            return true;
        } else if (newState.hasTriedToLoadMessages && !newState.loadingMessages) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Check if the messages are still being loaded
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildLoadingMessages = function(state, newState) {
        if (!state.loadingMessages && newState.loadingMessages) {
            return true;
        } else if (state.loadingMessages && !newState.loadingMessages) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Check if the messages are still being send
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null} User Object if Object.
     */
    var buildSendingMessage = function(state, newState) {
        if (!state.sendingMessage && newState.sendingMessage) {
            return true;
        } else if (state.sendingMessage && !newState.sendingMessage) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Get the user Object of user to be blocked if pending.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object|Bool|Null} User Object if Object.
     */
    var buildConfirmBlockUser = function(state, newState) {
        if (newState.pendingBlockUserIds.length) {
            // We currently only support a single user;
            var userId = newState.pendingBlockUserIds[0];
            return newState.members[userId];
        } else if (state.pendingBlockUserIds.length) {
            return false;
        }

        return null;
    };

    /**
     * Get the user Object of user to be unblocked if pending.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object|Bool|Null} User Object if Object.
     */
    var buildConfirmUnblockUser = function(state, newState) {
        if (newState.pendingUnblockUserIds.length) {
            // We currently only support a single user;
            var userId = newState.pendingUnblockUserIds[0];
            return newState.members[userId];
        } else if (state.pendingUnblockUserIds.length) {
            return false;
        }

        return null;
    };

    /**
     * Get the user Object of user to be added as contact if pending.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object|Bool|Null} User Object if Object.
     */
    var buildConfirmAddContact = function(state, newState) {
        if (newState.pendingAddContactIds.length) {
            // We currently only support a single user;
            var userId = newState.pendingAddContactIds[0];
            return newState.members[userId];
        } else if (state.pendingAddContactIds.length) {
            return false;
        }

        return null;
    };

    /**
     * Get the user Object of user to be removed as contact if pending.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object|Bool|Null} User Object if Object.
     */
    var buildConfirmRemoveContact = function(state, newState) {
        if (newState.pendingRemoveContactIds.length) {
            // We currently only support a single user;
            var userId = newState.pendingRemoveContactIds[0];
            return newState.members[userId];
        } else if (state.pendingRemoveContactIds.length) {
            return false;
        }

        return null;
    };

    /**
     * Check if there are any messages to be deleted.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildConfirmDeleteSelectedMessages = function(state, newState) {
        if (newState.pendingDeleteMessageIds.length) {
            return true;
        } else if (state.pendingDeleteMessageIds.length) {
            return false;
        }

        return null;
    };

    /**
     * Check if there is a conversation to be deleted.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildConfirmDeleteConversation = function(state, newState) {
        if (!state.pendingDeleteConversation && newState.pendingDeleteConversation) {
            return true;
        } else if (state.pendingDeleteConversation && !newState.pendingDeleteConversation) {
            return false;
        }

        return null;
    };

    /**
     * Check if there is a pending contact request to accept or decline.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildConfirmContactRequest = function(state, newState) {
        var loggedInUserId = state.loggedInUserId;
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);
        var oldReceivedRequests = !oldOtherUser ? [] : oldOtherUser.contactrequests.filter(function(request) {
            return request.requesteduserid == loggedInUserId && request.userid == oldOtherUser.id;
        });
        var newReceivedRequests = !newOtherUser ? [] : newOtherUser.contactrequests.filter(function(request) {
            return request.requesteduserid == loggedInUserId && request.userid == newOtherUser.id;
        });
        var oldRequest = oldReceivedRequests.length ? oldReceivedRequests[0] : null;
        var newRequest = newReceivedRequests.length ? newReceivedRequests[0] : null;

        if (!oldRequest && newRequest) {
            return newOtherUser;
        } else if (oldRequest && !newRequest) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Check if there are any changes in blocked users.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildIsBlocked = function(state, newState) {
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);

        if (!oldOtherUser && !newOtherUser) {
            return null;
        } else if (!oldOtherUser && newOtherUser) {
            return newOtherUser.isblocked ? true : null;
        } else if (!newOtherUser && oldOtherUser) {
            return oldOtherUser.isblocked ? false : null;
        } else if (oldOtherUser.isblocked && !newOtherUser.isblocked) {
            return false;
        } else if (!oldOtherUser.isblocked && newOtherUser.isblocked) {
            return true;
        } else {
            return null;
        }
    };

    /**
     * Check if there are any changes the conversation favourite state.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildIsFavourite = function(state, newState) {
        var oldIsFavourite = state.isFavourite;
        var newIsFavourite = newState.isFavourite;

        if (state.id === null && newState.id === null) {
            // The conversation isn't yet created so don't change anything.
            return null;
        } else if (state.id === null && newState.id !== null) {
            // The conversation was created so we can show the add favourite button.
            return 'show-add';
        } else if (state.id !== null && newState.id === null) {
            // We're changing from a created conversation to a new conversation so hide
            // the favouriting functionality for now.
            return 'hide';
        } else if (oldIsFavourite == newIsFavourite) {
            // No change.
            return null;
        } else if (!oldIsFavourite && newIsFavourite) {
            return 'show-remove';
        } else if (oldIsFavourite && !newIsFavourite) {
            return 'show-add';
        } else {
            return null;
        }
    };

    /**
     * Check if there are any changes in the contact status of the current user
     * and other user.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildIsContact = function(state, newState) {
        var loggedInUserId = state.loggedInUserId;
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);
        var oldContactRequests = !oldOtherUser ? [] : oldOtherUser.contactrequests.filter(function(request) {
            return (request.userid == loggedInUserId && request.requesteduserid == oldOtherUser.id) ||
                (request.userid == oldOtherUser.id && request.requesteduserid == loggedInUserId);
        });
        var newContactRequests = !newOtherUser ? [] : newOtherUser.contactrequests.filter(function(request) {
            return (request.userid == loggedInUserId && request.requesteduserid == newOtherUser.id) ||
                (request.userid == newOtherUser.id && request.requesteduserid == loggedInUserId);
        });
        var oldHasContactRequests = oldContactRequests.length > 0;
        var newHasContactRequests = newContactRequests.length > 0;

        if (!oldOtherUser && !newOtherUser) {
            return null;
        } else if (oldHasContactRequests && newHasContactRequests) {
            return null;
        } else if (!oldHasContactRequests && newHasContactRequests && !newOtherUser.iscontact) {
            return 'pending-contact';
        } else if (!oldOtherUser && newOtherUser) {
            return newOtherUser.iscontact ? 'contact' : null;
        } else if (!newOtherUser && oldOtherUser) {
            return oldOtherUser.iscontact ? 'non-contact' : null;
        } else if (oldOtherUser.iscontact && !newOtherUser.iscontact) {
            return newHasContactRequests ? 'pending-contact' : 'non-contact';
        } else if (!oldOtherUser.iscontact && newOtherUser.iscontact) {
            return 'contact';
        } else {
            return null;
        }
    };

    /**
     * Check if a confirm action is active.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildLoadingConfirmationAction = function(state, newState) {
        if (!state.loadingConfirmAction && newState.loadingConfirmAction) {
            return true;
        } else if (state.loadingConfirmAction && !newState.loadingConfirmAction) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Check if a edit mode is active.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildInEditMode = function(state, newState) {
        var oldHasSelectedMessages = state.selectedMessageIds.length > 0;
        var newHasSelectedMessages = newState.selectedMessageIds.length > 0;
        var numberOfMessagesHasChanged = state.messages.length != newState.messages.length;

        if (!oldHasSelectedMessages && newHasSelectedMessages) {
            return true;
        } else if (oldHasSelectedMessages && !newHasSelectedMessages) {
            return false;
        } else if (oldHasSelectedMessages && numberOfMessagesHasChanged) {
            return true;
        } else {
            return null;
        }
    };

    /**
     * Build a patch for the messages selected.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} patch
     */
    var buildSelectedMessages = function(state, newState) {
        var oldSelectedMessages = state.selectedMessageIds;
        var newSelectedMessages = newState.selectedMessageIds;

        if (isArrayEqual(oldSelectedMessages, newSelectedMessages)) {
            return null;
        }

        var diff = diffArrays(oldSelectedMessages, newSelectedMessages, function(a, b) {
            return a == b;
        });

        return {
            count: newSelectedMessages.length,
            add: diff.missingFromA,
            remove: diff.missingFromB
        };
    };

    /**
     * Get a list of users from the state that are not the logged in user. Use to find group
     * message members or the other user in a conversation.
     *
     * @param  {Object} state State
     * @return {Array} List of users.
     */
    var getOtherUserFromState = function(state) {
        return Object.keys(state.members).reduce(function(carry, userId) {
            if (userId != state.loggedInUserId && !carry) {
                carry = state.members[userId];
            }

            return carry;
        }, null);
    };

    /**
     * Check if the given user requires a contact request from the logged in user.
     *
     * @param  {Integer} loggedInUserId The logged in user id
     * @param  {Object} user User record
     * @return {Bool}
     */
    var requiresContactRequest = function(loggedInUserId, user) {
        var contactRequests = user.contactrequests.filter(function(request) {
            return request.userid == loggedInUserId || request.requesteduserid;
        });
        var hasSentContactRequest = contactRequests.length > 0;
        return user.requirescontact && !user.iscontact && !hasSentContactRequest;
    };

    /**
     * Check if other users are required to be added as contact.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} Object controlling the required to add contact dialog variables.
     */
    var buildRequireAddContact = function(state, newState) {
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);
        var hadMessages = state.messages.length > 0;
        var hasMessages = newState.messages.length > 0;
        var loggedInUserId = newState.loggedInUserId;
        var prevRequiresContactRequest = oldOtherUser && requiresContactRequest(loggedInUserId, oldOtherUser);
        var nextRequiresContactRequest = newOtherUser && requiresContactRequest(loggedInUserId, newOtherUser);
        var confirmAddContact = buildConfirmAddContact(state, newState);
        var finishedAddContact = confirmAddContact === false;

        // Still doing first load.
        if (!state.hasTriedToLoadMessages && !newState.hasTriedToLoadMessages) {
            return null;
        }

        // No users yet.
        if (!oldOtherUser && !newOtherUser) {
            return null;
        }

        // We've loaded a new user and they require a contact request.
        if (!oldOtherUser && nextRequiresContactRequest) {
            return {
                show: true,
                hasMessages: hasMessages,
                user: newOtherUser
            };
        }

        // The logged in user has completed the confirm contact request dialogue
        // but the other user still requires a contact request which means the logged
        // in user either declined the confirmation or it failed.
        if (finishedAddContact && nextRequiresContactRequest) {
            return {
                show: true,
                hasMessages: hasMessages,
                user: newOtherUser
            };
        }

        // Everything is loaded.
        if (state.hasTriedToLoadMessages && newState.hasTriedToLoadMessages) {
            if (!prevRequiresContactRequest && nextRequiresContactRequest) {
                return {
                    show: true,
                    hasMessages: hasMessages,
                    user: newOtherUser
                };
            }

            if (prevRequiresContactRequest && !nextRequiresContactRequest) {
                return {
                    show: false,
                    hasMessages: hasMessages
                };
            }
        }

        // First load just completed.
        if (!state.hasTriedToLoadMessages && newState.hasTriedToLoadMessages) {
            if (nextRequiresContactRequest) {
                return {
                    show: true,
                    hasMessages: hasMessages,
                    user: newOtherUser
                };
            }
        }

        // Being reset.
        if (state.hasTriedToLoadMessages && !newState.hasTriedToLoadMessages) {
            if (prevRequiresContactRequest) {
                return {
                    show: false,
                    hasMessages: hadMessages
                };
            }
        }

        return null;
    };

    /**
     * Check if other users are required to be unblocked.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildRequireUnblock = function(state, newState) {
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);

        if (!oldOtherUser && !newOtherUser) {
            return null;
        } else if (oldOtherUser && !newOtherUser) {
            return oldOtherUser.isblocked ? false : null;
        } else if (!oldOtherUser && newOtherUser) {
            return newOtherUser.isblocked ? true : null;
        } else if (!oldOtherUser.isblocked && newOtherUser.isblocked) {
            return true;
        } else if (oldOtherUser.isblocked && !newOtherUser.isblocked) {
            return false;
        }

        return null;
    };

    /**
     * Check if other users can be messaged.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Bool|Null}
     */
    var buildUnableToMessage = function(state, newState) {
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);

        if (!oldOtherUser && !newOtherUser) {
            return null;
        } else if (oldOtherUser && !newOtherUser) {
            return oldOtherUser.canmessage ? null : true;
        } else if (!oldOtherUser && newOtherUser) {
            return newOtherUser.canmessage ? null : true;
        } else if (!oldOtherUser.canmessage && newOtherUser.canmessage) {
            return false;
        } else if (oldOtherUser.canmessage && !newOtherUser.canmessage) {
            return true;
        }

        return null;
    };

    /**
     * Build patch for footer information for a private conversation.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} containing footer state type.
     */
    var buildFooterPatchTypePrivate = function(state, newState) {
        var loadingFirstMessages = buildLoadingFirstMessages(state, newState);
        var inEditMode = buildInEditMode(state, newState);
        var requireAddContact = buildRequireAddContact(state, newState);
        var requireUnblock = buildRequireUnblock(state, newState);
        var unableToMessage = buildUnableToMessage(state, newState);
        var showRequireAddContact = requireAddContact !== null ? requireAddContact.show && requireAddContact.hasMessages : null;
        var otherUser = getOtherUserFromState(newState);
        var generateReturnValue = function(checkValue, successReturn) {
            if (checkValue) {
                return successReturn;
            } else if (checkValue !== null && !checkValue) {
                if (!otherUser) {
                    return {type: 'content'};
                } else if (otherUser.isblocked) {
                    return {type: 'unblock'};
                } else if (newState.messages.length && requiresContactRequest(newState.loggedInUserId, otherUser)) {
                    return {
                        type: 'add-contact',
                        user: otherUser
                    };
                } else if (!otherUser.canmessage || (otherUser.requirescontact && !otherUser.iscontact)) {
                    return {type: 'unable-to-message'};
                }
            }

            return null;
        };

        if (
            loadingFirstMessages === null &&
            inEditMode === null &&
            requireAddContact === null &&
            requireUnblock === null
        ) {
            return null;
        }

        var checks = [
            [loadingFirstMessages, {type: 'placeholder'}],
            [inEditMode, {type: 'edit-mode'}],
            [unableToMessage, {type: 'unable-to-message'}],
            [requireUnblock, {type: 'unblock'}],
            [showRequireAddContact, {type: 'add-contact', user: otherUser}]
        ];

        for (var i = 0; i < checks.length; i++) {
            var checkValue = checks[i][0];
            var successReturn = checks[i][1];
            var result = generateReturnValue(checkValue, successReturn);

            if (result !== null) {
                return result;
            }
        }

        return {
            type: 'content'
        };
    };

    /**
     * Build patch for footer information for a public conversation.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} containing footer state type.
     */
    var buildFooterPatchTypePublic = function(state, newState) {
        var loadingFirstMessages = buildLoadingFirstMessages(state, newState);
        var inEditMode = buildInEditMode(state, newState);

        if (loadingFirstMessages === null && inEditMode === null) {
            return null;
        }

        if (loadingFirstMessages) {
            return {type: 'placeholder'};
        }

        if (inEditMode) {
            return {type: 'edit-mode'};
        }

        return {
            type: 'content'
        };
    };

    /**
     * Check if we're viewing a different conversation. If so then we need to
     * reset the UI.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {bool|null} If a reset needs to occur
     */
    var buildReset = function(state, newState) {
        var oldType = state.type;
        var newType = newState.type;
        var oldConversationId = state.id;
        var newConversationId = newState.id;
        var oldMemberIds = Object.keys(state.members);
        var newMemberIds = Object.keys(newState.members);

        oldMemberIds.sort();
        newMemberIds.sort();

        var membersUnchanged = oldMemberIds.every(function(id, index) {
            return id == newMemberIds[index];
        });

        if (oldType != newType) {
            // If we've changed conversation type then we need to reset.
            return true;
        } else if (oldConversationId && !newConversationId) {
            // We previously had a conversation id but no longer do. This likely means
            // the user is viewing the conversation with someone they've never spoken to
            // before.
            return true;
        } else if (oldConversationId && newConversationId && oldConversationId != newConversationId) {
            // If we had a conversation id and it's changed then we need to reset.
            return true;
        } else if (!oldConversationId && !newConversationId && !membersUnchanged) {
            // If we never had a conversation id but the members of the conversation have
            // changed then we need to reset. This can happen if the user goes from viewing
            // a user they've never had a conversation with to viewing a different user that
            // they've never had a conversation with.
            return true;
        }

        return null;
    };

    /**
     * We should show the contact request sent message if the user just sent
     * a contact request to the other user and there are no messages in the
     * conversation.
     *
     * The messages should be hidden when there are messages in the conversation.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {string|false|null}
     */
    var buildContactRequestSent = function(state, newState) {
        var loggedInUserId = newState.loggedInUserId;
        var oldOtherUser = getOtherUserFromState(state);
        var newOtherUser = getOtherUserFromState(newState);
        var oldSentRequests = !oldOtherUser ? [] : oldOtherUser.contactrequests.filter(function(request) {
            return request.userid == loggedInUserId;
        });
        var newSentRequests = !newOtherUser ? [] : newOtherUser.contactrequests.filter(function(request) {
            return request.userid == loggedInUserId;
        });
        var oldRequest = oldSentRequests.length > 0;
        var newRequest = newSentRequests.length > 0;
        var hadMessages = state.messages.length > 0;
        var hasMessages = state.messages.length > 0;

        if (!oldRequest && newRequest && !newOtherUser.iscontact && !hasMessages) {
            return newOtherUser.fullname;
        } else if (oldOtherUser && !oldOtherUser.iscontact && newRequest && newOtherUser.iscontact) {
            // Contact request accepted.
            return false;
        } else if (oldRequest && !newRequest) {
            return false;
        } else if (!hadMessages && hasMessages) {
            return false;
        } else {
            return null;
        }
    };

    /**
     * Build the full patch comparing the current state and the new state. This patch is used by
     * the conversation renderer to render the UI on any update.
     *
     * @param  {Object} state The current state.
     * @param  {Object} newState The new state.
     * @return {Object} Patch containing all information changed.
     */
    var buildPatch = function(state, newState) {
        var config = {
            all: {
                reset: buildReset,
                conversation: buildConversationPatch,
                scrollToMessage: buildScrollToMessagePatch,
                loadingMembers: buildLoadingMembersPatch,
                loadingFirstMessages: buildLoadingFirstMessages,
                loadingMessages: buildLoadingMessages,
                sendingMessage: buildSendingMessage,
                confirmDeleteSelectedMessages: buildConfirmDeleteSelectedMessages,
                inEditMode: buildInEditMode,
                selectedMessages: buildSelectedMessages,
                isFavourite: buildIsFavourite
            }
        };
        // These build functions are only applicable to private conversations.
        config[Constants.CONVERSATION_TYPES.PRIVATE] = {
            header: buildHeaderPatchTypePrivate,
            footer: buildFooterPatchTypePrivate,
            confirmBlockUser: buildConfirmBlockUser,
            confirmUnblockUser: buildConfirmUnblockUser,
            confirmAddContact: buildConfirmAddContact,
            confirmRemoveContact: buildConfirmRemoveContact,
            confirmContactRequest: buildConfirmContactRequest,
            confirmDeleteConversation: buildConfirmDeleteConversation,
            isBlocked: buildIsBlocked,
            isContact: buildIsContact,
            loadingConfirmAction: buildLoadingConfirmationAction,
            requireAddContact: buildRequireAddContact,
            contactRequestSent: buildContactRequestSent
        };
        // These build functions are only applicable to public (group) conversations.
        config[Constants.CONVERSATION_TYPES.PUBLIC] = {
            header: buildHeaderPatchTypePublic,
            footer: buildFooterPatchTypePublic,
        };

        var patchConfig = $.extend({}, config.all);
        if (newState.type && newState.type in config) {
            // Add the type specific builders to the patch config.
            patchConfig = $.extend(patchConfig, config[newState.type]);
        }

        return Object.keys(patchConfig).reduce(function(patch, key) {
            var buildFunc = patchConfig[key];
            var value = buildFunc(state, newState);

            if (value !== null) {
                patch[key] = value;
            }

            return patch;
        }, {});
    };

    return {
        buildPatch: buildPatch
    };
});
