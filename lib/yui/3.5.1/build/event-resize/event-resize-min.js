/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("event-resize",function(a){a.Event.define("windowresize",{on:(a.UA.gecko&&a.UA.gecko<1.91)?function(d,b,c){b._handle=a.Event.attach("resize",function(f){c.fire(f);});}:function(e,c,d){var b=a.config.windowResizeDelay||100;c._handle=a.Event.attach("resize",function(f){if(c._timer){c._timer.cancel();}c._timer=a.later(b,a,function(){d.fire(f);});});},detach:function(c,b){if(b._timer){b._timer.cancel();}b._handle.detach();}});},"3.5.1",{requires:["event-synthetic"]});