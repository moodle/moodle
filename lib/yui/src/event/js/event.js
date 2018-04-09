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
 * @module moodle-core-event
 */

var LOGNAME = 'moodle-core-event';

/**
 * List of published global JS events in Moodle. This is a collection
 * of global events that can be subscribed to, or fired from any plugin.
 *
 * @namespace M.core
 * @class event
 */
M.core = M.core || {};

M.core.event = M.core.event || {
    /**
     * This event is triggered when a page has added dynamic nodes to a page
     * that should be processed by the filter system. An example is loading
     * user text that could have equations in it. MathJax can typeset the equations
     * but only if it is notified that there are new nodes in the page that need processing.
     * To trigger this event use M.core.Event.fire(M.core.Event.FILTER_CONTENT_UPDATED, {nodes: list});
     *
     * @event "filter-content-updated"
     * @param nodes {Y.NodeList} List of nodes added to the DOM.
     */
    FILTER_CONTENT_UPDATED: "filter-content-updated",
    /**
     * This event is triggered when an editor has recovered some draft text.
     * It can be used to determine let other sections know that they should reset their
     * form comparison for changes.
     *
     * @event "editor-content-restored"
     */
    EDITOR_CONTENT_RESTORED: "editor-content-restored"
};

M.core.globalEvents = M.core.globalEvents || {
    /**
     * This event is triggered when form has an error
     *
     * @event "form_error"
     * @param formid {string} Id of form with error.
     * @param elementid {string} Id of element with error.
     */
    FORM_ERROR: "form_error",

    /**
     * This event is triggered when the content of a block has changed
     *
     * @event "block_content_updated"
     * @param instanceid ID of the block instance that was updated
     */
    BLOCK_CONTENT_UPDATED: "block_content_updated"
};


var eventDefaultConfig = {
    emitFacade: true,
    defaultFn: function(e) {
        Y.log('Event fired: ' + e.type, 'debug', LOGNAME);
    },
    preventedFn: function(e) {
        Y.log('Event prevented: ' + e.type, 'debug', LOGNAME);
    },
    stoppedFn: function(e) {
        Y.log('Event stopped: ' + e.type, 'debug', LOGNAME);
    }
};

// Publish events with a custom config here.

// Publish all the events with a standard config.
var key;
for (key in M.core.event) {
    if (M.core.event.hasOwnProperty(key) && Y.getEvent(M.core.event[key]) === null) {
        Y.publish(M.core.event[key], eventDefaultConfig);
    }
}

// Publish global events.
for (key in M.core.globalEvents) {
    // Make sure the key exists and that the event has not yet been published. Otherwise, skip publishing.
    if (M.core.globalEvents.hasOwnProperty(key) && Y.Global.getEvent(M.core.globalEvents[key]) === null) {
        Y.Global.publish(M.core.globalEvents[key], Y.merge(eventDefaultConfig, {broadcast: true}));
        Y.log('Global event published: ' + key, 'debug', LOGNAME);
    }
}
