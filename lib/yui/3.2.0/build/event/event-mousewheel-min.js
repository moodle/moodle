/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("event-mousewheel",function(c){var b="DOMMouseScroll",a=function(e){var d=c.Array(e,0,true),f;if(c.UA.gecko){d[0]=b;f=c.config.win;}else{f=c.config.doc;}if(d.length<3){d[2]=f;}else{d.splice(2,0,f);}return d;};c.Env.evt.plugins.mousewheel={on:function(){return c.Event._attach(a(arguments));},detach:function(){return c.Event.detach.apply(c.Event,a(arguments));}};},"3.2.0",{requires:["node-base"]});