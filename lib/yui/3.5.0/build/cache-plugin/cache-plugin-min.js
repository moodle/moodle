/*
YUI 3.5.0 (build 5089)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("cache-plugin",function(b){function a(e){var d=e&&e.cache?e.cache:b.Cache,f=b.Base.create("dataSourceCache",d,[b.Plugin.Base]),c=new f(e);f.NS="tmpClass";return c;}b.mix(a,{NS:"cache",NAME:"cachePlugin"});b.namespace("Plugin").Cache=a;},"3.5.0",{requires:["plugin","cache-base"]});