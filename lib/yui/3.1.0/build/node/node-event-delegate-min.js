/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.0
build: 2026
*/
YUI.add("node-event-delegate",function(A){A.Node.prototype.delegate=function(F,E,B){var D=Array.prototype.slice.call(arguments,3),C=[F,E,A.Node.getDOMNode(this),B];C=C.concat(D);return A.delegate.apply(A,C);};},"3.1.0",{requires:["node-base","event-delegate","pluginhost"]});