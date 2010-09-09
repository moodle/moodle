/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("querystring-stringify-simple",function(C){var B=C.namespace("QueryString"),A=encodeURIComponent;B.stringify=function(I,J){var D=[],H=J&&J.arrayKey?true:false,G,F,E;for(G in I){if(I.hasOwnProperty(G)){if(C.Lang.isArray(I[G])){for(F=0,E=I[G].length;F<E;F++){D.push(A(H?G+"[]":G)+"="+A(I[G][F]));}}else{D.push(A(G)+"="+A(I[G]));}}}return D.join("&");};},"3.2.0");