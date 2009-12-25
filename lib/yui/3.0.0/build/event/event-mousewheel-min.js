/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("event-mousewheel",function(C){var B="DOMMouseScroll",A=function(E){var D=C.Array(E,0,true),F;if(C.UA.gecko){D[0]=B;F=C.config.win;}else{F=C.config.doc;}if(D.length<3){D[2]=F;}else{D.splice(2,0,F);}return D;};C.Env.evt.plugins.mousewheel={on:function(){return C.Event._attach(A(arguments));},detach:function(){return C.Event.detach.apply(C.Event,A(arguments));}};},"3.0.0",{requires:["node-base"]});