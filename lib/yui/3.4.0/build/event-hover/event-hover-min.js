/*
YUI 3.4.0 (build 3928)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("event-hover",function(d){var c=d.Lang.isFunction,b=function(){},a={processArgs:function(e){var f=c(e[2])?2:3;return(c(e[f]))?e.splice(f,1)[0]:b;},on:function(h,f,g,e){f._detach=h[(e)?"delegate":"on"]({mouseenter:function(i){i.phase="over";g.fire(i);},mouseleave:function(i){var j=f.context||this;i.type="hover";i.phase="out";f._extra.apply(j,[i].concat(f.args));}},e);},detach:function(g,e,f){e._detach.detach();}};a.delegate=a.on;a.detachDelegate=a.detach;d.Event.define("hover",a);},"3.4.0",{requires:["event-mouseenter"]});