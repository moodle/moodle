/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("querystring-parse-simple",function(B){var A=B.namespace("QueryString");A.parse=function(E,G,D){G=G||"&";D=D||"=";for(var J={},H=0,I=E.split(G),F=I.length,C;H<F;H++){C=I[H].split(D);if(C.length>0){J[A.unescape(C.shift())]=A.unescape(C.join(D));}}return J;};A.unescape=function(C){return decodeURIComponent(C.replace(/\+/g," "));};},"3.1.1");