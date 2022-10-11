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

    /** @property {boolean} Whether the drawer is ready or not */
    var drawerMarkedReady = false;

    /**
     * Trigger an event to create a new conversation in the message drawer.
     *
     * @param {object} args
     * @param {Number} args.userId The user id to start a conversation.
     */
    var createConversationWithUser = function(args) {
        waitForDrawerToLoad().then(function() {
            PubSub.publish(MessageDrawerEvents.CREATE_CONVERSATION_WITH_USER, args);
            return;
        }).catch();
    };

    /**
     * Trigger an event to hide the message drawer.
     */
    var hide = function() {
        waitForDrawerToLoad().then(function() {
            PubSub.publish(MessageDrawerEvents.HIDE);
            return;
        }).catch();
    };

    /**
     * Trigger an event to show the message drawer.
     */
    var show = function() {
        waitForDrawerToLoad().then(function() {
            PubSub.publish(MessageDrawerEvents.SHOW);
            return;
        }).catch();
    };

    /**
     * Trigger an event to show the given conversation.
     *
     * @param {object} args
     * @param {int} args.conversationId Id for the conversation to show.
     */
    var showConversation = function(args) {
        waitForDrawerToLoad().then(function() {
            PubSub.publish(MessageDrawerEvents.SHOW_CONVERSATION, args);
            return;
        }).catch();
    };

    /**
     * Trigger an event to show messaging settings.
     */
    var showSettings = function() {
        waitForDrawerToLoad().then(function() {
            PubSub.publish(MessageDrawerEvents.SHOW_SETTINGS);
            return;
        }).catch();
    };

    /**
     * Helper to wait for the drawer to be ready before performing an action.
     *
     * @returns {Promise<void>}
     */
    var waitForDrawerToLoad = function() {
        return new Promise(function(resolve) {
            if (drawerMarkedReady) {
                resolve();
            } else {
                PubSub.subscribe(MessageDrawerEvents.READY, resolve);
            }
        });
    };

    /**
     * Helper to allow the drawer to mark itself as ready.
     */
    var markDrawerReady = function() {
        drawerMarkedReady = true;
        PubSub.publish(MessageDrawerEvents.READY);
    };

    return {
        createConversationWithUser: createConversationWithUser,
        hide: hide,
        show: show,
        showConversation: showConversation,
        showSettings: showSettings,
        markDrawerReady: markDrawerReady,
    };
});
