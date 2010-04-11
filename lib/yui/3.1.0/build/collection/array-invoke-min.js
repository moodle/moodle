/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.0
build: 2026
*/
YUI.add("array-invoke",function(A){A.Array.invoke=function(B,E){var D=A.Array(arguments,2,true),F=A.Lang.isFunction,C=[];A.Array.each(A.Array(B),function(H,G){if(F(H[E])){C[G]=H[E].apply(H,D);}});return C;};},"3.1.0");