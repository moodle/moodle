/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datasource-jsonschema",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NS:"schema",NAME:"dataSourceJSONSchema",ATTRS:{schema:{}}});B.extend(A,B.Plugin.Base,{initializer:function(C){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(E){var D=E.data?(E.data.responseText?E.data.responseText:E.data):E.data,C=B.DataSchema.JSON.apply.call(this,this.get("schema"),D);if(!C){C={meta:{},results:D};}this.get("host").fire("response",B.mix({response:C},E));return new B.Do.Halt("DataSourceJSONSchema plugin halted _defDataFn");}});B.namespace("Plugin").DataSourceJSONSchema=A;},"3.2.0",{requires:["plugin","datasource-local","dataschema-json"]});