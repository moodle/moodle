/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datatype-number-parse",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Number"),{parse:function(D){var C=(D===null)?D:+D;if(A.isNumber(C)){return C;}else{return null;}}});B.namespace("Parsers").number=B.DataType.Number.parse;},"3.2.0");