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
 * @namespace M.core
 * @class event
 */
M.core = M.core || {};

var eventsConfigured = !!M.core.event;

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
    EDITOR_CONTENT_RESTORED: "editor-content-restored",

    /**
     * This event is triggered when an mform is about to be submitted via ajax.
     *
     * @event "form-submit-ajax"
     */
    FORM_SUBMIT_AJAX: "form-submit-ajax"

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


if (!eventsConfigured) {
    var eventDefaultConfig = {
        emitFacade: true,
        defaultFn: function(e) {
        },
        preventedFn: function(e) {
        },
        stoppedFn: function(e) {
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
        }
    }

    /**
     * Apply a callback when the DOM is modified and matchecs the supplied targetSelector.
     *
     * @method listen
     * @param {String} targetSelector The selector to apply
     * @param {Function} applyCallback The function to call on the found node
     */
    var listenForMutation = function(targetSelector, applyCallback) {
        // Add a MutationObserver to check for new children to the tree.
        var newNodeObserver = new MutationObserver(function(mutationList) {
            mutationList.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node instanceof Element && node.matches(targetSelector)) {
                        applyCallback(node);
                    }
                });
            });
        });

        newNodeObserver.observe(document, {childList: true, subtree: true});
    };


    // YUI Custom Events do not bubble up the DOM, only up the inheritance path of custom classes.
    // We have to add a Node Observer to watch for new forms that may need to be watched in future.
    require(['core_form/events'], function(formEvents) {
        listenForMutation('form', function(form) {
            Y.one(form).on(M.core.event.FORM_SUBMIT_AJAX, function(e) {
                // Prevent cyclical calls.
                if (e && e.fallbackHandled) {
                    return;
                }

                formEvents.notifyFormSubmittedByJavascript(form, window.skipValidation, true);
            });
        });
    });
}


}, '@VERSION@', {"requires": ["event-custom"]});
