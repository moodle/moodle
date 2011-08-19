/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add('node-event-simulate', function(Y) {

/**
 * Adds functionality to simulate events.
 * @module node
 * @submodule node-event-simulate
 */

    /**
     * Simulates an event on the node.
     * @param {String} type The type of event to simulate (i.e., "click").
     * @param {Object} options (Optional) Extra options to copy onto the event object.
     * @return {void}
     * @for Node
     * @method simulate
     */     
    Y.Node.prototype.simulate = function(type, options) {
        Y.Event.simulate(Y.Node.getDOMNode(this), type, options);
    };



}, '3.4.0' ,{requires:['node-base', 'event-simulate']});
