/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("classnamemanager",function(C){var B="classNamePrefix",D="classNameDelimiter",A=C.config;A[B]=A[B]||"yui";A[D]=A[D]||"-";C.ClassNameManager=function(){var E=A[B],F=A[D];return{getClassName:C.cached(function(I,G){var H=E+F+((G)?Array.prototype.join.call(arguments,F):I);return H.replace(/\s/g,"");})};}();},"3.0.0");