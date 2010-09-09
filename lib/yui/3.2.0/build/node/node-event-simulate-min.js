/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("node-event-simulate",function(A){A.Node.prototype.simulate=function(C,B){A.Event.simulate(A.Node.getDOMNode(this),C,B);};},"3.2.0",{requires:["node-base","event-simulate"]});