/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
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



}, '3.2.0' ,{requires:['node-base', 'event-simulate']});
