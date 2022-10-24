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

import {publish, subscribe} from 'core/pubsub';
import MessageDrawerEvents from 'core_message/message_drawer_events';

/** @property {boolean} Whether the drawer is ready or not */
let drawerMarkedReady = false;

/**
 * Trigger an event to create a new conversation in the message drawer.
 *
 * @param {object} args
 * @param {Number} args.userId The user id to start a conversation.
 */
export const createConversationWithUser = async(args) => {
    await waitForDrawerToLoad();
    publish(MessageDrawerEvents.CREATE_CONVERSATION_WITH_USER, args);
};

/**
 * Trigger an event to hide the message drawer.
 */
export const hide = async() => {
    await waitForDrawerToLoad();
    publish(MessageDrawerEvents.HIDE);
};

/**
 * Trigger an event to show the message drawer.
 */
export const show = async() => {
    await waitForDrawerToLoad();
    publish(MessageDrawerEvents.SHOW);
};

/**
 * Trigger an event to show the given conversation.
 *
 * @param {object} args
 * @param {int} args.conversationId Id for the conversation to show.
 */
export const showConversation = async(args) => {
    await waitForDrawerToLoad();
    publish(MessageDrawerEvents.SHOW_CONVERSATION, args);
};

/**
 * Trigger an event to show messaging settings.
 */
export const showSettings = async() => {
    await waitForDrawerToLoad();
    publish(MessageDrawerEvents.SHOW_SETTINGS);
};

/**
 * Helper to wait for the drawer to be ready before performing an action.
 *
 * @returns {Promise<void>}
 */
export const waitForDrawerToLoad = () => new Promise((resolve) => {
    if (drawerMarkedReady) {
        resolve();
    } else {
        subscribe(MessageDrawerEvents.READY, resolve);
    }
});

/**
 * Helper to allow the drawer to mark itself as ready.
 */
export const markDrawerReady = () => {
    drawerMarkedReady = true;
    publish(MessageDrawerEvents.READY);
};
