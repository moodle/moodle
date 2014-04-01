YUI.add('moodle-core-event', function (Y, NAME) {

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
 * @namespace M.core.event
 */
M.core = M.core || {};

M.core.event = {
    // This event is triggered when a page has added dynamic nodes to a page
    // that should be processed by the filter system. An example is loading
    // user text that could have equations in it. MathJax can typeset the equations
    // but only if it is notified that there are new nodes in the page that need processing.
    // This event must contain a property "nodes" listing the root of any new nodes in the DOM (as a NodeList).
    // To trigger this event use M.core.Event.fire(M.core.Event.FILTER_CONTENT_UPDATED, {nodes: list});
    FILTER_CONTENT_UPDATED: "filter-content-updated"
};


var eventDefaultConfig = {
    emitFacade: true,
    defaultFn: function(e) {
    },
    preventedFn: function(e) {
    },
    stoppedFn: function(e) {
    }
};

for (var key in M.core.event) {
    Y.publish(M.core.event[key], eventDefaultConfig);
}




}, '@VERSION@', {"requires": ["event-custom"]});
