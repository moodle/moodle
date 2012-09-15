/*
YUI 3.6.0 (build 5521)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datatype-number-parse",function(b){var a=b.Lang;b.mix(b.namespace("DataType.Number"),{parse:function(d){var c=(d===null)?d:+d;if(a.isNumber(c)){return c;}else{return null;}}});b.namespace("Parsers").number=b.DataType.Number.parse;},"3.6.0");