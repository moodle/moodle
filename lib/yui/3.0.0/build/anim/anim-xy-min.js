/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("anim-xy",function(B){var A=Number;B.Anim.behaviors.xy={set:function(F,D,I,H,C,G,E){F._node.setXY([E(C,A(I[0]),A(H[0])-A(I[0]),G),E(C,A(I[1]),A(H[1])-A(I[1]),G)]);},get:function(C){return C._node.getXY();}};},"3.0.0",{requires:["anim-base","node-screen"]});