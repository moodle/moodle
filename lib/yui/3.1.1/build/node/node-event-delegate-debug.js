/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add('node-event-delegate', function(Y) {

/**
 * Functionality to make the node a delegated event container
 * @module node
 * @submodule node-event-delegate
 */

/**
 * Functionality to make the node a delegated event container
 * @method delegate
 * @param type {String} the event type to delegate
 * @param fn {Function} the function to execute
 * @param selector {String} a selector that must match the target of the event.
 * @return {Event.Handle} the detach handle
 * @for Node
 */
Y.Node.prototype.delegate = function(type, fn, selector) {

    var args = Array.prototype.slice.call(arguments, 3),
        a = [type, fn, Y.Node.getDOMNode(this), selector];
    a = a.concat(args);

    return Y.delegate.apply(Y, a);
};


}, '3.1.1' ,{requires:['node-base', 'event-delegate', 'pluginhost']});
