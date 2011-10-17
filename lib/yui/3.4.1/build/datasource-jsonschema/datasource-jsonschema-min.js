/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datasource-jsonschema",function(b){var a=function(){a.superclass.constructor.apply(this,arguments);};b.mix(a,{NS:"schema",NAME:"dataSourceJSONSchema",ATTRS:{schema:{}}});b.extend(a,b.Plugin.Base,{initializer:function(c){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(g){var d=g.data&&(g.data.responseText||g.data),c=this.get("schema"),f=g.details[0];f.response=b.DataSchema.JSON.apply.call(this,c,d)||{meta:{},results:d};this.get("host").fire("response",f);return new b.Do.Halt("DataSourceJSONSchema plugin halted _defDataFn");}});b.namespace("Plugin").DataSourceJSONSchema=a;},"3.4.1",{requires:["datasource-local","plugin","dataschema-json"]});