/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datasource-arrayschema",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NS:"schema",NAME:"dataSourceArraySchema",ATTRS:{schema:{}}});B.extend(A,B.Plugin.Base,{initializer:function(C){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(E){var D=(B.DataSource.IO&&(this.get("host") instanceof B.DataSource.IO)&&B.Lang.isString(E.data.responseText))?E.data.responseText:E.data,C=B.DataSchema.Array.apply(this.get("schema"),D);if(!C){C={meta:{},results:D};}this.get("host").fire("response",B.mix({response:C},E));return new B.Do.Halt("DataSourceArraySchema plugin halted _defDataFn");}});B.namespace("Plugin").DataSourceArraySchema=A;},"3.0.0",{requires:["plugin","datasource-local","dataschema-array"]});