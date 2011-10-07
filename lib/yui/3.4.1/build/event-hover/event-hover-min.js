/*
YUI 3.4.1 (build 4118)
Copyright 2011 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("event-hover",function(d){var c=d.Lang.isFunction,b=function(){},a={processArgs:function(e){var f=c(e[2])?2:3;return(c(e[f]))?e.splice(f,1)[0]:b;},on:function(i,g,h,f){var e=(g.args)?g.args.slice():[];e.unshift(null);g._detach=i[(f)?"delegate":"on"]({mouseenter:function(j){j.phase="over";h.fire(j);},mouseleave:function(j){var k=g.context||this;e[0]=j;j.type="hover";j.phase="out";g._extra.apply(k,e);}},f);},detach:function(g,e,f){e._detach.detach();}};a.delegate=a.on;a.detachDelegate=a.detach;d.Event.define("hover",a);},"3.4.1",{requires:["event-mouseenter"]});