/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 3.0.0
build: 1549
*/
YUI.add("datasource-jsonschema",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NS:"schema",NAME:"dataSourceJSONSchema",ATTRS:{schema:{}}});B.extend(A,B.Plugin.Base,{initializer:function(C){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(E){var D=(B.DataSource.IO&&(this.get("host") instanceof B.DataSource.IO)&&B.Lang.isString(E.data.responseText))?E.data.responseText:E.data,C=B.DataSchema.JSON.apply(this.get("schema"),D);if(!C){C={meta:{},results:D};}this.get("host").fire("response",B.mix({response:C},E));return new B.Do.Halt("DataSourceJSONSchema plugin halted _defDataFn");}});B.namespace("Plugin").DataSourceJSONSchema=A;},"3.0.0",{requires:["plugin","datasource-local","dataschema-json"]});