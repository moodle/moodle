/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datasource-arrayschema",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NS:"schema",NAME:"dataSourceArraySchema",ATTRS:{schema:{}}});B.extend(A,B.Plugin.Base,{initializer:function(C){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(E){var D=(B.DataSource.IO&&(this.get("host") instanceof B.DataSource.IO)&&B.Lang.isString(E.data.responseText))?E.data.responseText:E.data,C=B.DataSchema.Array.apply.call(this,this.get("schema"),D);if(!C){C={meta:{},results:D};}this.get("host").fire("response",B.mix({response:C},E));return new B.Do.Halt("DataSourceArraySchema plugin halted _defDataFn");}});B.namespace("Plugin").DataSourceArraySchema=A;},"3.2.0",{requires:["plugin","datasource-local","dataschema-array"]});