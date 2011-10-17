/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datatype-date-parse",function(b){var a=b.Lang;b.mix(b.namespace("DataType.Date"),{parse:function(d){var c=null;if(!(a.isDate(d))){c=new Date(d);}else{return c;}if(a.isDate(c)&&(c!="Invalid Date")&&!isNaN(c)){return c;}else{return null;}}});b.namespace("Parsers").date=b.DataType.Date.parse;},"3.4.1");