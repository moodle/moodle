/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("node-event-delegate",function(A){A.Node.prototype.delegate=function(F,E,B){var D=Array.prototype.slice.call(arguments,3),C=[F,E,A.Node.getDOMNode(this),B];C=C.concat(D);return A.delegate.apply(A,C);};},"3.0.0",{requires:["node-base","event-delegate","pluginhost"]});