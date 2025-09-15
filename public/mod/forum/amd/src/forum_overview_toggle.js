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
 * Handle forum subscription/tracking toggling.
 *
 * @module     mod_forum/forum_overview_toggle
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import {getString} from 'core/str';
import SRLogger from 'core/local/reactive/srlogger';
import Repository from 'mod_forum/repository';

/**
 * Register event listeners for the subscription/tracking toggles in the overview.
 * @param {HTMLElement} toggleElement The toggle root element
 */
function registerEventListeners(toggleElement) {
    toggleElement.addEventListener('change', () => {
        if (toggleElement.dataset.type === 'forum-subscription-toggle') {
            subscriptionToggleClickHandler(toggleElement);
        }
        if (toggleElement.dataset.type === 'forum-track-toggle') {
            trackToggleClickHanldler(toggleElement);
        }
    });
}

/**
 * Toggle subscription element click handler.
 *
 * @param {HTMLElement} toggleElement The toggle element that was clicked
 * @return {Promise<void>}
 */
async function subscriptionToggleClickHandler(toggleElement) {
    const forumId = toggleElement.dataset.forumid;
    const forumName = toggleElement.dataset.forumname;
    const newState = toggleElement.dataset.targetstate;
    if (!forumId || !newState) {
        return;
    }
    try {
        const context = await Repository.setForumSubscriptionState(forumId, newState);
        const newTargetState = !!context.userstate.subscribed;

        await updateSwitchState(
            toggleElement,
            newTargetState,
            newTargetState ? 'subscribe' : 'unsubscribe',
        );

        const feedbackMessage = await getString(
            newTargetState ? 'subscribedtoforum' : 'unsubscribedfromforum',
            'mod_forum',
            forumName,
        );
        new SRLogger().add({feedbackMessage});
    } catch (error) {
        Notification.exception(error);
    }
}

/**
 * Toggle track element click handler.
 *
 * @param {HTMLElement} toggleElement The toggle element that was clicked
 * @return {Promise<void>}
 */
async function trackToggleClickHanldler(toggleElement) {
    const forumId = toggleElement.dataset.forumid;
    const forumName = toggleElement.dataset.forumname;
    const newState = toggleElement.dataset.targetstate;
    if (!forumId || !newState) {
        return;
    }
    try {
        const context = await Repository.setForumTrackingState(forumId, newState);
        const newTargetState = !!context.userstate.tracked;

        await updateSwitchState(
            toggleElement,
            newTargetState,
            newTargetState ? 'trackingon' : 'trackingoff',
        );

        const feedbackMessage = await getString(
            newTargetState ? 'trackedforforum' : 'untrackedforforum',
            'mod_forum',
            forumName,
        );
        new SRLogger().add({feedbackMessage});
    } catch (error) {
        Notification.exception(error);
    }
}

/**
 * Update the switch state of the toggle element.
 *
 * @param {HTMLElement} toggleElement The toggle element to update
 * @param {Boolean} newTargetState The new target state to set (true for subscribed, false for unsubscribed)
 * @param {string} stringKey The string key to retrieve the label text
 * @return {Promise<void>}
 */
async function updateSwitchState(toggleElement, newTargetState, stringKey) {
    toggleElement.dataset.targetstate = newTargetState ? 0 : 1;
    const string = await getString(stringKey, 'mod_forum');
    const label = toggleElement.closest('td').querySelector(`label[for="${toggleElement.id}"] span`);
    label.textContent = string;
}

/**
 * Initialize the forum overview toggle functionality.
 *
 * @param {string} toggleSelector The CSS selector for the toggle element to initialize
 * @throws {Error} If no elements are found with the provided selector
 */
export const init = (toggleSelector) => {
    const toggleElement = document.querySelector(toggleSelector);
    if (!toggleElement) {
        // If the user cannot track/subscribe to any course forum, the toggle will not be present.
        return;
    }
    registerEventListeners(toggleElement);
};
