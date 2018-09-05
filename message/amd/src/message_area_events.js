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
 * This module defines the events that are triggered in the message area.
 *
 * @module     core_message/message_area_events
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    /** @type {Object} The list of events triggered in the message area. */
    return {
        CANCELDELETEMESSAGES: 'cancel-delete-messages',
        CHOOSEMESSAGESTODELETE: 'choose-messages-to-delete',
        CONTACTADDED: 'contact-added',
        CONTACTBLOCKED: 'contact-blocked',
        CONTACTREMOVED: 'contact-removed',
        CONTACTSELECTED: 'contact-selected',
        CONTACTSSELECTED: 'contacts-selected',
        CONTACTUNBLOCKED: 'contact-unblocked',
        CONVERSATIONDELETED: 'conversation-deleted',
        CONVERSATIONSELECTED: 'conversation-selected',
        CONVERSATIONSSELECTED: 'conversations-selected',
        MESSAGESDELETED: 'messages-deleted',
        MESSAGESEARCHCANCELED: 'message-search-canceled',
        MESSAGESENT: 'message-sent',
        SENDMESSAGE: 'message-send',
        USERSSEARCHCANCELED: 'users-search-canceled'
    };
});