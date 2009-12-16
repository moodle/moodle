/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("event-focus",function(A){(function(){var I=A.UA,J=A.Event,E=A.Env.evt.plugins,C=I.ie,F=(I.opera||I.webkit),D={focus:(C?"focusin":(F?"DOMFocusIn":"focus")),blur:(C?"focusout":(F?"DOMFocusOut":"blur"))},G={capture:(I.gecko?true:false)},H=function(M,L){var K=A.Array(M,0,true);K[0]=D[K[0]];return J._attach(K,L);},B={on:function(){return H(arguments,G);}};J._attachFocus=H;J._attachBlur=H;E.focus=B;E.blur=B;})();},"3.0.0",{requires:["node-base"]});