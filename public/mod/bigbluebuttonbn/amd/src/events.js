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
 * Events for the mod_bigbluebuttonbn plugin.
 *
 * @module      mod_bigbluebuttonbn/events
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {dispatchEvent} from 'core/event_dispatcher';

export const eventTypes = {
    /**
     * Fired when a session has been ended.
     *
     * @event mod_bigbluebuttonbn/sessionEnded
     * @type CustomEvent
     * @property {object} detail
     * @property {number} detail.bbbId
     * @property {number} detail.groupId
     */
    sessionEnded: 'mod_bigbluebuttonbn/sessionEnded',

    /**
     * Fired when the current session has been ended.
     *
     * @event mod_bigbluebuttonbn/currentSessionEnded
     * @type CustomEvent
     * @property {object} detail
     */
    currentSessionEnded: 'mod_bigbluebuttonbn/currentSessionEnded',
};

/**
 * Trigger the sessionEnded event.
 *
 * @param {number} bbbId
 * @param {number} groupId
 * @returns {CustomEvent}
 * @fires event:mod_bigbluebuttonbn/sessionEnded
 */
export const notifySessionEnded = (bbbId, groupId) => dispatchEvent(eventTypes.sessionEnded, {
    bbbId,
    groupId,
});

/**
 * Trigger the currentSessionEnded event.
 *
 * @param {Element} container
 * @returns {CustomEvent}
 * @fires event:mod_bigbluebuttonbn/currentSessionEnded
 */
export const notifyCurrentSessionEnded = container => dispatchEvent(
    eventTypes.currentSessionEnded,
    {},
    container
);
