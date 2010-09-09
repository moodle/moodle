/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("node-event-delegate",function(A){A.Node.prototype.delegate=function(E,D,B){var C=A.Array(arguments,0,true);C.splice(2,0,this._node);return A.delegate.apply(A,C);};},"3.2.0",{requires:["node-base","event-delegate"]});