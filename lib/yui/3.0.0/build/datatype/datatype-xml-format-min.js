/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datatype-xml-format",function(B){var A=B.Lang;B.mix(B.namespace("DataType.XML"),{format:function(C){try{if(!A.isUndefined(XMLSerializer)){return(new XMLSerializer()).serializeToString(C);}}catch(D){if(C&&C.xml){return C.xml;}else{return(A.isValue(C)&&C.toString)?C.toString():"";}}}});},"3.0.0");