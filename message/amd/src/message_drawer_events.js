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
 * Events for the message drawer.
 *
 * @module     core_message/message_drawer_events
 * @package    message
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    return {
        CREATE_CONVERSATION_WITH_USER: 'message-drawer-create-conversation-with-user',
        CONTACT_BLOCKED: 'message-drawer-contact-blocked',
        CONTACT_UNBLOCKED: 'message-drawer-contact-unblocked',
        CONTACT_ADDED: 'message-drawer-contact-added',
        CONTACT_REMOVED: 'message-drawer-contact-removed',
        CONTACT_REQUEST_ACCEPTED: 'message-drawer-contact-request-accepted',
        CONTACT_REQUEST_DECLINED: 'message-drawer-contact-request-declined',
        CONVERSATION_CREATED: 'message-drawer-conversation-created',
        CONVERSATION_NEW_LAST_MESSAGE: 'message-drawer-conversation-new-last-message',
        CONVERSATION_DELETED: 'message-drawer-conversation-deleted',
        CONVERSATION_READ: 'message-drawer-conversation-read',
        CONVERSATION_SET_FAVOURITE: 'message-drawer-conversation-set-favourite',
        CONVERSATION_SET_MUTED: 'message-drawer-conversation-set-muted',
        CONVERSATION_UNSET_FAVOURITE: 'message-drawer-conversation-unset-favourite',
        CONVERSATION_UNSET_MUTED: 'message-drawer-conversation-unset-muted',
        PREFERENCES_UPDATED: 'message-drawer-preferences-updated',
        ROUTE_CHANGED: 'message-drawer-route-change',
        SHOW: 'message-drawer-show',
        HIDE: 'message-drawer-hide',
        TOGGLE_VISIBILITY: 'message-drawer-toggle',
        SHOW_CONVERSATION: 'message-drawer-show-conversation',
        SHOW_SETTINGS: 'message-drawer-show-settings',
    };
});
