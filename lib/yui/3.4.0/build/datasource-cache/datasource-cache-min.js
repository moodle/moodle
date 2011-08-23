/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("datasource-cache",function(c){var b=function(){};c.mix(b,{NS:"cache",NAME:"dataSourceCacheExtension"});b.prototype={initializer:function(d){this.doBefore("_defRequestFn",this._beforeDefRequestFn);this.doBefore("_defResponseFn",this._beforeDefResponseFn);},_beforeDefRequestFn:function(g){var d=(this.retrieve(g.request))||null,f=g.details[0];if(d&&d.response){f.cached=d.cached;f.response=d.response;f.data=d.data;this.get("host").fire("response",f);return new c.Do.Halt("DataSourceCache extension halted _defRequestFn");}},_beforeDefResponseFn:function(d){if(d.response&&!d.cached){this.add(d.request,d.response);}}};c.namespace("Plugin").DataSourceCacheExtension=b;function a(f){var e=f&&f.cache?f.cache:c.Cache,g=c.Base.create("dataSourceCache",e,[c.Plugin.Base,c.Plugin.DataSourceCacheExtension]),d=new g(f);g.NS="tmpClass";return d;}c.mix(a,{NS:"cache",NAME:"dataSourceCache"});c.namespace("Plugin").DataSourceCache=a;},"3.4.0",{requires:["datasource-local","cache-base","plugin"]});