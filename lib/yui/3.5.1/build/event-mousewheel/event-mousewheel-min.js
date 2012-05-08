/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("event-mousewheel",function(c){var b="DOMMouseScroll",a=function(e){var d=c.Array(e,0,true),f;if(c.UA.gecko){d[0]=b;f=c.config.win;}else{f=c.config.doc;}if(d.length<3){d[2]=f;}else{d.splice(2,0,f);}return d;};c.Env.evt.plugins.mousewheel={on:function(){return c.Event._attach(a(arguments));},detach:function(){return c.Event.detach.apply(c.Event,a(arguments));}};},"3.5.1",{requires:["node-base"]});