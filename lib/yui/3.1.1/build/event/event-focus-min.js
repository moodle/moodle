/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("event-focus",function(A){(function(){var I=A.UA,J=A.Event,E=A.Env.evt.plugins,C=I.ie,F=(I.opera||I.webkit),D={focus:(C?"focusin":(F?"DOMFocusIn":"focus")),blur:(C?"focusout":(F?"DOMFocusOut":"blur"))},G={capture:(I.gecko?true:false)},H=function(M,L){var K=A.Array(M,0,true),N=M[2];L.overrides=L.overrides||{};L.overrides.type=M[0];if(N){if(A.DOM.isWindow(N)){L.capture=false;}else{K[0]=D[K[0]];}}return J._attach(K,L);},B={on:function(){return H(arguments,G);}};J._attachFocus=H;J._attachBlur=H;E.focus=B;E.blur=B;})();},"3.1.1",{requires:["node-base"]});