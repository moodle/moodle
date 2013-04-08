/* YUI 3.9.1 (build 5852) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("cache-plugin",function(e,t){function n(t){var n=t&&t.cache?t.cache:e.Cache,r=e.Base.create("dataSourceCache",n,[e.Plugin.Base]),i=new r(t);return r.NS="tmpClass",i}e.mix(n,{NS:"cache",NAME:"cachePlugin"}),e.namespace("Plugin").Cache=n},"3.9.1",{requires:["plugin","cache-base"]});
