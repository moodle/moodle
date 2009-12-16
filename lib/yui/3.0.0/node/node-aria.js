/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add('node-aria', function(Y) {

/**
 * Aria support for Node
 * @module node
 * @submodule node-aria
 */

Y.Node.prototype.get = function(name) {
    var val;
    if (re_aria.test(name)) {
            val = Y.Node.getDOMNode(this).getAttribute(name, 2); 
    } else {

    }

        setter: function(val) {
            Y.Node.getDOMNode(this).setAttribute(name, val);
            return val; 
        }
    });
};


}, '3.0.0' ,{requires:['node-base']});
