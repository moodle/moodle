/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("sortable-scroll",function(b){var a=function(){a.superclass.constructor.apply(this,arguments);};b.extend(a,b.Base,{initializer:function(){var c=this.get("host");c.plug(b.Plugin.DDNodeScroll,{node:c.get("container")});c.delegate.on("drop:over",function(d){if(this.dd.nodescroll&&d.drag.nodescroll){d.drag.nodescroll.set("parentScroll",b.one(this.get("container")));}});}},{ATTRS:{host:{value:""}},NAME:"SortScroll",NS:"scroll"});b.namespace("Y.Plugin");b.Plugin.SortableScroll=a;},"3.4.1",{requires:["sortable","dd-scroll"]});