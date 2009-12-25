/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datatype-date-parse",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Date"),{parse:function(D){var C=null;if(!(A.isDate(D))){C=new Date(D);}else{return C;}if(A.isDate(C)&&(C!="Invalid Date")&&!isNaN(C)){return C;}else{return null;}}});B.namespace("Parsers").date=B.DataType.Date.parse;},"3.0.0");