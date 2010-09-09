/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datatype-date-parse",function(B){var A=B.Lang;B.mix(B.namespace("DataType.Date"),{parse:function(D){var C=null;if(!(A.isDate(D))){C=new Date(D);}else{return C;}if(A.isDate(C)&&(C!="Invalid Date")&&!isNaN(C)){return C;}else{return null;}}});B.namespace("Parsers").date=B.DataType.Date.parse;},"3.2.0");