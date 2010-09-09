/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("event-mouseenter",function(c){function b(h,d){var g=h.currentTarget,f=h.relatedTarget;if(g!==f&&!g.contains(f)){d.fire(h);}}var a={proxyType:"mouseover",on:function(f,d,e){d.onHandle=f.on(this.proxyType,b,null,e);},detach:function(e,d){d.onHandle.detach();},delegate:function(g,e,f,d){e.delegateHandle=c.delegate(this.proxyType,b,g,d,null,f);},detachDelegate:function(e,d){d.delegateHandle.detach();}};c.Event.define("mouseenter",a,true);c.Event.define("mouseleave",c.merge(a,{proxyType:"mouseout"}),true);},"3.2.0",{requires:["event-synthetic"]});