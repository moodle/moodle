/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("node-style",function(A){(function(C){var B=["getStyle","getComputedStyle","setStyle","setStyles"];C.Node.importMethod(C.DOM,B);C.NodeList.importMethod(C.Node.prototype,B);})(A);},"3.0.0",{requires:["dom-style","node-base"]});