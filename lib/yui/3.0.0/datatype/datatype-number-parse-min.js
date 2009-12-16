/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datatype-number-parse",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Number"),{parse:function(D){var C=(D===null)?D:+D;if(A.isNumber(C)){return C;}else{return null;}}});B.namespace("Parsers").number=B.DataType.Number.parse;},"3.0.0");