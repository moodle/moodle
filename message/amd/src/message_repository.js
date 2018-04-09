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

    return {
        query: query,
        countUnreadConversations: countUnreadConversations,
        markAllAsRead: markAllAsRead,
    };
});
