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
 * Provides some helper functions to trigger actions in the message drawer.
 *
 * @module     core_message/message_drawer_helper
 * @package    message
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'core/pubsub',
    'core_message/message_drawer_events'
],
function(
    PubSub,
    MessageDrawerEvents
) {

    /**
     * Trigger an event to create a new conversation in the message drawer.
     *
     * @param {Number} userId The user id to start a conversation.
     */
    var createConversationWithUser = function(args) {
        PubSub.publish(MessageDrawerEvents.CREATE_CONVERSATION_WITH_USER, args);
    };

    /**
     * Trigger an event to hide the message drawer.
     */
    var hide = function() {
        PubSub.publish(MessageDrawerEvents.HIDE);
    };

    /**
     * Trigger an event to show the message drawer.
     */
    var show = function() {
        PubSub.publish(MessageDrawerEvents.SHOW);
    };

    /**
     * Trigger an event to show the given conversation.
     *
     * @param {int} conversationId Id for the conversation to show.
     */
    var showConversation = function(args) {
        PubSub.publish(MessageDrawerEvents.SHOW_CONVERSATION, args);
    };

    /**
     * Trigger an event to show messaging settings.
     */
    var showSettings = function() {
        PubSub.publish(MessageDrawerEvents.SHOW_SETTINGS);
    };

    return {
        createConversationWithUser: createConversationWithUser,
        hide: hide,
        show: show,
        showConversation: showConversation,
        showSettings: showSettings
    };
});
