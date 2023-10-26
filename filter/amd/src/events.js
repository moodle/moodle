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
 * Javascript events for the `core_filters` subsystem.
 *
 * @module     core_filters/events
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 *
 * @example <caption>Example of listening to a filter event.</caption>
 * import {eventTypes as filterEventTypes} from 'core_filters/events';
 *
 * document.addEventListener(filterEventTypes.filterContentUpdated, e => {
 *     window.console.log(e.detail.nodes); // A list of the HTMLElements whose content was updated
 * });
 */

import {dispatchEvent} from 'core/event_dispatcher';
import {getList as normalistNodeList} from 'core/normalise';
import jQuery from 'jquery';

/**
 * Events for the `core_filters` subsystem.
 *
 * @constant
 * @property {String} filterContentUpdated See {@link event:filterContentUpdated}
 */
export const eventTypes = {
    /**
     * An event triggered when page content is updated and must be processed by the filter system.
     *
     * An example of this is loading user text that could have equations in it. MathJax can typeset the equations but
     * only if it is notified that there are new nodes in the page that need processing.
     *
     * @event filterContentUpdated
     * @type {CustomEvent}
     * @property {object} detail
     * @property {NodeElement[]} detail.nodes The list of parent nodes which were updated
     */
    filterContentUpdated: 'core_filters/contentUpdated',
};

/**
 * Trigger an event to indicate that the specified nodes were updated and should be processed by the filter system.
 *
 * @method notifyFilterContentUpdated
 * @param {jQuery|Array} nodes
 * @returns {CustomEvent}
 * @fires filterContentUpdated
 */
export const notifyFilterContentUpdated = nodes => {
    // Historically this could be a jQuery Object.
    // Normalise the list of nodes to a NodeList.
    nodes = normalistNodeList(nodes);

    return dispatchEvent(eventTypes.filterContentUpdated, {nodes});
};

let legacyEventsRegistered = false;
if (!legacyEventsRegistered) {
    // The following event triggers are legacy and will be removed in the future.
    // The following approach provides a backwards-compatability layer for the new events.
    // Code should be updated to make use of native events.

    Y.use('event', 'moodle-core-event', () => {
        // Provide a backwards-compatability layer for YUI Events.
        document.addEventListener(eventTypes.filterContentUpdated, e => {
            // Trigger the legacy jQuery event.
            jQuery(document).trigger(M.core.event.FILTER_CONTENT_UPDATED, [jQuery(e.detail.nodes)]);

            // Trigger the legacy YUI event.
            Y.fire(M.core.event.FILTER_CONTENT_UPDATED, {nodes: new Y.NodeList(e.detail.nodes)});
        });
    });

    legacyEventsRegistered = true;
}
