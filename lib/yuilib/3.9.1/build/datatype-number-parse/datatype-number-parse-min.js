/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("datatype-number-parse",function(e,t){var n=e.Lang;e.mix(e.namespace("Number"),{parse:function(e){var t=e===null||e===""?e:+e;return n.isNumber(t)?t:null}}),e.namespace("Parsers").number=e.Number.parse,e.namespace("DataType"),e.DataType.Number=e.Number},"3.9.1");
