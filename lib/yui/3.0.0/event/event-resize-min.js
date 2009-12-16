/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("event-resize",function(A){(function(){var C,B,E="window:resize",D=function(F){if(A.UA.gecko){A.fire(E,F);}else{if(B){B.cancel();}B=A.later(A.config.windowResizeDelay||40,A,function(){A.fire(E,F);});}};A.Env.evt.plugins.windowresize={on:function(H,G){if(!C){C=A.Event._attach(["resize",D]);}var F=A.Array(arguments,0,true);F[0]=E;return A.on.apply(A,F);}};})();},"3.0.0",{requires:["node-base"]});