/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("dataschema-base",function(B){var A=B.Lang,C={apply:function(D,E){return E;},parse:function(D,E){if(E.parser){var F=(A.isFunction(E.parser))?E.parser:B.Parsers[E.parser+""];if(F){D=F.call(this,D);}else{}}return D;}};B.namespace("DataSchema").Base=C;B.namespace("Parsers");},"3.0.0",{requires:["base"]});