/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datasource-textschema",function(b){var a=function(){a.superclass.constructor.apply(this,arguments);};b.mix(a,{NS:"schema",NAME:"dataSourceTextSchema",ATTRS:{schema:{}}});b.extend(a,b.Plugin.Base,{initializer:function(c){this.doBefore("_defDataFn",this._beforeDefDataFn);},_beforeDefDataFn:function(g){var c=this.get("schema"),f=g.details[0],d=g.data.responseText||g.data;f.response=b.DataSchema.Text.apply.call(this,c,d)||{meta:{},results:d};this.get("host").fire("response",f);return new b.Do.Halt("DataSourceTextSchema plugin halted _defDataFn");}});b.namespace("Plugin").DataSourceTextSchema=a;},"3.4.0",{requires:["datasource-local","plugin","dataschema-text"]});