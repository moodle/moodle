/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("datasource-xmlschema",function(B){var A=function(){A.superclass.constructor.apply(this,arguments);};B.mix(A,{NS:"schema",NAME:"dataSourceXMLSchema",ATTRS:{schema:{}}});B.extend(A,B.Plugin.Base,{initializer:function(C){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(E){var D=(B.DataSource.IO&&(this.get("host") instanceof B.DataSource.IO)&&E.data.responseXML&&(E.data.responseXML.nodeType===9))?E.data.responseXML:E.data,C=B.DataSchema.XML.apply.call(this,this.get("schema"),D);if(!C){C={meta:{},results:D};}this.get("host").fire("response",B.mix({response:C},E));return new B.Do.Halt("DataSourceXMLSchema plugin halted _defDataFn");}});B.namespace("Plugin").DataSourceXMLSchema=A;},"3.2.0",{requires:["plugin","datasource-local","dataschema-xml"]});