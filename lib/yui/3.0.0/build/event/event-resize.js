/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add('event-resize', function(Y) {

/**
 * Adds a window resize event that has its behavior normalized to fire at the
 * end of the resize rather than constantly during the resize.
 * @module event
 * @submodule event-resize
 */
(function() {

var detachHandle,

    timerHandle,

    CE_NAME = 'window:resize',

    handler = function(e) {

        if (Y.UA.gecko) {

            Y.fire(CE_NAME, e);

        } else {

            if (timerHandle) {
                timerHandle.cancel();
            }

            timerHandle = Y.later(Y.config.windowResizeDelay || 40, Y, function() {
                Y.fire(CE_NAME, e);
            });
        }
        
    };


/**
 * Firefox fires the window resize event once when the resize action
 * finishes, other browsers fire the event periodically during the
 * resize.  This code uses timeout logic to simulate the Firefox 
 * behavior in other browsers.
 * @event windowresize
 * @for YUI
 */
Y.Env.evt.plugins.windowresize = {

    on: function(type, fn) {

        // check for single window listener and add if needed
        if (!detachHandle) {
            detachHandle = Y.Event._attach(['resize', handler]);
        }

        var a = Y.Array(arguments, 0, true);
        a[0] = CE_NAME;

        return Y.on.apply(Y, a);
    }
};

})();


}, '3.0.0' ,{requires:['node-base']});
