/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("datatype-date-parse",function(e,t){e.mix(e.namespace("Date"),{parse:function(t){var n=new Date(+t||t);return e.Lang.isDate(n)?n:null}}),e.namespace("Parsers").date=e.Date.parse,e.namespace("DataType"),e.DataType.Date=e.Date},"3.9.1");
