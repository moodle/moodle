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

import ForumEvents from 'mod_forum/forum_events';
import Notification from 'core/notification';
import {publish} from 'core/pubsub';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import Repository from 'mod_forum/repository';

/**
 * Register event listeners for the subscription/tracking toggles in the overview.
 * @param {HTMLElement} toggleElement The toggle root element
 * @param {Boolean} changeLabel Whether to update the visible label text
 */
function registerEventListeners(toggleElement, changeLabel = true) {
    toggleElement.addEventListener('change', () => {
        if (toggleElement.dataset.type === 'forum-subscription-toggle') {
            subscriptionToggleClickHandler(toggleElement, changeLabel);
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
 * @param {Boolean} changeLabel Whether to update the visible label text
 * @return {Promise<void>}
 */
async function subscriptionToggleClickHandler(toggleElement, changeLabel = true) {
    const forumId = toggleElement.dataset.forumid;
    const forumName = toggleElement.dataset.forumname;
    const userName = toggleElement.dataset.username;
    const newState = toggleElement.dataset.targetstate;
    if (!forumId || !newState) {
        return;
    }
    try {
        const context = await Repository.setForumSubscriptionState(forumId, newState);
        const newTargetState = !!context.userstate.subscribed;

        let labelKey = '';
        if (changeLabel) {
            labelKey = newTargetState ? 'unsubscribe' : 'subscribe';
        }

        await updateSwitchState(
            toggleElement,
            newTargetState,
            labelKey,
            newTargetState ? 'unsubscribefromforum' : 'subscribetoforum',
            forumName,
        );

        const feedbackMessage = await getString(
            newTargetState ? 'nowsubscribed' : 'nownotsubscribed',
            'mod_forum',
            newTargetState ? {forum: forumName} : {name: userName, forum: forumName},
        );
        addToast(feedbackMessage);

        const newLabel = await getString(newTargetState ? 'unsubscribediscussion' : 'subscribediscussion', 'mod_forum');
        publish(ForumEvents.ALL_SUBSCRIPTION_TOGGLED, {
            forumId: forumId,
            subscriptionState: newTargetState,
            newLabel: newLabel,
        });
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
            newTargetState ? 'trackforforum' : 'untrackforforum',
            forumName,
        );

        const feedbackMessage = await getString(
            newTargetState ? 'trackedforforum' : 'untrackedforforum',
            'mod_forum',
            forumName,
        );
        addToast(feedbackMessage, {visuallyHidden: true});
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
 * @param {string} ariaLabelStringKey The string key to retrieve the aria-label text
 * @param {string} forumName The forum name for aria-label interpolation
 * @return {Promise<void>}
 */
async function updateSwitchState(toggleElement, newTargetState, stringKey, ariaLabelStringKey, forumName) {
    toggleElement.dataset.targetstate = newTargetState ? 0 : 1;
    if (stringKey) {
        const string = await getString(stringKey, 'mod_forum');
        const labelSelector = `label[for="${toggleElement.id}"] span`;
        const label = toggleElement.closest('td')?.querySelector(labelSelector) || document.querySelector(labelSelector);
        if (label) {
            label.textContent = string;
        }
    }

    if (ariaLabelStringKey && forumName) {
        const ariaLabelString = await getString(ariaLabelStringKey, 'mod_forum', forumName);
        toggleElement.setAttribute('aria-label', ariaLabelString);
    }
}

/**
 * Initialize the forum overview toggle functionality.
 *
 * @param {string} toggleSelector The CSS selector for the toggle element to initialize
 * @param {Boolean} changeLabel Whether to update the visible label text
 * @throws {Error} If no elements are found with the provided selector
 */
export const init = (toggleSelector, changeLabel = true) => {
    const toggleElement = document.querySelector(toggleSelector);
    if (!toggleElement) {
        // If the user cannot track/subscribe to any course forum, the toggle will not be present.
        return;
    }
    registerEventListeners(toggleElement, changeLabel);
};
