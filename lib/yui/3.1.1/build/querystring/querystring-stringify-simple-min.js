/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.1.1
build: 47
*/
YUI.add("querystring-stringify-simple",function(B){var A=B.namespace("QueryString");A.escape=encodeURIComponent;A.stringify=function(H,E,D){E=E||"&";D=D||"=";var C=[],F,G=A.escape;for(F in H){if(H.hasOwnProperty(F)){C.push(G(F)+D+G(String(H[F])));}}return C.join(E);};},"3.1.1");