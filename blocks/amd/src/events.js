// This file is part of Moodle - http://moodle.org/ //
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
 * Javascript events for the `core_block` subsystem.
 *
 * @module     core_block/events
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 *
 * @example <caption>Example of listening to a block event.</caption>
 * import {eventTypes as blockEventTypes} from 'core_block/events';
 *
 * document.addEventListener(blockEventTypes.blockContentUpdated, e => {
 *     window.console.log(e.target); // The HTMLElement relating to the block whose content was updated.
 *     window.console.log(e.detail.instanceId); // The instanceId of the block that was updated.
 * });
 */

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Events for `core_block`.
 *
 * @constant
 * @property {String} blockContentUpdated See {@link event:blockContentUpdated}
 */
export const eventTypes = {
    /**
     * An event triggered when the content of a block has changed.
     *
     * @event blockContentUpdated
     * @type {CustomEvent}
     * @property {HTMLElement} target The block element that was updated
     * @property {object} detail
     * @property {number} detail.instanceId The block instance id
     */
    blockContentUpdated: 'core_block/contentUpdated',
};

/**
 * Trigger an event to indicate that the content of a block was updated.
 *
 * @method notifyBlockContentUpdated
 * @param {HTMLElement} element The HTMLElement containing the updated block.
 * @returns {CustomEvent}
 * @fires blockContentUpdated
 */
export const notifyBlockContentUpdated = element => dispatchEvent(
    eventTypes.blockContentUpdated,
    {
        instanceId: element.dataset.instanceId,
    },
    element
);

let legacyEventsRegistered = false;
if (!legacyEventsRegistered) {
    // The following event triggers are legacy and will be removed in the future.
    // The following approach provides a backwards-compatability layer for the new events.
    // Code should be updated to make use of native events.

    Y.use('event', 'moodle-core-event', Y => {
        // Provide a backwards-compatability layer for YUI Events.
        document.addEventListener(eventTypes.blockContentUpdated, e => {
            // Trigger the legacy YUI event.
            Y.Global.fire(M.core.event.BLOCK_CONTENT_UPDATED, {instanceid: e.detail.instanceId});
        });
    });

    legacyEventsRegistered = true;
}
